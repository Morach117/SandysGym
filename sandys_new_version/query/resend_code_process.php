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

    $checkQuery = "SELECT soc_nombres, validation_code FROM san_socios WHERE soc_correo = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(1, $email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
        $name = $row['soc_nombres'];
        $validation_code = $row['validation_code'];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.ionos.mx';
            $mail->SMTPAuth = true;
            $mail->Username = 'administracion@sandysgym.com';
            $mail->Password = 'Splc1979.';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('administracion@sandysgym.com', 'Sandys Gym');
            $mail->addAddress($email, $name);
            $mail->Subject = 'Código de validación';
            $mail->isHTML(true);
            $mail->Body = '
                <h1 style="color: #333;">Código de Validación</h1>
                <p>Hola ' . $name . ',</p>
                <p>Tu código de validación es: <strong>' . $validation_code . '</strong></p>
                <p>¡Gracias por unirte a Sandys Gym!</p>
            ';

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Código de validación reenviado. Revisa tu correo.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo electrónico de validación. Inténtalo de nuevo.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'El correo electrónico no está registrado.']);
    }

    $checkStmt->closeCursor();
    $conn = null;
}
?>
