<?php
	function guardar_servicio()
	{
		global $conexion, $id_giro, $id_consorcio;
		$exito		= array();
		
		$datos		= array
		(
			'ser_clave'			=> request_var( 's_clave', '' ),
			'ser_descripcion'	=> request_var( 's_descripcion', '' ),
			'ser_tipo'			=> request_var( 's_tipo', '' ),
			'ser_cuota'			=> request_var( 's_cuota', 0.0 ),
			'ser_dias'			=> request_var( 's_dias', 0 ),
			'ser_meses'			=> request_var( 's_meses', 0 ),
			'ser_id_giro'		=> $id_giro,
			'ser_id_consorcio'	=> $id_consorcio
		);
		$query		= construir_insert( 'san_servicios', $datos );
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( mysqli_affected_rows( $conexion ) )
			{
				$exito['num']	= 1;
				$exito['msj']	= "Información guardado con exito.";
			}
			else
			{
				$exito['num']	= 3;
				$exito['msj']	= "No se ha guardado nada.";
			}
		}
		else
		{
			$exito['num']	= 2;
			$exito['msj']	= "Ocurrió un problema la tratar de guardar la información de este servicio. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
?>