<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-plus-sign"></span> Agregar nuevo socio
        </h4>
    </div>
</div>

<hr/>

<?php
    // Aquí se deberían incluir las funciones de validación y guardado de datos

    // Inicializar variables
    $soc_nombres            = isset($_POST['soc_nombres']) ? $_POST['soc_nombres'] : '';
    $soc_apepat             = isset($_POST['soc_apepat']) ? $_POST['soc_apepat'] : '';
    $soc_apemat             = isset($_POST['soc_apemat']) ? $_POST['soc_apemat'] : '';
    $soc_genero             = isset($_POST['soc_genero']) ? $_POST['soc_genero'] : '';
    $soc_turno              = isset($_POST['soc_turno']) ? $_POST['soc_turno'] : '';
    $soc_direccion          = isset($_POST['soc_direccion']) ? $_POST['soc_direccion'] : '';
    $soc_tel_fijo           = isset($_POST['soc_tel_fijo']) ? $_POST['soc_tel_fijo'] : '';
    $soc_tel_cel            = isset($_POST['soc_tel_cel']) ? $_POST['soc_tel_cel'] : '';
    $soc_correo             = isset($_POST['soc_correo']) ? $_POST['soc_correo'] : '';
    $soc_emer_tel           = isset($_POST['soc_emer_tel']) ? $_POST['soc_emer_tel'] : '';
    $soc_observaciones      = isset($_POST['soc_observaciones']) ? $_POST['soc_observaciones'] : '';
    $soc_descuento          = isset($_POST['soc_descuento']) ? $_POST['soc_descuento'] : '';
    $soc_fecha_nacimiento   = isset($_POST['soc_fecha_nacimiento']) ? $_POST['soc_fecha_nacimiento'] : '';

    // Procesar el formulario cuando se envíe
    if(isset($_POST['enviar'])) {
        // Validar los datos
        $validar = validar_registro_socios();
        
        if($validar['num'] == 1) {
            $exito = guardar_nuevo_socio();
            
            if($exito['num'] == 1) {
                header("Location: .?s=socios&pag_opciones=1"); // 1 es la opción de socios agregados hoy
                exit;
            } else {
                mostrar_mensaje_div($exito['num'].". ".$exito['msj'], 'danger');
            }
        } else {
            mostrar_mensaje_div($validar['msj'], 'warning');
        }
    }
?>

<form role="form" method="post" action=".?s=socios&i=nuevo">
    <div class="row">
        <label class="col-md-2">A. Paterno <span class="text-danger">*</span></label>
        <div class="col-md-4">
            <input type="text" class="form-control text-uppercase" name="soc_apepat" maxlength="50" required="required" value="<?= $soc_apepat ?>" />
        </div>
        
        <label class="col-md-2">A. Materno</label>
        <div class="col-md-4">
            <input type="text" class="form-control text-uppercase" name="soc_apemat" maxlength="50" value="<?= $soc_apemat ?>" />
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Nombres <span class="text-danger">*</span></label>
        <div class="col-md-4">
            <input type="text" class="form-control text-uppercase" name="soc_nombres" maxlength="50" required="required" value="<?= $soc_nombres ?>" />
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Género</label>
        <div class="col-md-4">
            <input type="radio" name="soc_genero" checked value="M" <?= ($soc_genero == 'M') ? 'checked':'' ?> />Masculino
            <input type="radio" name="soc_genero" value="F" <?= ($soc_genero == 'F') ? 'checked':'' ?> />Femenino
        </div>
        
        <label class="col-md-2">Turno</label>
        <div class="col-md-4">
            <input type="radio" name="soc_turno" checked value="M" <?= ($soc_turno == 'M') ? 'checked':'' ?> />Matutino
            <input type="radio" name="soc_turno" value="V" <?= ($soc_turno == 'V') ? 'checked':'' ?> />Vespertino
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Dirección</label>
        <div class="col-md-10">
            <input type="text" class="form-control text-uppercase" name="soc_direccion" maxlength="100" value="<?= $soc_direccion ?>" />
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Teléfono fijo</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_tel_fijo" maxlength="15" value="<?= $soc_tel_fijo ?>" />
        </div>
        
        <label class="col-md-2">Tel. celular</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_tel_cel" maxlength="15" value="<?= $soc_tel_cel ?>" />
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Correo</label>
        <div class="col-md-4">
            <input type="email" class="form-control" name="soc_correo" maxlength="50" value="<?= $soc_correo ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h5 class="text-info"><strong>Persona a quién llamar en casos de emergencia.</strong></h5>
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Teléfono</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_emer_tel" maxlength="15" value="<?= $soc_emer_tel ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h5 class="text-info"><strong>Observaciones.</strong></h5>
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Observaciones</label>
        <div class="col-md-10">
            <textarea rows="2" class="form-control text-uppercase" name="soc_observaciones"><?= $soc_observaciones ?></textarea>
        </div>
    </div>

    <div class="row">
    <label class="col-md-2" <?php if ($_SESSION['sans_rol'] != 'S') echo 'style="display: none;"'; ?>>Descuento (%)</label>
    <div class="col-md-4">
        <input type="number" class="form-control" name="soc_descuento" min="0" max="100" value="<?= $soc_descuento ?>" <?php if ($_SESSION['sans_rol'] != 'S') echo 'style="display: none;"'; ?> />
    </div>
</div>



<div class="row">
    <label class="col-md-2">Fecha de Nacimiento</label>
    <div class="col-md-4">
        <input type="date" class="form-control" name="soc_fecha_nacimiento" value="<?= $soc_fecha_nacimiento ?>" />
    </div>
</div>


    <div class="row text-center">
        <div class="col-md-12">
            <p class="text-right text-danger">* <em>Campos obligatorios</em></p>
        </div>
    </div>

    <div class="row text-center">
        <div class="col-md-12">
            <input type="button" name="cancelar" value="Cancelar" class="btn btn-default" onclick="location.href='.?s=socios'" />
            <input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
        </div>
    </div>
</form>
