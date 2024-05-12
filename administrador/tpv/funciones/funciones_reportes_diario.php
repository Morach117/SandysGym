<?php
	function obtener_pagos_monedero( $fecha_mov, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT fn_tpv_monto_pag_monedero( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function obtener_vendidos_total_mayoreo( $fecha_mov, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT fn_tpv_monto_ventas_mayoreo( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function obtener_vendidos_total( $fecha_mov, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT fn_tpv_monto_ventas( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function obtener_devoluciones( $fecha_mov, $tipo, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		if( $tipo == 1 )//del día
			$query		= "SELECT fn_tpv_monto_devs_del_dia( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		else			//otros días
			$query		= "SELECT fn_tpv_monto_devs_otros_dias( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function obtener_sa_vendidos_total( $fecha_mov, $status, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT fn_tpv_monto_s_a_ventas( $id_empresa, $cajero, '$status', '$fecha_mov', 'D' ) AS total";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function obtener_sa_cancelaciones( $fecha_mov, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT fn_tpv_monto_s_a_cancelaciones( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function lista_cortes_del_dia( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$datos		= "";
		$condicion	= "";
		$colspan	= 7;
		$total		= 0;
		
		$query		= "	SELECT		IF( DATE_FORMAT( cor_fecha, '%d-%m-%Y' ) = DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' ), 1, 2 ) AS tipo,
									LOWER( DATE_FORMAT( cor_fecha, '%d-%m-%Y %r' ) ) AS movimiento,
									LOWER( DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' ) ) as fecha_venta,
									a.usua_nombres AS usuario,
									IF( cor_id_cajero > 0, b.usua_nombres, 'No Seleccionado' ) AS cajero,
									cor_importe AS importe,
									cor_observaciones AS notas
						FROM		san_corte
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = cor_id_usuario
						LEFT JOIN	san_usuarios b ON b.usua_id_usuario = cor_id_cajero
						WHERE		(
										'$fecha_mov' = DATE_FORMAT( cor_fecha, '%d-%m-%Y' )
										OR
										'$fecha_mov' = DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' )
									)
						AND			cor_id_empresa = $id_empresa
						ORDER BY	movimiento DESC";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$total	+= $fila['importe'];
				$class	= ( $fila['tipo'] == 2 ) ? 'info':'';
				
				$datos	.= "<tr class='$class'>
								<td>$i</td>
								<td>".$fila['movimiento']."</td>
								<td>".$fila['fecha_venta']."</td>
								<td>".$fila['usuario']."</td>
								<td>".$fila['cajero']."</td>
								<td class='text-right'>$".number_format( $fila['importe'], 2 )."</td>
								<td>".$fila['notas']."</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
			
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
	
	function total_importe_corte_del_dia( $fecha_mov, $id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$condicion	= "";
		
		if( $id_cajero )
			$condicion = "AND cor_id_cajero = $id_cajero";
		
		$query		= "	SELECT	SUM( cor_importe ) AS total
						FROM	san_corte
						WHERE	cor_id_empresa = $id_empresa
						AND		'$fecha_mov' = DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' )
								$condicion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		}
		
		return false;
	}
	
	function realizar_corte( $fecha, $id_cajero = 0 )
	{
		global $conexion, $id_usuario, $id_empresa;
		
		$exito			= array();
		$tot_calcular	= 0;
		$fecha_mysql	= fecha_formato_mysql( $fecha );
		
		$cor_tipo		= request_var( 'cor_tipo', 1 );
		$cor_importe	= request_var( 'cor_importe', 0.0 );
		
		$cor_b_1000		= request_var( 'cor_b_1000', 0 );
		$cor_b_500		= request_var( 'cor_b_500', 0 );
		$cor_b_200		= request_var( 'cor_b_200', 0 );
		$cor_b_100		= request_var( 'cor_b_100', 0 );
		$cor_b_50		= request_var( 'cor_b_50', 0 );
		$cor_b_20		= request_var( 'cor_b_20', 0 );
		$cor_m_20		= request_var( 'cor_m_20', 0 );
		$cor_m_10		= request_var( 'cor_m_10', 0 );
		$cor_m_5		= request_var( 'cor_m_5', 0 );
		$cor_m_2		= request_var( 'cor_m_2', 0 );
		$cor_m_1		= request_var( 'cor_m_1', 0 );
		$cor_c_50		= request_var( 'cor_c_50', 0 );
		
		$tot_pendiente	= request_var( 'pendiente_retirar', 0.0 );
		$total_dia		= request_var( 'total_dia', 0.0 );
		$cor_obs		= request_var( 'cor_observaciones', '' );
		
		if( $cor_tipo == 2 )
		{
			$tot_calcular += ( ( $cor_b_1000 * 1000 ) + ( $cor_b_500 * 500 ) + ( $cor_b_200 * 200 ) + ( $cor_b_100 * 100 ) + ( $cor_b_50 * 50 ) + ( $cor_b_20 * 20 ) );
			$tot_calcular += ( ( $cor_m_20 * 20 ) + ( $cor_m_10 * 10 ) + ( $cor_m_5 * 5 ) + ( $cor_m_2 * 2 ) + ( $cor_m_1 * 1 ) );
			$tot_calcular += ( $cor_c_50 * 0.5 );
		}
		
		if( $fecha_mysql )
		{
			if( $cor_importe )
			{
				if( $cor_tipo == 1 || ( $cor_tipo == 2 && ( $cor_importe == $tot_calcular ) ) )
				{
					if( $cor_importe <= $total_dia && $cor_importe <= $tot_pendiente )//falta calcular lo que falta por retirar
					{
						$datos_sql		= array
						(
							'cor_id_usuario'	=> $id_usuario,
							'cor_id_cajero'		=> $id_cajero,
							'cor_id_empresa'	=> $id_empresa,
							'cor_tipo_corte'	=> $cor_tipo,
							'cor_fecha'			=> date( 'Y-m-d H:i:s' ),
							'cor_fecha_venta'	=> $fecha_mysql,
							'cor_importe'		=> $cor_importe,
							'cor_observaciones'	=> request_var( 'cor_observaciones', '' ),
							'cor_b_1000'		=> $cor_b_1000,
							'cor_b_500'			=> $cor_b_500,
							'cor_b_200'			=> $cor_b_200,
							'cor_b_100'			=> $cor_b_100,
							'cor_b_50'			=> $cor_b_50,
							'cor_b_20'			=> $cor_b_20,
							'cor_m_20'			=> $cor_m_20,
							'cor_m_10'			=> $cor_m_10,
							'cor_m_5'			=> $cor_m_5,
							'cor_m_2'			=> $cor_m_2,
							'cor_m_1'			=> $cor_m_1,
							'cor_c_50'			=> $cor_c_50
						);
						
						$query		= construir_insert( 'san_corte', $datos_sql );
						$resultado	= mysqli_query( $conexion, $query );
						$id_corte	= mysqli_insert_id( $conexion );
						
						if( $resultado )
						{
							$exito['num'] = 1;
							$exito['msj'] = "Corte realizado exitosamente por $".number_format( $cor_importe, 2 );
							$exito['IDC'] = $id_corte;
							$exito['IDU'] = $id_usuario;
							$exito['CTC'] = $cor_tipo;
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "Ocurrió un error al procesar la petición y no se realizó el corte. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "El importe del corte no puede ser mayor al <strong>Total del día en caja</strong> ni mayor a lo <strong>Pendiente de retirar</strong>.";
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "El Importe del Corte no coincide con la validación de Billetes y Monedas seleccionados.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se escribió la cantidad por la que se desea hacer el corte.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Fecha inválida seleccionada para el corte.";
		}
		
		return $exito;
	}
	
	function total_gastos( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT 	SUM( gas_total ) AS total
						FROM 	san_gastos
						WHERE	gas_id_empresa = $id_empresa
						AND		DATE_FORMAT( gas_fecha_fnota, '%d-%m-%Y' ) = '$fecha_mov'";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function total_venta_costos( $fecha_mov, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT fn_tpv_monto_ventas_costos( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function total_venta_utilidad( $fecha_mov, $cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT fn_tpv_monto_ventas_utilidad( $id_empresa, $cajero, '$fecha_mov', 'D' ) AS total";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		
		return false;
	}
	
	function lista_ventas_diaria( $fecha, $cajero = 0 )
	{
		global $conexion, $id_empresa, $id_consorcio;
		
		$colspan		= 11;
		$tot_cantidad	= 0;
		$tot_costo_imp	= 0;
		$tot_descuento	= 0;
		$tot_monedero	= 0;
		$tot_importe	= 0;
		$tot_utilidad	= 0;
		$datos			= "";
		$class			= "";
		$condicion		= "";
		
		if( $cajero )
			$condicion = "AND IF( ven_status = 'V', ven_id_usuario, venh_id_usuario ) = $cajero";
		
		$query		= "	SELECT		ven_folio AS folio,
									LOWER( DATE_FORMAT( ven_fecha, '%r' ) ) AS hora,
									art_codigo AS codigo,
									art_descripcion AS descripcion,
									ven_total AS total,
									vende_cantidad AS cantidad,
									vende_costo AS costo,
									ROUND( vende_cantidad * vende_costo, 2 ) AS costo_i,
									ROUND( vende_cantidad * vende_descuento, 2 ) AS descuento,
									ROUND( vende_cantidad * vende_monedero, 2 ) AS monedero,
									vende_precio AS precio,
									ROUND( vende_cantidad * vende_precio, 2 ) AS importe,
									ven_status AS status,
									vende_status AS vende_status
						FROM		san_venta
						LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						INNER JOIN	san_venta_detalle ON vende_id_venta = ven_id_venta
						INNER JOIN	san_articulos ON art_id_articulo = vende_id_articulo
						WHERE		'$fecha' = DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%d-%m-%Y' )
						AND			ven_id_empresa = $id_empresa
						AND			art_id_consorcio = $id_consorcio
									$condicion
						ORDER BY	folio,
									descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['status'] == 'C' )
					$class = "danger";
				elseif( $fila['status'] == 'P' && $fila['vende_status'] == 'D' )
					$class = "warning";
				else
					$class= "";
				
				if( !$fila['vende_status'] )
				{
					$tot_cantidad	+= $fila['cantidad'];
					$tot_costo_imp	+= $fila['costo_i'];
					$tot_descuento	+= $fila['descuento'];
					$tot_monedero	+= $fila['monedero'];
					$tot_importe	+= $fila['importe'];
					$tot_utilidad	+= ( $fila['importe'] - $fila['costo_i'] );
				}
				
				$datos	.= "<tr class='$class'>
								<td>$fila[folio]</td>
								<td width=100px>$fila[codigo]</td>
								<td>$fila[descripcion]</td>
								<td class='text-right'>".number_format( $fila['cantidad'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['costo'], 2 )."</td>
								<td class='danger text-right'>$".number_format( $fila['costo_i'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['descuento'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['monedero'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['precio'], 2 )."</td>
								<td class='info text-right'>$".number_format( $fila['importe'], 2 )."</td>
								<td class='success text-right'>$".( number_format( $fila['importe'] - $fila['costo_i'], 2 ) )."</td>
							</tr>";
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		// $colspan -= 8;
		// $datos	.= "<tr class='success text-bold'>
						// <td colspan='$colspan' class='text-right'>(No incluye artículos de las devoluciones de ventas de otros días)</td>
						// <td class='text-right'>".number_format( $tot_cantidad, 2 )."</td>
						// <td class='text-right'>&nbsp;</td>
						// <td class='text-right'>$".number_format( $tot_costo_imp, 2 )."</td>
						// <td class='text-right'>$".number_format( $tot_descuento, 2 )."</td>
						// <td class='text-right'>$".number_format( $tot_monedero, 2 )."</td>
						// <td class='text-right'>&nbsp;</td>
						// <td class='text-right'>$".number_format( $tot_importe, 2 )."</td>
						// <td class='text-right'>$".number_format( $tot_utilidad, 2 )."</td>
					// </tr>";
		
		return $datos;
	}
	
?>