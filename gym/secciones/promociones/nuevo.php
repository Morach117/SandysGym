<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-gift"></span> Registrar nueva Promoción
        </h4>
    </div>
</div>

<hr/>

<?php
    $titulo_promocion       = request_var('titulo_promocion', '');
    $vigencia_inicial       = request_var('vigencia_inicial', '');
    $vigencia_final         = request_var('vigencia_final', '');
    $porcentaje_descuento   = request_var('porcentaje_descuento', '');
    $utilizado              = request_var('utilizado', '');
    $tipo_promocion         = request_var('tipo_promocion', '');
    $cantidad_codigos       = request_var('cantidad_codigos', '');
    
    if ($enviar) {
        $validar = validar_registro_promociones();
        
        if ($validar['num'] == 1) {
            $exito = guardar_nueva_promocion();
            
            if ($exito['num'] == 1) {
                header("Location: .?s=promociones");
                exit;
            } else {
                mostrar_mensaje_div($exito['num'].". ".$exito['msj'], 'danger');
            }
        } else {
            mostrar_mensaje_div($validar['msj'], 'warning');
        }
    }
?>

<form role="form" method="post" action=".?s=promociones&i=nuevo">
    <div class="row">
        <label class="col-md-2">Título de la Promoción</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="titulo_promocion" maxlength="50" required value="<?= $titulo_promocion ?>" />
        </div>
        
        <label class="col-md-2">Vigencia Inicial</label>
        <div class="col-md-4">
            <input type="date" class="form-control" name="vigencia_inicial" required value="<?= $vigencia_inicial ?>" />
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Vigencia Final</label>
        <div class="col-md-4">
            <input type="date" class="form-control" name="vigencia_final" required value="<?= $vigencia_final ?>" />
        </div>
        
        <label class="col-md-2">Porcentaje de Descuento</label>
        <div class="col-md-4">
            <input type="number" class="form-control" name="porcentaje_descuento" required value="<?= $porcentaje_descuento ?>" />
        </div>
    </div>
    
    <div class="row">
        <label class="col-md-2">Utilizado</label>
        <div class="col-md-4">
            <select class="form-control" name="utilizado" required>
                <option disabled selected>Seleccionar...</option>
                <option value="S" <?= ($utilizado == 'S') ? 'selected' : '' ?>>Sí</option>
                <option value="N" <?= ($utilizado == 'N') ? 'selected' : '' ?>>No</option>
            </select>
        </div>
        
        <label class="col-md-2">Tipo de Promoción</label>
        <div class="col-md-4">
            <select class="form-control" name="tipo_promocion" required onchange="toggleCantidadCodigos(this)">
                <option disabled selected>Seleccionar...</option>
                <option value="Individual" <?= ($tipo_promocion == 'Individual') ? 'selected' : '' ?>>Individual</option>
                <option value="Masivo" <?= ($tipo_promocion == 'Masivo') ? 'selected' : '' ?>>Masivo</option>
            </select>
        </div>
    </div>
    
    <div class="row" id="row_cantidad_codigos" style="<?= ($tipo_promocion == 'Individual') ? '' : 'display: none;' ?>">
        <label class="col-md-2">Cantidad de Códigos</label>
        <div class="col-md-4">
            <input type="number" class="form-control" name="cantidad_codigos" min="1" max="100" value="<?= $cantidad_codigos ?>" />
        </div>
    </div>
    
    <div class="row text-center">
        <div class="col-md-12">
            <input type="button" name="cancelar" value="Cancelar" class="btn btn-default" onclick="location.href='.?s=promociones'" />
            <input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
        </div>
    </div>
</form>

<script>
    function toggleCantidadCodigos(select) {
        var cantidadCodigosRow = document.getElementById('row_cantidad_codigos');
        if (select.value === 'Individual') {
            cantidadCodigosRow.style.display = '';
        } else {
            cantidadCodigosRow.style.display = 'none';
        }
    }
</script>
