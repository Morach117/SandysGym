<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tower"></span> Editar Sucursal
		</h4>
	</div>
</div>

<hr/>

<?php
	$datos	= obtener_detalle_empresa();
	$class	= "";
	
	if( !$datos )
	{
		header( "Location: .?s=configuracion" );
		exit;
	}
	
	if( $enviar )
	{
		$exito	= actualizar_datos();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=configuracion" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	switch( $datos['status'] )
	{
		case 'I': $class = "bg-warning";	break;
		case 'B': $class = "bg-danger";		break;
	}
?>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<div class="row">
		<label class="col-md-3">IDE</label>
		<label class="col-md-3"><?= $datos['id_sucursal'] ?></label>
	</div>
	
	<div class="row">
		<label class="col-md-3">Giro</label>
		<label class="col-md-3"><?= $datos['giro'] ?></label>
	</div>
	
	<div class="row">
		<label class="col-md-3">STATUS</label>
		<label class="col-md-3 <?= $class ?>"><?= $datos['status_desc'] ?></label>
	</div>
	
	<div class="row">
		<label class="col-md-3">Sucursal</label>
		<div class="col-md-9">
			<input type="text" class="form-control" name="e_descripcion" maxlength="50" required="required" value="<?= $datos['sucursal'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">ABR o nombre corto</label>
		<div class="col-md-3">
			<input type="text" class="form-control" name="e_abr" maxlength="10" required="required" value="<?= $datos['abr'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Dirección</label>
		<div class="col-md-9">
			<input type="text" class="form-control" name="e_direccion" maxlength="100" value="<?= $datos['direccion'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Colonia</label>
		<div class="col-md-9">
			<input type="text" class="form-control" name="e_colonia" maxlength="50" value="<?= $datos['colonia'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Ciudad</label>
		<div class="col-md-9">
			<input type="text" class="form-control" name="e_ciudad" maxlength="30" value="<?= $datos['ciudad'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Teléfono</label>
		<div class="col-md-3">
			<input type="text" class="form-control" name="e_telefono" maxlength="15" value="<?= $datos['telefono'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Correo</label>
		<div class="col-md-3">
			<input type="text" class="form-control" name="e_correo" maxlength="50" value="<?= $datos['correo'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Notificación</label>
		<div class="col-md-9">
			<input type="radio" name="e_not_corte" value="S" <?= ( $datos['not_correo'] == 'S' ) ? 'checked':'' ?> /> Habilita notificación de corte de caja por correo <small><em>(solo si hay correo ingresado)</em></small><br/>
			<input type="radio" name="e_not_corte" value="N" <?= ( $datos['not_correo'] == 'N' ) ? 'checked':'' ?> /> Sin notificaciones de corte de caja
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="editar_id_sucursal" value="<?= $datos['id_sucursal'] ?>" />
			<input type="hidden" name="editar_id_giro" value="<?= $datos['id_giro'] ?>" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
			<input type="button" name="regresar" class="btn btn-default" value="Regresar" onclick="location.href='.?s=configuracion'" />
		</div>
	</div>
</form>