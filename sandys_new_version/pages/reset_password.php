<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="path/to/your/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
</head>
<body>

<!-- Área de Banner -->
<section class="breadcrumb-section set-bg" data-setbg="./assets/img/breadcrumb-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb-text">
                    <h2>Registro</h2>
                    <div class="bt-option">
                        <a href="index.php?page=home">Inicio</a>
                        <span>Registro</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Fin del Área de Banner -->

<!-- Sección de Restablecimiento de Contraseña -->
<section class="password_reset section_gap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="password_reset_form_inner">
                    <h3>Restablecer Contraseña</h3>
                    <form class="row password_reset_form" method="post" id="passwordResetFrm">
                        <input type="hidden" name="token" id="token" value="<?php echo $_GET['token']; ?>">
                        <div class="col-md-12 form-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nueva Contraseña" required>
                            <small id="passwordHelpBlock" class="form-text text-muted">
                                La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.
                            </small>
                        </div>
                        <div class="col-md-12 form-group">
                            <button type="submit" class="primary-btn">Restablecer Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Fin de la Sección de Restablecimiento de Contraseña -->

<script>
$(document).ready(function() {
    $('#passwordResetFrm').on('submit', function(event) {
        event.preventDefault();
        var token = $('#token').val();
        var newPassword = $('#new_password').val();

        // Expresión regular para validar la fortaleza de la contraseña
        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordRegex.test(newPassword)) {
            Swal.fire(
                'Error',
                'La contraseña no cumple con los requisitos mínimos de seguridad.',
                'error'
            );
            return;
        }

        $.ajax({
            url: './query/reset_password.php',
            method: 'POST',
            data: {
                token: token,
                new_password: newPassword
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    Swal.fire(
                        '¡Éxito!',
                        'Tu contraseña ha sido restablecida correctamente.',
                        'success'
                    ).then(() => {
                        window.location.href = 'index.php?page=login'; // Redirigir al usuario a la página de inicio de sesión
                    });
                } else {
                    Swal.fire(
                        'Error',
                        data.message,
                        'error'
                    );
                }
            },
            error: function() {
                Swal.fire(
                    'Error',
                    'Hubo un problema al intentar restablecer la contraseña. Por favor, inténtalo de nuevo más tarde.',
                    'error'
                );
            }
        });
    });
});
</script>

</body>
</html>
