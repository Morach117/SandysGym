<?php
require '../conn.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Verificar si el correo electrónico está registrado
    $query = $conn->prepare("SELECT soc_id_socio FROM san_socios WHERE soc_correo = ?");
    $query->execute([$email]);
    $result = $query->fetch();

    if ($result) {
        // Generar un token de restablecimiento
        $token = bin2hex(random_bytes(50));
        $expDate = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Insertar token en la base de datos
        $insertQuery = $conn->prepare("INSERT INTO password_resets (email, token, expDate) VALUES (?, ?, ?)");
        $insertQuery->execute([$email, $token, $expDate]);

        // Enviar correo electrónico con el enlace de restablecimiento
        $resetLink = "http://localhost/sandys_new_version/index.php?page=reset_password&token=" . $token;
        $subject = "Restablece tu contraseña";
        $message = "
            <h1>Restablece tu contraseña</h1>
            <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
            <a href='" . $resetLink . "'>Restablecer contraseña</a>
        ";

        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor de correo
            $mail->isSMTP();
            $mail->Host = 'smtp.ionos.mx';
            $mail->SMTPAuth = true;
            $mail->Username = 'administracion@sandysgym.com'; // Tu correo electrónico
            $mail->Password = 'Splc1979.'; // Tu contraseña
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuración del correo electrónico
            $mail->setFrom('administracion@sandysgym.com', 'Sandys Gym');
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Correo de restablecimiento enviado.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo de restablecimiento.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo electrónico no registrado.']);
    }
}
?>
