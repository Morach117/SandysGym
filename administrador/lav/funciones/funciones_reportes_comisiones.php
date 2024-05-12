<?php
	function procesar_comisiones()
	{
		global $conexion, $id_empresa;
		
		$folios_ids	= isset( $_POST['folio_id'] ) ? $_POST['folio_id'] : 0;
		$folio		= 0;
		$id_venta	= 0;
		$fo_act		= "";
		$fecha_mov	= date( 'Y-m-d H:i:s' );
		
		mysqli_autocommit( $conexion, false );
		
		if( $folios_ids )
		{
			foreach( $folios_ids as $folio_id )
			{
				list( $id_venta, $folio ) = explode( '-', $folio_id );
				
				if( $folio && $id_venta )
				{
					$query		= "	UPDATE	san_venta
									SET		ven_observaciones = 'Comisión pagada',
											ven_status = 'P',
											ven_fecha = '$fecha_mov'
									WHERE	ven_id_venta = $id_venta
									AND		ven_folio = $folio
									AND		ven_id_empresa = $id_empresa";
					
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) == 1 )
						{
							$query		= "INSERT INTO san_venta_historico(	venh_id_venta, 
																			venh_folio, 
																			venh_anio, 
																			venh_fecha, 
																			venh_entrega, 
																			venh_total_efectivo, 
																			venh_total_prepago, 
																			venh_total_credito, 
																			venh_total, 
																			venh_tipo_pago, 
																			venh_status, 
																			venh_observaciones, 
																			venh_n_m,
																			venh_id_prepago, 
																			venh_id_socio, 
																			venh_id_usuario, 
																			venh_id_lavador, 
																			venh_id_empresa
																		)
																		( 	SELECT	ven_id_venta, 
																					ven_folio, 
																					ven_anio, 
																					ven_fecha, 
																					ven_entrega, 
																					ven_total_efectivo, 
																					ven_total_prepago, 
																					ven_total_credito, 
																					ven_total, 
																					ven_tipo_pago, 
																					ven_status, 
																					ven_observaciones, 
																					5 AS n_m,
																					ven_id_prepago, 
																					ven_id_socio, 
																					ven_id_usuario, 
																					ven_id_lavador, 
																					ven_id_empresa 
																			FROM 	san_venta 
																			WHERE 	ven_id_venta = $id_venta
																			AND		ven_folio = $folio 
																			AND		ven_id_empresa = $id_empresa
																		)";
																		
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								if( mysqli_affected_rows( $conexion ) == 1 )
								{
									$fo_act .= " $folio, ";
									$fo_act	= substr( $fo_act, 0, -2 );
									
									$exito['num'] = 1;
									$exito['msj'] = "Cambios realizados, folios afectados: $fo_act.";
									
									continue;
								}
								else
								{
									$exito['num'] = 7;
									$exito['msj'] = "No se realizó ningún cambio del STATUS en el histórico con el folio: $folio.";
								}
							}
							else
							{
								$exito['num'] = 6;
								$exito['msj'] = "Error al guardar el histórico del STATUS con el folio: $folio. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num'] = 5;
							$exito['msj'] = "No se realizó ningún cambio con el folio: $folio.";
						}
					}
					else
					{
						$exito['num'] = 4;
						$exito['msj'] = "Error al actualizar el STATUS con el folio: $folio. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 3;
					$exito['msj'] = "El folio seleccionado es inválido.";
				}
				
				break;
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se ha seleccionado ninguna nota.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}

	function lista_comisiones_del_dia( $pagadas = 'N', $fecha_movimiento )
	{
		global $conexion, $id_empresa;
		
		list( $d, $m, $Y )	= explode( '-', $fecha_movimiento );
		$datos		= "";
		$colspan	= 9;
		$pagado		= 0;
		$total		= 0;
		$total_a	= 0;
		$total_b	= 0;
		
		if( $pagadas == 'N' )
			$condicion = "AND ven_status != 'P'";
		elseif( $pagadas == 'S' )
			$condicion = "AND ven_status = 'P'";
		else
			$condicion = "ERROR";
		
		$query		= "	SELECT		ven_folio AS folio,
									ven_id_venta AS id_venta,
									CONCAT( 'F', LPAD( ven_folio, 7, '0' ) ) AS desc_folio,
									LOWER( DATE_FORMAT( ven_fecha, '%r' ) ) AS hora,
									ven_status AS status,
									CASE ven_status 
										WHEN 'R' THEN 'Recepcionado'
										WHEN 'L' THEN 'Lavado'
										WHEN 'E' THEN 'Entregado'
										WHEN 'P' THEN 'Pagado'
										ELSE ven_status
									END AS status_desc,
									ROUND( ven_total - ven_total_credito, 2 ) AS pagado,
									ROUND( ven_total, 2 ) AS total,
									ROUND( IF( emp_comision IS NULL, 0, emp_comision ), 2 ) AS comision,
									ROUND( IF( emp_comision IS NULL, 0, ven_total - ( ven_total * ( emp_comision / 100 ) ) ), 2 ) AS comision_a,
									ROUND( IF( emp_comision IS NULL, 0, ven_total * ( emp_comision / 100 ) ), 2 ) AS comision_b
						FROM		san_venta
						INNER JOIN	san_empresas ON emp_id_empresa = ven_id_empresa
						WHERE		ven_id_empresa = $id_empresa
									$condicion
						AND			'$fecha_movimiento' = DATE_FORMAT( ven_fecha, '%d-%m-%Y' )
						ORDER BY	status,
									desc_folio DESC";
		
		if( checkdate( $m, $d, $Y ) )
		{
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				$i = 1;
				while( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$pagado		+= $fila['pagado'];
					$total		+= $fila['total'];
					$total_a	+= $fila['comision_a'];
					$total_b	+= $fila['comision_b'];
					
					if( $pagadas == 'N' && $fila['status'] == 'E' )
						$check = "<input type='checkbox' name='folio_id[]' value='$fila[id_venta]-$fila[folio]' />";
					else
						$check = "";
					
					$datos	.= "<tr>
									<td>$i</td>
									<td>$check</td>
									<td>".$fila['desc_folio']."</td>
									<td>".$fila['status_desc']."</td>
									<td>".$fila['hora']."</td>
									<td class='text-right'>$".$fila['pagado']."</td>
									<td class='text-right'>$".$fila['total']."</td>
									<td class='text-right'>$".$fila['comision_a']."</td>
									<td class='text-right'>$".$fila['comision_b']."</td>
								</tr>";
								
					$i++;
				}
			}
			else
			{
				$datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
			}
		}
		else
		{
			$datos = "<tr><td colspan='$colspan'>Fecha inválida seleccionada.</td></tr>";
		}
			
		if( !$datos )
		{
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		}
		
		$colspan -= 4;
		$datos	.= "<tr class='success text-bold'>
						<td colspan='$colspan' class='text-right'>Totales</td>
						<td class='text-right'>$".number_format( $pagado, 2 )."</td>
						<td class='text-right'>$".number_format( $total, 2 )."</td>
						<td class='text-right'>$".number_format( $total_a, 2 )."</td>
						<td class='text-right'>$".number_format( $total_b, 2 )."</td>
					</tr>";
		
		return $datos;
	}
	
	function obtener_comision()
	{
		global $conexion, $conexion, $id_empresa;
		
		$query		= "	SELECT	ROUND( IF( emp_comision IS NULL, 0, emp_comision ), 2 ) AS comision
						FROM	san_empresas
						WHERE	emp_id_empresa = $id_empresa";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['comision'];
		}
		else
			return mysqli_error( $conexion );
		
		return ":(";
	}
	
?>