<?php
function preocesar_venta($array_cant_idart, $p_chk_prepago, $id_socio, $p_tot_prepago, $p_tot_efectivo, $p_sub_total, $p_metodo_pago, $p_tot_tarjeta) {
    global $conexion, $id_usuario, $id_empresa, $id_consorcio, $gbl_key;

    $continuar  = false;
    $socio_deta = array();
    $artic_deta = array();
    $folio      = array();
    $id_venta   = 0;
    $tipo_pago  = 'E';
    $exito      = array();
    $fecha_mov  = date('Y-m-d H:i:s');

    // Asegurarse de que id_usuario tenga un valor predeterminado si no se proporciona o si es menor o igual a 0
    if (!isset($id_usuario) || $id_usuario <= 0) {
        $id_usuario = null; // Asignar valor nulo si no está definido o si es menor o igual a 0
    }

    mysqli_autocommit($conexion, false); //comienza la transaccion

    if ($p_chk_prepago) {
        $tipo_pago = 'P';

        $query = "UPDATE san_socios SET soc_mon_saldo = soc_mon_saldo - $p_tot_prepago WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            $query = "SELECT soc_mon_saldo AS saldo, soc_id_socio AS id_socio, CONCAT(soc_apepat, ' ', soc_apemat, ' ', soc_nombres) AS nombre_s
                      FROM san_socios
                      WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";

            $resultado = mysqli_query($conexion, $query);

            if ($resultado) {
                if ($fila = mysqli_fetch_assoc($resultado)) {
                    $socio_deta['saldo']    = $fila['saldo'];
                    $socio_deta['id_socio'] = $fila['id_socio'];
                    $socio_deta['nombre_s'] = $fila['nombre_s'];
                }

                $datos_sql = array(
                    'pred_id_socio'   => $id_socio,
                    'pred_descripcion'  => 'Pago de Artículos',
                    'pred_importe'      => $p_tot_prepago,
                    'pred_saldo'        => $socio_deta['saldo'],
                    'pred_movimiento'   => 'R',
                    'pred_fecha'        => "$fecha_mov",
                    'pred_id_usuario'   => $id_usuario
                );

                $query = construir_insert('san_prepago_detalle', $datos_sql);
                $resultado = mysqli_query($conexion, $query);

                if ($resultado) {
                    $continuar = true;
                } else {
                    $exito['num'] = 4;
                    $exito['msj'] = "No se ha terminado la venta. No se pudo guardar el detalle del Saldo del Socio.";
                }
            } else {
                $exito['num'] = 3;
                $exito['msj'] = "No se ha terminado la venta. No se pudo obtener el Saldo del Socio después de descontar el importe.";
            }
        } else {
            $exito['num'] = 2;
            $exito['msj'] = "No se ha terminado la venta. No se pudo actualizar el Saldo del Socio.";
        }
    } else {
        $continuar = true;
    }

    if ($continuar) {
        $folio = nuevo_folio();

        if ($folio['folio'] && $folio['anio']) {
            if ($p_metodo_pago == 'T')
                $tipo_pago = 'T';

            $datos_sql = array(
                'ven_folio'             => $folio['folio'],
                'ven_anio'              => $folio['anio'],
                'ven_fecha'             => $fecha_mov,
                'ven_total_efectivo'    => $p_tot_efectivo,
                'ven_total_tarjeta'     => $p_tot_tarjeta,
                'ven_total_prepago'     => $p_tot_prepago,
                'ven_total'             => round($p_sub_total, 2),
                'ven_tipo_pago'         => $tipo_pago,
                'ven_status'            => 'V', /*V=vendido, C=cancelado, P=CanceladoParcial*/
                'ven_id_prepago'        => $id_socio,
                'ven_id_socio'          => isset($socio_deta['id_socio']) ? $socio_deta['id_socio'] : 0,
                'ven_id_usuario'        => $id_usuario,
                'ven_id_empresa'        => $id_empresa
            );

            $query = construir_insert('san_venta', $datos_sql);
            $resultado = mysqli_query($conexion, $query);
            $id_venta = mysqli_insert_id($conexion);

            if ($resultado && $folio['folio'] && $id_venta) {
                foreach ($array_cant_idart as $cant_idart) {
                    list($cantidad, $id_articulo) = explode('-', $cant_idart);

                    $query = "UPDATE san_stock SET stk_existencia = stk_existencia - $cantidad WHERE stk_id_articulo = $id_articulo AND stk_id_empresa = $id_empresa";
                    $resultado = mysqli_query($conexion, $query);

                    if (!$resultado) {
                        $exito['num'] = 5;
                        $exito['msj'] = "No se ha terminado la venta. No se pudo actualizar el Stock. " . mysqli_error($conexion);
                        break;
                    }

                    $artic_deta = obtener_detalle_articulo($id_articulo);

                    $datos_sql = array(
                        'vende_id_articulo'   => $artic_deta['art_id_articulo'],
                        'vende_id_venta'      => $id_venta,
                        'vende_cantidad'      => $cantidad,
                        'vende_costo'         => $artic_deta['art_costo'],
                        'vende_precio_pre'    => $artic_deta['art_precio'],
                        'vende_precio'        => $artic_deta['art_precio']
                    );

                    $query = construir_insert('san_venta_detalle', $datos_sql);
                    $resultado = mysqli_query($conexion, $query);

                    if ($resultado) {
                        $exito['num'] = 1;
                        $exito['msj'] = "Venta terminada. Transacción finalizada.";
                        $exito['folio'] = $folio['folio'];
                        $exito['anio'] = $folio['anio'];
                        $exito['ticket'] = $folio['ticket'];
                        $exito['IDV'] = $id_venta;
                    } else {
                        $exito['num'] = 6;
                        $exito['msj'] = "No se ha terminado la venta. No se puede guardar el detalle de la Venta. " . mysqli_error($conexion);
                        break;
                    }
                }
            } else {
                $exito['num'] = 7;
                $exito['msj'] = "No se ha terminado la venta. No se pudo guardar la Venta o no se pudo obtener el ID de la Venta. " . mysqli_error($conexion);
            }
        } else {
            $exito['num'] = 8;
            $exito['msj'] = "No se pudo obtener el folio de la venta.";
        }
    }

    if ($exito['num'] == 1) {
        if ($p_chk_prepago) {
            $exito['msj'] .= " Se descontó $" . number_format($p_tot_prepago, 2) . " del saldo del socio: " . (isset($socio_deta['nombre_s']) ? $socio_deta['nombre_s'] : '') . ". Saldo actual: $" . number_format($socio_deta['saldo'], 2);
        }

        // Incremento de saldo en monedero para pago con efectivo o tarjeta (si hay un usuario válido)
        if (($p_metodo_pago == 'E' || $p_metodo_pago == 'T') && $id_usuario !== null) {
            $incremento = 0;
            $nuevo_saldo_monedero = 0;

            $query_saldo_monedero = "SELECT soc_mon_saldo FROM san_socios WHERE soc_id_socio = $id_socio";
            $resultado_saldo_monedero = mysqli_query($conexion, $query_saldo_monedero);
            if ($resultado_saldo_monedero && mysqli_num_rows($resultado_saldo_monedero) > 0) {
                $fila_saldo_monedero = mysqli_fetch_assoc($resultado_saldo_monedero);
                $saldo_monedero = $fila_saldo_monedero['soc_mon_saldo'];

                $query_consorcio = "SELECT con_venta FROM san_consorcios WHERE con_id_consorcio = $id_consorcio";
                $resultado_consorcio = mysqli_query($conexion, $query_consorcio);
                if ($resultado_consorcio && mysqli_num_rows($resultado_consorcio) > 0) {
                    $fila_consorcio = mysqli_fetch_assoc($resultado_consorcio);
                    $porcentaje_incremento = floatval($fila_consorcio['con_venta']);
                }

                $incremento = $p_sub_total * ($porcentaje_incremento / 100);
                $nuevo_saldo_monedero = $saldo_monedero + $incremento;

                $query_actualizar_saldo_monedero = "UPDATE san_socios SET soc_mon_saldo = ? WHERE soc_id_socio = ? AND soc_id_empresa = ?";
                $stmt_monedero = mysqli_prepare($conexion, $query_actualizar_saldo_monedero);
                if ($stmt_monedero) {
                    mysqli_stmt_bind_param($stmt_monedero, 'dii', $nuevo_saldo_monedero, $id_socio, $id_empresa);
                    $resultado_monedero = mysqli_stmt_execute($stmt_monedero);

                    if ($resultado_monedero) {
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
                    } else {
                        $exito['num'] = 8;
                        $exito['msj'] = "No se ha podido actualizar el monedero del socio. " . mysqli_error($conexion);
                        return $exito;
                    }
                    mysqli_stmt_close($stmt_monedero);
                } else {
                    $exito['num'] = 2;
                    $exito['msj'] = "Error al preparar la consulta de actualización del monedero del socio.";
                    return $exito;
                }
            }
        }

        // Enviar correo electrónico al socio con los detalles de la compra
        $correo_socio = obtener_correo_socio($id_socio);
        $asunto = "Confirmación de compra";
        $mensaje = "Estimado socio, su compra ha sido registrada exitosamente.<br>";
        if ($p_metodo_pago == 'E' || $p_metodo_pago == 'T') {
            $mensaje .= "Incremento por pago con efectivo o tarjeta: $" . number_format($incremento, 2) . ".<br>";
            $mensaje .= "Nuevo saldo del monedero: $" . number_format($nuevo_saldo_monedero, 2) . ".<br>";
        }
        $mensaje .= "<label>Socio: </label> " . (isset($socio_deta['nombre_s']) ? $socio_deta['nombre_s'] : '') . "<br/>
                    <label>Fecha: </label> " . fecha_generica($fecha_mov, true) . "<br/>
                    <label>Importe total: </label> $" . number_format($p_sub_total, 2) . "<br/><br/>";

        // Agregar detalles de los productos comprados al mensaje
        $mensaje .= "<label>Productos comprados:</label><br/>";
        foreach ($array_cant_idart as $cant_idart) {
            list($cantidad, $id_articulo) = explode('-', $cant_idart);
            $artic_deta = obtener_detalle_articulo($id_articulo);
            $mensaje .= "Producto: " . $artic_deta['art_descripcion'] . "<br/>Cantidad: " . $cantidad . "<br/>Precio: $" . number_format($artic_deta['art_precio'], 2) . "<br/><br/>";
        }

        enviar_correo($correo_socio, $asunto, $mensaje);

        mysqli_commit($conexion);
    } else {
        mysqli_rollback($conexion);
    }

    return $exito;
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

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

function obtener_detalle_articulo($id_articulo) {
    global $conexion, $id_consorcio;

    $query = "SELECT * FROM san_articulos WHERE art_id_articulo = $id_articulo AND art_id_consorcio = $id_consorcio";

    mysqli_autocommit($conexion, false);
    $resultado = mysqli_query($conexion, $query);

    if ($resultado)
        if ($fila = mysqli_fetch_assoc($resultado))
            return $fila;

    return false;
}

function obtener_saldo_socio($id_socio) {
    global $conexion, $id_empresa;

    $query = "SELECT soc_mon_saldo AS saldo FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado)
        if ($fila = mysqli_fetch_assoc($resultado))
            return $fila['saldo'];

    return false;
}

function lista_articulos() {
    global $conexion, $id_empresa, $id_consorcio;

    $datos = "";
    $colspan = 3;

    $query = "SELECT art_id_articulo AS id_articulo, art_codigo AS codigo, art_descripcion AS descripcion, stk_existencia AS existencia, ROUND(art_precio, 2) AS precio
              FROM san_articulos
              INNER JOIN san_stock ON stk_id_articulo = art_id_articulo
              AND stk_id_empresa = $id_empresa
              AND art_id_consorcio = $id_consorcio
              AND art_status = 'A'
              ORDER BY existencia DESC, descripcion";

    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $class = '';
            if ($fila['existencia'] <= 0)
                $class = "danger";

            $datos .= "<tr onclick='agregar_articulo_venta($fila[id_articulo])' class='$class'>
                            <td>".$fila['descripcion']."</td>
                            <td class='text-right'>".$fila['existencia']."</td>
                            <td class='text-right'>$".$fila['precio']."</td>
                        </tr>";
        }
    } else {
        $datos = "<tr><td colspan='$colspan'>".mysqli_error($conexion)."</td></tr>";
    }

    if (!$datos)
        $datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";

    return $datos;
}
?>
