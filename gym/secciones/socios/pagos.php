<?php
	$eliminar		= request_var( 'eliminar', false );
	$servicio		= request_var( 'servicio', '' );
	$pag_fecha_pago	= request_var( 'pag_fecha_pago', date( 'd-m-Y' ) );
	$pag_fecha_ini	= request_var( 'pag_fecha_ini', '' );
	$pag_fecha_fin	= request_var( 'pag_fecha_fin', '' );
	$id_socio		= request_var( 'id_socio', 0 );
	$id_pago		= request_var( 'IDP', 0 );
	$pag_efectivo	= request_var( 'pag_efectivo', '' );
	$pag_tarjeta	= request_var( 'pag_tarjeta', '' );
	$token			= request_var( 'token', '' );
	$pag_importe	= '';
	$id_servicio	= 0;
	$servicio_cve	= '';
	$class_oculto	= 'hide';
	$op_fecha_pago	= "";
	$volver			= ".?s=socios";

	$codigo_promocion = isset($_POST['codigo_promocion']) ? $_POST['codigo_promocion'] : '';

	
	//para el paginado
	$pag_opciones	= request_var( 'pag_opciones', 0 );
	$pag_busqueda	= request_var( 'pag_busqueda', '' );
	$pag_fechai		= request_var( 'pag_fechai', '' );
	$pag_fechaf		= request_var( 'pag_fechaf', '' );
	$pag_item		= request_var( 'item', '' );
	$pag_blq		= request_var( 'blq', 0 );
	$pag_pag		= request_var( 'pag', 0 );
	
	if( $pag_item )
		$volver .= "&i=$pag_item";
	
	if( $pag_opciones )
		$volver .= "&pag_opciones=$pag_opciones";
	
	if( $pag_busqueda )
		$volver .= "&pag_busqueda=$pag_busqueda";
	
	if( $pag_fechai )
		$volver .= "&pag_fechai=$pag_fechai";
	
	if( $pag_fechaf )
		$volver .= "&pag_fechaf=$pag_fechaf";
	
	if( $pag_blq )
		$volver .= "&bql=$pag_blq";
	
	if( $pag_pag )
		$volver .= "&pag=$pag_pag";
	
	if( !$id_socio)
	{
		header( "Location: .?s=socios" );
		exit;
	}
	
	if( $id_pago && $token )
	{
		$impresion	= checar_impresion_pagos();
		$chk_token	= hash_hmac( 'md5', $id_pago, $gbl_key );
		
		if( $chk_token == $token && $impresion == 'S' )
			echo "<script>mostrar_modal_pago( $id_pago, '$token' )</script>";
	}
	
	$servicios		= obtener_servicios( $servicio );
	
	/*MEN PARCIAL solo se utiliza en socios es decir en s=socio y todos los item(i) que lo puedan contener. index,  js, funciones
	configuracion, configuracion -> mensualidades*/
	if( $servicio )
	{
		list( $id_servicio, $meses ) = explode( '-', $servicio );
		
		$servicio_cve	= obtener_servicio( $id_servicio );
		$servicio_cve	= $servicio_cve['clave'];
		
		if( $servicio_cve == 'MEN PARCIAL' )
			$class_oculto = '';
	}
	
	if( file_exists( "../imagenes/avatar/$id_socio.jpg" ) )
		$fotografia	= "	<img src='../imagenes/avatar/$id_socio.jpg' class='img-thumbnail' style='width:100%' />";
	else
		$fotografia	= "	<img src='../imagenes/avatar/noavatar.jpg' class='img-thumbnail' style='width:100%' />";
	
	if( $eliminar )
	{
		$mensaje	= eliminar_pago_socio();
		
		if( $mensaje['num'] == 1 )
			mostrar_mensaje_div( $mensaje['msj'], 'success' );
		else
			mostrar_mensaje_div( $mensaje['num'].". ".$mensaje['msj'], 'danger' );
	}
	
	//solo superadministrador
	if( $rol == 'S' )
	{
		$op_fecha_pago	= "	<div class='row'>
								<label class='col-md-5'>Fecha pago</label>
								<div class='col-md-7'>
									<input type='text' class='form-control' name='pag_fecha_pago' id='pag_fecha_pago' maxlength='10' value='$pag_fecha_pago' />
								</div>
							</div>";
	}
	
	if( $enviar )
	{
		$pag_importe	= request_var( 'pag_importe', 0.0 );
		$validar 		= validar_pago_socio();
		
		if( $validar['num'] == 1 )
		{
			$exito = guardar_pago_socio();
			
			if( $exito['num'] == 1 )
			{
				header( "Location: .?s=socios&i=pagos&id_socio=$exito[IDS]&IDP=$exito[IDP]&token=$exito[tkn]" );
				// header( "Location: .?s=socios&pag_opciones=2" );
				exit;
			}
			else
				mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
		}
		else
			mostrar_mensaje_div( $validar['msj'], 'warning' );
	}
	
	$nombre			= obtener_datos_socio();
	$tabla			= lista_pagos_socio();
	$archivo_img	= nombre_archivo_imagen( $id_socio );
	$v_comision		= obtener_p_comision_tarjeta();
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-usd"></span> Captura de Pagos</h4>
	</div>
</div>

<hr/>

<form role="form" method="post" action=".?s=socios&i=pagos" name="form_pago" enctype="multipart/form-data">
	<div class="row">
		<label class="col-md-3">Socio</label>
		<label class="col-md-9"><?= $nombre['soc_apepat']." ".$nombre['soc_apemat']." ".$nombre['soc_nombres'] ?></label>
	</div>

	<div class="row">
        <label class="col-md-3">Descuento del Cliente (%)</label>
        <label class="col-md-9"><?= $nombre['soc_descuento']?>%</label>
    </div>
	
	<div class="row">
		<label class="col-md-3">Archivo de Img</label>
		<label class="col-md-9"><?= $archivo_img ?></label>
	</div>
	
	<div class="row">
		<div class="col-md-7">
				<div class="row">
					<label class="col-md-5">Fecha de pago</label>
					<div class="col-md-7">
						<input type="text" class="form-control" value="<?= fecha_generica( date( 'd-m-Y' ) ); ?>" readonly="on" />
					</div>
				</div>
				
				<?= $op_fecha_pago ?>
				
				<div class="row">
					<label class="col-md-5">Servicio</label>
					<div class="col-md-7">
						<select class="form-control" name="servicio" id="servicio" onchange="calcular_servicio()" required>
							<?= $servicios ?>
						</select>
					</div>
				</div>
				
				<div class="row <?= $class_oculto ?>" id="importe">
					<label class="col-md-offset-5 col-md-4"><em>Importe a pagar</em></label>
					<div class="col-md-3">
						<input type="text" class="form-control" name="pag_importe" maxlength="5" value="<?= $pag_importe ?>" />
					</div>
				</div>
				
				<div class="row">
					<label class="col-md-5">Fecha inicial</label>
					<div class="col-md-7">
						<input type="text" class="form-control" name="pag_fecha_ini" id="pag_fecha_ini" onchange="calcular_servicio()" required="required" maxlength="10" value="<?= $pag_fecha_ini ?>" autocomplete="off" />
					</div>
				</div>
				
				<div class="row">
					<label class="col-md-5">Fecha vencimiento</label>
					<div class="col-md-7">
						<input type="text" class="form-control" name="pag_fecha_fin" id="pag_fecha_fin" value="<?= $pag_fecha_fin ?>" autocomplete="off" />
					</div>
				</div>
				<div class="row">
					<label class="col-md-5">Código de Promoción</label>
					<div class="col-md-7">
						<input type="text" class="form-control" name="codigo_promocion" id="codigo_promocion" value="<?= $codigo_promocion ?>" autocomplete="off" />
					</div>
				</div>

		</div>
		
		<div class="col-md-5" align="center">
			<div class="row">
				<div class="col-md-12">	
					<?= $fotografia ?>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<input type="file" name="avatar" />
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<h5 class="text-info"><strong>Método de pago</strong></h5>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12 text-bold">
			<input type="radio" name="m_pago" value="E" required checked /> Efectivo </br>
			<input type="radio" name="m_pago" value="T" required /> Tarjeta (comisión: <?= $v_comision ?>%)
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="pag_opciones" value="<?= $pag_opciones ?>" />
			<input type="hidden" name="pag_busqueda" value="<?= $pag_busqueda ?>" />
			<input type="hidden" name="pag_fechai" value="<?= $pag_fechai ?>" />
			<input type="hidden" name="pag_fechaf" value="<?= $pag_fechaf ?>" />
			<input type="hidden" name="pag_item" value="<?= $pag_item ?>" />
			<input type="hidden" name="blq" value="<?= $pag_blq ?>" />
			<input type="hidden" name="pag" value="<?= $pag_pag ?>" />
			
			<input type="hidden" name="comision" value="<?= $v_comision ?>" />
			<input type="hidden" name="id_socio" value="<?= $id_socio ?>" />
			<input type="submit" name="enviar" value="Cobrar y guardar" class="btn btn-primary" />
			<input type="button" name="Regresar" value="Regresar" class="btn btn-default" onclick="location.href='<?= $volver ?>'" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info"><strong>Historico de pagos</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<th></th>
				<th>Servicio pagado</th>
				<th>Fecha de pago</th>
				<th>Fecha inicial</th>
				<th>Vencimiento</th>
				<th class="text-right">Importe</th>
			</thead>
			
			<tbody>
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>