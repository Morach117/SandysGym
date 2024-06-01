<?php
// Incluir el archivo de conexión
include "../../funciones_globales/funciones_conexion.php";

// Obtener la conexión
$conexion = obtener_conexion();

// Obtener el ID del servicio desde la solicitud GET
$id_servicio = isset($_GET['id_servicio']) ? $_GET['id_servicio'] : null;

if ($id_servicio) {
    // Realizar la consulta para verificar si hay descuentos promocionales permitidos para el servicio
    $query = "SELECT * FROM san_descuentos_promociones WHERE id_servicio = $id_servicio";
    
    // Ejecutar la consulta
    $result = mysqli_query($conexion, $query);
    
    // Verificar si hay algún resultado
    if ($result && mysqli_num_rows($result) > 0) {
        // Si hay resultados, significa que el servicio tiene descuentos promocionales permitidos
        $response = array(
            'success' => true
        );
    } else {
        // Verificar si el ID del servicio es 125 o 126 y está en la tabla san_servicios
        $query_check = "SELECT * FROM san_servicios WHERE ser_id_servicio IN (125, 126) AND ser_id_servicio = $id_servicio";
        $result_check = mysqli_query($conexion, $query_check);

        if ($result_check && mysqli_num_rows($result_check) > 0) {
            // Si el ID es 125 o 126 y está en la tabla san_servicios, permitir el descuento
            $response = array(
                'success' => true
            );
        } else {
            // Si no hay resultados y el ID no es 125 ni 126, el servicio no tiene descuentos promocionales permitidos
            $response = array(
                'success' => false,
                'error' => 'El servicio seleccionado no tiene descuentos promocionales permitidos.'
            );
        }
    }
} else {
    // Si no se proporcionó el ID del servicio, devolver un error
    $response = array(
        'success' => false,
        'error' => 'No se proporcionó un ID de servicio válido.'
    );
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>
