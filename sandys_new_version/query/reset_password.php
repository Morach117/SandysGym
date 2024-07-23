<?php
// Archivo: reset_password.php
include '../conn.php'; // Archivo que contiene la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    // Verificar el token y obtener el correo electrónico del usuario
    $query = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expDate > NOW()");
    $query->execute([$token]);
    $row = $query->fetch();

    if ($row) {
        $email = $row['email'];

        // Encriptar la contraseña con SHA-256
        $hashed_password = hash('sha256', $new_password);

        // Actualizar la contraseña del usuario
        $update_query = $conn->prepare("UPDATE san_socios SET san_password = ? WHERE soc_correo = ?");
        $update_success = $update_query->execute([$hashed_password, $email]);

        if ($update_success) {
            // Eliminar el token de restablecimiento
            $delete_query = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delete_success = $delete_query->execute([$token]);

            if ($delete_success) {
                echo json_encode(['success' => true, 'message' => 'Contraseña restablecida correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el token de restablecimiento.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al restablecer la contraseña.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Token inválido o caducado.']);
    }
}
?>
