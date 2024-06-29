<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-time"></span> Registrar nueva Visita
		</h4>
	</div>
</div>

<hr/>

<?php
	$cuota		= obtener_servicio( 'VISITA' );
	$hor_sexo	= request_var( 'hor_genero', '' );
	$hor_nombre	= request_var( 'hor_nombre', '' );
	
	if( $enviar )
	{
		$validar	= validar_registro_dia();
		
		if( $validar['num'] == 1 )
		{
			$exito	= guardar_nuevo_dia();
			
			if( $exito['num'] == 1 )
			{
				header( "Location: .?s=visitas&IDV=$exito[IDV]&token=$exito[tkn]" );
				exit;
			}
			else
				mostrar_mensaje_div( $exito['msj'], 'danger' );
		}
		else
			mostrar_mensaje_div( $validar['msj'], 'warning' );
	}
?>

<form action=".?s=visitas&i=nuevo" method="post" >
	<div class="row">
		<label class="col-md-1">Sexo</label>
		<div class="col-md-5">
					<input type="radio" name="hor_genero" value="M" <?= ( $hor_sexo == 'M' ) ? 'checked':'' ?> /> Masculino
			<br/>	<input type="radio" name="hor_genero" value="F" <?= ( $hor_sexo == 'F' ) ? 'checked':'' ?> checked /> Femenino
		</div>
	</div>

	<div class="row">
		<label class="col-md-1">Nombre</label>
		<div class="col-md-5">
			<input type="text" name="hor_nombre" class="form-control" required="required" value="<?= $hor_nombre ?>" />
		</div>
	</div>
		
	<div class="row">
		<label class="col-md-1">Cuota</label>
		<label class="col-md-5 text-info" id="cuota">$<?= number_format( $cuota['cuota'], 2 ) ?></label>
	</div>

	<div class="row">
		<div class="col-md-12 text-center">
			<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=visitas'" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
		</div>
	</div>
</form>