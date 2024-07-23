<?php
require '../conn.php'; // Archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica si se recibió un código de validación
    if (isset($_POST['validation_code'])) {
        $validation_code_received = $_POST['validation_code']; // Código de validación recibido del formulario

        // Realiza la consulta para verificar si el código de validación es válido
        $query = "SELECT * FROM san_socios WHERE validation_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $validation_code_received);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // El código de validación es válido
            // Actualiza el valor de soc_correo_status a 1
            $query_update = "UPDATE san_socios SET soc_correo_status = 1 WHERE validation_code = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bindParam(1, $validation_code_received);
            $stmt_update->execute();

            // Aquí puedes realizar otras acciones necesarias
            echo json_encode(['success' => true, 'message' => '¡Cuenta validada correctamente!']);
        } else {
            // El código de validación es inválido
            echo json_encode(['success' => false, 'message' => '¡Código de validación inválido!']);
        }

        $stmt->closeCursor();
    } else {
        // Si no se recibió el código de validación en el formulario
        echo json_encode(['success' => false, 'message' => '¡No se recibió ningún código de validación!']);
    }
}
?>
