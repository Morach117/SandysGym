<?php
	function obtener_importe_venta_efectivo( $fecha = '', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		$exito		= array();
		
		if( $fecha )
			$condicion = "AND '$fecha' = DATE_FORMAT( venh_fecha, '%d-%m-%Y' )";
		else
			$condicion = "AND CURDATE() = DATE_FORMAT( venh_fecha, '%Y-%m-%d' )";
		
		if( $p_id_cajero )
			$condicion .= " AND venh_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT		SUM( venh_total_efectivo ) AS venta_efectivo
						FROM		san_venta_historico
						INNER JOIN	san_venta ON ven_id_venta = venh_id_venta
						WHERE		ven_status NOT IN ( 'C', 'I' )
									$condicion
						AND			venh_id_empresa = $id_empresa";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = $fila['venta_efectivo'];
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe por la Venta en Efectivo.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el importe por la Venta en Efectivo. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function realizar_corte( $fecha_venta )
	{
		global $conexion, $id_usuario, $id_empresa;
		
		$mysql_fecha	= fecha_formato_mysql( $fecha_venta );
		$cor_importe	= request_var( 'cor_importe', 0.0 );
		$v_id_cajero	= request_var( 'cajero', 0 );
		$exito			= array();
		
		if( $mysql_fecha )
		{
			if( $cor_importe )
			{
				$datos_sql		= array
				(
					'cor_id_usuario'	=> $id_usuario,
					'cor_id_empresa'	=> $id_empresa,
					'cor_fecha'			=> date( 'Y-m-d H:i:s' ),
					'cor_fecha_venta'	=> $mysql_fecha,
					'cor_importe'		=> $cor_importe,
					'cor_id_cajero'		=> $v_id_cajero,
					'cor_observaciones'	=> request_var( 'cor_observaciones', '' )
				);
				
				$query		= construir_insert( 'san_corte', $datos_sql );
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					$exito['num'] = 1;
					$exito['msj'] = "Corte realizado exitosamente.";
				}
				else
				{
					$exito['num'] = 2;
					$exito['msj'] = "No se puedo precesar la petición. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se puede retirar más de lo que se ha indicado que se tiene.";
			}
		}
		else
		{
			$exito['num'] = 4;
			$exito['msj'] = "Fecha inválida seleccionada.";
		}
		
		return $exito;
	}
	
	function lista_cortes_del_dia( $fecha_movimiento )
	{
		global $conexion, $id_empresa;
		
		list( $d, $m, $Y )	= explode( '-', $fecha_movimiento );
		$datos		= "";
		$colspan	= 10;
		$total		= 0;
		$contador	= 1;
		$fecha		= request_var( 'fecha', date( 'd-m-Y' ) );
		$v_id_cajero= request_var( 'cajero', 0 );
		
		$query		= "	SELECT		LOWER( DATE_FORMAT( cor_fecha, '%d-%m-%Y %r' ) ) AS movimiento,
									LOWER( DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' ) ) as fecha_venta,
									a.usua_nombres AS usuario,
									b.usua_nombres AS cajero,
									cor_id_corte AS id_corte,
									cor_importe AS importe,
									cor_caja AS caja,
									CASE cor_tipo_corte
										WHEN 3 THEN 'APERTURA'
										WHEN 4 THEN 'CIERRE'
										WHEN 5 THEN 'RETIRO'
										ELSE 'CORTE'
									END as tipo_mov,
									cor_observaciones AS notas
						FROM		san_corte
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = cor_id_usuario
						LEFT JOIN	san_usuarios b ON b.usua_id_usuario = cor_id_cajero
						WHERE		(
										'$Y-$m-$d' = DATE_FORMAT( cor_fecha, '%Y-%m-%d' )
										OR
										'$Y-$m-$d' = DATE_FORMAT( cor_fecha_venta, '%Y-%m-%d' )
									)
						AND			cor_id_empresa = $id_empresa
						ORDER BY	movimiento DESC";
		
		if( checkdate( $m, $d, $Y ) )
		{
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				while( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$total += $fila['importe'];
					
					if( substr( $fila['movimiento'], 0, 10 ) != $fila['fecha_venta'] )
						$class = "warning";
					else
						$class = "";
					
					$datos	.= "<tr class='$class'>
									<td>$contador</td>
									<td>
										<div class='btn-group'>
											<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
												<span class='glyphicon glyphicon-chevron-down'></span>
											</a>
											<ul class='dropdown-menu'>
												<li><a href='.?s=reportes&i=corte_diario&idc=$fila[id_corte]&accion=e&fecha=$fecha&cajero=$v_id_cajero'><span class='glyphicon glyphicon-remove-sign'></span> Eliminar</a></li>
											</ul>
										</div>
									</td>
									<td>".$fila['movimiento']."</td>
									<td>".$fila['fecha_venta']."</td>
									<td>".$fila['usuario']."</td>
									<td>".$fila['cajero']."</td>
									<td>".$fila['tipo_mov']."</td>
									<td class='text-right'>$".number_format( $fila['caja'], 2 )."</td>
									<td class='text-right'>$".number_format( $fila['importe'], 2 )."</td>
									<td>".$fila['notas']."</td>
								</tr>";
					
					$contador++;
				}
			}
			else
				$datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
		}
		else
			$datos = "<tr><td colspan='$colspan'>Fecha inválida seleccionada.</td></tr>";
			
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$colspan -= 2;
		$datos	.= "<tr class='success text-bold'>
								<td colspan='$colspan' class='text-right'>Total en retiros del día</td>
								<td class='text-right'>$".number_format( $total, 2 )."</td>
								<td>&nbsp;</td>
							</tr>";
		
		return $datos;
	}
	
	function lista_ventas_del_dia( $fecha_movimiento, $efectivo = true, $p_id_cajero )
	{
		global $conexion, $id_empresa;
		
		list( $d, $m, $Y )	= explode( '-', $fecha_movimiento );
		$datos		= "";
		$condicion	= "";
		$colspan	= 8;
		$tot_efec	= 0;
		$tot_cred	= 0;
		$tot_tar_com= 0; // tarjeta + comision
		$tot_total	= 0;
		
		if( $efectivo )
			$condicion = "AND venh_total_efectivo > 0";
		else
			$condicion = "AND venh_status IN ( 'R', 'S' )";
		
		if( $p_id_cajero )
			$condicion .= " AND venh_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT		CONCAT( 'F', LPAD( venh_folio, 7, '0' ) ) AS folio_desc,
									venh_folio AS folio,
									CASE venh_status 
										WHEN 'S' THEN 'Para planchar'
										WHEN 'T' THEN 'Planchado y entregado'
										
										WHEN 'R' THEN 'Recepcionado'
										WHEN 'L' THEN 'Lavado'
										WHEN 'E' THEN 'Entregado'
										WHEN 'P' THEN 'Pagado'
									END AS status,
									ROUND( venh_total_efectivo, 2 ) AS efectivo,
									ROUND( venh_total_tarjeta + venh_comision, 2 ) AS tar_com,
									ROUND( venh_total_credito, 2 ) AS credito,
									ROUND( venh_total, 2 ) AS total,
									CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) AS usuario
						FROM		san_venta_historico
						INNER JOIN	san_venta on ven_id_venta = venh_id_venta
						AND			ven_status NOT IN ( 'C', 'I' )
						INNER JOIN	san_usuarios ON usua_id_usuario = venh_id_usuario
						WHERE		venh_id_empresa = $id_empresa
									$condicion
						AND			'$fecha_movimiento' = DATE_FORMAT( venh_fecha, '%d-%m-%Y' )
						ORDER BY	folio";
		
		if( checkdate( $m, $d, $Y ) )
		{
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				$i = 1;
				while( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$tot_efec	+= $fila['efectivo'];
					$tot_tar_com+= $fila['tar_com'];
					$tot_cred 	+= $fila['credito'];
					$tot_total	+= $fila['total'];
					
					$datos	.= "<tr>
									<td>$i</td>
									<td>".$fila['folio_desc']."</td>
									<td>".$fila['status']."</td>
									<td class='text-right info'>$".$fila['efectivo']."</td>
									<td class='text-right info'>$".$fila['tar_com']."</td>
									<td class='text-right'>$".$fila['credito']."</td>
									<td class='text-right'>$".$fila['total']."</td>
									<td>".$fila['usuario']."</td>
								</tr>";
					$i++;
				}
			}
			else
				$datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
		}
		else
			$datos = "<tr><td colspan='$colspan'>Fecha inválida seleccionada.</td></tr>";
			
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$tot_cred	= ( $efectivo ) ? '-/-':'$'.number_format( $tot_cred, 2 );
		$tot_total	= ( $efectivo ) ? '-/-':'$'.number_format( $tot_total, 2 );
		
		$colspan -= 5;
		$datos	.= "<tr class='success text-bold'>
						<td class='text-right' colspan='$colspan'>Totales</td>
						<td class='text-right'>$".number_format( $tot_efec, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_tar_com, 2 )."</td>
						<td class='text-right'>$tot_cred</td>
						<td class='text-right'>$tot_total</td>
						<td>&nbsp;</td>
					</tr>";
		
		return $datos;
	}
	
	function total_importe_corte_del_dia( $fecha_movimiento, $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		list( $d, $m, $Y )	= explode( '-', $fecha_movimiento );
		
		$condicion	= "";
		
		if( $p_id_cajero )
			$condicion = "AND cor_id_cajero = $p_id_cajero";
		
		$query		= "	SELECT	SUM( cor_importe ) AS total_dia
						FROM	san_corte
						WHERE	'$Y-$m-$d' = DATE_FORMAT( cor_fecha_venta, '%Y-%m-%d' )
								$condicion
						AND		cor_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total_dia'];
		
		return 0;
	}
	
	function validar_corte_caja()
	{
		$validar	= array
		(
			'cor_importe'		=> array( 'tipo' => 'N',	'max' => 8,		'req' => 'S',	'for' => '',	'txt' => 'Importe'),
			'cor_observaciones'	=> array( 'tipo' => 'T',	'max' => 100,	'req' => 'N',	'for' => '',	'txt' => 'Observaciones')
		);
		
		$exito		= validar_php( $validar );
		
		return $exito;
	}
	
	function eliminar_corte()
	{
		global $conexion, $id_empresa;
		
		$v_fecha	= request_var( 'fecha', '' );
		$v_id_cajero= request_var( 'cajero', 0 );
		$v_id_corte	= request_var( 'idc', 0 );
		
		if( $v_id_corte )
		{
			$query		= "DELETE FROM san_corte WHERE cor_id_corte = $v_id_corte AND cor_id_empresa = $id_empresa";
			$resultado	= mysqli_query( $conexion, $query );
		}
		
		header( "location: .?s=reportes&i=corte_diario&fecha=$v_fecha&cajero=$v_id_cajero" );
		exit;
	}
	
?>