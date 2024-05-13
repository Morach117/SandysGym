<h4 class="text-info">
    <span class="glyphicon glyphicon-bullhorn"></span> Detalles del Servicio
</h4>

<hr/>

<?php
    if ($enviar) {
        $exito = actualizar_servicio();

        if ($exito['num'] == 1) {
            header("Location: .?s=catalogos&i=servicios");
            exit;
        } else {
            mostrar_mensaje_div($exito['num'] . ". " . $exito['msj'], 'danger');
        }
    }

    $datos = obtener_servicio();

    if (!$datos) {
        header("Location: .?s=catalogos&i=servicios");
        exit;
    }
?>

<form role="form" method="post" action=".?s=catalogos&i=servicios_editar">
    <div class="row">
        <div class="col-md-offset-2 col-md-10 text-justify">Si se modifican estas cuotas y después se procede a hacer movimientos en ventas de servicios, el corte no se cuadrara de manera correcta. Por lo tanto, estos cambios se deben realizar después de cerrar un día de venta o antes de comenzar con algún movimiento.</div>
    </div>

    <div class="row">
        <div class="col-md-2">ID</div>
        <label class="col-md-10"><?= $datos['id_servicio'] ?></label>
    </div>

    <div class="row">
        <div class="col-md-2">CLAVE</div>
        <label class="col-md-10"><?= $datos['clave'] ?></label>
    </div>

    <div class="row">
        <label class="col-md-2">Descripción</label>
        <div class="col-md-10">
            <input type="text" class="form-control" name="s_descripcion" maxlength="50" value="<?= $datos['descripcion'] ?>" required="required" />
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Tipo</label>
        <div class="col-md-2">
            <select name="s_tipo" class="form-control">
                <option <?= ($datos['tipo'] == 'PERIODO') ? 'selected' : '' ?> value="PERIODO">Período</option>
                <option <?= ($datos['tipo'] == 'PARCIAL') ? 'selected' : '' ?> value="PARCIAL">Parcial</option>
            </select>
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Costo Cuota</label>
        <div class="col-md-2">
            <input type="text" class="form-control" name="s_cuota" maxlength="7" value="<?= $datos['cuota'] ?>" required="required" />
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Días</label>
        <div class="col-md-2">
            <input type="text" class="form-control" name="s_dias" maxlength="2" value="<?= $datos['dias'] ?>" />
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Meses</label>
        <div class="col-md-2">
            <input type="text" class="form-control" name="s_meses" maxlength="2" value="<?= $datos['meses'] ?>" />
        </div>
    </div>

	<div class="row">
        <label class="col-md-2">Estado</label>
        <div class="col-md-2">
            <select name="s_estado" class="form-control">
                <option <?= ($datos['status'] == 'A') ? 'selected' : '' ?> value="A" >Activo</option>
                <option <?= ($datos['status'] == 'D') ? 'selected' : '' ?> value="D" >Descontinuado</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <input type="hidden" name="id_servicio" value="<?= $datos['id_servicio'] ?>" />
            <input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
            <input type="button" class="btn btn-default" value="Regresar" onclick="location.href='.?s=catalogos&i=servicios'" />
        </div>
    </div>
</form>
