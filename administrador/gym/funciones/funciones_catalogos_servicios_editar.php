<?php
	function actualizar_servicio()
	{
		global $conexion, $id_giro, $id_consorcio;
		
		$id_servicio	= request_var( 'id_servicio', 0 );
		$exito			= array();
		
		$datos		= array
		(
			'ser_descripcion'	=> request_var( 's_descripcion', '' ),
			'ser_tipo'			=> request_var( 's_tipo', '' ),
			'ser_cuota'			=> request_var( 's_cuota', 0.0 ),
			'ser_dias'			=> request_var( 's_dias', 0 ),
			'ser_meses'			=> request_var( 's_meses', 0 ),
		);
		
		$query		= construir_update( "san_servicios", $datos, "ser_id_servicio = $id_servicio AND ser_id_giro = $id_giro AND ser_id_consorcio = $id_consorcio" );
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( mysqli_affected_rows( $conexion ) )
			{
				$exito['num']	= 1;
				$exito['msj']	= "Actualización confirmada.";
			}
			else
			{
				$exito['num']	= 3;
				$exito['msj']	= "No se ha actializado ningun dato.";
			}
		}
		else
		{
			$exito['num']	= 2;
			$exito['msj']	= "Ocurrió un problema al tratar de actualizar. ".mysqli_error( $conexion )."";
		}
		
		return $exito;
	}
	
	function obtener_servicio()
	{
		global $conexion, $id_giro, $id_consorcio;
		
		$id_servicio	= request_var( 'id_servicio', 0 );
		
		$query		= "	SELECT	ser_id_servicio AS id_servicio,
								ser_clave AS clave,
								ser_descripcion AS descripcion,
								ser_tipo AS tipo,
								ser_cuota AS cuota,
								ser_dias AS dias,
								ser_meses AS meses
						FROM	san_servicios
						WHERE	ser_id_giro = $id_giro
						AND		ser_id_servicio = $id_servicio
						AND		ser_id_consorcio = $id_consorcio";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		else
			echo "Error: ".mysqli_error( $conexion );
			
		return false;
	}
	
?>