<?php
include('conn.php');
include('./query/select_data.php');

// Definir la variable $userName
$userName = '';

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['admin'])) {
    // Obtener el nombre del usuario si está iniciado sesión
    $user_email = $_SESSION['admin']['soc_correo'];
    $consulta = "SELECT soc_nombres, soc_apepat, soc_apemat FROM san_socios WHERE soc_correo = :user_email";
    
    // Preparar la consulta
    $stmt = $conn->prepare($consulta);
    
    // Vincular el parámetro
    $stmt->bindParam(':user_email', $user_email);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Verificar si se encontró el usuario
    if ($stmt->rowCount() > 0) {
        // Obtener el nombre del usuario y mostrarlo
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $userName = $row['soc_nombres'] . ' ' . $row['soc_apepat'] . ' ' . $row['soc_apemat'];
    }

    // Cerrar la cursor
    $stmt->closeCursor();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sandys</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/barfiller.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/style.css" type="text/css">
</head>

<body>

    <!-- Offcanvas Menu Section Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="canvas-close">
            <i class="fa fa-close"></i>
        </div>
        <div class="canvas-search search-switch">
            <i class="fa fa-search"></i>
        </div>
        <nav class="canvas-menu mobile-menu">
            <ul>
                <li><a href="index.php?page=home">Inicio</a></li>
                <li><a href="index.php?page=about_us">Sobre Nosotros</a></li>
                <li><a href="index.php?page=classes">Clases</a></li>
                <li><a href="index.php?page=services">Servicios</a></li>
                <li><a href="index.php?page=team">Nuestro Equipo</a></li>
                <li><a href="index.php?page=contact">Contacto</a></li>
                <?php if ($userName): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" id="userDropdownMobile" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user"></i> <?php echo $userName; ?>
                        </a>
                        <ul class="dropdown-menu" id="userMenuMobile" aria-labelledby="userDropdownMobile">
                            <li><a class="dropdown-item" href="index.php?page=user">Ver datos</a></li>
                            <li><a class="dropdown-item" href="index.php?page=user_information">Modificar información</a></li>
                            <li><a class="dropdown-item" href="./query/logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="index.php?page=login"><i class="fa fa-user"></i> Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
            <div class="canvas-social">
                <a href="https://www.facebook.com/gymsandy" target="_blank"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-youtube-play"></i></a>
                <a href="https://www.instagram.com/sandysgym?igsh=MXU0c3NrNWZjZzMzYw==" target="_blank"><i class="fa fa-instagram"></i></a>
                <a href="index.php?page=login"><i class="fa fa-user"></i></a>
            </div>
        </nav>
        <div id="mobile-menu-wrap"></div>
    </div>
    <!-- Offcanvas Menu Section End -->

    <!-- Header Section Begin -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <div class="logo">
                        <a href="index.php?page=home">
                            <img src="./assets/img/logo.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="nav-menu">
                        <ul>
                            <li><a href="index.php?page=home">Inicio</a></li>
                            <li><a href="index.php?page=about_us">Sobre Nosotros</a></li>
                            <li><a href="index.php?page=classes">Clases</a></li>
                            <li><a href="index.php?page=services">Servicios</a></li>
                            <li><a href="index.php?page=team">Nuestro Equipo</a></li>
                            <li><a href="index.php?page=contact">Contacto</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3">
                    <div class="top-option d-flex justify-content-end align-items-center">
                        <div class="to-search search-switch mr-3 d-none d-lg-block">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="to-social mr-3 d-none d-lg-block">
                            <a href="https://www.facebook.com/gymsandy" target="_blank"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-youtube-play"></i></a>
                            <a href="https://www.instagram.com/sandysgym?igsh=MXU0c3NrNWZjZzMzYw==" target="_blank"><i class="fa fa-instagram"></i></a>
                        </div>
                        <div class="mr-3 d-none d-lg-block">
                            <?php if ($userName): ?>
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle" id="userDropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-user"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" id="userMenu" aria-labelledby="userDropdown">
                                        <span class="dropdown-item-text"><?php echo $userName; ?></span>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.php?page=user">Ver datos</a>
                                        <a class="dropdown-item" href="index.php?page=user_information">Modificar información</a>
                                        <a class="dropdown-item" href="./query/logout.php">Cerrar sesión</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="index.php?page=login"><i class="fa fa-user"></i></a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="index.php?page=inscribite" class="primary-btn btn-inscribete">Inscríbete ya</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="canvas-open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Scripts necesarios -->
    <script src="./assets/js/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.magnific-popup.min.js"></script>
    <script src="./assets/js/masonry.pkgd.min.js"></script>
    <script src="./assets/js/jquery.barfiller.js"></script>
    <script src="./assets/js/jquery.slicknav.js"></script>
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/main.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var userDropdown = document.getElementById("userDropdown");
        var userMenu = document.getElementById("userMenu");
        var userDropdownMobile = document.getElementById("userDropdownMobile");
        var userMenuMobile = document.getElementById("userMenuMobile");
        
        // Agregar un evento clic al icono de usuario para escritorio
        userDropdown.addEventListener("click", function(event) {
            event.stopPropagation(); // Evitar que el clic se propague al contenedor del menú
            userMenu.classList.toggle("show");
        });
        
        // Cerrar el menú desplegable cuando el usuario haga clic en cualquier parte de la página
        document.addEventListener("click", function(event) {
            if (!userDropdown.contains(event.target)) {
                userMenu.classList.remove("show");
            }
        });

        // Agregar un evento clic al icono de usuario para móvil
        userDropdownMobile.addEventListener("click", function(event) {
            event.stopPropagation(); // Evitar que el clic se propague al contenedor del menú
            userMenuMobile.classList.toggle("show");
        });
        
        // Cerrar el menú desplegable cuando el usuario haga clic en cualquier parte de la página
        document.addEventListener("click", function(event) {
            if (!userDropdownMobile.contains(event.target)) {
                userMenuMobile.classList.remove("show");
            }
        });
    });
    </script>
</body>

</html>
