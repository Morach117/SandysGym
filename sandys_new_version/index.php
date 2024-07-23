<?php
session_start(); // Iniciar sesión

// Definir páginas públicas y privadas
$publicPages = array(
    'home',
    'team',
    'services',
    'contact',
    'classes',
    'about_us',
    'login',
    'registration',
    'validate',
    'reset_password',
    'inscribite'
);

$privatePages = array(
    'user',
    'user_information',
    'user_home'
);

// Obtener la página solicitada
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'home';

// Verificar si el usuario está autenticado
$loggedIn = isset($_SESSION['admin']);

// Verificar si la página solicitada es válida
if (!in_array($page, $publicPages) && !in_array($page, $privatePages)) {
    // Página no válida, redirigir al inicio
    header("Location: index.php?page=home");
    exit;
}

// Verificar acceso a páginas privadas
if (!in_array($page, $publicPages) && !$loggedIn) {
    // Página privada y usuario no autenticado, redirigir al inicio de sesión
    header("Location: index.php?page=login");
    exit;
}

// Incluir archivos necesarios
include('includes/navbar.php');

// Incluir la página solicitada
require(__DIR__ . "/pages/$page.php");

// Incluir el pie de página
include('includes/footer.php');

?>
