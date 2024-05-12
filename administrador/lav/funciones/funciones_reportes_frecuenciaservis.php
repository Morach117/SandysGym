<?php
	function obtener_datos( $mes_evaluar, $orden, $dias_mes )
	{
		global $conexion, $id_empresa;
		$datos		= "";
		$condicion	= "";
		$colspan	= 6;
		$totales	= array( 'frecuencia' => 0, 'kgs' => 0, 'promc' => 0, 'promd' => 0 );
		
		$query		= "	SELECT	a.id_servicio,
								ser_descripcion AS descripcion,
								a.frecuencia,
								ROUND( a.kgs, 2 ) AS kgs,
								ROUND( a.kgs / a.frecuencia, 2 ) AS prom_cliente,
								ROUND( a.kgs / $dias_mes, 2 ) AS prom_dias
						FROM
						(
							SELECT		vense_id_servicio AS id_servicio,
										COUNT( * ) AS frecuencia,
										SUM( vense_kilogramo ) AS kgs
							FROM		san_venta 
							INNER JOIN	san_venta_servicio ON vense_id_venta = ven_id_venta
							WHERE 		ven_id_empresa = $id_empresa
							AND			ven_status != 'C'
							AND			DATE_FORMAT( ven_fecha, '%Y-%m' ) = '$mes_evaluar'
							GROUP BY	vense_id_servicio
						) a
						INNER JOIN	san_servicios ON ser_id_servicio = a.id_servicio
						ORDER BY	a.$orden DESC";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$i</td>
								<td>".$fila['descripcion']."</td>
								<td class='text-right'>".$fila['frecuencia']."</td>
								<td class='text-right'>".$fila['kgs']."</td>
								<td class='text-right'>".$fila['prom_cliente']."</td>
								<td class='text-right'>".$fila['prom_dias']."</td>
							</tr>";
							
				$totales['frecuencia']	+= $fila['frecuencia'];
				$totales['kgs']			+= $fila['kgs'];
				$totales['promc']		+= $fila['prom_cliente'];
				$totales['promd']		+= $fila['prom_dias'];
				$i++;
			}
		}
		else
		{
			$datos = "<tr><td colspan='$colspan'>Ocurrió un error al obtener los datos. ".mysqli_error( $conexion )."</td></tr>";
		}
		
		if( !$datos )
		{
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		}
		
		$colspan -= 4;
		$datos	.= "<tr class='success text-bold'>
						<td colspan='$colspan' class='text-right'>Totales</td>
						<td class='text-right'>".$totales['frecuencia']."</td>
						<td class='text-right'>".number_format( $totales['kgs'], 2 )."</td>
						<td class='text-right'>".number_format( $totales['kgs'] / (( $totales['frecuencia'] > 0 ) ? $totales['frecuencia']:1), 2 )."</td>
						<td class='text-right'>".number_format( $totales['promd'], 2 )."</td>
					</tr>";
		
		return $datos;
	}
	
?>