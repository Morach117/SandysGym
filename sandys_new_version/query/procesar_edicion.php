<?php
// Incluir el archivo de conexión a la base de datos
include '../conn.php';

// Array para almacenar la respuesta
$response = array();

// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibió el ID del socio
    if (isset($_POST["id_socio"])) {
        // Obtener el ID del socio
        $idSocio = $_POST["id_socio"];

        // Realizar las validaciones necesarias y procesar los demás datos del formulario

        // Por ejemplo, actualizar el nombre del socio
        if (isset($_POST["nombres"])) {
            $nombres = $_POST["nombres"];
            $apePaterno = $_POST["ap_paterno"];
            $apeMaterno = isset($_POST["ap_materno"]) ? $_POST["ap_materno"] : null;
            $genero = $_POST["genero"];
            $turno = $_POST["turno"];
            $direccion = $_POST["direccion"];
            $colonia = isset($_POST["colonia"]) ? $_POST["colonia"] : null;
            $telFijo = isset($_POST["tel_fijo"]) ? $_POST["tel_fijo"] : null;
            $telCel = isset($_POST["tel_cel"]) ? $_POST["tel_cel"] : null;
            $correo = isset($_POST["correo"]) ? $_POST["correo"] : null;
            $fechaNacimiento = isset($_POST["fecha_nacimiento"]) ? $_POST["fecha_nacimiento"] : null;
            $emerNombres = isset($_POST["emer_nombres"]) ? $_POST["emer_nombres"] : null;
            $emerParentesco = isset($_POST["emer_parentesco"]) ? $_POST["emer_parentesco"] : null;
            $emerDireccion = isset($_POST["emer_direccion"]) ? $_POST["emer_direccion"] : null;
            $emerTel = isset($_POST["emer_tel"]) ? $_POST["emer_tel"] : null;
            $observaciones = isset($_POST["observaciones"]) ? $_POST["observaciones"] : null;

            // Realizar la actualización en la base de datos
            try {
                // Consulta preparada para actualizar la información del socio
                $stmt = $conn->prepare("UPDATE san_socios SET 
                soc_nombres = :nombres,
                                        soc_apepat = :ape_paterno,
                                        soc_apemat = :ape_materno,
                                        soc_genero = :genero,
                                        soc_turno = :turno,
                                        soc_direccion = :direccion,
                                        soc_colonia = :colonia,
                                        soc_tel_fijo = :tel_fijo,
                                        soc_tel_cel = :tel_cel,
                                        soc_correo = :correo,
                                        soc_fecha_nacimiento = :fecha_nacimiento,
                                        soc_emer_nombres = :emer_nombres,
                                        soc_emer_parentesco = :emer_parentesco,
                                        soc_emer_direccion = :emer_direccion,
                                        soc_emer_tel = :emer_tel,
                                        soc_observaciones = :observaciones
                                        WHERE soc_id_socio = :id_socio");
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':ape_paterno', $apePaterno);
                $stmt->bindParam(':ape_materno', $apeMaterno);
                $stmt->bindParam(':genero', $genero);
                $stmt->bindParam(':turno', $turno);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':colonia', $colonia);
                $stmt->bindParam(':tel_fijo', $telFijo);
                $stmt->bindParam(':tel_cel', $telCel);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento);
                $stmt->bindParam(':emer_nombres', $emerNombres);
                $stmt->bindParam(':emer_parentesco', $emerParentesco);
                $stmt->bindParam(':emer_direccion', $emerDireccion);
                $stmt->bindParam(':emer_tel', $emerTel);
                $stmt->bindParam(':observaciones', $observaciones);
                $stmt->bindParam(':id_socio', $idSocio);
                $stmt->execute();

                // Construir la respuesta JSON
                $response['status'] = 'success';
                $response['message'] = 'La información del socio se ha actualizado correctamente.';
            } catch (PDOException $e) {
                // Error al ejecutar la consulta
                $response['status'] = 'error';
                $response['message'] = 'Error al actualizar la información del socio: ' . $e->getMessage();
            }
        } else {
            // Error: No se recibió el nombre del socio
            $response['status'] = 'error';
            $response['message'] = 'Error: No se recibió el nombre del socio.';
        }
    } else {
        // Error: No se recibió el ID del socio
        $response['status'] = 'error';
        $response['message'] = 'Error: No se recibió el ID del socio.';
    }
} else {
    // Error: Método de solicitud no válido
    $response['status'] = 'error';
    $response['message'] = 'Error: Método de solicitud no válido.';
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
