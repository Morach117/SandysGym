<!-- Breadcrumb Section Begin -->
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
<!-- Breadcrumb Section End -->

<!-- Registration Section Begin -->
<section class="registration-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title">
                    <span>Regístrate Ahora</span>
                    <h2>Únete a Nuestro Gimnasio</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <form class="registration-form" id="registrationForm" novalidate="novalidate">
                    <div class="form-group">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required>
                        <button type="button" class="primary-btn btn-normal" id="verifyEmail">Verificar Correo</button>
                    </div>
                    <div id="additionalFields" style="display:none;">
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nombre Completo" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="paternal_surname" name="paternal_surname" placeholder="Apellido Paterno" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="maternal_surname" name="maternal_surname" placeholder="Apellido Materno" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña" required>
                        </div>
                        <button type="submit" class="primary-btn btn-normal">Registrarse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- Registration Section End -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
$(document).ready(function() {
    $('#verifyEmail').click(function() {
        var email = $('#email').val();

        if (!email) {
            Swal.fire('Error', 'Por favor, introduce un correo electrónico.', 'error');
            return;
        }

        $.ajax({
            type: 'POST',
            url: './query/check_email.php',
            data: { email: email },
            dataType: 'json',
            encode: true
        }).done(function(data) {
            if (data.exists) {
                $('#name').val(data.name);
                $('#paternal_surname').val(data.paternal_surname);
                $('#maternal_surname').val(data.maternal_surname);
                $('#additionalFields').show();
                $('#verifyEmail').prop('disabled', true);
                $('#email').prop('readonly', true);
                localStorage.setItem('email', email); // Store the email in localStorage
            } else {
                if (data.message) {
                    Swal.fire('Error', data.message, 'error');
                } else {
                    $('#name').val('');
                    $('#paternal_surname').val('');
                    $('#maternal_surname').val('');
                    $('#additionalFields').show();
                    $('#verifyEmail').prop('disabled', true);
                    $('#email').prop('readonly', true);
                }
            }
        }).fail(function() {
            Swal.fire('Error', 'Hubo un problema al verificar el correo electrónico. Por favor, inténtalo de nuevo más tarde.', 'error');
        });
    });

    $('#registrationForm').submit(function(event) {
        event.preventDefault();

        var name = $('#name').val();
        var paternal_surname = $('#paternal_surname').val();
        var maternal_surname = $('#maternal_surname').val();
        var email = $('#email').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();

        if (!name || !paternal_surname || !maternal_surname || !email || !password || !confirm_password) {
            Swal.fire('Error', 'Por favor, rellena todos los campos.', 'error');
            return;
        }

        if (password !== confirm_password) {
            Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');
            return;
        }

        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/ ;
        if (!passwordRegex.test(password)) {
            Swal.fire('Error', 'La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial.', 'error');
            return;
        }

        var formData = {
            name: name,
            paternal_surname: paternal_surname,
            maternal_surname: maternal_surname,
            email: email,
            password: password,
            confirm_password: confirm_password
        };

        $.ajax({
            type: 'POST',
            url: './query/registration_process.php',
            data: formData,
            dataType: 'json',
            encode: true
        }).done(function(data) {
            if (data.success) {
                Swal.fire('Éxito', 'Registrado correctamente. Verifica tu correo para activar tu cuenta.', 'success')
                    .then(() => {
                        window.location.href = 'index.php?page=validate';
                    });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Hubo un problema al intentar registrarte. Por favor, inténtalo de nuevo más tarde.', 'error');
        });
    });
});
</script>
