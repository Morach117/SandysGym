$(document).on("submit", "#adminLoginFrm", function () {  
    $.post("query/login.php", $(this).serialize(), function (data) { 
        if (data.res == "invalid_email") {
            Swal.fire(
                'Correo electrónico no válido', 
                'Ingrese un correo electrónico válido.',
                'error'
            );
        } else if (data.res == "invalid") {
            Swal.fire(
                'Credenciales inválidas', 
                'Ingrese un nombre de usuario / contraseña válidos.',
                'error'
            );
        } else if (data.res == "inactive_email") {
            Swal.fire(
                'Correo inactivo', 
                'Su cuenta de correo electrónico no está activa. Por favor, póngase en contacto con el administrador.',
                'error'
            );
        } else if (data.res == "success") {
            $('body').fadeOut();
            window.location.href = 'index.php?page=user_home';
        } else if (data.res == "locked") {
            Swal.fire(
                'Bloqueado', 
                'Demasiados intentos fallidos. Por favor, espere antes de intentar nuevamente.',
                'error'
            );
        }
    }, 'json');

    return false;
});
