<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-bullhorn"></span> ¿Estas seguro de eliminar este Servicio?
		</h4>
	</div>
</div>

<hr/>

<?php
	$tipo			= request_var( 'tipo', '' );
	$id_servicio	= request_var( 'id_servicio', 0 );
	
	if( $enviar )
	{
		$exito	= eliminar_servicio( $id_servicio );
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=servicios&i=lav&tipo=$tipo" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$datos		= datos_servicio_lav( $id_servicio );
	
	if( !$datos )
	{
		header( "Location: .?s=servicios&i=lav" );
		exit;
	}
?>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<div class="row">
		<label class="col-md-2">Descripción</label>
		<div class="col-md-10"><?= $datos['descripcion'] ?></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Status</label>
		<div class="col-md-10"><?= $datos['status_desc'] ?></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Tipo</label>
		<label class="col-md-10"><?= $datos['tipo'] ?></label>
	</div>
	
	<div class="row">
		<label class="col-md-2">Orden</label>
		<div class="col-md-10"><?= $datos['orden'] ?></div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="tipo" value="<?= $tipo ?>" />
			<input type="hidden" name="id_servicio" value="<?= $id_servicio ?>" />
			<input type="submit" name="enviar" class="btn btn-danger" value="Eliminar" />
			<input type="button" class="btn btn-default" value="Regresar" onclick="location.href='.?s=servicios&i=lav&tipo=<?= $tipo ?>'" />
		</div>
	</div>
</form>