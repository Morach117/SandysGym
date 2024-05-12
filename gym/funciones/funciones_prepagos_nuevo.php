<?php
	function guardar_nuevo_prepago()
	{
		global $conexion, $id_usuario, $id_empresa, $gbl_key;
		
		$prep_id_prepago	= 0;
		$prep_id_prepago_d	= 0;
		$prep_id_socio		= request_var( 'id_socio', 0 );
		$prep_importe		= request_var( 'prep_importe', 0.0 );
		$fecha_mov			= date( 'Y-m-d H:i:s' );
		
		$datos_sql			= array
		(
			'prep_id_socio'		=> $prep_id_socio,
			'prep_saldo'		=> $prep_importe,
			'prep_id_empresa'	=> $id_empresa
		);
		
		$query				= construir_insert( 'san_prepago', $datos_sql );
		
		mysqli_autocommit( $conexion, false );
		
		$resultado			= mysqli_query( $conexion, $query );
		$prep_id_prepago	= mysqli_insert_id( $conexion );
		
		if( $resultado && $prep_id_prepago && $prep_importe )
		{
			$datos_sql			= array
			(
				'pred_id_prepago'	=> $prep_id_prepago,
				'pred_descripcion'	=> 'APERTURA DE CUENTA PREPAGO',
				'pred_importe'		=> $prep_importe,
				'pred_saldo'		=> $prep_importe,
				'pred_movimiento'	=> 'S',
				'pred_fecha'		=> "$fecha_mov",
				'pred_id_usuario'	=> $id_usuario
			);
			
			$query				= construir_insert( 'san_prepago_detalle', $datos_sql );
			$resultado			= mysqli_query( $conexion, $query );
			$prep_id_prepago_d	= mysqli_insert_id( $conexion );
			$token				= hash_hmac( 'md5', $prep_id_prepago, $gbl_key );
			
			if( $resultado && $prep_id_prepago_d && $token )
			{
				$mensaje['num'] = 1;
				$mensaje['msj'] = "El Prepago se ha capturado de manera correcta.";
				$mensaje['IDP']	= $prep_id_prepago;
				$mensaje['IDD']	= $prep_id_prepago_d;
				$mensaje['IDS']	= $prep_id_socio;
				$mensaje['tkn'] = $token;
			}
			else
			{
				$mensaje['num'] = 3;
				$mensaje['msj'] = "No se ha podido guardar los detalles del Prepago. Intenta nuevamente. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$mensaje['num'] = 2;
			$mensaje['msj'] = "No se ha podido guardar el Importe para el Prepago. Intenta nuevamente. ".mysqli_error( $conexion );
		}
		
		if( $mensaje['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $mensaje;
	}
	
	function obtener_socios_sin_prepago()
	{
		global $conexion, $id_empresa, $gbl_paginado;
		$pagina		= ( request_var( 'pag', 1 ) - 1 ) * $gbl_paginado;
		$condicion	= "LIMIT $pagina, $gbl_paginado";
		$datos		= "";
		$colspan	= 2;
		
		$query		= "	SELECT 	soc_id_socio AS id_socio,
								CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres) AS socio
						FROM	san_socios
						WHERE	soc_id_empresa = $id_empresa
						AND		soc_id_socio NOT IN (	SELECT	prep_id_socio
														FROM	san_prepago
														WHERE	prep_id_empresa = $id_empresa
													)
								$condicion";
			
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos .= "	<tr onclick='mostrar_captura_prepago( $fila[id_socio] )'>
								<td>".( $pagina + $i )."</td>
								<td>$fila[socio]</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>Ocurrio un error al obtener los datos. $error</td></tr>";
		
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
?>