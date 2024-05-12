<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	
	require_once( "../funciones/sesiones.php" );
	require_once( "../funciones/funciones_catalogos_gastos.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$js_anio	= request_var( 'anio', 0 );
	$js_mes		= request_var( 'mes', 0 );
	$js_ids		= request_var( 'sucursal', 0 );
	$datos		= "";
	
	if( $js_mes < 10 && strlen( $js_mes ) == 1 )
		$js_mes = "0".$js_mes;
	
	if( $enviar )
		$datos	= obtener_gastos( "$js_anio-$js_mes", $js_ids );
	
	mysqli_close( $conexion );
	
	echo $datos;
?>