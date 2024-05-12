<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-calendar"></span> Actualización en la Vigencia de la fecha
		</h4>
	</div>
</div>

<hr/>

<?php
	$id_socio		= request_var( 'id_socio', 0 );
	$id_pago		= request_var( 'id_pago', 0 );
	$fecha_pago		= request_var( 'pag_fecha_pago', '' );
	$fecha_ini		= request_var( 'pag_fecha_ini', '' );
	$fecha_fin		= request_var( 'pag_fecha_fin', '' );
	$comentario		= request_var( 'pag_comentario', '' );
	
	if( !$id_socio && !$id_pago )
	{
		header( "Location: .?s=socios" );
		exit;
	}
	
	if( $enviar )
	{
		$validar = validar_fechas();
		
		if( $validar['num'] == 1 )
		{
			$guardar = guardar_fechas();
			
			if( $guardar['num'] == 1 )
				mostrar_mensaje_div( $guardar['msj'], 'success' );
			else
				mostrar_mensaje_div( $guardar['num'].". ".$guardar['msj'], 'danger' );
		}
		else
			mostrar_mensaje_div( $validar['msj'], 'warning' );
	}
	
	$datos		= obtener_fecha_seccionada( $id_socio, $id_pago );
	
	if( !$fecha_pago )
		$fecha_pago = $datos['fecha_pago'];
	
	if( !$fecha_ini )
		$fecha_ini = $datos['fecha_ini'];
	
	if( !$fecha_fin )
		$fecha_fin = $datos['fecha_fin'];
	
	if( $superusuario == 'S' )
	{
		$readonly	= "";
		$id			= "pag_fecha_pago";
	}
	else
	{
		$readonly	= "readonly";
		$id			= "";
	}
	
	if( !$datos['id_pago'] )
	{
		echo "	<div class='row'>
					<div class='col-md-12'>
						<div class='alert alert-warning'>
							No se encontró pago para este socio. Por lo tanto, no se guardará ningun cambio, primero se debe capturar el pago.
						</div>
					</div>
				</div>";
	}
	else
	{
?>
		<form role="form" method="post" action=".?s=socios&i=fechas" name="form_fechas" >
			<div class="row">
				<label class="col-md-3">Socio</label>
				<div class="col-md-9">
					<?= $datos['apellidos']." ".$datos['nombres'] ?>
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Actual</label>
				<div class="col-md-9"><?= "del ".fecha_generica( $datos['fecha_ini'] )." al ".fecha_generica( $datos['fecha_fin'] ) ?></div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Fecha de pago</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pag_fecha_pago" id="<?= $id ?>" required="required" maxlength="10" value="<?= $fecha_pago ?>" <?= $readonly ?> />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Cambiar inicio a</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pag_fecha_ini" id="pag_fecha_ini" required="required" maxlength="10" value="<?= $fecha_ini ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Cambiar vencimiento a</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pag_fecha_fin" id="pag_fecha_fin" required="required" maxlength="10" value="<?= $fecha_fin ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Comentarios</label>
				<div class="col-md-9">
					<textarea class="form-control" rows="2" name="pag_comentario" maxlength="190"><?= $comentario ?></textarea>
				</div>
			</div>
			
			<div class="row text-center">
				<div class="col-md-12">
					<input type="hidden" name="id_socio" value="<?= $id_socio ?>" />
					<input type="hidden" name="id_pago" value="<?= $id_pago ?>" />
					
					<input type="button" name="cancelar" value="Cancelar" class="btn btn-default" onclick="location.href='.?s=socios'" />
					<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
				</div>
			</div>
		</form>
<?php
	}
?>