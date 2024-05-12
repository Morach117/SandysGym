<?php
	function guardar_proveedora()
	{
		global $conexion, $id_consorcio;
		$exito		= array();
		$datos		= array
		(
			'pro_id_consorcio'	=> $id_consorcio,
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
		
		$query		= construir_insert( 'san_proveedores', $datos );
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
			$exito['msj']	= "Ocurrió un problema la tratar de guardar la información de este proveedor. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
?>