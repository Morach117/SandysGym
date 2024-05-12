<?php
	function obtener_datos( $orden, $limit, $mes_evaluar )
	{
		global $conexion, $id_empresa;
		$datos		= "";
		$condicion	= "";
		$colspan	= 4;
		
		$query		= "	SELECT	a.id_socio,
								CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS socio,
								a.frecuencia,
								ROUND( a.kgs, 2 ) AS kgs
						FROM
						(
							SELECT		ven_id_socio AS id_socio,
										COUNT( * ) AS frecuencia,
										SUM( vense_kilogramo ) AS kgs
							FROM		san_venta 
							INNER JOIN	san_venta_servicio ON vense_id_venta = ven_id_venta
							WHERE 		ven_id_empresa = $id_empresa
							AND			ven_status != 'C'
							AND			'$mes_evaluar' = DATE_FORMAT( ven_fecha, '%Y-%m' )
							GROUP BY	ven_id_socio
						) a
						INNER JOIN	san_socios ON soc_id_socio = a.id_socio
						ORDER BY	a.$orden DESC
						LIMIT		0, $limit";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$i</td>
								<td>".$fila['socio']."</td>
								<td class='text-right'>".$fila['frecuencia']."</td>
								<td class='text-right'>".$fila['kgs']."</td>
							</tr>";
							
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
		
		return $datos;
	}
	
?>