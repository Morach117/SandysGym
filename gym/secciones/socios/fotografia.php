<?php
	$eliminar	= request_var( 'eliminar', false );
	$id_socio	= request_var( 'id_socio', 0 );
	
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
		$exito = subir_fotografia();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=socios" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['msj'], 'danger' );
	}
	
	$actualizador	= date( 'His' );
	
	if( file_exists( "../imagenes/avatar/$id_socio.jpg" ) )
		$fotografia	= "	<img src='../imagenes/avatar/$id_socio.jpg?$actualizador' class='img-thumbnail' />";
	else
		$fotografia	= "	<img src='../imagenes/avatar/noavatar.jpg' class='img-thumbnail' />";
	
	$nombre			= obtener_datos_socio();
	$archivo_img	= nombre_archivo_imagen( $id_socio );
?>

<div class="">
	<div class="">
		<h4 class="text-info"><span class="glyphicon glyphicon-picture"></span> Fotografía</h4>
	</div>
</div>

<hr/>

<form method="post" action=".?s=socios&i=fotografia" enctype="multipart/form-data" >
	<div class="row">
		<label class="col-md-2">Nombre</label>
		<div class="col-md-4">
			<h5 class="text-info"><strong><?= $nombre['soc_apepat']." ".$nombre['soc_apemat']." ".$nombre['soc_nombres'] ?></strong></h5>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Archivo de Img</label>
		<div class="col-md-4">
			<h5 class="text-info"><strong><?= $archivo_img ?></strong></h5>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="file" name="avatar" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="id_socio" value="<?= $id_socio ?>" />
			<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
			<input type="button" value="Eliminar" class="btn btn-danger" onclick="location.href='.?s=socios&i=fotografia&id_socio=<?= $id_socio ?>&eliminar=true'" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<?= $fotografia ?>
	</div>
</div>