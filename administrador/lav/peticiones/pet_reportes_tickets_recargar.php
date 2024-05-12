<?php
	require_once( "../../../funciones_globales/funciones_conexion.php" );
	require_once( "../../../funciones_globales/funciones_comunes.php" );
	require_once( "../../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_reportes_tickets.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$mes_evaluar	= request_var( 'mes_evaluar', '' );
	$busqueda		= request_var( 'busqueda', '' );
	$anio			= request_var( 'anio', 0 );
	
	if( $enviar )
	{
		$datos	= obtener_tickets( $busqueda, $anio, $mes_evaluar );
		
		echo $datos;
	}
	
	mysqli_close( $conexion );
?>