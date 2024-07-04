<?php
require_once("../../funciones_globales/funciones_phpBB.php");

$envio = isset($_POST['envio']) ? true : false;
$fecha = request_var('fecha', '');   // formato esperado: dd-mm-yyyy
$servicio = request_var('servicio', ''); // formato esperado: servicio-meses

if ($envio) {
    // Verificar que tanto $fecha como $servicio contengan el formato esperado
    if (strpos($fecha, '-') !== false && strpos($servicio, '-') !== false) {
        list($dia, $mes, $año) = explode('-', $fecha);
        list($servicio_nombre, $meses) = explode('-', $servicio);

        // Validar que los valores obtenidos sean correctos
        if (checkdate($mes, $dia, $año) && is_numeric($meses) && strlen($año) == 4) {
            // Convertir la fecha inicial a un timestamp
            $fecha_seleccionada = mktime(0, 0, 0, $mes, $dia, $año);

            // Obtener el mes y el año de la fecha seleccionada
            $mes_siguiente = date('m', strtotime("+$meses months", $fecha_seleccionada));
            $año_siguiente = date('Y', strtotime("+$meses months", $fecha_seleccionada));

            // Validar si la fecha siguiente es válida
            while (!checkdate($mes_siguiente, $dia, $año_siguiente)) {
                // Si la fecha no es válida, reducir un día hasta encontrar una fecha válida
                $dia--;
                if ($dia < 1) {
                    $mes_siguiente--;
                    if ($mes_siguiente < 1) {
                        $mes_siguiente = 12;
                        $año_siguiente--;
                    }
                    $ultimo_dia_mes_anterior = date('t', mktime(0, 0, 0, $mes_siguiente, 1, $año_siguiente));
                    $dia = $ultimo_dia_mes_anterior;
                }
            }

            // Construir la fecha final válida
            $fecha_final = sprintf('%02d-%02d-%04d', $dia, $mes_siguiente, $año_siguiente);

            echo $fecha_final;
        } else {
            echo "Error: La fecha o el período de servicio no son válidos.";
        }
    } else {
        echo "Error: El formato de fecha o servicio es incorrecto.";
    }
}
?>
