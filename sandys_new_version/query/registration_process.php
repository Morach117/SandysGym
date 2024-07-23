<?php
require '../conn.php'; // Archivo de conexión a la base de datos
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $paternal_surname = $_POST['paternal_surname'];
    $maternal_surname = $_POST['maternal_surname'];
    $email = $_POST['email'];
    $password = hash('sha256', $_POST['password']);
    $validation_code = substr(md5(rand()), 0, 4); // Código de validación de 4 dígitos
    $fecha_captura = date("Y-m-d H:i:s");
    $fecha_nacimiento = '0000-00-00'; // Fecha de nacimiento por defecto

    // Verificar si el correo ya existe
    $checkQuery = "SELECT soc_id_socio FROM san_socios WHERE soc_correo = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(1, $email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        // El correo ya existe, actualizar la contraseña y otros valores
        $updateQuery = "UPDATE san_socios SET 
            san_password = ?, 
            validation_code = ?, 
            soc_nombres = ?, 
            soc_apepat = ?, 
            soc_apemat = ?, 
            soc_fecha_captura = ? 
            WHERE soc_correo = ?";
        
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(1, $password);
        $updateStmt->bindParam(2, $validation_code);
        $updateStmt->bindParam(3, $name);
        $updateStmt->bindParam(4, $paternal_surname);
        $updateStmt->bindParam(5, $maternal_surname);
        $updateStmt->bindParam(6, $fecha_captura);
        $updateStmt->bindParam(7, $email);

        if ($updateStmt->execute()) {
            $success = true;
        } else {
            $success = false;
        }

        $updateStmt->closeCursor();
    } else {
        // El correo no existe, realizar una nueva inserción
        $insertQuery = "INSERT INTO san_socios (
            soc_nombres, soc_apepat, soc_apemat, soc_correo, san_password, soc_fecha_captura, soc_fecha_nacimiento, validation_code, soc_id_usuario, soc_id_empresa, soc_id_consorcio) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 17, 1, 1)";
        
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(1, $name);
        $insertStmt->bindParam(2, $paternal_surname);
        $insertStmt->bindParam(3, $maternal_surname);
        $insertStmt->bindParam(4, $email);
        $insertStmt->bindParam(5, $password);
        $insertStmt->bindParam(6, $fecha_captura);
        $insertStmt->bindParam(7, $fecha_nacimiento);
        $insertStmt->bindParam(8, $validation_code);

        if ($insertStmt->execute()) {
            $success = true;
        } else {
            $success = false;
        }

        $insertStmt->closeCursor();
    }

    $checkStmt->closeCursor();

    // Envío de correo electrónico con PHPMailer
    if ($success) {
        $mail = new PHPMailer(true);

        try {
            // Activa el modo de depuración
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function($str, $level) {
                file_put_contents('phpmailer_debug.log', date('Y-m-d H:i:s')." [$level] $str\n", FILE_APPEND);
            };

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.ionos.mx'; // Cambia esto por tu servidor SMTP de Ionos
            $mail->SMTPAuth = true;
            $mail->Username = 'administracion@sandysgym.com'; // Cambia esto por tu dirección de correo electrónico
            $mail->Password = 'Splc1979.'; // Cambia esto por tu contraseña
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587; // Puerto SMTP para STARTTLS

            // Configuración del correo electrónico
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

            // Envío del correo electrónico
            $mail->send();

            echo json_encode(['success' => true, 'validation_code' => $validation_code]);
        } catch (Exception $e) {
            // Registro del error en el archivo de depuración
            file_put_contents('phpmailer_debug.log', date('Y-m-d H:i:s')." [ERROR] ".$e->getMessage()."\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo electrónico de validación. Inténtalo de nuevo.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar. Inténtalo de nuevo.']);
    }

    $conn = null;
}
?>
