<?php
// Incluir el archivo de conexión
include "../../funciones_globales/funciones_conexion.php";

// Obtener el ID del socio de la variable id_socio en la URL
$id_socio = isset($_GET['id_socio']) ? intval($_GET['id_socio']) : '';

// Obtener la conexión
$conexion = obtener_conexion();

if ($conexion) {
    if ($id_socio) {
        // Realizar la consulta SQL para obtener la fecha del último pago del socio
        $query = "SELECT pag_fecha_fin FROM san_pagos WHERE pag_id_socio = '$id_socio' ORDER BY pag_id_pago DESC LIMIT 1";
        $result = mysqli_query($conexion, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $fecha_ultimo_pago = date('Y-m-d', strtotime($row['pag_fecha_fin']));
            
            // Calcula la fecha límite de 4 días adicionales
            $fecha_limite = date('Y-m-d', strtotime($fecha_ultimo_pago . ' + 4 days'));
            
            // Comprueba si hoy es menor o igual a la fecha límite
            $hoy = date('Y-m-d');
            
            if ($hoy <= $fecha_limite) {
                // Si estamos dentro de los 4 días adicionales, devuelve la fecha del último pago
                $fecha = date('d-m-Y', strtotime($row['pag_fecha_fin']));
            } else {
                // Si han pasado más de 4 días, devuelve la fecha actual
                $fecha = date('d-m-Y');
            }
            
            // Devolver la fecha en formato JSON
            echo json_encode(array("success" => true, "fecha_pago" => $fecha));
        } else {
            // No se encontró ningún pago para el usuario dado
            echo json_encode(array("success" => false, "error" => "No se encontró ningún pago para el usuario dado."));
        }
    } else {
        // No se proporcionó ningún ID de socio en la URL
        echo json_encode(array("success" => false, "error" => "No se proporcionó ningún ID de socio en la URL."));
    }

    // Cerrar la conexión
    mysqli_close($conexion);
} else {
    // No se pudo conectar a la base de datos
    echo json_encode(array("success" => false, "error" => "No se pudo conectar a la base de datos."));
}
?>
