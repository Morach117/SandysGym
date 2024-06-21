<?php
// Incluir el archivo de conexión
include "../../funciones_globales/funciones_conexion.php";

// Obtener el ID del socio de la variable id_socio en la URL
$id_socio = isset($_GET['id_socio']) ? $_GET['id_socio'] : '';

// Obtener la conexión
$conexion = obtener_conexion();

if ($conexion) {
    if ($id_socio) {
        // Realizar la consulta SQL para obtener el saldo del monedero
        $query = "SELECT soc_mon_saldo FROM san_socios WHERE soc_id_socio = '$id_socio'";

        $result = mysqli_query($conexion, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $saldo_monedero = $row['soc_mon_saldo'];

            // Devolver el saldo del monedero en formato JSON
            echo json_encode(array("success" => true, "saldo_monedero" => $saldo_monedero));
        } else {
            // No se encontró el saldo del monedero para el usuario dado
            echo json_encode(array("success" => false, "error" => "No se encontró el saldo del monedero para el usuario dado."));
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
