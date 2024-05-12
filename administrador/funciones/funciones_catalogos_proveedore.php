<?php
	function actualizar_proveedor()
	{
		global $conexion, $id_consorcio;
		
		$id_proveedor	= request_var( 'id_proveedor', 0 );
		$exito			= array();
		
		$datos		= array
		(
			'pro_nombres'		=> request_var( 'p_nombres', '' ),
			'pro_direccion'		=> request_var( 'p_direccion', '' ),
			'pro_contacto_1'	=> request_var( 'pc_nombres_1', '' ),
			'pro_tel_fijo_1'	=> request_var( 'pc_telefono_1', '' ),
			'pro_tel_ext_1'		=> request_var( 'pc_ext_1', '' ),
			'pro_tel_cel_1'		=> request_var( 'pc_telcel_1', '' ),
			'pro_correo_1'		=> request_var( 'pc_correo_1', '' ),
			'pro_contacto_2'	=> request_var( 'pc_nombres_2', '' ),
			'pro_tel_fijo_2'	=> request_var( 'pc_telefono_2', '' ),
			'pro_tel_ext_2'		=> request_var( 'pc_ext_2', '' ),
			'pro_tel_cel_2'		=> request_var( 'pc_telcel_2', '' ),
			'pro_correo_2'		=> request_var( 'pc_correo_2', '' )
		);
		
		$query		= construir_update( "san_proveedores", $datos, "pro_id_proveedor = $id_proveedor AND pro_id_consorcio = $id_consorcio" );
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
			$exito['msj']	= "Ocurrió un problema al tratar de actualizar. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	function obtener_proveedor()
	{
		global $conexion, $id_consorcio;
		
		$id_proveedor	= request_var( 'id_proveedor', 0 );
		
		$query		= "	SELECT		pro_id_proveedor AS id_proveedor,
									pro_nombres AS nombres,
									pro_direccion AS direccion,
									pro_contacto_1 AS contacto_1,
									pro_tel_fijo_1 AS telefono_1,
									pro_tel_ext_1 AS ext_1,
									pro_tel_cel_1 AS telcel_1,
									pro_correo_1 AS correo_1,
									pro_contacto_2 AS contacto_2,
									pro_tel_fijo_2 AS telefono_2,
									pro_tel_ext_2 AS ext_2,
									pro_tel_cel_2 AS telcel_2,
									pro_correo_2 AS correo_2
						FROM		san_proveedores
						WHERE		pro_id_proveedor = $id_proveedor
						AND			pro_id_consorcio = $id_consorcio";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
			
		return false;
	}
	
?>