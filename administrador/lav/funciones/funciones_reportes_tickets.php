<?php
	function eliminar_por_opcion_z()
	{
		global $conexion;
		
		$ids_venta	= isset( $_POST['id'] ) ? $_POST['id']:false;
		
		if( $ids_venta )
		{
			$v_ids_venta	= implode( ',', $ids_venta );
			$commit			= false;
			
			mysqli_autocommit( $conexion, false );
			
			// delete from san_venta_servicio where vense_id_venta = 75527;
			// delete from san_venta_historico where venh_id_venta = 75527;
			// delete from san_venta where ven_id_venta = 75527;
			
			$query		= "DELETE FROM san_venta_servicio WHERE vense_id_venta IN ( $v_ids_venta )";
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				$query		= "DELETE FROM san_venta_historico WHERE venh_id_venta IN ( $v_ids_venta )";
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					$query		= "DELETE FROM san_venta WHERE ven_id_venta IN ( $v_ids_venta )";
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
						$commit = true;
				}
			}
		}
		
		if( $commit )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
	}
	
	function obtene_info_bd( $busqueda, $año, $mes_evaluar, $nomnbre_cliente = '' )
	{
		global $conexion, $id_empresa;
		
		$datos		= array();
		$condicion	= "";
		
		switch( $busqueda )
		{
			case 'credito':	$condicion = "AND ven_total_credito > 0 AND ven_status NOT IN ( 'C', 'I', 'Z' )";	break;
			case 'R':		$condicion = "AND ven_status = 'R' AND venh_n_m = 1";		break;
			
			case 'LS':		$condicion = "AND ven_status IN ( 'L', 'S' )";		break;
			
			case 'L':		$condicion = "AND ven_status = 'L'";			break;
			case 'E':		$condicion = "AND ven_status = 'E'";			break;
			case 'C':		$condicion = "AND ven_status = 'C'";			break;
			case 'I':		$condicion = "AND ven_status = 'I'";			break;
			
			case 'S':		$condicion = "AND ven_status = 'S'";			break;
			case 'T':		$condicion = "AND ven_status = 'T'";			break;
			case 'Z':		$condicion = "AND ven_status = 'Z'";			break;
		}
		
		if( $mes_evaluar )
			$condicion .= " AND '$mes_evaluar' = DATE_FORMAT( venh_fecha, '%Y-%m' ) ";
		
		if( $nomnbre_cliente )
		{
			$condicion .= " AND (
									UPPER( soc_apepat ) LIKE UPPER( '%$nomnbre_cliente%' )
									OR
									UPPER( soc_apemat ) LIKE UPPER( '%$nomnbre_cliente%' )
									OR
									UPPER( soc_nombres ) LIKE UPPER( '%$nomnbre_cliente%' )
									OR
									UPPER( ven_folio ) LIKE UPPER( '%$nomnbre_cliente%' )
								)";
		}
		
		$query		= "	SELECT		ven_id_venta AS id_venta,
									ven_folio AS folio,
									ven_anio,
									CONCAT( 'F', LPAD( ven_folio, 7, '0' ) ) AS folio_desc,
									ven_status AS status,
									CASE ven_status
										WHEN 'C' THEN 'Cancelado'
										WHEN 'R' THEN 'Recepcionado'
										WHEN 'L' THEN 'Lavado'
										WHEN 'E' THEN 'Entregado'
										WHEN 'I' THEN 'Inactivo'
										
										WHEN 'S' THEN 'Para planchar'
										WHEN 'T' THEN 'Planchado y entregado'
										WHEN 'Z' THEN 'Revisado'
										
										ELSE 'Otro'
									END AS status_desc,
									CASE ven_status
										WHEN 'C' THEN 'danger text-bold'
										WHEN 'I' THEN 'danger'
										WHEN 'R' THEN 'info'
										WHEN 'L' THEN 'warning'
										WHEN 'E' THEN 'success'
										
										WHEN 'S' THEN 'info'
										WHEN 'T' THEN 'success'
										WHEN 'Z' THEN 'warning'
									END AS class,
									LOWER( DATE_FORMAT( venh_fecha, '%d-%m-%Y %r' ) ) AS primer_m,
									ROUND( ven_total_efectivo, 2 ) AS efectivo,
									ROUND( ven_total_tarjeta + ven_comision, 2 ) AS tar_com,
									ROUND( ven_total_credito, 2 ) AS credito,
									ROUND( ven_total, 2 ) AS total,
									CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS cliente,
									CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) AS usuario
						FROM		san_venta
						INNER JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						LEFT JOIN	san_socios ON soc_id_socio = ven_id_socio
						LEFT JOIN	san_usuarios ON usua_id_usuario = ven_id_usuario
						WHERE		ven_id_empresa = $id_empresa
						AND 		'$año' = DATE_FORMAT( venh_fecha, '%Y' )
									$condicion
						GROUP BY	ven_id_venta,
									ven_folio,
									ven_anio
						ORDER BY	ven_folio DESC,
									ven_id_venta";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		}
		else
			echo mysqli_error( $conexion );
			
		return $datos;
	}
	
	function obtener_tickets( $busqueda, $año, $mes_evaluar, $nomnbre_cliente = '', $p_z = '' )
	{
		global $conexion, $id_empresa;
		$datos			= "";
		$condicion		= "";
		$colspan		= 10;
		$tot_credito	= 0;
		
		$informacion	= obtene_info_bd( $busqueda, $año, $mes_evaluar, $nomnbre_cliente );
		
		if( $informacion )
		{
			$i = 1;
			foreach( $informacion as $fila )
			{
				if( $p_z == 'E' )
					$opcion_z = "<input type='checkbox' name='id[]' value='$fila[id_venta]' />";
				else
					$opcion_z = "";
				
				( $fila['status'] != 'C' && $fila['status'] != 'I' ) ? $tot_credito += $fila['credito']:0;
				
				$datos	.= "<tr class='$fila[class]'>
								<td>$i</td>
								<td>
									<div class='btn-group pointer'>
										<a class='' dropdown-toggle' data-toggle='dropdown'>
											<span class='glyphicon glyphicon-chevron-down'></span>
										</a>
										<ul class='dropdown-menu'>
											<li><a onclick='mostrar_detalle_ticket( $fila[folio], $fila[id_venta] )'><span class='glyphicon glyphicon-list'></span> Detalles</a></li>
											<li><a onclick='cambiar_status( $fila[folio], $fila[id_venta] )'><span class='glyphicon glyphicon-refresh'></span> Devolución</a></li>
										</ul>
									</div>
								</td>
								<td>$opcion_z ".$fila['folio_desc']."</td>
								<td>".$fila['primer_m']."</td>
								<td class='text-right'>$".$fila['efectivo']."</td>
								<td class='text-right'>$".$fila['tar_com']."</td>
								<td class='text-right'>$".$fila['credito']."</td>
								<td class='text-right'>$".$fila['total']."</td>
								<td>".$fila['cliente']."</td>
								<td>".$fila['usuario']."</td>
							</tr>";
							
				$i++;
			}
		}
		
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$colspan -= 4;
		$datos	.= "<tr class='success text-bold'>
						<td colspan='$colspan' class='text-right'>Por cobrar</td>
						<td class='text-right'>$".number_format( $tot_credito, 2 )."</td>
						<td colspan='3'>&nbsp;</td>
					</tr>";
		
		return $datos;
	}
	
?>