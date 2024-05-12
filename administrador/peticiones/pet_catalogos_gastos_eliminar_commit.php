<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	
	require_once( "../funciones/sesiones.php" );
	require_once( "../funciones/funciones_catalogos_gastos.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$id_gasto	= request_var( 'id_gasto', 0 );
	$datos		= array();
	
	if( $enviar )
		$datos	= eliminar_gasto( $id_gasto );
	else
	{
		$exito['num'] = 2;
		$exito['msj'] = "No se validó en envio del formulario.";
	}
	
	mysqli_close( $conexion );
	
	echo json_encode( $datos );
?>