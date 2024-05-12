<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-edit"></span> Editar Prepago
		</h4>
	</div>
</div>

<hr/>

<?php
	$id_prepago			= request_var( 'id_prepago', 0 );
	
	if( !$id_prepago )
	{
		header( "Location: .?s=prepagos" );
		exit;
	}
	
	if( $enviar )
	{
		$exito = actualizar_prepago();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=prepagos&IDP=$exito[IDP]&IDD=$exito[IDD]&IDS=$exito[IDS]&token=$exito[tkn]" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].'. '.$exito['msj'], 'danger' );
	}
	
	$prepago			= obtener_prepago();
	$prepago_detalle	= obtener_prepago_detalle();
?>

<div class="row">
	<div class="col-md-1">Socio</div>
	<div class="col-md-11"><strong><?= $prepago['nombre'] ?></strong></div>
</div>

<div class="row">	
	<div class="col-md-1">Saldo</div>
	<div class="col-md-11"><strong>$<?= number_format( $prepago['saldo'], 2 ) ?></strong></div>
</div>

<form action=".?s=prepagos&i=editar" method="post" >
	<div class="row">	
		<div class="col-md-1">Agregar</div>
		<div class="col-md-2">
			<input type="text" name="prep_importe" class="form-control" required="required" maxlength="6" />
		</div>
		<div class="col-md-9">
			<input type="hidden" name="id_socio" value="<?= $prepago['id_socio'] ?>" />
			<input type="hidden" name="id_prepago" value="<?= $id_prepago ?>" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-edit"></span> Historial de Prepagos
		</h4>
	</div>
</div>

<hr/>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<th>#</th>
				<th>Descripción</th>
				<th class="text-right">Importe</th>
				<th class="text-right">Saldo</th>
				<th>Movimiento</th>
				<th>Fecha</th>
				<th>Hora</th>
			</thead>
			
			<tbody>
				<?= $prepago_detalle ?>
			</tbody>
		</table>
	</div>
</div>