<?php
// Verificar si hay una sesión iniciada y obtener el correo electrónico del socio
if (isset($_SESSION['admin']['soc_correo'])) {
    $socioCorreo = $_SESSION['admin']['soc_correo'];

    // Consultar los datos del socio utilizando su correo electrónico
    $consulta = "SELECT * FROM san_socios WHERE soc_correo = :socioCorreo";

    // Preparar la consulta
    $stmt = $conn->prepare($consulta);

    // Vincular el parámetro
    $stmt->bindParam(':socioCorreo', $socioCorreo);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los datos del socio
    $selSocioData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cerrar el cursor
    $stmt->closeCursor();
} else {
    // Si no hay una sesión iniciada, devolver un arreglo vacío
    $selSocioData = array();
}
?>
