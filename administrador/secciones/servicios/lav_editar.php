<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-bullhorn"></span> Detalles del Servicio
		</h4>
	</div>
</div>

<hr/>

<?php
	$id_servicio	= request_var( 'id_servicio', 0 );
	
	if( $enviar )
	{
		$exito	= actualizar_servicio( $id_servicio );
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$datos		= datos_servicio_lav( $id_servicio );
	$detalle	= detalle_servicio( $id_servicio );
	
	if( !$datos )
	{
		header( "Location: .?s=servicios&i=lav" );
		exit;
	}
?>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<div class="row">
		<label class="col-md-2">Descripción</label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="s_descripcion" maxlength="50" value="<?= $datos['descripcion'] ?>" required="required" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Status</label>
		<div class="col-md-2">
			<select name="s_status" class="form-control">
				<option <?= ( $datos['status'] == 'A' ) ? 'selected':'' ?> value="A">Activo</option>
				<option <?= ( $datos['status'] == 'D' ) ? 'selected':'' ?> value="D">Descontinuado</option>
			</select>
		</div>
		
		<label class="col-md-2">Orden</label>
		<div class="col-md-2"><input type="text" class="form-control" name="s_orden" maxlength="2" value="<?= $datos['orden'] ?>" required="required" /></div>
		
		<label class="col-md-1">Tipo</label>
		<label class="col-md-3"><?= $datos['tipo'] ?></label>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr>
						<th>Sucursal</th>
						<th class="text-right">Cuota</th>
						<th class="text-right">Promo Cuota</th>
						<th class="text-right">Promo día</th>
						<th class="text-right">Kg mínimo</th>
						<th>Mostrar</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $detalle ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="tipo" value="<?= $datos['tipo'] ?>" />
			<input type="hidden" name="id_servicio" value="<?= $id_servicio ?>" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
			<input type="button" class="btn btn-default" value="Regresar" onclick="location.href='.?s=servicios&i=lav&tipo=<?= $tipo ?>'" />
		</div>
	</div>
</form>