<?php
// Incluir el archivo de conexión
include "../../funciones_globales/funciones_conexion.php";

// Obtener la conexión
$conexion = obtener_conexion();

if ($conexion) {
    // Realizar la consulta SQL para obtener los nombres y saldo del monedero de todos los socios
    $query = "SELECT soc_id_socio, CONCAT(soc_nombres, ' ', soc_apepat, ' ', soc_apemat) AS nombre_completo, soc_mon_saldo
              FROM san_socios";

    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $socios = array();

        while ($row = mysqli_fetch_assoc($result)) {
            // Construir el array de datos del socio
            $socio_data = array(
                'soc_id_socio'     => $row['soc_id_socio'],
                'nombre_completo'  => $row['nombre_completo'],
                'soc_mon_saldo'     => $row['soc_mon_saldo']
            );

            // Agregar el socio al array de socios
            $socios[] = $socio_data;
        }

        // Devolver los datos de los socios en formato JSON
        echo json_encode(array("success" => true, "socios" => $socios));
    } else {
        // No se encontraron datos de socios
        echo json_encode(array("success" => false, "error" => "No se encontraron datos de socios en la base de datos."));
    }

    // Cerrar la conexión
    mysqli_close($conexion);
} else {
    // No se pudo conectar a la base de datos
    echo json_encode(array("success" => false, "error" => "No se pudo conectar a la base de datos."));
}
?>
