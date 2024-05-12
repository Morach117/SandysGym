<?php
	function lista_ventas_del_mes( $año )
	{
		Global $conexion, $id_empresa;
		
		$datos			= "";
		$colspan		= 9;
		$contador		= 1;
		$d_mensual		= lista_detalle_mensualidades( $año );
		$d_horas		= lista_detalle_horas_visitas( $año, 'HORA' );
		$d_visitas		= lista_detalle_horas_visitas( $año, 'VISITA' );
		$d_ventas		= lista_detalle_venta_articulos( $año );
		$d_prepagos		= lista_detalle_prepagos( $año );
		$d_cortes		= lista_cortes( $año );

		$fila_mensual	= 0;
		$fila_horas		= 0;
		$fila_visitas	= 0;
		$fila_articulos	= 0;
		$fila_prepagos	= 0;
		$fila_cortes	= 0;
		$fila_total		= 0;
		
		$tot_mensual	= 0;
		$tot_horas		= 0;
		$tot_visitas	= 0;
		$tot_articulos	= 0;
		$tot_prepagos	= 0;
		$tot_cortes		= 0;
		$tot_total		= 0;

		if( $d_mensual || $d_horas || $d_visitas || $d_ventas || $d_prepagos || $d_cortes )
		{
			for( $i = 1; $i <= 12; $i++ )
			{
				foreach( $d_mensual as $fila )
				{
					if( (int)$fila['mes'] == $i )
					{
						$fila_mensual = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_horas as $fila )
				{
					if( (int)$fila['mes'] == $i )
					{
						$fila_horas = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_visitas as $fila )
				{
					if( (int)$fila['mes'] == $i )
					{
						$fila_visitas = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_ventas as $fila )
				{
					if( (int)$fila['mes'] == $i )
					{
						$fila_articulos = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_prepagos as $fila )
				{
					if( (int)$fila['mes'] == $i )
					{
						$fila_prepagos = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_cortes as $fila )
				{
					if( (int)$fila['mes'] == $i )
					{
						$fila_cortes = $fila['importe'];
						break;
					}
				}
				
				$fecha			= "$i-$año";
				$fila_total		= ( $fila_mensual + $fila_horas + $fila_visitas + $fila_articulos + $fila_prepagos );

				if( $fila_total )
				{
					$datos	.= "<tr>
									<td>$contador</td>
									<td>".fecha_a_mes( $fecha )."</td>
									<td class='text-right'>$".number_format( $fila_mensual, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_horas, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_visitas, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_articulos, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_prepagos, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_cortes, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_total, 2 )."</td>
								</tr>";
					$contador++;
					
					$tot_mensual	+= $fila_mensual;
					$tot_horas		+= $fila_horas;
					$tot_visitas	+= $fila_visitas;
					$tot_articulos	+= $fila_articulos;
					$tot_prepagos	+= $fila_prepagos;
					$tot_cortes		+= $fila_cortes;
					$tot_total		+= $fila_total;
					
					$fila_mensual	= 0;
					$fila_horas		= 0;
					$fila_visitas	= 0;
					$fila_articulos	= 0;
					$fila_prepagos	= 0;
					$fila_cortes	= 0;
				}
			}

			$colspan -= 7;
			$datos	.= "<tr class='success text-bold'>
							<td class='text-right' colspan='$colspan'>Totales</td>
							<td class='text-right'>$".number_format( $tot_mensual, 2 )."</td>
							<td class='text-right'>$".number_format( $tot_horas, 2 )."</td>
							<td class='text-right'>$".number_format( $tot_visitas, 2 )."</td>
							<td class='text-right'>$".number_format( $tot_articulos, 2 )."</td>
							<td class='text-right'>$".number_format( $tot_prepagos, 2 )."</td>
							<td class='text-right'>$".number_format( $tot_cortes, 2 )."</td>
							<td class='text-right'>$".number_format( $tot_total, 2 )."</td>
						</tr>";
		}
		else
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
	function lista_detalle_mensualidades( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$datos		= array();
		
		$query		= "	SELECT		DATE_FORMAT( pag_fecha_pago, '%m' ) AS mes,
									SUM( pag_importe ) AS importe
						FROM		san_pagos
						WHERE		DATE_FORMAT( pag_fecha_pago, '%Y' ) = '$fecha_mov'
						AND			pag_status = 'A'
						AND			pag_id_empresa = $id_empresa
						GROUP BY	DATE_FORMAT( pag_fecha_pago, '%m-%Y' )";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		
		return $datos;
	}
	
	function lista_detalle_horas_visitas( $fecha_mov, $tipo )
	{
		global $conexion, $id_empresa, $id_consorcio;
		
		$datos		= array();
		
		$query		= "	SELECT		DATE_FORMAT( hor_fecha, '%m' ) AS mes,
									SUM( hor_importe ) AS importe
						FROM		san_horas
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						AND			ser_id_consorcio = $id_consorcio
						WHERE		DATE_FORMAT( hor_fecha, '%Y' ) = '$fecha_mov'
						AND			hor_status = 'A'
						AND			hor_id_empresa = $id_empresa
						AND			ser_clave = '$tipo'
						GROUP BY	DATE_FORMAT( hor_fecha, '%m-%Y' )";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		
		return $datos;
	}
	
	function lista_detalle_venta_articulos( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$datos		= array();
		
		$query		= "	SELECT		DATE_FORMAT( ven_fecha, '%m' ) AS mes,
									SUM( ven_total_efectivo + ven_total_tarjeta + ven_comision ) AS importe
						FROM		san_venta
						WHERE		DATE_FORMAT( ven_fecha, '%Y' ) = '$fecha_mov'
						AND			ven_id_empresa = $id_empresa
						AND			ven_status = 'V'
						GROUP BY	DATE_FORMAT( ven_fecha, '%m-%Y' )";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		
		return $datos;
	}
	
	function lista_detalle_prepagos( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$datos		= array();
		
		$query		= "	SELECT		DATE_FORMAT( pred_fecha, '%m' ) AS mes,
									SUM( pred_importe ) AS importe
						FROM		san_prepago
						INNER JOIN	san_prepago_detalle ON pred_id_prepago = prep_id_prepago
						WHERE		DATE_FORMAT( pred_fecha, '%Y' ) = '$fecha_mov'
						AND			prep_id_empresa = $id_empresa
						AND			pred_movimiento = 'S'
						GROUP BY	DATE_FORMAT( pred_fecha, '%m-%Y' )";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		
		return $datos;
	}
	
	function lista_cortes( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$datos		= array();
		
		$query		= "	SELECT 		DATE_FORMAT( cor_fecha_venta, '%m' ) AS mes,
									SUM( cor_importe ) AS importe
						FROM 		san_corte
						WHERE		DATE_FORMAT( cor_fecha_venta, '%Y' ) = '$fecha_mov'
						AND			cor_id_empresa = $id_empresa
						GROUP BY	DATE_FORMAT( cor_fecha_venta, '%m-%Y' )";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		
		return $datos;
	}
	
?>