<?php
	function obtener_importe_mensualidades( $mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$exito['total']		= 0;
		$exito['efectivo']	= 0;
		$exito['tar_com']	= 0;
		$exito['tarjeta']	= 0;
		$exito['comision']	= 0;
		
		if( $tipo_corte == 'A' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pag_fecha_pago, '%Y' )";
		elseif( $tipo_corte == 'M' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pag_fecha_pago, '%m-%Y' )";
		else
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y' )";
		
		if( $p_id_cajero )
			$condicion .= " AND pag_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT	ROUND( total, 2 ) AS total,
								ROUND( efectivo, 2 ) AS efectivo,
								ROUND( tarjeta + comision, 2 ) AS tar_com,
								ROUND( tarjeta, 2 ) AS tarjeta,
								ROUND( comision, 2 ) AS comision
						FROM
						(
							SELECT 		SUM( pag_importe ) AS total,
										SUM( pag_efectivo ) AS efectivo,
										SUM( pag_tarjeta ) AS tarjeta,
										SUM( pag_comision ) AS comision
							FROM 		san_pagos
							INNER JOIN	san_servicios ON ser_id_servicio = pag_id_servicio
							WHERE 		pag_status = 'A'
										$condicion
							AND			pag_id_empresa = $id_empresa
						) a";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total']		= $fila['total'];
				$exito['efectivo']	= $fila['efectivo'];
				$exito['tar_com']	= $fila['tar_com'];
				$exito['tarjeta']	= $fila['tarjeta'];
				$exito['comision']	= $fila['comision'];
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe de las mensualidades.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el importe de las mensualidades. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_importe_por_horas( $desc = 'HORA', $mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$condicion	= "";
		$exito		= array();
		$exito['total']		= 0;
		$exito['efectivo']	= 0;
		$exito['tar_com']	= 0;
		$exito['tarjeta']	= 0;
		$exito['comision']	= 0;
		
		if( $desc == 'HORA' )
			$condicion .= " AND ser_clave = 'HORA' ";
		else
			$condicion .= " AND ser_clave = 'VISITA' ";
		
		if( $tipo_corte == 'A' )
			$condicion .= " AND '$mes_ganancia' = DATE_FORMAT( hor_fecha, '%Y' ) ";
		elseif( $tipo_corte == 'M' )
			$condicion .= " AND '$mes_ganancia' = DATE_FORMAT( hor_fecha, '%m-%Y' ) ";
		else
			$condicion .= " AND '$mes_ganancia' = DATE_FORMAT( hor_fecha, '%d-%m-%Y' ) ";
		
		if( $p_id_cajero )
			$condicion .= " AND hor_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT		ROUND( SUM( hor_importe ), 2 ) AS total,
									ROUND( SUM( hor_efectivo ), 2 ) AS efectivo,
									ROUND( SUM( hor_tarjeta + hor_comision ), 2 ) AS tar_com,
									ROUND( SUM( hor_tarjeta ), 2 ) AS tarjeta,
									ROUND( SUM( hor_comision ), 2 ) AS comision
						FROM		san_horas
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						AND			ser_tipo = 'PARCIAL'
						AND			ser_descripcion = '$desc'
						WHERE		hor_status = 'A'
									$condicion
						AND			hor_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total']		= $fila['total'];
				$exito['efectivo']	= $fila['efectivo'];
				$exito['tar_com']	= $fila['tar_com'];
				$exito['tarjeta']	= $fila['tarjeta'];
				$exito['comision']	= $fila['comision'];
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe por horas.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el importe por horas. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_importe_prepago( $mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$exito['total']		= 0;
		$exito['efectivo']	= 0;
		$exito['tar_com']	= 0;
		$exito['tarjeta']	= 0;
		$exito['comision']	= 0;
		
		if( $tipo_corte == 'A' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pred_fecha, '%Y' )";
		elseif( $tipo_corte == 'M' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pred_fecha, '%m-%Y' )";
		else
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pred_fecha, '%d-%m-%Y' )";
		
		if( $p_id_cajero )
			$condicion .= " AND pred_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT ROUND( IF( total > 0, total, 0 ) ) AS total
						FROM
						(
							SELECT 		SUM( IF( pred_movimiento = 'S', pred_importe, 0 ) ) - SUM( IF( pred_movimiento = 'R', pred_importe, 0 ) ) AS total
							FROM		san_prepago_detalle
							INNER JOIN	san_prepago ON prep_id_prepago = pred_id_prepago
							WHERE		prep_id_empresa = $id_empresa
										$condicion
						) a";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total']		= $fila['total']; // para no generar error
				$exito['efectivo']	= $fila['total']; // para no generar error
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe por prepago.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el importe por prepago. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_importe_venta_efectivo( $mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$exito['total']		= 0;
		$exito['efectivo']	= 0;
		$exito['tar_com']	= 0;
		$exito['tarjeta']	= 0;
		$exito['comision']	= 0;
		
		if( $tipo_corte == 'A' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( ven_fecha, '%Y' )";
		elseif( $tipo_corte == 'M' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( ven_fecha, '%m-%Y' )";
		else
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( ven_fecha, '%d-%m-%Y' )";
		
		if( $p_id_cajero )
			$condicion .= " AND ven_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT	ROUND( SUM( ven_total ), 2 ) AS total,
								ROUND( SUM( ven_total_efectivo ), 2 ) AS efectivo,
								ROUND( SUM( ven_total_tarjeta + ven_comision ), 2 ) AS tar_com,
								ROUND( SUM( ven_total_tarjeta ), 2 ) AS tarjeta,
								ROUND( SUM( ven_comision ), 2 ) AS comision
						FROM	san_venta
						WHERE	ven_status = 'V'
						AND		ven_id_empresa = $id_empresa
								$condicion";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total']		= $fila['total'];
				$exito['efectivo']	= $fila['efectivo'];
				$exito['tar_com']	= $fila['tar_com'];
				$exito['tarjeta']	= $fila['tarjeta'];
				$exito['comision']	= $fila['comision'];
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
	
	function obtener_gastos( $mes_ganancia = '', $tipo_corte = 'D' )
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$condicion	= '';
		
		if( $tipo_corte == 'A' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( gas_fecha_fnota, '%Y' )";
		elseif( $tipo_corte == 'M' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( gas_fecha_fnota, '%m-%Y' )";
		
		$query		= "	SELECT	SUM( gas_importe ) AS importe,
								SUM( gas_iva ) AS iva,
								SUM( gas_descuento ) AS descuento,
								SUM( gas_total ) AS total
						FROM	san_gastos
						WHERE	gas_id_empresa = $id_empresa
								$condicion";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = $fila;
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el total de gastos.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener los gastos. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
?>