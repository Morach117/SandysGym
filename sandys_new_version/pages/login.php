<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
    <!-- Bootstrap 4 Modal -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
</head>
<body>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="./assets/img/breadcrumb-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb-text">
                    <h2>Iniciar sesión</h2>
                    <div class="bt-option">
                        <a href="index.php?page=home">Inicio</a>
                        <span>Iniciar sesión</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Login Section Begin -->
<section class="login_box_area section_gap">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="login_box_img">
                    <img class="img-fluid" src="./assets/img/login.jpg" alt="">
                    <div class="hover">
                        <h4>¿Nuevo en nuestro gimnasio?</h4>
                        <p>Estamos constantemente mejorando nuestras instalaciones y servicios para ofrecerte la mejor experiencia posible. ¡Únete a nosotros y transforma tu vida!</p>
                        <a class="primary-btn" href="index.php?page=inscribite">Crear una Cuenta</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login_form_inner">
                    <h3>Inicia sesión para entrar</h3>
                    <form class="row login_form" method="post" id="adminLoginFrm">
                        <div class="col-md-12 form-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <div class="creat_account">
                                <input type="checkbox" id="f-option2" name="selector">
                                <label for="f-option2">Mantenerme conectado</label>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <button type="submit" class="primary-btn">Iniciar Sesión</button>
                            <a href="#" data-toggle="modal" data-target="#forgotPasswordModal">¿Olvidaste tu contraseña?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Login Section End -->

<!-- Modal para restablecer contraseña -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Restablecer Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="passwordResetRequestFrm">
                    <div class="form-group">
                        <label for="reset_email">Correo Electrónico</label>
                        <input type="email" class="form-control" id="reset_email" name="reset_email" placeholder="Correo Electrónico" required>
                    </div>
                    <button type="submit" class="primary-btn">Enviar enlace de restablecimiento</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 Modal -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script type="text/javascript" src="js/login.js"></script> <!-- importa el archivo ajax -->


<script>
$(document).ready(function() {
    $('#passwordResetRequestFrm').on('submit', function(event) {
        event.preventDefault();
        var email = $('#reset_email').val();

        $.ajax({
            url: './query/password_reset_request.php',
            method: 'POST',
            data: { email: email },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    Swal.fire(
                        '¡Éxito!',
                        'Se ha enviado un enlace de restablecimiento de contraseña a tu correo electrónico.',
                        'success'
                    );
                    $('#forgotPasswordModal').modal('hide');
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
                    'Hubo un problema al intentar enviar el enlace de restablecimiento. Por favor, inténtalo de nuevo más tarde.',
                    'error'
                );
            }
        });
    });
});
</script>

</body>
</html>
