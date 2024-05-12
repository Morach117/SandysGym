<?php
	ob_start();
	date_default_timezone_set('america/mexico_city');
	
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	
	require_once( "funciones/sesiones.php" );
	
	$mensajes[1]	= "Bienvenido!!";
	$mensajes[2]	= "No hay conexión con la BD.";
	$mensajes[3]	= "Escribe un correo y contraseña.";
	$mensajes[4]	= "No es un correo válido.";
	$mensajes[5]	= "Ocurrió un problema al buscar la información.";
	$mensajes[6]	= "No se encontró información con los datos ingresados.";
	$mensajes[7]	= "Combinación de correo y contraseña incorrectos.";
	$mensajes[8]	= "Se perdió la conexión con la BD.";
	$mensajes[9]	= "Acceso no permitido.";
	$mensajes[10]	= "Sesión terminada.";
	$mensajes[11]	= "No se pudo obtener el Catalogo de Aplicaciones.";
	$mensajes[12]	= "Acceso Denegado";
	$mensajes[13]	= "Acceso no permitido a este Giro";
	$mensajes[14]	= ".";
	$mensajes[15]	= ".";
	
	if( $error == 1 || $error == 10 )
		$msj_error = "text-success";
	else
		$msj_error = "text-danger";
?>

<!DOCTYPE html>

<html lang="es-MX">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="iSac Vázquez">

		<title>Acceso - SERGYM</title>
		
		<meta http-equiv="expires" content="0">
		<meta http-equiv="pragma" content="no-cache">
		
		<link href="../css/bootstrap.css" rel="stylesheet">
		<link href="../css/css.css" rel="stylesheet">
	</head>
	
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-offset-2 col-md-4">
					<h4 class="text-primary">Acceso para Administrativos</h4>
					
					<p>
						<form role="form" action="." method="post">
							<div class="form-group">
								<label for="exampleInputEmail1">Correo electrónico</label>
								<input type="text" name="san_correo" class="form-control" placeholder="correo@ejemplo.com" required="required" />
							</div>
							
							<div class="form-group">
								<label for="exampleInputPassword1">Contraseña</label>
								<input type="password" name="san_pass" class="form-control" required="required" />
							</div>
							
							<div class="form-group text-center">
								<label class="<?= $msj_error ?>"><?= $mensajes[$error] ?></label>
							</div>
							
							<div class="checkbox">
								<label>
								<input type="checkbox"> No cerrar sesión
								</label>
							</div>
							
							<input type="submit" name="enviar" class="btn btn-primary" value="Ingresar" />
						</form>
					</p>
				</div>
				
				<div class="col-md-4">
					<h4 class="text-primary">Bienvenido !!!</h4>
					
					<p>Bienvenido, para ingresar escriba su correo y contraseña proporcionado por el Administrador del Sitio.</p>
				</div>
			</div>
		</div><!-- /.container -->
		
		<footer class="footer">
			<div class="container">
				<div class="row text-muted">
					<div class="col-md-12 text-center">
						<a href="http://sergym.com">http://sergym.com</a> | SERGYM &copy; <?= date('Y') ?> | Servicios Generales y de Mantenimiento
					</div>
				</div>
			</div>
		</footer>
	</body>
</html>
