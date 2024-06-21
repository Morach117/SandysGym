<?php

function obtener_prepago()
{
    global $conexion, $id_empresa;
    
    $id_socio = request_var('id_socio', 0);
    
    $query = "SELECT CONCAT(soc_apepat, ' ', soc_apemat, ' ', soc_nombres) AS nombre,
                     soc_mon_saldo AS saldo,
                     soc_id_socio AS id_socio
              FROM san_socios
              WHERE soc_id_socio = $id_socio
              AND soc_id_empresa = $id_empresa";
    
    $resultado = mysqli_query($conexion, $query);
    
    if ($resultado) {
        if ($fila = mysqli_fetch_assoc($resultado)) {
            return $fila;
        } else {
            echo "No se encontraron resultados para la consulta.";
            return null;
        }
    } else {
        echo "Error en la consulta: " . mysqli_error($conexion);
        return false;
    }
}

function obtener_prepago_detalle()
{
    global $conexion, $id_empresa;
    
    $id_socio = request_var('id_socio', 0);
    $datos = "";
    $colspan = 7;
    
    $query = "SELECT pred_id_pdetalle AS id_pdetalle,
                     pred_descripcion AS p_descripcion,
                     ROUND(pred_importe, 2) AS importe,
                     ROUND(pred_saldo, 2) AS saldo,
                     CASE pred_movimiento
                         WHEN 'R' THEN 'Resta'
                         WHEN 'S' THEN 'Suma'
                     END AS movimiento,
                     DATE_FORMAT(pred_fecha, '%d-%m-%Y') AS fecha,
                     LOWER(DATE_FORMAT(pred_fecha, '%r')) AS hora
              FROM san_prepago_detalle
              WHERE pred_id_socio = $id_socio
              ORDER BY id_pdetalle DESC";
    
    $resultado = mysqli_query($conexion, $query);
    
    if ($resultado) {
        $i = 1;
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos .= "<tr>
                            <td>$i</td>
                            <td>$fila[p_descripcion]</td>
                            <td class='text-right'>$$fila[importe]</td>
                            <td class='text-right'>$$fila[saldo]</td>
                            <td>$fila[movimiento]</td>
                            <td>$fila[fecha]</td>
                            <td>$fila[hora]</td>
                        </tr>";
            $i++;
        }
    } else {
        $datos = "<tr><td colspan='$colspan'>" . mysqli_error($conexion) . "</td></tr>";
    }
    
    if (!$datos) {
        $datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
    }
    
    return $datos;
}

require '../funciones_globales/phpmailer/src/PHPMailer.php';
require '../funciones_globales/phpmailer/src/SMTP.php';
require '../funciones_globales/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


function actualizar_prepago()
{
    global $conexion, $id_usuario, $id_empresa, $gbl_key, $id_consorcio;

    $prep_importe = request_var('prep_importe', 0.0);
    $prep_id_socio = request_var('id_socio', 0);
    $fecha_mov = date('Y-m-d H:i:s');

    // Iniciar transacción
    mysqli_autocommit($conexion, false);

    // Actualizar el saldo del socio
    $query = "UPDATE san_socios 
              SET soc_mon_saldo = soc_mon_saldo + $prep_importe 
              WHERE soc_id_socio = $prep_id_socio 
              AND soc_id_empresa = $id_empresa";

    $resultado = mysqli_query($conexion, $query);

    if ($resultado !== false && mysqli_affected_rows($conexion) > 0) {
        // Obtener el nuevo saldo del socio
        $query = "SELECT soc_mon_saldo AS saldo, soc_correo AS email, soc_nombres AS nombre
                  FROM san_socios
                  WHERE soc_id_socio = $prep_id_socio
                  AND soc_id_empresa = $id_empresa";

        $resultado_saldo = mysqli_query($conexion, $query);

        if ($resultado_saldo !== false) {
            $fila_saldo = mysqli_fetch_assoc($resultado_saldo);
            if ($fila_saldo) {
                $nuevo_saldo = $fila_saldo['saldo'];
                $email = $fila_saldo['email'];
                $name = $fila_saldo['nombre'];

                // Obtener el porcentaje de incremento del consorcio
                $query_consorcio = "SELECT con_abono FROM san_consorcios WHERE con_id_consorcio = $id_consorcio";
                $resultado_consorcio = mysqli_query($conexion, $query_consorcio);

                if ($resultado_consorcio && mysqli_num_rows($resultado_consorcio) > 0) {
                    $fila_consorcio = mysqli_fetch_assoc($resultado_consorcio);
                    $porcentaje_incremento = floatval($fila_consorcio['con_abono']);
                } else {
                    $porcentaje_incremento = 10; // Valor por defecto en caso de que la consulta falle
                }

                $incremento = $prep_importe * ($porcentaje_incremento / 100);
                $nuevo_saldo += $incremento;

                // Actualizar el saldo del socio con el incremento adicional
                $query_actualizar_saldo = "UPDATE san_socios 
                                           SET soc_mon_saldo = $nuevo_saldo 
                                           WHERE soc_id_socio = $prep_id_socio 
                                           AND soc_id_empresa = $id_empresa";

                $resultado_actualizar_saldo = mysqli_query($conexion, $query_actualizar_saldo);

                if ($resultado_actualizar_saldo !== false) {
                    // Insertar en san_prepago_detalle
                    $datos_sql = array(
                        'pred_descripcion'  => 'ABONO A CUENTA PREPAGO',
                        'pred_importe'      => $prep_importe,
                        'pred_saldo'        => $nuevo_saldo, // Usamos el saldo actualizado
                        'pred_movimiento'   => 'S',
                        'pred_fecha'        => $fecha_mov,
                        'pred_id_socio'     => $prep_id_socio,
                        'pred_id_usuario'   => $id_usuario
                    );

                    $query_insert = construir_insert('san_prepago_detalle', $datos_sql);
                    $resultado_insert = mysqli_query($conexion, $query_insert);

                    // Insertar el detalle del incremento en san_prepago_detalle
                    $detalle_incremento_sql = array(
                        'pred_descripcion'  => 'Incremento por abono',
                        'pred_importe'      => $incremento,
                        'pred_saldo'        => $nuevo_saldo,
                        'pred_movimiento'   => 'S',
                        'pred_fecha'        => $fecha_mov,
                        'pred_id_socio'     => $prep_id_socio,
                        'pred_id_usuario'   => $id_usuario
                    );

                    $query_detalle_incremento = construir_insert('san_prepago_detalle', $detalle_incremento_sql);
                    mysqli_query($conexion, $query_detalle_incremento);

                    $token = hash_hmac('md5', $prep_id_socio, $gbl_key);

                    if ($resultado_insert !== false && $token) {
                        $mensaje['num'] = 1;
                        $mensaje['msj'] = "El Prepago se ha agregado de manera correcta.";
                        $mensaje['IDS'] = $prep_id_socio;
                        $mensaje['tkn'] = $token;

                        // Enviar correo al socio
                        enviar_correo($email, $name, $nuevo_saldo, $prep_importe, $incremento);
                    } else {
                        $mensaje['num'] = 3;
                        $mensaje['msj'] = "No se ha podido actualizar el detalle del Prepago. Intenta nuevamente. " . mysqli_error($conexion);
                    }
                } else {
                    $mensaje['num'] = 5;
                    $mensaje['msj'] = "No se ha podido actualizar el saldo del socio con el incremento adicional. " . mysqli_error($conexion);
                }
            } else {
                $mensaje['num'] = 4;
                $mensaje['msj'] = "No se ha podido obtener los detalles del socio. Intenta nuevamente.";
            }
        } else {
            $mensaje['num'] = 4;
            $mensaje['msj'] = "Error en la consulta para obtener el saldo del socio: " . mysqli_error($conexion);
        }
    } else {
        $mensaje['num'] = 2;
        $mensaje['msj'] = "No se ha podido agregar el Importe para el Prepago. Intenta nuevamente. " . mysqli_error($conexion);
    }

    if ($mensaje['num'] == 1) {
        mysqli_commit($conexion);
    } else {
        mysqli_rollback($conexion);
    }

    return $mensaje;
}

function enviar_correo($email, $name, $nuevo_saldo, $prep_importe, $incremento)
{
    $mail = new PHPMailer(true);

    try {
        // Activa el modo de depuración
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function($str, $level) {
            file_put_contents('phpmailer_debug.log', date('Y-m-d H:i:s')." [$level] $str\n", FILE_APPEND);
        };

        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.ionos.mx'; // Cambia esto por tu servidor SMTP de Ionos
        $mail->SMTPAuth = true;
        $mail->Username = 'administracion@sandysgym.com'; // Cambia esto por tu dirección de correo electrónico
        $mail->Password = 'Splc1979.'; // Cambia esto por tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; // Puerto SMTP para STARTTLS

        // Configuración del correo electrónico
        $mail->setFrom('administracion@sandysgym.com', 'Sandys Gym');
        $mail->addAddress($email, $name);
        $mail->Subject = 'Actualización de Saldo de Prepago';
        $mail->isHTML(true);
        $mail->Body = '
            <h1 style="color: #333;">Actualización de Saldo de Prepago</h1>
            <p>Hola ' . $name . ',</p>
            <p>Se ha realizado un abono a tu cuenta prepago de: <strong>' . $prep_importe . '</strong></p>
            <p>Se ha aplicado un incremento de: <strong>' . $incremento . '</strong></p>
            <p>Tu nuevo saldo es: <strong>' . $nuevo_saldo . '</strong></p>
            <p>¡Gracias por ser parte de Sandys Gym!</p>
        ';

        // Envío del correo electrónico
        $mail->send();
    } catch (Exception $e) {
        // Registro del error en el archivo de depuración
        file_put_contents('phpmailer_debug.log', date('Y-m-d H:i:s')." [ERROR] ".$e->getMessage()."\n", FILE_APPEND);
    }
}
?>


