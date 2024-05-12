<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-user"></span> Perfil</h4>
	</div>
</div>

<hr/>

<?php
	$exito	= obtener_datos_usuario();
	$perfil	= array();
	
	if( $exito['num'] != 1 )
	{
		mostrar_mensaje_div( $exito['msj'], 'danger' );
	}
	else
	{
		$perfil = $exito['msj'];
?>

		<div class="row">
			<label class="col-md-2">Empresa</label>
			<div class="col-md-10"><?= $perfil['empresa'] ?></div>
		</div>

		<div class="row">
			<label class="col-md-2">A. Paterno</label>
			<div class="col-md-10"><?= $perfil['ape_pat'] ?></div>
		</div>

		<div class="row">
			<label class="col-md-2">A. Materno</label>
			<div class="col-md-10"><?= $perfil['ape_mat'] ?></div>
		</div>

		<div class="row">
			<label class="col-md-2">Nombres</label>
			<div class="col-md-10"><?= $perfil['nombres'] ?></div>
		</div>

		<div class="row">
			<label class="col-md-2">Correo</label>
			<div class="col-md-10"><?= $perfil['correo'] ?></div>
		</div>

		<div class="row">
			<label class="col-md-2">Contraseña</label>
			<div class="col-md-10"><?= $perfil['pass'] ?> <small>Encriptado</small></div>
		</div>

<?php
	}
?>