<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-folder-open"></span> Datos Generales
		</h4>
	</div>
</div>

<hr/>

<?php
	$exito		= array();
	$eliminar	= request_var( 'eliminar', false );
	$tag_desc	= "";
	
	if( $eliminar )
	{
		$exito	= eliminar_socio();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=socios" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	if( $enviar )
	{
		$exito = validar_registro_socios();
		
		if( $exito['num'] == 1 )
		{
			$exito = actualizar_socio();
			
			if( $exito['num'] == 1 )
			{
				header( "Location: .?s=socios" );
				exit;
			}
			else
				mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
		}
		else
		{
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'warning' );
		}
	}
	
	$datos = obtener_datos_socio();
	
	if( $rol == 'S' )
		$tag_desc	= "<input type='text' class='form-control' name='soc_descuento' maxlength='5' value='$datos[soc_descuento]' />";
	else
		$tag_desc	= "<input type='hidden' name='soc_descuento' value='$datos[soc_descuento]' /><label>$datos[soc_descuento]</label>";
	
	/*se envia al item EDITAR para cuando se de regresar no se pierda la busqueda, tambien esta en el ITEM de EDITAR*/
	$var_array_regresar	= array( 'blq', 'pag', 'letra', 'pag_busqueda' );
	$var_regresar		= "";
	
	foreach( $var_array_regresar as $ind )
	{
		$var	= request_var( "$ind", '' );
		
		if( $var )
			$var_regresar .= "&$ind=$var";
	}
	
	if( $datos )
	{
?>
		<form role="form" method="post" action=".?s=socios&i=datosg" >
			<div class="row">
				<label class="col-md-2">Nombres <span class="text-danger">*</span></label>
				<div class="col-md-4">
					<input type="text" class="form-control" name="soc_nombres" maxlength="50" required="required" value="<?= $datos['soc_nombres'] ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">A. Paterno</label>
				<div class="col-md-4">
					<input type="text" class="form-control" name="soc_apepat" maxlength="50" value="<?= $datos['soc_apepat'] ?>" />
				</div>
				
				<label class="col-md-2">A. Materno</label>
				<div class="col-md-4">
					<input type="text" class="form-control" name="soc_apemat" maxlength="50" value="<?= $datos['soc_apemat'] ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Dirección</label>
				<div class="col-md-6">
					<input type="text" class="form-control" name="soc_direccion" maxlength="100" value="<?= $datos['soc_direccion'] ?>" />
				</div>
				
				<label class="col-md-1">Colonia</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="soc_colonia" maxlength="100" value="<?= $datos['soc_colonia'] ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Tel. celular</label>
				<div class="col-md-4">
					<input type="text" class="form-control" name="soc_tel_cel" maxlength="15" value="<?= $datos['soc_tel_cel'] ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Correo</label>
				<div class="col-md-4">
					<input type="email" class="form-control" name="soc_correo" maxlength="50" value="<?= $datos['soc_correo'] ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Descuento %</label>
				<div class="col-md-4">
					<?= $tag_desc ?>
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Observaciones</label>
				<div class="col-md-10">
					<textarea rows="2" class="form-control" name="soc_observaciones"><?= $datos['soc_observaciones'] ?></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<p class="text-right text-danger">* <em>Campos obligatorios</em></p>
				</div>
			</div>
			
			<div class="row text-center">
				<div class="col-md-12">
					<input type="hidden" name="soc_id_socio" value="<?= $datos['soc_id_socio'] ?>" />
					<input type="button" value="Eliminar" class="btn btn-danger" onclick="location.href='.?s=socios&i=datosg&eliminar=true&soc_id_socio=<?= $datos['soc_id_socio'] ?>'" />
					<input type="button" value="Regresar" class="btn btn-default" onclick="location.href='.?s=socios<?= $var_regresar ?>'" />
					<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
				</div>
			</div>
		</form>
<?php
	}
	else
		mostrar_mensaje_div( 'El proceso de busqueda se ha completado, pero no se encontró información con los datos ingresados o es una busqueda inválida.', 'warning' );
?>