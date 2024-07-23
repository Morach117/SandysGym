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
    <title>Panel de Usuario</title>

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
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <!-- Banner de Inicio de Sesión -->
    <section class="breadcrumb-section set-bg" data-setbg="./assets/img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>Panel de Usuario</h2>
                        <div class="bt-option">
                            <a href="index.php">Inicio</a>
                            <span>Panel de Usuario</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <!-- Título del Panel -->
        <h2 class="text-center mb-4">Panel de Usuario</h2>

        <!-- Opciones del Usuario -->
        <section class="row text-center mb-4">
            <div class="col-md-3 mb-4">
                <a href="index.php?page=pay_membership" class="btn btn-outline-primary d-flex flex-column align-items-center">
                    <i class="fas fa-credit-card fa-3x mb-2"></i>
                    <span>Pagar Membresía</span>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="index.php?page=routines" class="btn btn-outline-primary d-flex flex-column align-items-center">
                    <i class="fas fa-dumbbell fa-3x mb-2"></i>
                    <span>Rutinas</span>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="index.php?page=edit_info" class="btn btn-outline-primary d-flex flex-column align-items-center">
                    <i class="fas fa-user-edit fa-3x mb-2"></i>
                    <span>Editar Información</span>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="index.php?page=info" class="btn btn-outline-primary d-flex flex-column align-items-center">
                    <i class="fas fa-info-circle fa-3x mb-2"></i>
                    <span>Información</span>
                </a>
            </div>
        </section>

        <!-- Información del Usuario -->
        <section class="card mb-4">
            <div class="card-header bg-primary text-white">
                Información del Usuario
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-user mr-2"></i>Datos Personales</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-user mr-2"></i><strong>Nombre Completo:</strong> <?php echo $selSocioData['soc_nombres'] . ' ' . $selSocioData['soc_apepat'] . ' ' . $selSocioData['soc_apemat']; ?></li>
                            <li><i class="fas fa-venus-mars mr-2"></i><strong>Género:</strong> <?php echo $selSocioData['soc_genero']; ?></li>
                            <li><i class="fas fa-birthday-cake mr-2"></i><strong>Fecha de Nacimiento:</strong> <?php echo $selSocioData['soc_fecha_nacimiento']; ?></li>
                            <li><i class="fas fa-envelope mr-2"></i><strong>Correo Electrónico:</strong> <?php echo $selSocioData['soc_correo']; ?></li>
                            <li><i class="fas fa-mobile-alt mr-2"></i><strong>Teléfono Celular:</strong> <?php echo $selSocioData['soc_tel_cel']; ?></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-map-marker-alt mr-2"></i>Dirección</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-home mr-2"></i><strong>Dirección:</strong> <?php echo $selSocioData['soc_direccion']; ?></li>
                            <li><i class="fas fa-city mr-2"></i><strong>Colonia:</strong> <?php echo $selSocioData['soc_colonia']; ?></li>
                            <!-- Agregar más datos de dirección aquí -->
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>

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
</body>

</html>
