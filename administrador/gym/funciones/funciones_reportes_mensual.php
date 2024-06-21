<?php
	function lista_ventas_del_mes( $mes_movimiento )
	{
		global $conexion, $id_empresa;
		
		$datos			= "";
		$colspan		= 9;
		$contador		= 1;
		$d_mensual		= lista_detalle_mensualidades( $mes_movimiento );
		$d_horas		= lista_detalle_horas_visitas( $mes_movimiento, 'HORA' );
		$d_visitas		= lista_detalle_horas_visitas( $mes_movimiento, 'VISITA' );
		$d_ventas		= lista_detalle_venta_articulos( $mes_movimiento );
		$d_prepagos		= lista_detalle_prepagos( $mes_movimiento );
		$d_cortes		= lista_cortes( $mes_movimiento );
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
			for( $i = 1; $i <= 31; $i++ )
			{
				foreach( $d_mensual as $fila )
				{
					if( $fila['dia'] == $i )
					{
						$fila_mensual = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_horas as $fila )
				{
					if( $fila['dia'] == $i )
					{
						$fila_horas = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_visitas as $fila )
				{
					if( $fila['dia'] == $i )
					{
						$fila_visitas = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_ventas as $fila )
				{
					if( $fila['dia'] == $i )
					{
						$fila_articulos = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_prepagos as $fila )
				{
					if( $fila['dia'] == $i )
					{
						$fila_prepagos = $fila['importe'];
						break;
					}
				}
				
				foreach( $d_cortes as $fila )
				{
					if( $fila['dia'] == $i )
					{
						$fila_cortes = $fila['importe'];
						break;
					}
				}
				
				$fecha			= "$i-$mes_movimiento";
				$fila_total		= ( $fila_mensual + $fila_horas + $fila_visitas + $fila_articulos + $fila_prepagos );
				
				if( $fila_total )
				{
					$datos	.= "<tr>
									<td>$contador</td>
									<td>".fecha_generica( $fecha )."</td>
									<td class='text-right'>$".number_format( $fila_mensual, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_horas, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_visitas, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_articulos, 2 )."</td>
									<td class='text-right'>$".number_format( $fila_prepagos, 2 )."</td>
									<td class='text-right info'>$".number_format( $fila_cortes, 2 )."</td>
									<td class='text-right success text-bold'>$".number_format( $fila_total, 2 )."</td>
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
		
		$query		= "	SELECT		DAYOFMONTH( pag_fecha_pago ) AS dia,
									SUM( pag_importe ) AS importe
						FROM		san_pagos
						WHERE		DATE_FORMAT( pag_fecha_pago, '%m-%Y' ) = '$fecha_mov'
						AND			pag_status = 'A'
						AND			pag_id_empresa = $id_empresa
						GROUP BY	DAYOFMONTH( pag_fecha_pago )";
		
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
		
		$query		= "	SELECT		DAYOFMONTH( hor_fecha ) AS dia,
									SUM( hor_importe ) AS importe
						FROM		san_horas
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						AND			ser_id_consorcio = $id_consorcio
						WHERE		DATE_FORMAT( hor_fecha, '%m-%Y' ) = '$fecha_mov'
						AND			hor_status = 'A'
						AND			hor_id_empresa = $id_empresa
						AND			ser_clave = '$tipo'
						GROUP BY	DAYOFMONTH( hor_fecha )";
		
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
		
		$query		= "	SELECT		DAYOFMONTH( ven_fecha ) AS dia,
									SUM( ven_total_efectivo + ven_total_tarjeta + ven_comision ) AS importe
						FROM		san_venta
						WHERE		DATE_FORMAT( ven_fecha, '%m-%Y' ) = '$fecha_mov'
						AND			ven_id_empresa = $id_empresa
						AND			ven_status = 'V'
						GROUP BY	DAYOFMONTH( ven_fecha )";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		
		return $datos;
	}
	
	function lista_detalle_prepagos($fecha_mov)
	{
		global $conexion, $id_empresa;
		
		$datos = array();
		
		$query = "   SELECT      pred_id_pdetalle, 
									pred_descripcion, 
									pred_importe, 
									pred_saldo, 
									pred_movimiento, 
									pred_fecha, 
									pred_id_socio, 
									pred_id_usuario 
						FROM        san_prepago_detalle 
						WHERE       DATE_FORMAT(pred_fecha, '%m-%Y') = '$fecha_mov' 
						AND         pred_movimiento = 'S' 
						AND         pred_id_pdetalle IN (
										SELECT  prep_id_prepago
										FROM    san_prepago
										WHERE   prep_id_empresa = $id_empresa
									)";
		
		$resultado = mysqli_query($conexion, $query);
		
		if ($resultado)
			while ($fila = mysqli_fetch_assoc($resultado))
				array_push($datos, $fila);
		
		return $datos;
	}
	
	
	function lista_cortes( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$datos		= array();
		
		$query		= "	SELECT 		DAYOFMONTH( cor_fecha_venta ) AS dia,
									SUM( cor_importe ) AS importe
						FROM 		san_corte
						WHERE		DATE_FORMAT( cor_fecha_venta, '%m-%Y' ) = '$fecha_mov'
						AND			cor_id_empresa = $id_empresa
						GROUP BY	DAYOFMONTH( cor_fecha_venta )";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		
		return $datos;
	}
	
?>