<h4 class="text-info">
	<span class="glyphicon glyphicon-edit"></span> Modificar horas
</h4>

<hr/>

<?php
	$cuota		= obtener_servicio( 'HORA' );
	$id_horas	= request_var( 'id_horas', '' );
	
	if( !$id_horas )
	{
		header( "Location: .?s=horas" );
		exit;
	}
	
	$datos		= obtener_hora_seleccionada();
	
	if( !$datos )
	{
		mostrar_mensaje_div( 'No se encontró información con los datos seleccionados. Verifica que la hora sea válida.', 'danger' );
		exit;
	}
	
	$hor_tiempo	= $datos['tiempo'];
	$hor_sexo	= $datos['sexo'];
	$hor_nombre	= $datos['nombre'];
	
	if( $enviar )
	{
		$validar	= validar_registro_hora();
		
		$hor_tiempo	= request_var( 'hor_horas', '' );
		$hor_sexo	= request_var( 'hor_genero', '' );
		$hor_nombre	= request_var( 'hor_nombre', '' );
		
		if( $validar['num'] == 1 )
		{
			if( !actualizar_horario() )
				mostrar_mensaje_div( 'No se ha podido actualizar la información.', 'danger' );
		}
		else
			mostrar_mensaje_div( $validar['msj'], 'danger' );
	}
?>

<form action=".?s=horas&i=editar" method="post" >
	<div class="row">
		<div class="col-md-12">
			<h5 class="text-center text-success">Hora de entrada</h5>
			<h1 class="text-center text-success"><label id="h_entrada"><?= date('h:i:s a') ?></label></h1>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<h5 class="text-center text-danger">Hora de salida</h5>
			<h1 class="text-center text-danger"><label id="h_salida"><?= date('h:i:s a') ?></label></h1>
		</div>
	</div>

	<div class="row">
		<label class="col-md-2">Tiempo</label>
		<div class="col-md-4">
					<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 1.0 )" value="01:00:00" <?= ( $hor_tiempo == '01:00:00' ) ? 'checked':'' ?> /> 1 hora
			<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 1.5 )" value="01:30:00" <?= ( $hor_tiempo == '01:30:00' ) ? 'checked':'' ?> /> 1 hora y media
			<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 2.0 )" value="02:00:00" <?= ( $hor_tiempo == '02:00:00' ) ? 'checked':'' ?> /> 2 horas
			<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 2.5 )" value="02:30:00" <?= ( $hor_tiempo == '02:30:00' ) ? 'checked':'' ?> /> 2 horas y media
			<br/>	<input type="radio" name="hor_horas" onclick="actualizar_couta( <?= $cuota['cuota'] ?>, 3.0 )" value="03:00:00" <?= ( $hor_tiempo == '03:00:00' ) ? 'checked':'' ?> /> 3 horas
		</div>
		
		<label class="col-md-2">Sexo</label>
		<div class="col-md-4">
					<input type="radio" name="hor_genero" value="M" <?= ( $hor_sexo == 'M' ) ? 'checked':'' ?> /> Masculino
			<br/>	<input type="radio" name="hor_genero" value="F" <?= ( $hor_sexo == 'F' ) ? 'checked':'' ?>/> Femenino
		</div>
	</div>

	<div class="row">
		<label class="col-md-2">Nombre</label>
		<div class="col-md-4">
			<input type="text" name="hor_nombre" class="form-control" required="required" value="<?= $hor_nombre ?>" />
		</div>
		
		<label class="col-md-2">Cuota</label>
		<label class="col-md-5 text-info" id="cuota">$<?= ( $cuota['cuota'] * $hor_tiempo ) ?></label>
	</div>

	<div class="row">
		<div class="col-md-12 text-center">
			<input type="hidden" name="h_inicial" value="<?= $datos['h_inicio'] ?>" />
			<input type="hidden" name="id_horas" value="<?= $datos['id_horas'] ?>" />
			<input type="hidden" name="cuota_hora" value="<?= $cuota['cuota'] ?>" />
			<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=horas'" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
		</div>
	</div>
</div>