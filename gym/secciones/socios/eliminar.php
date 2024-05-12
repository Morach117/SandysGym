<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-folder-open"></span> Datos Generales
		</h4>
	</div>
</div>

<hr/>

<?php
	$id_socio	= request_var( 'id_socio', 0 );
	$datos		= obtener_datos_socio();
	
	if( !$datos )
	{
		header( "Location: .?s=socios" );
		exit;
	}
	
	if( $enviar )
	{
		$exito = eliminar_socio();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=socios" );
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
	
	<label class="col-md-6">¿Estas seguro de Eliminar este Socio?</label>
</div>

<div class="row">
	<div class="col-md-6">	
		<?= $fotografia ?>
	</div>
	
	<div class="col-md-6">
		<p>Si se elimina, también serán eliminados los pagos que haya efectuado, así como prepagos y todo el histórico de venta y ya no aparecerá en ninguna de las estadísticas de cortes.</p>
	</div>
</div>

<form method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="id_socio" value="<?= $id_socio ?>" />
			<input type="submit" name="enviar" class="btn btn-danger" value="Si, Eliminar todo" />
			<input type="button" name="cancel" class="btn btn-default" value="No, Cancelar" onclick="location.href='.?s=socios'">
		</div>
	</div>
</form>