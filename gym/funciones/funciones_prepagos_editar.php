<?php
	function obtener_prepago()
	{
		global $conexion, $id_empresa;
		
		$id_prepago	= request_var( 'id_prepago', 0 );
		
		$query		= "	SELECT 		CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS nombre,
									prep_saldo AS saldo,
									prep_id_socio AS id_socio
						FROM		san_prepago
						INNER JOIN	san_socios ON soc_id_socio = prep_id_socio
						AND			prep_id_empresa = soc_id_empresa
						WHERE		prep_id_prepago = $id_prepago
						AND			prep_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		else
			echo "Error: ".mysqli_error( $conexion );
			
		return false;
	}
	
	function obtener_prepago_detalle()
	{
		global $conexion, $id_empresa;
		
		$id_prepago	= request_var( 'id_prepago', 0 );
		$datos		= "";
		$colspan	= 7;
		
		$query		= "	SELECT		prep_id_prepago AS id_prepago,
									pred_id_pdetalle AS id_pdetalle,
									pred_descripcion AS p_descripcion,
									ROUND( pred_importe, 2 ) AS importe,
									ROUND( pred_saldo, 2 ) AS saldo,
									CASE pred_movimiento
										WHEN 'R' THEN 'Resta'
										WHEN 'S' THEN 'Suma'
									END
									AS movimiento,
									DATE_FORMAT( pred_fecha, '%d-%m-%Y' ) AS fecha,
									LOWER( DATE_FORMAT( pred_fecha, '%r' ) ) AS hora
						FROM		san_prepago
						INNER JOIN	san_prepago_detalle ON pred_id_prepago = prep_id_prepago
						WHERE		prep_id_prepago = $id_prepago
						AND			prep_id_empresa = $id_empresa
						ORDER BY	id_pdetalle DESC";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$i</td>
								<td>$fila[p_descripcion]</td>
								<td class='text-right'>$$fila[importe]</td>
								<td class='text-right'>$$fila[saldo]</td>
								<td>$fila[movimiento]</td>
								<td>$fila[fecha]</td>
								<td>$fila[hora]</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
			
		return $datos;
	}
	
	function actualizar_prepago()
	{
		global $conexion, $id_usuario, $id_empresa, $gbl_key;
		
		$prep_id_prepago_d	= 0;
		$prep_id_prepago	= request_var( 'id_prepago', 0 );
		$prep_importe		= request_var( 'prep_importe', 0.0 );
		$prep_id_socio		= request_var( 'id_socio', 0 );
		$fecha_mov			= date( 'Y-m-d H:i:s' );
		
		$query				= "	UPDATE	san_prepago 
								SET 	prep_saldo = ( prep_saldo + $prep_importe ) 
								WHERE 	prep_id_prepago = $prep_id_prepago 
								AND 	prep_id_empresa = $id_empresa
								AND		prep_id_socio = $prep_id_socio";
		
		mysqli_autocommit( $conexion, false );
		
		$resultado			= mysqli_query( $conexion, $query );
		
		if( $resultado && $prep_id_prepago && $prep_importe )
		{
			$query			= "	SELECT 	prep_saldo AS saldo
								FROM 	san_prepago 
								WHERE 	prep_id_prepago = $prep_id_prepago
								AND		prep_id_empresa = $id_empresa
								AND		prep_id_socio = $prep_id_socio";
			
			$resultado		= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$datos_sql			= array
					(
						'pred_id_prepago'	=> $prep_id_prepago,
						'pred_descripcion'	=> 'ABONO A CUENTA PREPAGO',
						'pred_importe'		=> $prep_importe,
						'pred_saldo'		=> $fila['saldo'],
						'pred_movimiento'	=> 'S',
						'pred_fecha'		=> $fecha_mov,
						'pred_id_usuario'	=> $id_usuario
					);
					
					$query				= construir_insert( 'san_prepago_detalle', $datos_sql );
					$resultado			= mysqli_query( $conexion, $query );
					$prep_id_prepago_d	= mysqli_insert_id( $conexion );
					$token				= hash_hmac( 'md5', $prep_id_prepago, $gbl_key );
					
					if( $resultado && $prep_id_prepago_d )
					{
						$mensaje['num'] = 1;
						$mensaje['msj'] = "El Prepago se ha agregado de manera correcta.";
						$mensaje['IDP']	= $prep_id_prepago;
						$mensaje['IDD']	= $prep_id_prepago_d;
						$mensaje['IDS']	= $prep_id_socio;
						$mensaje['tkn'] = $token;
					}
					else
					{
						$mensaje['num'] = 3;
						$mensaje['msj'] = "No se ha podido actualizar el detalle del Prepago. Intenta nuevamente. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$mensaje['num'] = 4;
					$mensaje['msj'] = "No se ha podido obtener el Saldo para actualizar el historico. Intenta nuevamente.";
				}
			}
			else
			{
				$mensaje['num'] = 5;
				$mensaje['msj'] = "Ocurrió un problema al tratar de obtener el Saldo para actualizar el historico. Intenta nuevamente. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$mensaje['num'] = 2;
			$mensaje['msj'] = "No se ha podido agregar el Importe para el Prepago. Intenta nuevamente. ".mysqli_error( $conexion );
		}
		
		if( $mensaje['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $mensaje;
	}
	
?>