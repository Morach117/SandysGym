<?php

require '../funciones_globales/phpmailer/src/PHPMailer.php';
require '../funciones_globales/phpmailer/src/SMTP.php';
require '../funciones_globales/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function guardar_nuevo_dia()
{
    global $conexion, $id_usuario, $id_empresa, $gbl_key, $id_consorcio;

    $cuota = obtener_servicio('VISITA');
    $exito = array();

    // Obtener los valores del formulario
    $hor_nombre = request_var('hor_nombre', '');
    $hor_genero = request_var('hor_genero', '');
    $metodo_pago = request_var('metodo_pago', '');
    $id_socio = request_var('id_socio', 0); // Obtener el ID del socio, puede ser 0 si no hay socio
    $cantidad_efectivo = floatval(request_var('cantidad_efectivo', 0.0)); // Obtener el monto en efectivo

    // Inicializar variables de pago
    $hor_efectivo = 0;
    $hor_tarjeta = 0;
    $hor_monedero = 0;
    $importe_con_descuento = $cuota['cuota'];
    $fecha_mov = date('Y-m-d H:i:s');

    // Determinar los valores de pago según el método seleccionado
    if ($metodo_pago == 'E') {
        $hor_efectivo = $importe_con_descuento;
    } else if ($metodo_pago == 'T') {
        $hor_tarjeta = $importe_con_descuento;
    } else if ($metodo_pago == 'M' && $id_socio > 0) {
        // Check the member's monedero balance
        $query_saldo_monedero = "SELECT soc_mon_saldo FROM san_socios WHERE soc_id_socio = $id_socio";
        $resultado_saldo_monedero = mysqli_query($conexion, $query_saldo_monedero);

        if ($resultado_saldo_monedero && mysqli_num_rows($resultado_saldo_monedero) > 0) {
            $fila_saldo_monedero = mysqli_fetch_assoc($resultado_saldo_monedero);
            $saldo_monedero = $fila_saldo_monedero['soc_mon_saldo'];

            if ($saldo_monedero >= $importe_con_descuento) {
                $hor_monedero = $importe_con_descuento;
            } else {
                $hor_monedero = $saldo_monedero;
                $hor_efectivo = $importe_con_descuento - $saldo_monedero; // Calculate the required cash
            }
        } else {
            $hor_monedero = 0.0;
            $hor_efectivo = $importe_con_descuento; // If no balance, full amount in cash
        }

        // Calculate the total paid in cash and monedero
        $total_pagado_efectivo = $hor_efectivo + $hor_monedero;

        if ($total_pagado_efectivo < $importe_con_descuento) {
            $exito['num'] = 12;
            $exito['msj'] = "El monto pagado no es suficiente para cubrir el importe.";
            return $exito;
        }
    }

    // Construir el array con los datos para la inserción
    $datos_sql = array(
        'hor_nombre' => $hor_nombre,
        'hor_fecha' => date('Y-m-d H:i:s'),
        'hor_importe' => $cuota['cuota'],
        'hor_genero' => $hor_genero,
        'hor_id_servicio' => $cuota['id_servicio'],
        'hor_id_usuario' => $id_usuario,
        'hor_id_empresa' => $id_empresa,
        'hor_efectivo' => $hor_efectivo,
        'hor_tarjeta' => $hor_tarjeta,
        'hor_monedero' => $hor_monedero,
        'hor_tipo_pago' => $metodo_pago
    );

    // Construir la consulta de inserción utilizando sentencias preparadas
    $query = "INSERT INTO san_horas (hor_nombre, hor_fecha, hor_importe, hor_genero, hor_id_servicio, hor_id_usuario, hor_id_empresa, hor_efectivo, hor_tarjeta, hor_monedero, hor_tipo_pago) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssdsiiiiiss', $datos_sql['hor_nombre'], $datos_sql['hor_fecha'], $datos_sql['hor_importe'], $datos_sql['hor_genero'], $datos_sql['hor_id_servicio'], $datos_sql['hor_id_usuario'], $datos_sql['hor_id_empresa'], $datos_sql['hor_efectivo'], $datos_sql['hor_tarjeta'], $datos_sql['hor_monedero'], $datos_sql['hor_tipo_pago']);

        // Ejecutar la consulta preparada
        $resultado_insert = mysqli_stmt_execute($stmt);

        // Obtener el ID de la visita insertada
        $id_visita = mysqli_insert_id($conexion);
        $token = hash_hmac('md5', $id_visita, $gbl_key);

        // Verificar si la inserción fue exitosa
        if ($resultado_insert && $id_visita && $token) {
            if ($metodo_pago == 'M' && $id_socio > 0) {
                // Actualizar el monedero del socio si el pago fue por monedero
                $nuevo_saldo_monedero = $saldo_monedero - $hor_monedero;

                // Construir la consulta de actualización del monedero utilizando sentencias preparadas
                $query_actualizar_saldo_monedero = "UPDATE san_socios SET soc_mon_saldo = ? WHERE soc_id_socio = ? AND soc_id_empresa = ?";
                $stmt_monedero = mysqli_prepare($conexion, $query_actualizar_saldo_monedero);
                if ($stmt_monedero) {
                    mysqli_stmt_bind_param($stmt_monedero, 'dii', $nuevo_saldo_monedero, $id_socio, $id_empresa);
                    $resultado_monedero = mysqli_stmt_execute($stmt_monedero);

                    if ($resultado_monedero) {
                        // Insertar el detalle de la operación en san_prepago_detalle
                        $detalle_sql = array(
                            'pred_descripcion' => 'Pago con monedero',
                            'pred_importe' => $hor_monedero,
                            'pred_saldo' => $nuevo_saldo_monedero,
                            'pred_movimiento' => 'Salida',
                            'pred_fecha' => $fecha_mov,
                            'pred_id_socio' => $id_socio,
                            'pred_id_usuario' => $id_usuario
                        );
                        $query_detalle = construir_insert('san_prepago_detalle', $detalle_sql);
                        mysqli_query($conexion, $query_detalle);

                        // Enviar correo electrónico al socio
                        $correo_socio = obtener_correo_socio($id_socio);
                        $asunto = "Confirmación de visita";
                        $mensaje = "Estimado socio, su visita ha sido registrada exitosamente.<br>
                        Monto gastado de su monedero: $" . number_format($hor_monedero, 2) . "<br>
                        Nuevo saldo del monedero: $" . number_format($nuevo_saldo_monedero, 2) . ".<br>
                        <label>Socio: </label> $hor_nombre <br/>
                        <label>Fecha: </label> " . fecha_generica($datos_sql['hor_fecha'], true) . "<br/>
                        <label>Importe: </label> $" . number_format($datos_sql['hor_importe'], 2) . "<br/>
                        <label>Modalidad: </label> VISITA";

                        enviar_correo($correo_socio, $asunto, $mensaje);

                        // Inserción y actualización exitosa
                        $exito['num'] = 1;
                        $exito['msj'] = "Guardado.";
                        $exito['IDV'] = $id_visita;
                        $exito['tkn'] = $token;
                    } else {
                        // Error al actualizar el monedero
                        $exito['num'] = 8;
                        $exito['msj'] = "No se ha podido actualizar el monedero del socio. " . mysqli_error($conexion);
                        return $exito;
                    }

                    mysqli_stmt_close($stmt_monedero);
                } else {
                    // Error al preparar la consulta de actualización del monedero
                    $exito['num'] = 2;
                    $exito['msj'] = "Error al preparar la consulta de actualización del monedero del socio.";
                    return $exito;
                }
            } else {
                if ($id_socio > 0) {
                    // Obtener el saldo del monedero anterior
                    $query_saldo_monedero = "SELECT soc_mon_saldo FROM san_socios WHERE soc_id_socio = $id_socio";
                    $resultado_saldo_monedero = mysqli_query($conexion, $query_saldo_monedero);
                    if ($resultado_saldo_monedero && mysqli_num_rows($resultado_saldo_monedero) > 0) {
                        $fila_saldo_monedero = mysqli_fetch_assoc($resultado_saldo_monedero);
                        $saldo_monedero = $fila_saldo_monedero['soc_mon_saldo'];
                    }

                    // Obtener el porcentaje de incremento del consorcio
                    $query_consorcio = "SELECT con_visita FROM san_consorcios WHERE con_id_consorcio = $id_consorcio";
                    $resultado_consorcio = mysqli_query($conexion, $query_consorcio);
                    if ($resultado_consorcio && mysqli_num_rows($resultado_consorcio) > 0) {
                        $fila_consorcio = mysqli_fetch_assoc($resultado_consorcio);
                        $porcentaje_incremento = floatval($fila_consorcio['con_visita']);
                    }

                    $incremento = $importe_con_descuento * ($porcentaje_incremento / 100);

                    // Sumar el incremento al saldo del monedero
                    $nuevo_saldo_monedero = $saldo_monedero + $incremento;
                    $query_actualizar_saldo_monedero = "UPDATE san_socios SET soc_mon_saldo = ? WHERE soc_id_socio = ? AND soc_id_empresa = ?";
                    $stmt_monedero = mysqli_prepare($conexion, $query_actualizar_saldo_monedero);
                    if ($stmt_monedero) {
                        mysqli_stmt_bind_param($stmt_monedero, 'dii', $nuevo_saldo_monedero, $id_socio, $id_empresa);
                        $resultado_monedero = mysqli_stmt_execute($stmt_monedero);

                        if ($resultado_monedero) {
                            // Insertar el detalle del incremento en san_prepago_detalle
                            $detalle_incremento_sql = array(
                                'pred_descripcion' => 'Incremento por pago con efectivo o tarjeta',
                                'pred_importe' => $incremento,
                                'pred_saldo' => $nuevo_saldo_monedero,
                                'pred_movimiento' => 'Entrada',
                                'pred_fecha' => $fecha_mov,
                                'pred_id_socio' => $id_socio,
                                'pred_id_usuario' => $id_usuario
                            );
                            $query_detalle_incremento = construir_insert('san_prepago_detalle', $detalle_incremento_sql);
                            mysqli_query($conexion, $query_detalle_incremento);

                            // Enviar correo electrónico al socio
                            $correo_socio = obtener_correo_socio($id_socio);
                            $asunto = "Confirmación de visita";
                            $mensaje = "Estimado socio, su visita ha sido registrada exitosamente.<br>
                            Incremento por pago con efectivo o tarjeta: $" . number_format($incremento, 2) . ".<br>
                            Nuevo saldo del monedero: $" . number_format($nuevo_saldo_monedero, 2) . ".<br>
                            <label>Socio: </label> $hor_nombre <br/>
                            <label>Fecha: </label> " . fecha_generica($datos_sql['hor_fecha'], true) . "<br/>
                            <label>Importe: </label> $" . number_format($datos_sql['hor_importe'], 2) . "<br/>
                            <label>Modalidad: </label> VISITA";

                            enviar_correo($correo_socio, $asunto, $mensaje);

                            // Inserción y actualización exitosa
                            $exito['num'] = 1;
                            $exito['msj'] = "Guardado.";
                            $exito['IDV'] = $id_visita;
                            $exito['tkn'] = $token;
                        } else {
                            // Error al actualizar el monedero
                            $exito['num'] = 8;
                            $exito['msj'] = "No se ha podido actualizar el monedero del socio. " . mysqli_error($conexion);
                            return $exito;
                        }

                        mysqli_stmt_close($stmt_monedero);
                    } else {
                        // Error al preparar la consulta de actualización del monedero
                        $exito['num'] = 2;
                        $exito['msj'] = "Error al preparar la consulta de actualización del monedero del socio.";
                        return $exito;
                    }
                } else {
                    // No hay ID de socio, se omite la parte de monedero y correo electrónico

                    // Inserción y actualización exitosa
                    $exito['num'] = 1;
                    $exito['msj'] = "Guardado.";
                    $exito['IDV'] = $id_visita;
                    $exito['tkn'] = $token;
                }
            }
        } else {
            // Error en la inserción
            $exito['num'] = 2;
            $exito['msj'] = "No se ha podido guardar los datos capturados. Intenta nuevamente. " . mysqli_error($conexion);
        }

        mysqli_stmt_close($stmt);
    } else {
        // Error al preparar la consulta de inserción
        $exito['num'] = 2;
        $exito['msj'] = "Error al preparar la consulta de inserción.";
    }

    return $exito;
}

function enviar_correo($destinatario, $asunto, $mensaje)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor de correo
        $mail->isSMTP();
        $mail->Host = 'smtp.ionos.mx'; // Cambia esto por tu servidor SMTP de Ionos
        $mail->SMTPAuth = true;
        $mail->Username = 'administracion@sandysgym.com'; // Cambia esto por tu dirección de correo electrónico
        $mail->Password = 'Splc1979.'; // Cambia esto por tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; // Puerto SMTP para STARTTLS

        // Configuración del correo
        $mail->setFrom('administracion@sandysgym.com', 'Sandys Gym');
        $mail->addAddress($destinatario);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;

        // Enviar el correo
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function obtener_correo_socio($id_socio) {
    global $conexion;
    $query = "SELECT soc_correo FROM san_socios WHERE soc_id_socio = $id_socio";
    $resultado = mysqli_query($conexion, $query);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        return $fila['soc_correo'];
    } else {
        return false;
    }
}
?>
