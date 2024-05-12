<h4 class="text-info">
	<span class="glyphicon glyphicon-picture"></span> Fotografía
</h4>

<hr/>

<?php
	$eliminar	= request_var( 'eliminar', false );
	
	if( !$id_socio)
	{
		header( "Location: .?s=socios" );
		exit;
	}
	
	if( $eliminar )
	{
		if( eliminar_fotografia() )
			mostrar_mensaje_div( 'Fotografía eliminada.', 'success' );
		else
			mostrar_mensaje_div( 'No se puede eliminar esta fotografia..', 'danger' );
	}
	
	if( $enviar )
	{
		$mensaje = subir_fotografia();
		
		if( $mensaje['num'] == 1 )
			mostrar_mensaje_div( $mensaje['msj'], 'success' );
		elseif( $mensaje['num'] == 4 )
			mostrar_mensaje_div( $mensaje['msj'], 'warning' );
		else
			mostrar_mensaje_div( $mensaje['msj'], 'danger' );
	}
	
	if( file_exists( "../imagenes/avatar/$id_socio.jpg" ) )
		$fotografia	= "	<img src='../imagenes/avatar/$id_socio.jpg' class='img-thumbnail' />";
	else
		$fotografia	= "	<img src='../imagenes/avatar/noavatar.jpg' class='img-thumbnail' />";
	
	$nombre	= obtener_nombre_socio();
?>
<form method="post" action=".?s=socios&i=fotografia" enctype="multipart/form-data" >
	<div class="row">
		<div class="col-md-12">
			<h5 class="text-info"><strong><?= $nombre ?></strong></h5>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="file" name="avatar" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="soc_id_socio" value="<?= $id_socio ?>" />
			<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
			<input type="button" value="Eliminar" class="btn btn-danger" onclick="location.href='.?s=socios&i=fotografia&soc_id_socio=<?= $id_socio ?>&eliminar=true'" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-4">
		<?= $fotografia ?>
	</div>
</div>