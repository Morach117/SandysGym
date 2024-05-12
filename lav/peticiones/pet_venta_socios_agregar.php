<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$soc_nombres	= request_var( 'soc_nombres', '' );
	$soc_apepat		= request_var( 'soc_apepat', '' );
	$soc_apemat		= request_var( 'soc_apemat', '' );
	$soc_correo		= request_var( 'soc_correo', '' );
	
	if( $enviar )
	{
		$datos_sql	= array
		(
			'soc_nombres'		=> $soc_nombres,
			'soc_apepat'		=> $soc_apepat,
			'soc_apemat'		=> $soc_apemat,
			'soc_correo'		=> $soc_correo,
			'soc_id_usuario'	=> $id_usuario,
			'soc_id_empresa'	=> $id_empresa,
			'soc_id_consorcio'	=> $id_consorcio
		);
		
		if( !existe_correo_socio( $soc_correo ) )
		{
			$query		= construir_insert( 'san_socios', $datos_sql );
			
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( mysqli_affected_rows( $conexion ) != 1 )
					echo "No se guardo este Cliente.";
			}
			else
				echo "No se ha podido guardar la información de este socio. ".mysqli_error( $conexion );
		}
		else
			echo "El correo ingresado ya ha sido capturado para otro socio, es necesario cambiarlo.";
	}
	else
		echo "No se valido el envio del formulario.";
	
	mysqli_close( $conexion );
?>