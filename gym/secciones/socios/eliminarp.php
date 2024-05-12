<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Eliminar pago de Socio
		</h4>
	</div>
</div>

<hr/>

<?php
	$id_socio	= request_var( 'id_socio', 0 );
	$id_pago	= request_var( 'id_pago', 0 );
	$datos		= obtener_datos_socio();
	$detalle	= obtener_detalle_pago( $id_socio, $id_pago );
	
	if( !$datos )
	{
		header( "Location: .?s=socios" );
		exit;
	}
	
	if( $enviar )
	{
		$exito = eliminar_pago_socio( $id_socio, $id_pago );
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=socios&i=pagos&id_socio=$id_socio" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['msj'], 'danger' );
	}
	
	$nombre = $datos['soc_nombres']." ".$datos['soc_apepat']." ".$datos['soc_apemat'];
	
	if( file_exists( "../imagenes/avatar/$id_socio.jpg" ) )
		$fotografia	= "	<img src='../imagenes/avatar/$id_socio.jpg' class='img-thumbnail' style='width:100%' />";
	else
		$fotografia	= "	<img src='../imagenes/avatar/noavatar.jpg' class='img-thumbnail' style='width:100%' />";
?>

<div class="row">
	<label class="col-md-1">Socio</label>
	<div class="col-md-5"><?= strtoupper( $nombre ) ?></div>
	
	<label class="col-md-6">¿Estas seguro de Eliminar este pago?</label>
</div>

<div class="row">
	<div class="col-md-6">	
		<?= $fotografia ?>
	</div>
	
	<div class="col-md-6">
		<label>Fecha de pago: &nbsp; </label><?= $detalle['f_pago'] ?><br/>
		<label>Fecha inicio: &nbsp; </label><?= $detalle['f_ini'] ?><br/>
		<label>Fecha fin: &nbsp; </label><?= $detalle['f_fin'] ?><br/>
		<label>Importe: &nbsp; </label>$<?= number_format( $detalle['importe'], 2 ) ?>
	</div>
</div>

<form method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="id_socio" value="<?= $id_socio ?>" />
			<input type="hidden" name="id_pago" value="<?= $id_pago ?>" />
			<input type="submit" name="enviar" class="btn btn-danger" value="Si, Eliminar este pago" />
			<input type="button" name="cancel" class="btn btn-default" value="No, Cancelar" onclick="location.href='.?s=socios&i=pagos&id_socio=<?= $id_socio ?>'">
		</div>
	</div>
</form>