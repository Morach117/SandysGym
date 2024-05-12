<?php
	function guardar_nuevo_dia()
	{
		global $conexion, $id_usuario, $id_empresa, $gbl_key;
		
		$cuota				= obtener_servicio( 'VISITA' );
		$exito				= array();
		
		$datos_sql			= array
		(
			'hor_nombre'		=> request_var( 'hor_nombre', '' ),
			'hor_fecha'			=> date( 'Y-m-d H:i:s' ),
			'hor_importe'		=> $cuota['cuota'],
			'hor_genero'		=> request_var( 'hor_genero', '' ),
			'hor_id_servicio'	=> $cuota['id_servicio'],
			'hor_id_usuario'	=> $id_usuario,
			'hor_id_empresa'	=> $id_empresa
		);
		
		$query		= construir_insert( 'san_horas', $datos_sql );
		
		$resultado	= mysqli_query( $conexion, $query );
		$id_visita	= mysqli_insert_id( $conexion );
		$token		= hash_hmac( 'md5', $id_visita, $gbl_key );
		
		if( $resultado && $id_visita && $token )
		{
			$exito['num'] = 1;
			$exito['msj'] = "Guardado.";
			$exito['IDV'] = $id_visita;
			$exito['tkn'] = $token;
		}
		else
		{
			$exito['num']	= 2;
			$exito['msj']	= "No se ha podido guardar los datos capturados. Intenta nuevamente. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
?>