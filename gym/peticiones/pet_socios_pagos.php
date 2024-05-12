<?php
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	
	$envio		= isset( $_POST['envio'] ) ? true:false;
	$fecha		= request_var( 'fecha', '' );//dd-mm-yyyy
	$servicio	= request_var( 'servicio', '' );//servicio-meses
	
	if( $envio )
	{
		if( strpos( $fecha, '-' ) && strpos( $servicio, '-' ) )
		{
			list( $dia, $mes, $año )	= explode( '-', $fecha );
			list( $servicio, $meses )	= explode( '-', $servicio );
			
			if( checkdate( $mes, $dia, $año ) && $servicio && $meses && strlen( $año ) == 4 )
			{
				$fecha_selec	= mktime( 0, 0, 0, $mes, $dia, $año );
				$fecha_selec	= date( 'd-m-Y', strtotime( "+$meses month", $fecha_selec ) );
				
				echo $fecha_selec;
			}
		}
	}
?>