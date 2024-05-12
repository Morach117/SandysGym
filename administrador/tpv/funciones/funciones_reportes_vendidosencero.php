<?php
	function obtener_agotados( $mes_evaluar, $destino = 'web' )
	{
		global $conexion, $id_empresa, $id_consorcio;
		
		$colspan	= 7;
		$datos		= "";
		$excel		= array();
		
		$query		= "	SELECT	art_codigo AS codigo,
								a.id_articulo,
								art_descripcion AS descripcion,
								ROUND( art_costo, 2 ) AS costo,
								ROUND( art_precio, 2 ) AS precio,
								ROUND( a.total, 2 ) AS total
						FROM
						(
							SELECT		vende_id_articulo AS id_articulo,
										SUM( vende_cantidad ) AS total
							FROM		san_venta_detalle
							INNER JOIN	san_venta ON ven_id_venta = vende_id_venta
							INNER JOIN	san_stock ON stk_id_articulo = vende_id_articulo
							AND			ven_id_empresa = stk_id_empresa
							WHERE		ven_id_empresa = $id_empresa
							AND			'$mes_evaluar' = DATE_FORMAT( ven_fecha, '%m-%Y' )
							AND			stk_existencia <= 0
							AND			ven_status = 'V'
							GROUP BY	vende_id_articulo
						) a
						INNER JOIN	san_articulos ON art_id_articulo = a.id_articulo
						WHERE		art_id_consorcio = $id_consorcio
						AND			art_unidad != 'S'
						ORDER BY	descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $destino == 'web' )
				{
					$datos	.= "<tr>
									<td>$i</td>
									<td>
										<div class='btn-group'>
											<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
												<span class='glyphicon glyphicon-chevron-down'></span>
											</a>
											<ul class='dropdown-menu pointer'>
												<li><a onclick='agregar_a_transferencia( $fila[id_articulo], 0, \"articulos\" )'><span class='glyphicon glyphicon-refresh'></span> Agregar a transferencia</a></li>
											</ul>
										</div>
									</td>
									<td>$fila[codigo]</td>
									<td>$fila[descripcion]</td>
									<td class='text-right'>$$fila[costo]</td>
									<td class='text-right'>$$fila[precio]</td>
									<td class='text-right'>$fila[total]</td>
								</tr>";
					$i++;
				}
				elseif( $destino == 'excel' )
					array_push( $excel, $fila );
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		if( $destino == 'web' )
			return $datos;
		elseif( $destino == 'excel' )
			return $excel;
			
		return false;
	}
	
?>