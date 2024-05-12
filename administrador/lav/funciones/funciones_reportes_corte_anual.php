<?php
	function obtener_total_venta_efectivo( $año = '' )
	{
		global $conexion, $id_empresa;
		$exito		= array();
		
		$query		= "	SELECT		SUM( venh_total_efectivo ) AS venta_efectivo
						FROM		san_venta_historico
						INNER JOIN	san_venta ON ven_id_venta = venh_id_venta
						WHERE		'$año' = DATE_FORMAT( venh_fecha, '%Y' )
						AND			venh_id_empresa = $id_empresa
						AND			ven_status NOT IN ( 'C', 'I' )";
							
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
				$exito['msj'] = "No se pudo obtener el total por la Venta en Efectivo.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el total de la venta en efectivo del mes. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function lista_ventas_del_mes( $año )
	{
		global $conexion, $id_empresa;
		
		$datos			= "";
		$colspan		= 7;
		$tot_efectivo	= 0;
		$tot_tar_com	= 0;
		$tot_credito	= 0;
		$tot_x_pagar	= 0;
		$tot_total		= 0;
		
		$query		= "	SELECT		DATE_FORMAT( venh_fecha, '%m-%Y' ) AS fecha,
									ROUND( SUM( IF( a.ven_total IS NULL, 0, a.ven_total ) ), 2 ) AS total,
									ROUND( SUM( IF( venh_total_efectivo IS NULL, 0 , venh_total_efectivo ) ), 2 ) AS efectivo,
									ROUND( SUM( IF( venh_total_tarjeta IS NULL, 0 , venh_total_tarjeta + venh_comision ) ), 2 ) AS tar_com,
									obtener_credito_fecha( DATE_FORMAT( venh_fecha, '%m-%Y' ), venh_id_empresa, 'A' ) AS credito,
									ROUND( SUM( IF( a.ven_total_credito IS NULL, 0, a.ven_total_credito ) ), 2 ) AS por_pagar
						FROM		san_venta_historico
						INNER JOIN	san_venta b ON b.ven_id_venta = venh_id_venta
						AND			b.ven_status NOT IN ( 'C', 'I' )
						LEFT JOIN 	san_venta a ON a.ven_id_venta = venh_id_venta
						AND			venh_n_m = 1
						AND			a.ven_status NOT IN ( 'C', 'I' )
						WHERE		'$año' = DATE_FORMAT( venh_fecha, '%Y' )
						AND			venh_id_empresa = $id_empresa
						GROUP BY	DATE_FORMAT( venh_fecha, '%m-%Y' )
						ORDER BY	fecha";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$tot_total		+= $fila['total'];
				$tot_efectivo	+= $fila['efectivo'];
				$tot_tar_com	+= $fila['tar_com'];
				$tot_credito	+= $fila['credito'];
				$tot_x_pagar	+= $fila['por_pagar'];
				$class_x_pagar	= ( $fila['por_pagar'] > 0 ) ? 'danger':'';
				
				$datos	.= "<tr>
								<td>$i</td>
								<td>".fecha_a_mes( $fila['fecha'] )."</td>
								<td class='text-right'>$".number_format( $fila['total'], 2 )."</td>
								<td class='text-right info'>$".number_format( $fila['efectivo'], 2 )."</td>
								<td class='text-right info'>$".number_format( $fila['tar_com'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['credito'], 2 )."</td>
								<td class='text-right $class_x_pagar'>$".$fila['por_pagar']."</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
			
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$colspan -= 5;
		$datos	.= "<tr class='success text-bold'>
						<td class='text-right' colspan='$colspan'>Totales</td>
						<td class='text-right'>$".number_format( $tot_total, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_efectivo, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_tar_com, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_credito, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_x_pagar, 2 )."</td>
					</tr>";
		
		return $datos;
	}
	
	function obtener_gastos( $año = '' )
	{
		global $conexion, $id_empresa;
		$exito		= array();
		
		$query		= "	SELECT	ROUND( SUM( gas_importe ), 2 ) AS importe,
								ROUND( SUM( gas_iva ), 2 ) AS iva,
								ROUND( SUM( gas_descuento ), 2 ) AS descuento,
								ROUND( SUM( gas_total ), 2 ) AS total
						FROM	san_gastos
						WHERE	gas_id_empresa = $id_empresa
						AND		'$año' = DATE_FORMAT( gas_fecha_fnota, '%Y' )";
							
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