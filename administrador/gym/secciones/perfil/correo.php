<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-envelope"></span> Cambio de Correo Electrónico</h4>
	</div>
</div>

<hr/>

<?php
	$correo_n	= request_var( 'correo_n', '' );
	$correo_r	= request_var( 'correo_r', '' );
	
	if( $enviar )
	{
		$exito = guardar_correo();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['msj'], 'danger' );
	}
?>

<form action=".?s=perfil&i=correo" method="post">
	<div class="row">
		<label class="col-md-2">Nuevo correo</label>
		<div class="col-md-4"><input type="emal" name="correo_n" maxlength="40" class="form-control" value="<?= $correo_n ?>" required="required" placeholder="correo@ejemplo.com" autofocus="on" /></div>
	</div>

	<div class="row">
		<label class="col-md-2">Repite correo</label>
		<div class="col-md-4"><input type="email" name="correo_r" maxlength="40" class="form-control" value="<?= $correo_r ?>" required="required" placeholder="Repetir el correo" /></div>
	</div>

	<div class="row">
		<div class="col-md-offset-2 col-md-10">
			<input type="submit" class="btn btn-primary" name="enviar" value="Guardar" />
		</div>
	</div>
</form>