<?php
	function eliminar_servicio( $p_id_servicio )
	{
		global $conexion, $id_consorcio;
		
		$exito		= array();
		$error		= "";
		$ides		= sucursales_por_empresa_validacion();
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "	SELECT	COUNT( * ) AS total
						FROM	san_venta_servicio
						WHERE	vense_id_servicio = $p_id_servicio";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['total'] == 0 )
				{
					$query		= "DELETE FROM san_servicios_cuotas WHERE src_id_servicio = $p_id_servicio";
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) <= count( $ides ) )
						{
							$query		= "DELETE FROM san_servicios WHERE ser_id_servicio = $p_id_servicio AND ser_id_consorcio = $id_consorcio AND ser_id_giro = 2";
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								if( mysqli_affected_rows( $conexion ) == 1 )
								{
									$exito['num'] = 1;
									$exito['msj'] = "Eliminado...";
								}
								else
								{
									$exito['num'] = 9;
									$exito['msj'] = "No se puede elimnar el Servicio.";
								}
							}
							else
							{
								$exito['num'] = 8;
								$exito['msj'] = "Ocurrió un problema al tratar de eliminar el Servicio. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num'] = 7;
							$exito['msj'] = "No se puede eliminar las cuotas del Servicio.";
						}
					}
					else
					{
						$exito['num'] = 6;
						$exito['msj'] = "Ocurrió un problema al tratar de eliminar las cuotas del Servicio. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 5;
					$exito['msj'] = "No se puede eliminar el Servicio porque ya hay registro de ventas.";
				}
			}
			else
			{
				$exito['num'] = 4;
				$exito['msj'] = "No se puede realizar el conteo de ventas del Servicio.";
			}
		}
		else
		{
			$exito['num'] = 3;
			$exito['msj'] = "Ocurrió un problema al tratar de consultar las ventas del Servicio. ".mysqli_error( $conexion );
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
?>