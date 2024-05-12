<?php
	function ultimos_tickets( $p_folio )
	{
		global $conexion, $id_empresa;
		
		$condicion	= "";
		$datos		= "";
		$colspan	= 6;
		
		if( $p_folio )
			$condicion = " AND ven_folio like '%$p_folio%' ";
		
		$query		= "	SELECT		CONCAT( 'F', LPAD( ven_folio, 7, '0' ) ) AS desc_folio,
									ven_id_venta AS id_venta,
									ven_folio AS folio,
									ven_id_socio AS id_socio,
									LOWER( DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) ) AS movimiento,
									LOWER( DATE_FORMAT( ven_entrega, '%d-%m-%Y %r' ) ) AS entrega,
									CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS cliente,
									ROUND( ven_total, 2 ) AS total,
									CASE ven_status
										WHEN 'R' THEN 'Recepcionado'
										WHEN 'L' THEN 'Lavado'
										WHEN 'E' THEN 'Entregado'
										WHEN 'C' THEN 'Cancelado'
										WHEN 'S' THEN 'Para planchar'
										WHEN 'T' THEN 'Planchado y entregado'
										WHEN 'Z' THEN 'Revisado'
										ELSE ven_status
									END AS status,
									CASE ven_status
										WHEN 'S' THEN 'PLANCHADURIA'
										WHEN 'T' THEN 'PLANCHADURIA'
										ELSE 'LAVANDERIA'
									END AS tipo
						FROM		san_venta
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						WHERE		ven_id_empresa = $id_empresa
									$condicion
						ORDER BY	id_venta DESC
						LIMIT		0, 20";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos .= "	<tr>
									<td>$i</td>
									<td>
										<div class='btn-group'>
											<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
												<span class='glyphicon glyphicon-chevron-down'></span>
											</a>
											<ul class='dropdown-menu pointer'>
												<li><a target='_blank' href='.?s=venta&i=ticket&folio=$fila[folio]&id=$fila[id_venta]&tipo=$fila[tipo]'><span class='glyphicon glyphicon-print'></span> Impresora laser/toner</a></li>
												<li onclick='impresion_termica( $fila[folio], $fila[id_venta], \"$fila[tipo]\" )'><a><span class='glyphicon glyphicon-print'></span> Impresora termica</a></li>
											</ul>
										</div>
									</td>
									<td>".$fila['desc_folio']."</td>
									<td>".fecha_generica( $fila['movimiento'] )."</td>
									<td>".$fila['status']."</td>
									<td>".$fila['cliente']."</td>
								</tr>";
				$i++;
			}
		}
		else
		{
			$datos = "	<tr>
							<td colspan='$colspan'>Ocurrió un error al realizar la consulta. ".mysqli_error( $conexion )."</td>
						</td>";
		}
		
		if( !$datos )
		{
			$datos = "	<tr>
							<td colspan='$colspan'>No hay datos.</td>
						</td>";
		}
		
		return $datos;
	}
	
?>