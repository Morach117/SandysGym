<?php
	ob_start();
	
	//se colocan aca por las peticiones
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones_globales/funciones_comunes.php" );
	
	require_once( "funciones/sesiones.php" );
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

		<title>Gymnasio | SERGYM</title>
		
		<link href="../css/bootstrap.css" rel="stylesheet">
		<link href="../css/css.css" rel="stylesheet">
		
		<script src="../js/jquery-2.1.0.min.js"></script>
		
		<script src="../js/bootstrap/modal.js"></script>
		<script src="../js/bootstrap/dropdown.js"></script>
		<script src="../js/bootstrap/collapse.js"></script>
		
		<script src="../js/datepicker/jquery-ui-1.10.4.custom.min.js"></script>
		<link href="../js/datepicker/jquery-ui.css" rel="stylesheet" type="text/css"/>
		
		<?php
			$js_version	= "?20200102";
			
			if( file_exists( "js/js.js" ) )
				echo "<script type='application/javascript' src='js/js.js$js_version'></script>";
			
			if( file_exists( "js/js_$seccion.js" ) )
				echo "<script type='application/javascript' src='js/js_$seccion.js$js_version'></script>";
			
			if( file_exists( "js/js_$seccion"."_$item.js" ) )
				echo "<script type='application/javascript' src='js/js_$seccion"."_$item.js$js_version'></script>";
		?>
	</head>
	
	<body>
		<div class="container">
			<nav class="navbar navbar-default" role="navigation">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="."><span class="glyphicon glyphicon-home"></span> <?= $empresa_abr ?></a>
					</div>
					
					<div id="navbar" class="collapse navbar-collapse">
						<ul class="nav navbar-nav">
							<li class="<?= ( $seccion == 'socios' ) ? 'active':'' ?>"><a href=".?s=socios"><span class="glyphicon glyphicon-user"></span> Socios</a></li>
							<li class="<?= ( $seccion == 'horas' ) ? 'active':'' ?>"><a href=".?s=horas"><span class="glyphicon glyphicon-time"></span> Horas</a></li>
							<li class="<?= ( $seccion == 'visitas' ) ? 'active':'' ?>"><a href=".?s=visitas"><span class="glyphicon glyphicon-time"></span> Visitas</a></li>
							<li class="<?= ( $seccion == 'venta' ) ? 'active':'' ?>"><a href=".?s=venta"><span class="glyphicon glyphicon-shopping-cart"></span> Venta</a></li>
							<li class="<?= ( $seccion == 'prepagos' ) ? 'active':'' ?>"><a href=".?s=prepagos"><span class="glyphicon glyphicon-usd"></span> Prepagos</a></li>
							<li class="<?= ( $seccion == 'promociones' ) ? 'active':'' ?>"><a href=".?s=promociones"><span class="glyphicon glyphicon-gift"></span> Promociones</a></li>
						</ul>
						
						<ul class="nav navbar-nav navbar-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-th"></span> <?= $nombres ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href=".?s=perfil"><span class="glyphicon glyphicon-user"></span> Perfil</a></li>
									<?= $administrador ?>
									<li class="divider"></li>
									<li><a href=".?s=salir"><span class="glyphicon glyphicon-log-out"></span> Salir</a></li>
								</ul>
							</li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</nav><!--menu-->
			
			<div class="row">
				<div class="col-md-3">
					<?php
						if( file_exists( "../imagenes/empresa_$id_empresa.png" ) )
						{
							echo "	<div align='center' style='background-color:#fff; padding:10px'>
										<img src='../imagenes/empresa_$id_empresa.png' class='img-responsive' alt='Logo' />
									</div>
									
									<br/>";
						}
						elseif( file_exists( "../imagenes/empresa_$id_empresa.jpg" ) )
						{
							echo "	<div align='center' style='background-color:#fff; padding:10px'>
										<img src='../imagenes/empresa_$id_empresa.jpg' class='img-responsive' alt='Logo' />
									</div>
									
									<br/>";
						}
						
						if( file_exists( "secciones/$seccion/menu.php" ) )
							require_once( "secciones/$seccion/menu.php" );
						elseif( file_exists( "secciones/inicio/menu.php" ) )
							require_once( "secciones/inicio/menu.php" );
					?>
				</div>
				
				<div class="col-md-9">
					<div class="well">
						<?php
							//funciones
							if( file_exists( "funciones/funciones_$seccion.php" ) )
								require_once( "funciones/funciones_$seccion.php" );
								
							if( file_exists( "funciones/funciones_$seccion"."_$item.php" ) )
								require_once( "funciones/funciones_$seccion"."_$item.php" );
							
							//item
							if( file_exists( "secciones/$seccion/$item.php" ) )
								require_once( "secciones/$seccion/$item.php" );
							elseif( file_exists( "secciones/$seccion/index.php" ) )
								require_once( "secciones/$seccion/index.php" );
							else
								require_once( "secciones/inicio/index.php" );
							
							mysqli_close( $conexion );
						?>
					</div>
				</div>
			</div>
		</div><!-- /.container -->
		
		<footer class="footer">
			<div class="container">
				<div class="row text-muted">
					<div class="col-md-12 text-center">
						<a href="http://sergym.com">http://sergym.com</a> | Gymnasio | SERGYM &copy; <?= date('Y') ?> | Servicios Generales y de Mantenimiento | <?= date( 'd/m/Y h:i:s a' ) ?>
					</div>
				</div>
			</div>
		</footer>
		
		<div id="ticket_cliente"></div>
		
		<div class="modal fade" id="modal_principal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<!--Este bloque muestra la parte de sombra, el que muestra el Cuadro de Dialogo se coloca en donde sera utilizado, como en peticiones por ejemplo-->
		</div><!-- /.modal -->
	</body>
</html>