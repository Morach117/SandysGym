<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-plus-sign"></span> Agregar nuevo socio
		</h4>
	</div>
</div>

<hr/>

<?php
	$validar				= array();
	$guardar				= array();
	
	$soc_nombres			= request_var( 'soc_nombres', '' );
	$soc_apepat				= request_var( 'soc_apepat', '' );
	$soc_apemat				= request_var( 'soc_apemat', '' );
	$soc_direccion			= request_var( 'soc_direccion', '' );
	$soc_colonia			= request_var( 'soc_colonia', '' );
	$soc_tel_fijo			= request_var( 'soc_tel_fijo', '' );
	$soc_tel_cel			= request_var( 'soc_tel_cel', '' );
	$soc_correo				= request_var( 'soc_correo', '' );
	$soc_descuento			= request_var( 'soc_descuento', 0.0 );
	$soc_noti				= request_var( 'soc_noti', 'C' );
	$soc_noti_c				= ( $soc_noti == 'C' ) ? "checked":"";
	$soc_noti_t				= ( $soc_noti == 'T' ) ? "checked":"";
	$soc_observaciones		= request_var( 'soc_observaciones', '' );
	
	if( $enviar )
	{
		$validar = validar_registro_socios();
		
		if( $validar['num'] == 1 )
		{
			$guardar = guardar_nuevo_socio();
			
			if( $guardar['num'] == 1 )
			{
				header( "Location: .?s=socios" );
				exit;
			}
			else
				mostrar_mensaje_div( $guardar['num'].". ".$guardar['msj'], 'danger' );
		}
		else
		{
			mostrar_mensaje_div( $validar['msj'], 'warning' );
		}
	}
	
	if( $rol == 'S' )
		$tag_desc	= "<input type='text' class='form-control' name='soc_descuento' maxlength='5' value='$soc_descuento' />";
	else
		$tag_desc	= "<input type='hidden' name='soc_descuento' value='0' /><label>0</label>";
?>
<form role="form" method="post" action=".?s=socios&i=nuevo" >
	<div class="row">
		<label class="col-md-2">Nombres <span class="text-danger">*</span></label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="soc_nombres" maxlength="50" required="required" value="<?= $soc_nombres ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">A. Paterno</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="soc_apepat" maxlength="50" value="<?= $soc_apepat ?>" />
		</div>
		
		<label class="col-md-2">A. Materno</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="soc_apemat" maxlength="50" value="<?= $soc_apemat ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Dirección</label>
		<div class="col-md-6">
			<input type="text" class="form-control" name="soc_direccion" maxlength="100" value="<?= $soc_direccion ?>" />
		</div>
		
		<label class="col-md-1">Colonia</label>
		<div class="col-md-3">
			<input type="text" class="form-control" name="soc_colonia" maxlength="100" value="<?= $soc_colonia ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Tel. celular</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="soc_tel_cel" maxlength="15" value="<?= $soc_tel_cel ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Correo</label>
		<div class="col-md-4">
			<input type="email" class="form-control" name="soc_correo" maxlength="50" value="<?= $soc_correo ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Descuento %</label>
		<div class="col-md-4">
			<?= $tag_desc ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<h5 class="text-info"><strong>Observaciones.</strong></h5>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Observaciones</label>
		<div class="col-md-10">
			<textarea rows="2" class="form-control" name="soc_observaciones"><?= $soc_observaciones ?></textarea>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<p class="text-right text-danger">* <em>Campos obligatorios</em></p>
		</div>
	</div>
	
	<div class="row text-center">
		<div class="col-md-12">
			<input type="button" name="cancelar" value="Cancelar" class="btn btn-default" onclick="location.href='.?s=socios'" />
			<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
		</div>
	</div>
</form>