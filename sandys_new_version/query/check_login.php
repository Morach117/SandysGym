<?php
session_start();


if (!isLoggedIn()) {
    header("Location: index.php?page=login"); // Redireccionar al usuario si no ha iniciado sesión
    exit;
}

// Imprimir un mensaje en la consola del navegador
echo "<script>";
if (isLoggedIn()) {
    echo "console.log('El usuario tiene una sesión activa.')";
} else {
    echo "console.log('El usuario no tiene una sesión activa.')";
}
echo "</script>";
?>
