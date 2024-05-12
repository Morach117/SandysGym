<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-time"></span> Horas
		</h4>
	</div>
</div>

<hr/>

<?php
	//esto es para la captura de nuevas horas
	$cuota		= obtener_servicio( 'HORA' );
	$hor_tiempo	= request_var( 'hor_horas', '' );
	$hor_sexo	= request_var( 'hor_genero', '' );
	$hor_nombre	= request_var( 'hor_nombre', '' );
	
	if( $enviar )
	{
		$validar	= validar_registro_hora();
		
		if( $validar['num'] == 1 )
		{
			$exito	= guardar_nuevo_horario();
			
			if( $exito['num'] == 1 )
			{
				header( "Location: .?s=horas&IDH=$exito[IDH]&token=$exito[tkn]" );
				exit;
			}
			else
				mostrar_mensaje_div( $exito['msj'], 'danger' );
		}
		else
			mostrar_mensaje_div( $validar['msj'], 'warning' );
	}
	
	//esto es la lista de horas, a la derecha
	$id_hora	= request_var( 'IDH', 0 );
	$token		= request_var( 'token', '' );
	$eliminar	= request_var( 'eliminar', false );
	
	//para impresion
	
	if( $id_hora && $token )
	{
		$impresion		= checar_impresion_hora();
		$validar_token	= hash_hmac( 'md5', $id_hora, $gbl_key );
		
		if( $validar_token == $token && $impresion == 'S' )
			echo "<script>mostrar_modal_hora( $id_hora, '$token' )</script>";
	}
	
	if( $eliminar )
	{
		$exito	= eliminar_horas();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=horas" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$v_comision		= obtener_p_comision_tarjeta();
	$horas_a		= lista_horas( 'activos' );
	$horas_i		= lista_horas( 'inactivos' );	
?>

<div class="row">
	<div class="col-md-6">
		<form action=".?s=horas" method="post" >
			<div class="row">
				<div class="col-md-12">
					<h5 class="text-info text-bold">Captura de Horas</h5>
				</div>
			</div>
				
			<div class="row">
				<div class="col-md-12">
					<h4 class="text-center text-success">Hora de entrada</h4>
					<h1 class="text-center text-success"><label id="h_entrada"><?= date('h:i:s a') ?></label></h1>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<h4 class="text-center text-danger">Hora de salida</h4>
					<h1 class="text-center text-danger"><label id="h_salida"><?= date('h:i:s a') ?></label></h1>
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-4">Tiempo</label>
				<div class="col-md-8">
							<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 1.0 )" value="01:00:00" <?= ( $hor_tiempo == '01:00:00' ) ? 'checked':'' ?> checked /> 1 hora
					<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 1.5 )" value="01:30:00" <?= ( $hor_tiempo == '01:30:00' ) ? 'checked':'' ?> /> 1 hora y media
					<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 2.0 )" value="02:00:00" <?= ( $hor_tiempo == '02:00:00' ) ? 'checked':'' ?> /> 2 horas
					<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 2.5 )" value="02:30:00" <?= ( $hor_tiempo == '02:30:00' ) ? 'checked':'' ?> /> 2 horas y media
					<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 3.0 )" value="03:00:00" <?= ( $hor_tiempo == '03:00:00' ) ? 'checked':'' ?> /> 3 horas
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-4">Sexo</label>
				<div class="col-md-8">
							<input type="radio" name="hor_genero" value="M" <?= ( $hor_sexo == 'M' ) ? 'checked':'' ?> /> Masculino
					<br/>	<input type="radio" name="hor_genero" value="F" <?= ( $hor_sexo == 'F' ) ? 'checked':'' ?> checked /> Femenino
				</div>
			</div>

			<div class="row">
				<label class="col-md-4">Nombre</label>
				<div class="col-md-8">
					<input type="text" name="hor_nombre" class="form-control" required="required" value="<?= $hor_nombre ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-4">Cuota</label>
				<label class="col-md-8 text-info" id="cuota">$<?= number_format( $cuota['cuota'], 2 ) ?></label>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<h5 class="text-info"><strong>Método de pago</strong></h5>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12 text-bold">
					<input type="radio" name="m_pago" id="m_pago_e" value="E" required onclick="calcular_total_pago()" checked /> Efectivo </br>
					<input type="radio" name="m_pago" id="m_pago_t" value="T" required onclick="calcular_total_pago()" /> Tarjeta (comisión: <?= $v_comision ?>%)
				</div>
			</div>
			
			<div class="row text-danger">
				<div class="col-md-4 text-bold">Total a pagar</div>
				<div class="col-md-4 text-bold" id="tag_total_pago">$00.00</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<input type="hidden" name="tiempo" id="tiempo" value="1" />
					<input type="hidden" name="comision" id="comision" value="<?= $v_comision ?>" />
					<input type="hidden" name="hor_cuota" id="hor_cuota" value="<?= $cuota['cuota'] ?>" />
					<input type="submit" name="enviar" class="btn btn-primary" value="Cobrar y guardar" />
					<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=horas'" />
				</div>
			</div>
		</form>
	</div>
	
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-12">
				<h5 class="text-info text-bold">Clientes por horas</h5>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<table class="table table-hover h6">
					<thead>
						<tr>
							<th></th>
							<th>Nombres</th>
							<th>Tiempo</th>
							<th>Hora de entrada</th>
							<th>Hora de salida</th>
						</tr>
					</thead>
					
					<tbody>
						<?= $horas_a ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h5 class="text-danger"><strong>Clientes por Hora vencidos</strong></h5>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<table class="table table-hover h6">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>Nombres</th>
							<th>Tiempo</th>
							<th>Hora de entrada</th>
							<th>Hora de salida</th>
						</tr>
					</thead>
					
					<tbody>
						<?= $horas_i ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>