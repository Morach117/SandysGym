<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_articulos_marcas.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$datos		= "";
	
	if( $enviar )
	{
		$datos = obtener_lista_marcas();
	}
	
	echo $datos;
	
	mysqli_close( $conexion );
?>