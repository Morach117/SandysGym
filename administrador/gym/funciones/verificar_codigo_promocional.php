<?php
// Incluir el archivo de conexión
include "../../funciones_globales/funciones_conexion.php";

// Obtener la conexión
$conexion = obtener_conexion();

if ($conexion) {
    // Obtener el código promocional enviado por la solicitud GET
    $codigo_promocion = isset($_GET['codigo_promocion']) ? $_GET['codigo_promocion'] : '';

    if ($codigo_promocion) {
        // Realizar la consulta SQL para verificar el código promocional
        $query = "SELECT p.porcentaje_descuento
                  FROM san_codigos c
                  INNER JOIN san_promociones p ON c.id_promocion = p.id_promocion
                  WHERE c.codigo_generado = '$codigo_promocion' 
                  AND c.status = '1' 
                  AND p.vigencia_inicial <= NOW() 
                  AND p.vigencia_final >= NOW()";

        $result = mysqli_query($conexion, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $descuento = $row['porcentaje_descuento'];

            // Devolver el descuento en formato JSON
            echo json_encode(array("success" => true, "porcentaje_descuento" => $descuento));
        } else {
            // El código promocional no es válido o no está activo
            echo json_encode(array("success" => false, "error" => "El código promocional no es válido o no está activo."));
        }
    } else {
        // No se proporcionó ningún código promocional
        echo json_encode(array("success" => false, "error" => "No se proporcionó ningún código promocional."));
    }

    // Cerrar la conexión
    mysqli_close($conexion);
} else {
    // No se pudo conectar a la base de datos
    echo json_encode(array("success" => false, "error" => "No se pudo conectar a la base de datos."));
}
?>
