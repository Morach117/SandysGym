<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_venta.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$f_entrega	= request_var( 'f_entrega', '' );
	$f_tipo		= request_var( 'f_tipo', '' ); //EDREDONES, PEDREDONES si se recepciona miercoles, se entrega lunes
	$datos		= "";
	
	if( $enviar )
	{
		$datos	= fecha_entrega( $f_tipo, $f_entrega );
		
		echo $datos['hora'];
	}
	else
		echo ":(";
	
	mysqli_close( $conexion );
?>