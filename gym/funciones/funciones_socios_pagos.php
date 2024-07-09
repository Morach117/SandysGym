<?php
function obtener_servicios($default = '')
{
    global $conexion, $id_consorcio, $id_giro;

    $datos = "<option value=''>Selecciona...</option>";

    $query = "SELECT ser_id_servicio AS id_servicio, 
                     ser_clave AS clave,
                     ser_descripcion AS descripcion,
                     ROUND( ser_cuota, 2 ) AS cuota,
                     ser_meses AS meses
              FROM   san_servicios 
              WHERE  ser_tipo = 'PERIODO'
                     AND ser_id_consorcio = $id_consorcio
                     AND ser_id_giro = $id_giro
                     AND ser_status != 'D'"; // Excluir servicios con estatus 0 y 'D'

    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $servicio = $fila['id_servicio'] . '-' . $fila['meses'];

            if ($default == $servicio)
                $datos .= "<option selected value='$servicio'>$fila[descripcion] - $$fila[cuota]</option>";
            else
                $datos .= "<option value='$servicio'>$fila[descripcion] - $$fila[cuota]</option>";
        }
    } else {
        echo "Error: " . mysqli_error($conexion);
    }

    return $datos;
}


function obtener_servicio($id_servicio)
{
    global $conexion, $id_consorcio, $id_giro;
    
    $query    = "  SELECT   ser_id_servicio AS id_servicio, 
                                ser_clave AS clave,
                                ser_descripcion AS descripcion,
                                ROUND( ser_cuota, 2 ) AS cuota,
                                ser_meses AS meses
                    FROM    san_servicios 
                    WHERE   ser_id_servicio = $id_servicio
                    AND     ser_id_consorcio = $id_consorcio
                    AND     ser_id_giro = $id_giro";
    
    $resultado    = mysqli_query( $conexion, $query );
    
    if( $resultado )
        if( $fila = mysqli_fetch_assoc( $resultado ) )
            return $fila;
    
    return false;
}

function lista_pagos_socio()
{
    Global $conexion, $id_empresa, $id_consorcio, $id_giro;
    
    $datos       = "";
    $colspan    = 6;
    $fecha_mov    = date( 'Y-m-d' );
    $id_socio    = request_var( 'id_socio', 0 );
    
    $query        = "   SELECT      pag_id_pago,
                                    pag_id_socio,
                                    pag_status AS status,
                                    ser_descripcion,
                                    LOWER( DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y %r' ) ) AS fecha_pago,
                                    DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini,
                                    DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin,
                                    ROUND( pag_importe, 2 ) AS importe,
                                    IF( '$fecha_mov' > pag_fecha_fin, 'VENCIDO', 'VIGENTE' ) AS vigencia
                        FROM        san_pagos 
                        INNER JOIN    san_servicios ON ser_id_servicio = pag_id_servicio
                        WHERE       pag_id_socio = $id_socio
                        AND            pag_id_empresa = $id_empresa
                        AND            ser_id_consorcio = $id_consorcio
                        AND            ser_id_giro = $id_giro
                        ORDER BY    pag_id_pago DESC";
    
    $resultado    = mysqli_query( $conexion, $query );
    
    if( $resultado )
    {
        while( $fila = mysqli_fetch_assoc( $resultado ) )
        {
            if( $fila['vigencia'] == 'VIGENTE' && $fila['status'] == 'A' )
                $opciones = "<a href='.?s=socios&i=eliminarp&id_pago=$fila[pag_id_pago]&id_socio=$fila[pag_id_socio]'><span class='text-danger glyphicon glyphicon-remove-sign'></span></a>";
            else
                $opciones = "";
            
            $class    = ( $fila['status'] == 'E' ) ? 'danger':'';
            
            $datos    .= "<tr class='$class'>
                            <td>$opciones</td>
                            <td>$fila[ser_descripcion]</td>
                            <td>$fila[fecha_pago]</td>
                            <td>$fila[fecha_ini]</td>
                            <td>$fila[fecha_fin]</td>
                            <td class='text-right'>$$fila[importe]</td>
                        </tr>";
        }
    }
    else
        $datos = "<tr><td colspan='$colspan'>Ocurrió un error al obtener los datos. ".mysqli_error( $conexion )."</td></tr>";
    
    if( !$datos )
        $datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
    
    return $datos;
}

require '../funciones_globales/phpmailer/src/PHPMailer.php';
require '../funciones_globales/phpmailer/src/SMTP.php';
require '../funciones_globales/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


function guardar_pago_socio()
{
    global $conexion, $id_usuario, $id_empresa, $gbl_key, $id_consorcio;

    $exito = array();
    $pag_fecha_pago = fecha_formato_mysql(request_var('pag_fecha_pago', date('d-m-Y')));
    $pag_fecha_ini = fecha_formato_mysql(request_var('pag_fecha_ini', ''));
    $pag_fecha_fin = fecha_formato_mysql(request_var('pag_fecha_fin', ''));
    list($id_servicio, $meses) = explode('-', request_var('servicio', ''));
    $importe = request_var('pag_importe', 0.0);
    $fecha_mov = $pag_fecha_pago . " " . date('H:i:s');
    $id_socio = request_var('id_socio', 0);
    $codigo_promocion = isset($_POST['codigo_promocion']) ? $_POST['codigo_promocion'] : '';
    $v_metodo_pago = request_var('m_pago', '');
    $cantidad_efectivo = floatval(request_var('cantidad_efectivo', 0.0)); // Nueva variable para cantidad_efectivo

    $v_pag_efectivo = 0;
    $v_pag_tarjeta = 0;

    if ($pag_fecha_ini && $pag_fecha_fin) {
        if ($id_servicio && $meses && $id_socio) {
            $servicio = obtener_servicio($id_servicio);

            if ($servicio) {
                if (($servicio['clave'] == 'MEN PARCIAL' && $importe >= 0) || $servicio['clave'] != 'MEN PARCIAL') {
                    if ($servicio['clave'] != 'MEN PARCIAL') {
                        $importe = $servicio['cuota'];
                    }

                    if ($importe >= 0) {
                        $descuento = 0.0;

                        if (!empty($codigo_promocion)) {
                            $current_date = date("Y-m-d");
                            $query_validar_codigo = "SELECT p.porcentaje_descuento, p.tipo_promocion, c.status
                                                    FROM san_codigos c
                                                    INNER JOIN san_promociones p ON c.id_promocion = p.id_promocion
                                                    WHERE c.codigo_generado = '$codigo_promocion' 
                                                    AND c.status = '1' 
                                                    AND p.vigencia_inicial <= '$current_date' 
                                                    AND p.vigencia_final >= '$current_date'";
                            $resultado_validar_codigo = mysqli_query($conexion, $query_validar_codigo);

                            if (mysqli_num_rows($resultado_validar_codigo) > 0) {
                                $fila_promocion = mysqli_fetch_assoc($resultado_validar_codigo);
                                $porcentaje_descuento = $fila_promocion['porcentaje_descuento'];
                                $tipo_promocion = $fila_promocion['tipo_promocion'];
                                $status = $fila_promocion['status'];

                                if ($tipo_promocion == 'Individual' && $status == '1') {
                                    $query_actualizar_codigo = "UPDATE san_codigos SET status = '0' WHERE codigo_generado = '$codigo_promocion'";
                                    mysqli_query($conexion, $query_actualizar_codigo);
                                }

                                $descuento += $porcentaje_descuento;
                            } else {
                                $exito['num'] = 9;
                                $exito['msj'] = "El código de promoción proporcionado no es válido o ya ha sido utilizado.";
                                return $exito;
                            }
                        }

                        $query_descuento = "SELECT soc_descuento FROM san_socios WHERE soc_id_socio = $id_socio";
                        $resultado_descuento = mysqli_query($conexion, $query_descuento);
                        $fila_descuento = mysqli_fetch_assoc($resultado_descuento);
                        $descuento += isset($fila_descuento['soc_descuento']) ? floatval($fila_descuento['soc_descuento']) : 0.0;

                        $importe_con_descuento = $importe * (1 - $descuento / 100);

                        $pag_monedero = 0.0;
                        if ($v_metodo_pago == 'M') {
                            $query_saldo_monedero = "SELECT soc_mon_saldo FROM san_socios WHERE soc_id_socio = $id_socio";
                            $resultado_saldo_monedero = mysqli_query($conexion, $query_saldo_monedero);

                            if ($resultado_saldo_monedero && mysqli_num_rows($resultado_saldo_monedero) > 0) {
                                $fila_saldo_monedero = mysqli_fetch_assoc($resultado_saldo_monedero);
                                $saldo_monedero = $fila_saldo_monedero['soc_mon_saldo'];

                                if ($saldo_monedero >= $importe_con_descuento) {
                                    $pag_monedero = $importe_con_descuento;
                                } else {
                                    $pag_monedero = $saldo_monedero;
                                }
                            } else {
                                $pag_monedero = 0.0;
                            }
                        }

                        $total_pagado_efectivo = $cantidad_efectivo + $pag_monedero;

                        if ($v_metodo_pago == 'M' && $total_pagado_efectivo < $importe_con_descuento) {
                            $exito['num'] = 12;
                            $exito['msj'] = "El monto pagado no es suficiente para cubrir el importe con descuento.";
                        } else {
                            if ($v_metodo_pago == 'E') {
                                $v_pag_efectivo = $importe_con_descuento;
                            } else if ($v_metodo_pago == 'T') {
                                $v_pag_tarjeta = $importe_con_descuento;
                            } else if ($v_metodo_pago == 'M') {
                                $v_pag_efectivo = $cantidad_efectivo;
                            }

                            $datos_sql = array(
                                'pag_id_socio' => $id_socio,
                                'pag_fecha_pago' => $fecha_mov,
                                'pag_id_servicio' => $id_servicio,
                                'pag_fecha_ini' => $pag_fecha_ini,
                                'pag_fecha_fin' => $pag_fecha_fin,
                                'pag_efectivo' => $v_pag_efectivo,
                                'pag_tarjeta' => $v_pag_tarjeta,
                                'pag_importe' => round($importe_con_descuento, 2),
                                'pag_tipo_pago' => $v_metodo_pago,
                                'pag_id_usuario' => $id_usuario,
                                'pag_id_empresa' => $id_empresa,
                                'pag_monedero' => $pag_monedero
                            );

                            $query = construir_insert('san_pagos', $datos_sql);
                            $resultado = mysqli_query($conexion, $query);
                            $id_pago = mysqli_insert_id($conexion);
                            $token = hash_hmac('md5', $id_pago, $gbl_key);

                            if ($resultado && $id_pago && $token) {
                                if ($v_metodo_pago == 'M') {
                                    $nuevo_saldo_monedero = $saldo_monedero - $pag_monedero;

                                    $query_actualizar_saldo_monedero = "UPDATE san_socios SET soc_mon_saldo = $nuevo_saldo_monedero WHERE soc_id_socio = $id_socio";
                                    $resultado_actualizar_saldo_monedero = mysqli_query($conexion, $query_actualizar_saldo_monedero);

                                    if (!$resultado_actualizar_saldo_monedero) {
                                        $exito['num'] = 8;
                                        $exito['msj'] = "No se ha podido actualizar el saldo del monedero. " . mysqli_error($conexion);
                                        return $exito;
                                    }

                                    // Insertar el detalle de la operación en san_prepago_detalle
                                    $detalle_sql = array(
                                        'pred_descripcion' => 'Pago con monedero',
                                        'pred_importe' => $pag_monedero,
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
                                    $asunto = "Confirmación de pago con monedero";
                                    $mensaje = "Estimado socio, su pago de $" . number_format($importe_con_descuento, 2) . " ha sido realizado exitosamente utilizando el monedero.";
                                    enviar_correo($correo_socio, $asunto, $mensaje);
                                } else {
                                    // Obtener el saldo del monedero anterior
                                    $query_saldo_monedero = "SELECT soc_mon_saldo FROM san_socios WHERE soc_id_socio = $id_socio";
                                    $resultado_saldo_monedero = mysqli_query($conexion, $query_saldo_monedero);
                                    if ($resultado_saldo_monedero && mysqli_num_rows($resultado_saldo_monedero) > 0) {
                                        $fila_saldo_monedero = mysqli_fetch_assoc($resultado_saldo_monedero);
                                        $saldo_monedero = $fila_saldo_monedero['soc_mon_saldo'];
                                    }

                                    // Obtener el porcentaje de incremento del consorcio
                                    $query_consorcio = "SELECT con_mensualidad FROM san_consorcios WHERE con_id_consorcio = $id_consorcio";
                                    $resultado_consorcio = mysqli_query($conexion, $query_consorcio);
                                    if ($resultado_consorcio && mysqli_num_rows($resultado_consorcio) > 0) {
                                        $fila_consorcio = mysqli_fetch_assoc($resultado_consorcio);
                                        $porcentaje_incremento = floatval($fila_consorcio['con_mensualidad']);
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
                                            $asunto = "Confirmación de pago";
                                            $mensaje = "Estimado socio, su pago de $" . number_format($importe_con_descuento, 2) . " ha sido realizado exitosamente.<br>
                                            Incremento por pago con efectivo o tarjeta: $" . number_format($incremento, 2) . ".";
                                            enviar_correo($correo_socio, $asunto, $mensaje);
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
                                }

                                $foto = subir_fotografia();

                                $exito['num'] = 1;
                                $exito['msj'] = "Pago y fechas guardados correctamente.";
                                $exito['IDS'] = $id_socio;
                                $exito['IDP'] = $id_pago;
                                $exito['tkn'] = $token;
                            } else {
                                $exito['num'] = 8;
                                $exito['msj'] = "No se ha podido guardar la información de este socio. " . mysqli_error($conexion);
                            }
                        }
                    } else {
                        $exito['num'] = 6;
                        $exito['msj'] = "El importe del servicio seleccionado es inválido.";
                    }
                } else {
                    $exito['num'] = 5;
                    $exito['msj'] = "Se ha detectado Servicio Parcial pero no se ha indicado el importe a pagar.";
                }
            } else {
                $exito['num'] = 4;
                $exito['msj'] = "No se puede identificar el tipo de servicio seleccionado.";
            }
        } else {
            $exito['num'] = 3;
            $exito['msj'] = "Faltan datos importantes para guardar el pago.";
        }
    } else {
        $exito['num'] = 2;
        $exito['msj'] = "Las fechas de inicio y fin son obligatorias.";
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
        $mail->CharSet = 'UTF-8'; // Establecer la codificación de caracteres a UTF-8
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
