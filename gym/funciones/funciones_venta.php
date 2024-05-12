<?php
	function preocesar_venta( $array_cant_idart, $p_chk_prepago, $id_prepago, $p_tot_prepago, $p_tot_efectivo, $p_sub_total, $p_metodo_pago, $p_tot_tarjeta, $p_monto_comision )
	{
		global $conexion, $id_usuario, $id_empresa, $id_consorcio;
		
		$continuar	= false;
		$socio_deta	= array();
		$artic_deta	= array();
		$folio		= array();
		$id_venta	= 0;
		$tipo_pago	= 'E';
		$exito		= array();
		$fecha_mov	= date( 'Y-m-d H:i:s' );
		
		mysqli_autocommit( $conexion, false );//comienza la transaccion
		
		if( $p_chk_prepago )
		{
			$tipo_pago = 'P';
				
			$query		= "UPDATE san_prepago SET prep_saldo = prep_saldo - $p_tot_prepago WHERE prep_id_prepago = $id_prepago AND prep_id_empresa = $id_empresa";
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				$query		= "	SELECT 		prep_saldo AS saldo,
											prep_id_socio AS id_socio,
											CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS nombre_s
								FROM 		san_prepago 
								INNER JOIN	san_socios ON soc_id_socio = prep_id_socio
								AND			soc_id_empresa = prep_id_empresa
								WHERE 		prep_id_prepago = $id_prepago
								AND			prep_id_empresa = $id_empresa";
									
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					if( $fila = mysqli_fetch_assoc( $resultado ) )
					{
						$socio_deta['saldo']	= $fila['saldo'];
						$socio_deta['id_socio']	= $fila['id_socio'];
						$socio_deta['nombre_s']	= $fila['nombre_s'];
					}
					
					$datos_sql	= array
					(
						'pred_id_prepago'	=> $id_prepago,
						'pred_descripcion'	=> 'Pago de Artículos',
						'pred_importe'		=> $p_tot_prepago,
						'pred_saldo'		=> $socio_deta['saldo'],
						'pred_movimiento'	=> 'R',
						'pred_fecha'		=> "$fecha_mov",
						'pred_id_usuario'	=> $id_usuario
					);
					
					$query		= construir_insert( 'san_prepago_detalle', $datos_sql );
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						$continuar = true;
					}
					else
					{
						$exito['num'] = 4;
						$exito['msj'] = "No se ha terminado la venta. No se pudo guardar el detalle del Saldo del Socio.";
					}
				}
				else
				{
					$exito['num'] = 3;
					$exito['msj'] = "No se ha terminado la venta. No se pudo obtener el Saldo del Socio despues de descontar el importe.";
				}
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se ha terminado la venta. No se pudo actualizar el Saldo del Socio.";
			}
		}
		else
			$continuar = true;
		
		if( $continuar )
		{
			$folio		= nuevo_folio();
			
			if( $folio['folio'] && $folio['anio'] )
			{
				if( $p_metodo_pago == 'T' )
					$tipo_pago = 'T';
				
				$datos_sql	= array
				(
					'ven_folio'				=> $folio['folio'],
					'ven_anio'				=> $folio['anio'],
					'ven_fecha'				=> $fecha_mov,
					'ven_total_efectivo'	=> $p_tot_efectivo,
					'ven_total_tarjeta'		=> $p_tot_tarjeta,
					'ven_comision'			=> $p_monto_comision,
					'ven_total_prepago'		=> $p_tot_prepago,
					'ven_total'				=> round( $p_sub_total + $p_monto_comision, 2 ),
					'ven_tipo_pago'			=> $tipo_pago,
					'ven_status'			=> 'V',	/*V=vendido, C=cancelado, P=CanceladoParcial*/
					'ven_id_prepago'		=> $id_prepago,
					'ven_id_socio'			=> isset( $socio_deta['id_socio'] ) ? $socio_deta['id_socio'] : 0,
					'ven_id_usuario'		=> $id_usuario,
					'ven_id_empresa'		=> $id_empresa
				);
				
				$query		= construir_insert( 'san_venta', $datos_sql );
				$resultado	= mysqli_query( $conexion, $query );
				$id_venta	= mysqli_insert_id( $conexion );
				
				if( $resultado && $folio['folio'] && $id_venta )
				{
					foreach( $array_cant_idart as $cant_idart )
					{
						list( $cantidad, $id_articulo ) = explode( '-', $cant_idart );
						
						$query		= "UPDATE san_stock SET stk_existencia = stk_existencia - $cantidad WHERE stk_id_articulo = $id_articulo AND stk_id_empresa = $id_empresa";
						$resultado	= mysqli_query( $conexion, $query );
						
						if( !$resultado )
						{
							$exito['num']	= 5;
							$exito['msj']	= "No se ha terminado la venta. No se pudo actualizar el Stock. ".mysqli_error( $conexion );
							break;
						}
						
						$artic_deta	= obtener_detalle_articulo( $id_articulo );
						
						$datos_sql	= array
						(
							'vende_id_articulo'			=> $artic_deta['art_id_articulo'],
							'vende_id_venta'			=> $id_venta,
							'vende_cantidad'			=> $cantidad,
							'vende_costo'				=> $artic_deta['art_costo'],
							'vende_precio_pre'			=> $artic_deta['art_precio'],
							'vende_precio'				=> $artic_deta['art_precio']
						);
						
						$query		= construir_insert( 'san_venta_detalle', $datos_sql );
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							$exito['num']	= 1;
							$exito['msj']	= "Venta terminada. Transacción finalizada.";
							$exito['folio']	= $folio['folio'];
							$exito['anio']	= $folio['anio'];
							$exito['ticket']	= $folio['ticket'];
							$exito['IDV']	= $id_venta;
						}
						else
						{
							$exito['num']	= 6;
							$exito['msj']	= "No se ha terminado la venta.  No se puede guardar el detalle de la Venta. ".mysqli_error( $conexion );
							break;
						}
					}
				}
				else
				{
					$exito['num']	= 7;
					$exito['msj']	= "No se ha terminado la venta. No se pudo guardar la Venta o no se pudo obtener el ID de la Venta. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num']	= 8;
				$exito['msj']	= "No se pudo obtener el folio de la venta.";
			}
		}
		
		if( $exito['num'] == 1 )
		{
			if( $p_chk_prepago )
				$exito['msj'] .= " Se descontó $".number_format( $p_tot_prepago, 2 )." del saldo del socio: $socio_deta[nombre_s]. Saldo actual: $".number_format( $socio_deta['saldo'], 2 );
			
			mysqli_commit( $conexion );
		}
		else
		{
			mysqli_rollback( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_detalle_articulo( $id_articulo )
	{
		global $conexion, $id_consorcio;
		
		$query		= "SELECT * FROM san_articulos WHERE art_id_articulo = $id_articulo AND art_id_consorcio = $id_consorcio";
		
		mysqli_autocommit( $conexion, false );
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		
		return false;
	}
	
	function obtener_saldo_socio( $id_prepago )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT prep_saldo AS saldo FROM san_prepago WHERE prep_id_prepago = $id_prepago AND prep_id_empresa = $id_empresa";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['saldo'];
		
		return false;
	}
	
	function lista_articulos()
	{
		global $conexion, $id_empresa, $id_consorcio;
		
		$datos		= "";
		$colspan	= 3;
		
		$query		= "	SELECT		art_id_articulo AS id_articulo,
									art_codigo AS codigo,
									art_descripcion AS descripcion,
									stk_existencia AS existencia,
									ROUND( art_precio, 2 ) AS precio
						FROM		san_articulos
						INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
						AND			stk_id_empresa = $id_empresa
						AND			art_id_consorcio = $id_consorcio
						AND			art_status = 'A'
						ORDER BY 	existencia DESC,
									descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$class	= '';
				if( $fila['existencia'] <= 0 )
					$class = "danger";
				
				$datos	.= "<tr onclick='agregar_articulo_venta( $fila[id_articulo] )' class='$class'>
								<td>".$fila['descripcion']."</td>
								<td class='text-right'>".$fila['existencia']."</td>
								<td class='text-right'>$".$fila['precio']."</td>
							</tr>";
			}
		}
		else
			$datos	= "<tr><td colspan='$colspan'>".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
?>