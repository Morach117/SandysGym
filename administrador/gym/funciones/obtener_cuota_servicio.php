<?php

// Incluir el archivo de funciones de conexión
include "../../funciones_globales/funciones_conexion.php";

// Obtener el valor del parámetro id_servicio
$id_servicio = $_GET['id_servicio'];

// Establecer la conexión
$conexion = obtener_conexion();

// Verificar si se pudo establecer la conexión
if (!$conexion) {
    echo json_encode(array('success' => false, 'error' => 'Error de conexión a la base de datos'));
    exit;
}

// Escapar el ID del servicio para prevenir inyección SQL
$id_servicio = mysqli_real_escape_string($conexion, $id_servicio);

// Consultar la base de datos para obtener la cuota del servicio
$query = "SELECT ser_cuota FROM san_servicios WHERE ser_id_servicio = $id_servicio";

$resultado = mysqli_query($conexion, $query);

// Verificar si se obtuvo algún resultado
if ($resultado) {
    $fila = mysqli_fetch_assoc($resultado);
    $cuota = $fila['ser_cuota'];

    // Devolver la cuota del servicio en formato JSON
    echo json_encode(array('success' => true, 'cuota' => $cuota));
} else {
    // Si hay un error en la consulta, devolver un mensaje de error en formato JSON
    echo json_encode(array('success' => false, 'error' => 'Error al obtener la cuota del servicio'));
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
