<?php
	function guardar_nuevo_socio()
	{
		Global $conexion, $id_usuario, $id_empresa, $id_consorcio;
		$mensaje	= array();
		$correo		= request_var( 'soc_correo', '' );
		$descuento	= request_var( 'soc_descuento', 0.0 );
		
		$datos_sql	= array
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
			'soc_observaciones'		=> request_var( 'soc_observaciones', '' ),
			'soc_id_usuario'		=> $id_usuario,
			'soc_id_empresa'		=> $id_empresa,
			'soc_id_consorcio'		=> $id_consorcio
		);
		
		if( $descuento >= 0 && $descuento <= 100 )
		{
			if( !existe_correo_socio( $correo ) )
			{
				$query		= construir_insert( 'san_socios', $datos_sql );
				
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					$mensaje['num']	= 1;
					$mensaje['msj']	= "Registro guardado correctamente.";
				}
				else
				{
					$mensaje['num']	= 3;
					$mensaje['msj']	= "No se ha podido guardar la información de este socio. ".mysqli_error( $conexion );
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