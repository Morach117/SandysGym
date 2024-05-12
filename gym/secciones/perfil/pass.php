<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-lock"></span> Cambio de Contraseña</h4>
	</div>
</div>

<hr/>

<?php
	$pass_a		= request_var( 'pass_a', '' );
	$pass_n		= request_var( 'pass_n', '' );
	$pass_r		= request_var( 'pass_r', '' );
	
	if( $enviar )
	{
		$exito = cambiar_contraseña();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['msj'], 'danger' );
	}
?>

<form action=".?s=perfil&i=pass" method="post">
	<div class="row">
		<label class="col-md-3">Contraseña actual</label>
		<div class="col-md-3"><input type="password" name="pass_a" maxlength="20" class="form-control" value="<?= $pass_a ?>" required="required" autofocus="on" /></div>
	</div>
	
	<hr/>
	
	<div class="row">
		<label class="col-md-3">Nueva contraseña</label>
		<div class="col-md-3"><input type="password" name="pass_n" maxlength="20" class="form-control" value="<?= $pass_n ?>" required="required" autofocus="on" /></div>
	</div>

	<div class="row">
		<label class="col-md-3">Repite contraseña</label>
		<div class="col-md-3"><input type="password" name="pass_r" maxlength="20" class="form-control" value="<?= $pass_r ?>" required="required" /></div>
	</div>

	<div class="row">
		<div class="col-md-offset-3 col-md-9">
			<input type="submit" class="btn btn-primary" name="enviar" value="Guardar" />
		</div>
	</div>
</form>