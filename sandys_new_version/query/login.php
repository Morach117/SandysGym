<?php
session_start();
include '../conn.php';

extract($_POST);

$loginAttemptsKey = 'login_attempts';
$lastAttemptTimeKey = 'last_attempt_time';
$maxLoginAttempts = 3;
$lockoutTime = 240; // 4 minutos en segundos

// Verificar si la variable de sesión de intentos de inicio de sesión existe
if (!isset($_SESSION[$loginAttemptsKey])) {
    $_SESSION[$loginAttemptsKey] = 0;
}

// Verificar si el usuario está bloqueado por exceder el máximo de intentos
if ($_SESSION[$loginAttemptsKey] >= $maxLoginAttempts) {
    $lastAttemptTime = $_SESSION[$lastAttemptTimeKey];

    // Verificar si ha pasado el tiempo de bloqueo
    if (time() - $lastAttemptTime < $lockoutTime) {
        $res = array('res' => 'locked');
        echo json_encode($res);
        exit;
    } else {
        // Reiniciar los intentos después del tiempo de bloqueo
        $_SESSION[$loginAttemptsKey] = 0;
    }
}

// Validar y sanitizar datos de entrada
$email = filter_var($email, FILTER_VALIDATE_EMAIL);

if ($email === false) {
    // Correo electrónico no válido, puede ser un intento de ataque SQL
    $res = array('res' => 'invalid_email');
    echo json_encode($res);
    exit;
}

// Utilizar consultas preparadas para evitar inyecciones SQL
$selAcc = $conn->prepare("SELECT * FROM san_socios WHERE soc_correo = :email");
$selAcc->bindParam(':email', $email);
$selAcc->execute();

if ($selAcc->rowCount() > 0) {
    $selAccRow = $selAcc->fetch(PDO::FETCH_ASSOC);
    
    if ($selAccRow['soc_correo_status'] != 1) {
        // El usuario tiene su correo inactivo
        $res = array('res' => 'inactive_email');
        echo json_encode($res);
        exit;
    }

    $providedHash = hash('sha256', $password);
    $storedHash = $selAccRow['san_password'];

    if ($providedHash === $storedHash) {
        $_SESSION['admin'] = array(
            'soc_correo' => $selAccRow['soc_correo'],
            'adminnakalogin' => true,
        );

        // Restablecer los intentos después de un inicio de sesión exitoso
        $_SESSION[$loginAttemptsKey] = 0;

        $res = array('res' => 'success');
    } else {
        // Incrementar el contador de intentos
        $_SESSION[$loginAttemptsKey]++;
        $_SESSION[$lastAttemptTimeKey] = time();

        $res = array('res' => 'invalid');
    }
} else {
    // Incrementar el contador de intentos
    $_SESSION[$loginAttemptsKey]++;
    $_SESSION[$lastAttemptTimeKey] = time();

    $res = array('res' => 'invalid');
}

echo json_encode($res);
?>
