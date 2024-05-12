<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_catalogos_gastos.php" );
	
	$rango_ini	= fecha_formato_mysql( request_var( 'rango_ini', '' ) );
	$rango_fin	= fecha_formato_mysql( request_var( 'rango_fin', '' ) );
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$datos		= "";
	
	if( $enviar )
	{
		$datos	= obtener_gastos( $rango_ini, $rango_fin );
	}
	
	echo $datos;
	
	mysqli_close( $conexion );
?>