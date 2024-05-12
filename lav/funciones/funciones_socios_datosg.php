<?php
	function eliminar_socio()
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$id_socio	= request_var( 'soc_id_socio', 0 );
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "SELECT ven_id_socio FROM san_venta WHERE ven_id_socio = $id_socio AND ven_id_empresa = $id_empresa";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$filas	= mysqli_num_rows( $resultado );
			
			if( $filas <= 0 )
			{
				$query		= "DELETE FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					if( mysqli_affected_rows( $conexion ) == 1 )
					{
						$exito['num'] = 1;
						$exito['msj'] = "Cliente eliminado con éxito.";
						
						mysqli_commit( $conexion );
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "No se ha eliminado al Cliente.";
						
						mysqli_rollback( $conexion );
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "Ocurrió un problema al tratar de eliminar al Cliente. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Este Cliente no puede ser eliminado porque tiene un historial en Ventas.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Ocurrió un problema al verificar las ventas al Cliente. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_datos_socio()
	{
		Global $conexion, $id_empresa;
		$datos		= array();
		$id_socio	= request_var( 'soc_id_socio', 0 );
		
		$query		= "SELECT * FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos = $fila;
			}
		}
		
		return $datos;
	}
	
	function actualizar_socio()
	{
		Global $conexion, $id_empresa;
		
		$mensaje		= array();
		$soc_id_socio	= request_var( 'soc_id_socio', 0 );
		$correo			= request_var( 'soc_correo', '' );
		$descuento		= request_var( 'soc_descuento', 0.0 );
		
		$datos_sql		= array
		(
			'soc_nombres'			=> request_var( 'soc_nombres', '' ),
			'soc_apepat'			=> request_var( 'soc_apepat', '' ),
			'soc_apemat'			=> request_var( 'soc_apemat', '' ),
			'soc_direccion'			=> request_var( 'soc_direccion', '' ),
			'soc_colonia'			=> request_var( 'soc_colonia', '' ),
			'soc_tel_fijo'			=> request_var( 'soc_tel_fijo', '' ),
			'soc_tel_cel'			=> request_var( 'soc_tel_cel', '' ),
			'soc_descuento'			=> $descuento,
			'soc_correo'			=> $correo,
			'soc_notificaciones'	=> request_var( 'soc_noti', '' ),
			'soc_observaciones'		=> request_var( 'soc_observaciones', '' )
		);
		
		if( $descuento >= 0 && $descuento <= 100 )
		{
			if( !existe_correo_socio( $correo, $soc_id_socio ) )
			{
				$query		= construir_update( 'san_socios', $datos_sql, "soc_id_socio = $soc_id_socio AND soc_id_empresa = $id_empresa" );
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					$mensaje['num']	= 1;
					$mensaje['msj']	= "Registro actualizado correctamente.";
				}
				else
				{
					$mensaje['num']	= 3;
					$mensaje['msj']	= "No se ha podido actualizar la información de este socio. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$mensaje['num']	= 2;
				$mensaje['msj']	= "El correo ingresado ya ha sido capturado para otro socio, es necesario cambiarlo.";
			}
		}
		else
		{
			$mensaje['num']	= 4;
			$mensaje['msj']	= "El monto del descuento no es válido.";
		}
		
		return $mensaje;
	}
	
?>