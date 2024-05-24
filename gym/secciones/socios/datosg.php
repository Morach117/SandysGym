<?php
    $mensaje = array();
    
    if($enviar) {
        $mensaje = validar_registro_socios();
        
        if($mensaje['num'] == 1) {
            $mensaje = actualizar_socio();
            
            if($mensaje['num'] == 1) {
                header("Location: .?s=socios");
                exit;
            } else {
                mostrar_mensaje_div($mensaje['msj'], 'danger');
            }
        } else {
            mostrar_mensaje_div($mensaje['msj'], 'warning');
        }
    }
    
    $datos = obtener_datos_socio();
    
    if(!$datos) {
        header("Location: .?s=socios");
        exit;
    }
?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-folder-open"></span> Datos Generales
        </h4>
    </div>
</div>

<hr/>

<form role="form" method="post" action=".?s=socios&i=datosg">
    <div class="row">
        <label class="col-md-2">Nombres <span class="text-danger">*</span></label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_nombres" maxlength="50" required="required" value="<?= $datos['soc_nombres'] ?>" />
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">A. Paterno <span class="text-danger">*</span></label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_apepat" maxlength="50" required="required" value="<?= $datos['soc_apepat'] ?>" />
        </div>
        
        <label class="col-md-2">A. Materno</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_apemat" maxlength="50" value="<?= $datos['soc_apemat'] ?>" />
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Género</label>
        <div class="col-md-4">
            <input type="radio" name="soc_genero" value="M" <?= ($datos['soc_genero'] == 'M') ? 'checked' : '' ?> />Masculino
            <input type="radio" name="soc_genero" value="F" <?= ($datos['soc_genero'] == 'F') ? 'checked' : '' ?> />Femenino
        </div>
        
        <label class="col-md-2">Turno</label>
        <div class="col-md-4">
            <input type="radio" name="soc_turno" value="M" <?= ($datos['soc_turno'] == 'M') ? 'checked' : '' ?> />Matutino
            <input type="radio" name="soc_turno" value="V" <?= ($datos['soc_turno'] == 'V') ? 'checked' : '' ?> />Vespertino
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Dirección</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="soc_direccion" maxlength="100" value="<?= $datos['soc_direccion'] ?>" />
        </div>
        
        <label class="col-md-1">Colonia</label>
        <div class="col-md-3">
            <input type="text" class="form-control" name="soc_colonia" maxlength="100" value="<?= $datos['soc_colonia'] ?>" />
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Teléfono fijo</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_tel_fijo" maxlength="15" value="<?= $datos['soc_tel_fijo'] ?>" />
        </div>
        
        <label class="col-md-2">Tel. celular</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_tel_cel" maxlength="15" value="<?= $datos['soc_tel_cel'] ?>" />
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Correo</label>
        <div class="col-md-4">
            <input type="email" class="form-control" name="soc_correo" maxlength="50" value="<?= $datos['soc_correo'] ?>" />
        </div>
    </div>
    
    <div class="row">
    <label class="col-md-2" <?php if ($_SESSION['sans_rol'] != 'S') echo 'style="display: none;"'; ?>>Descuento (%)</label>
    <div class="col-md-4">
        <input type="number" class="form-control" name="soc_descuento" min="0" max="100" value="<?= $soc_descuento ?>" <?php if ($_SESSION['sans_rol'] != 'S') echo 'style="display: none;"'; ?> />
    </div>
</div>

<div class="row">
    <label class="col-md-2">Fecha de Nacimiento <span class="text-danger">*</span></label>
    <div class="col-md-4">
        <input type="date" class="form-control" name="soc_fecha_nacimiento" required="required" value="<?= $datos['soc_fecha_nacimiento'] ?>" />
    </div>
</div>

    
    <div class="row">
        <div class="col-md-12">
            <h5 class="text-info"><strong>Persona a quién llamar en casos de emergencia.</strong></h5>
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Nombres</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_emer_nombres" maxlength="100" value="<?= $datos['soc_emer_nombres'] ?>" />
        </div>
        
        <label class="col-md-2">Parentesco</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_emer_parentesco" maxlength="50" value="<?= $datos['soc_emer_parentesco'] ?>" />
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Dirección</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_emer_direccion" maxlength="100" value="<?= $datos['soc_emer_direccion'] ?>" />
        </div>
        
        <label class="col-md-2">Teléfono</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="soc_emer_tel" maxlength="15" value="<?= $datos['soc_emer_tel'] ?>" />
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
            <textarea rows="2" class="form-control" name="soc_observaciones"><?= $datos['soc_observaciones'] ?></textarea>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <p class="text-right text-danger">* <em>Campos obligatorios</em></p>
        </div>
    </div>
    
    <div class="row text-center">
        <div class="col-md-12">
            <input type="hidden" name="id_socio" value="<?= $datos['soc_id_socio'] ?>" />
            <input type="button" name="cancelar" value="Cancelar" class="btn btn-default" onclick="location.href='.?s=socios'" />
            <input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
        </div>
    </div>
</form>
