<?php
	function total_venta( $año_mov )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT		SUM( vende_cantidad * vende_precio ) AS total
						FROM		san_venta
						LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						INNER JOIN	san_venta_detalle ON vende_id_venta = ven_id_venta
						WHERE		DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%Y' ) = '$año_mov'
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
	
	function total_gastos( $año_mov )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT 	SUM( gas_total ) AS total
						FROM 	san_gastos
						WHERE	gas_id_empresa = $id_empresa
						AND		DATE_FORMAT( gas_fecha_fnota, '%Y' ) = '$año_mov'";
		
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
	
	function total_venta_costos( $año_mov )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT		SUM( vende_cantidad * vende_costo ) AS total
						FROM		san_venta_detalle
						INNER JOIN	san_venta ON ven_id_venta = vende_id_venta
						LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						WHERE		'$año_mov' = DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%Y' )
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
		$colspan		= 8;
		$tot_descuentos	= 0;
		$tot_ventas		= 0;
		$tot_gastos		= 0;
		$tot_costos		= 0;
		$tot_utilidadx	= 0;
		$tot_utilidadn	= 0;
		
		$query		= "	SELECT	a.fecha AS fecha,
								a.gastos AS gastos,
								a.costos AS costos,
								a.descuentos AS descuentos,
								a.ventas AS ventas,
								( a.ventas - a.costos ) AS utilidadx,
								( a.ventas - ( a.gastos + a.costos ) ) AS utilidadn
						FROM
						(
							SELECT		DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%m-%Y' ) as fecha,
										gastos_sucursal_fecha( ven_id_empresa, DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%m-%Y' ), 'A' ) AS gastos,
										SUM( vende_cantidad * vende_costo ) AS costos,
										SUM( vende_cantidad * vende_descuento ) AS descuentos,
										SUM( vende_cantidad * vende_precio ) AS ventas
							FROM		san_venta
							LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
							INNER JOIN	san_venta_detalle ON vende_id_venta = ven_id_venta
							WHERE		ven_id_empresa = $id_empresa
							AND			ven_status IN ( 'V', 'P' )
							AND			vende_status IS NULL
							AND			'$fecha' = DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%Y' )
							GROUP BY	DATE_FORMAT( IF( ven_status = 'V', ven_fecha, venh_fecha ), '%m-%Y' )
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
								<td>".fecha_a_mes( $fila['fecha'] )."</td>
								<td class='text-right'>$".number_format( $fila['descuentos'], 2 )."</td>
								<td class='info text-right'>$".number_format( $fila['ventas'], 2 )."</td>
								<td class='danger text-right'>$".number_format( $fila['gastos'], 2 )."</td>
								<td class='danger text-right'>$".number_format( $fila['costos'], 2 )."</td>
								<td class='success text-right'>$".number_format( $fila['utilidadx'], 2 )."</td>
								<td class='success text-right'>$".number_format( $fila['utilidadn'], 2 )."</td>
							</tr>";
				$i++;
				$tot_descuentos	+= $fila['descuentos'];
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
		
		$datos	.= "<tr class='success text-bold'>
						<td colspan='".( $colspan - 6 )."' class='text-right'>Totales</td>
						<td class='text-right'>$".number_format( $tot_descuentos, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_ventas, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_gastos, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_costos, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_utilidadx, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_utilidadn, 2 )."</td>
					</tr>";
		
		return $datos;
	}
	
?>