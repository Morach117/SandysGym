<?php
	function total_venta( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT		SUM( vende_cantidad * vende_precio ) AS total
						FROM		san_venta
						LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						INNER JOIN	san_venta_detalle ON vende_id_venta = ven_id_venta
						WHERE		'$fecha_mov' = DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%m-%Y' )
						AND			ven_id_empresa = $id_empresa
						AND			ven_status IN ( 'V', 'P' )
						AND			vende_status IS NULL";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		}
		else
			echo "Error L20: ".mysqli_error( $conexion );
		
		return false;
	}
	
	function total_gastos( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT 	SUM( gas_total ) AS total
						FROM 	san_gastos
						WHERE	gas_id_empresa = $id_empresa
						AND		DATE_FORMAT( gas_fecha_fnota, '%m-%Y' ) = '$fecha_mov'";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		}
		else
			echo "Error: ".mysqli_error( $conexion );
		
		return false;
	}
	
	function total_venta_costos( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT		SUM( vende_cantidad * vende_costo ) AS total
						FROM		san_venta_detalle
						INNER JOIN	san_venta ON ven_id_venta = vende_id_venta
						LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						WHERE		'$fecha_mov' = DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%m-%Y' )
						AND			ven_id_empresa = $id_empresa
						AND			ven_status IN ( 'V', 'P' )
						AND			vende_status IS NULL";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total'];
		}
		else
			echo "Error: ".mysqli_error( $conexion );
		
		return false;
	}
	
	function deglose_por_dia( $fecha )
	{
		global $conexion, $id_empresa;
		$datos			= "";
		$colspan		= 12;
		$tot_may		= 0;
		$tot_sa			= 0;
		$tot_descuentos	= 0;
		$tot_devs		= 0;
		$tot_caja		= 0;
		$tot_ventas		= 0;
		$tot_gastos		= 0;
		$tot_costos		= 0;
		$tot_utilidadx	= 0;
		$tot_utilidadn	= 0;
		
		$query		= "	SELECT	a.fecha AS fecha,
								a.gastos AS gastos,
								a.costos AS costos,
								a.descuentos AS descuentos,
								a.sa,
								a.devs,
								fn_tpv_monto_ventas_mayoreo( a.id_empresa, 0, a.fecha, 'D' ) AS mayoreo,
								( fn_tpv_monto_ventas( a.id_empresa, 0, a.fecha, 'D' ) + a.sa ) - a.devs as caja,
								a.ventas AS ventas,
								( a.ventas - a.costos ) AS utilidadx,
								( a.ventas - ( a.gastos + a.costos ) ) AS utilidadn
						FROM
						(
							SELECT		DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%d-%m-%Y' ) as fecha,
										ven_id_empresa AS id_empresa,
										fn_s_a_ventas( ven_id_empresa, 0, DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%d-%m-%Y' ), 'D' ) AS sa,
										fn_devoluciones( ven_id_empresa, 0, DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%d-%m-%Y' ), 'D' ) AS devs,
										gastos_sucursal_fecha( ven_id_empresa, DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%d-%m-%Y' ), 'M' ) AS gastos,
										SUM( vende_cantidad * vende_costo ) AS costos,
										SUM( vende_cantidad * vende_descuento ) AS descuentos,
										SUM( vende_cantidad * vende_precio ) AS ventas
							FROM		san_venta
							LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
							INNER JOIN	san_venta_detalle ON vende_id_venta = ven_id_venta
							WHERE		ven_id_empresa = $id_empresa
							AND			ven_status IN ( 'V', 'P' )
							AND			vende_status IS NULL
							AND			'$fecha' = DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%m-%Y' )
							GROUP BY	DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%d-%m-%Y' )
							ORDER BY	fecha
						) a";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$i</td>
								<td>".fecha_generica( $fila['fecha'], true )."</td>
								<td class='text-right text-primary'>$".number_format( $fila['mayoreo'], 2 )."</td>
								<td class='text-right text-primary'>$".number_format( $fila['sa'], 2 )."</td>
								<td class='text-right text-primary'>$".number_format( $fila['devs'], 2 )."</td>
								<td class='text-right text-bold text-info'>$".number_format( $fila['caja'] + $fila['mayoreo'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['descuentos'], 2 )."</td>
								<td class='info text-right'>$".number_format( $fila['ventas'], 2 )."</td>
								<td class='danger text-right'>$".number_format( $fila['gastos'], 2 )."</td>
								<td class='danger text-right'>$".number_format( $fila['costos'], 2 )."</td>
								<td class='success text-right'>$".number_format( $fila['utilidadx'], 2 )."</td>
								<td class='success text-right'>$".number_format( $fila['utilidadn'], 2 )."</td>
							</tr>";
				
				$i++;
				$tot_descuentos	+= $fila['descuentos'];
				$tot_may		+= $fila['mayoreo'];
				$tot_sa			+= $fila['sa'];
				$tot_devs		+= $fila['devs'];
				$tot_caja		+= ( $fila['caja'] + $fila['mayoreo'] );
				$tot_ventas		+= $fila['ventas'];
				$tot_gastos		+= $fila['gastos'];
				$tot_costos		+= $fila['costos'];
				$tot_utilidadx	+= $fila['utilidadx'];
				$tot_utilidadn	+= $fila['utilidadn'];
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$colspan = 2;
		$datos	.= "<tr class='success text-right text-bold'>
						<td colspan='$colspan'>Totales</td>
						<td>$".number_format( $tot_may , 2 )."</td>
						<td>$".number_format( $tot_sa , 2 )."</td>
						<td>$".number_format( $tot_devs, 2 )."</td>
						<td>$".number_format( $tot_caja, 2 )."</td>
						<td>$".number_format( $tot_descuentos, 2 )."</td>
						<td>$".number_format( $tot_ventas, 2 )."</td>
						<td>$".number_format( $tot_gastos, 2 )."</td>
						<td>$".number_format( $tot_costos, 2 )."</td>
						<td>$".number_format( $tot_utilidadx, 2 )."</td>
						<td>$".number_format( $tot_utilidadn, 2 )."</td>
					</tr>";
		
		return $datos;
	}
	
?>