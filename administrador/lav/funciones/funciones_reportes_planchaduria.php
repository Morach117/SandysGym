<?php
	function obtener_planchado( $rango_ini, $rango_fin, $status, $id_usuario )
	{
		global $conexion, $id_empresa;
		
		$datos		= "";
		$condicion	= "";
		$colspan	= 7;
		$contador	= 1;
		$t_piezas	= 0;
		$t_importe	= 0;
		$rango_ini	= fecha_formato_mysql( $rango_ini );
		$rango_fin	= fecha_formato_mysql( $rango_fin );
		
		switch( $status )
		{
			case 'S':	$condicion = " AND a.venh_status = 'S' ";	break;
			case 'T':	$condicion = " AND a.venh_status = 'T' ";	break;
			case 'Z':	$condicion = " AND a.venh_status = 'Z' ";	break;
			default	:	$condicion = " AND a.venh_status IN ( 'S', 'T', 'Z' ) ";
		}
		
		if( $id_usuario )
			$condicion .= " AND venh_id_usuario = $id_usuario ";
		
		$query		= "	SELECT		CONCAT( 'F', LPAD( ven_folio, 7, '0' ) ) AS folio_desc,
									ven_id_venta AS id_venta,
									DATE_FORMAT( b.venh_fecha, '%d-%m-%Y %r' ) AS fecha,
									vense_kilogramo AS piezas,
									vense_precio AS precio,
									vense_importe AS importe,
									ser_descripcion AS servicio
						FROM		san_venta
						INNER JOIN	san_venta_historico a ON a.venh_id_venta = ven_id_venta
						INNER JOIN	san_venta_historico b ON b.venh_id_venta = ven_id_venta
						AND			b.venh_n_m = 1
						AND			b.venh_status IN ( 'S', 'T', 'Z' )
						INNER JOIN	san_venta_servicio ON vense_id_venta = ven_id_venta
						INNER JOIN	san_servicios ON ser_id_servicio = vense_id_servicio
						WHERE		ven_id_empresa = $id_empresa
						AND			DATE_FORMAT( b.venh_fecha, '%Y-%m-%d' ) BETWEEN DATE_FORMAT( '$rango_ini', '%Y-%m-%d' ) AND DATE_FORMAT( '$rango_fin', '%Y-%m-%d' )
									$condicion
						ORDER BY	a.venh_id_venta,
									vense_id_servicio";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i 			= 0;
			$idv_aux	= 0;
			
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $idv_aux == $fila['id_venta'] && $i == 1 )
				{
					$tmp_class	= 'info text-info text-bold';
				}
				else
				{
					if( $i == 0 && $idv_aux != $fila['id_venta'] )
					{
						$tmp_class	= 'info text-info text-bold';
						$i			= 1;
					}
					else
					{
						$tmp_class	= 'warning text-warning text-bold';
						$i			= 0;
					}
				}
				
				$datos	.= "<tr>
								<td>$contador</td>
								<td class='$tmp_class'>$fila[folio_desc]</td>
								<td>$fila[fecha]</td>
								<td class='text-right'>".number_format( $fila['piezas'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['precio'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['importe'], 2 )."</td>
								<td>$fila[servicio]</td>
							</tr>";
				
				$idv_aux	= $fila['id_venta'];
				$t_piezas	+= $fila['piezas'];
				$t_importe	+= $fila['importe'];
				$contador++;
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>Error encontrado: ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$colspan -= 4;
		$datos	.= "<tr class='success text-bold'>
						<td colspan='$colspan' class='text-right'>Totales</td>
						<td class='text-right'>".number_format( $t_piezas, 2 )."</td>
						<td>&nbsp;</td>
						<td class='text-right'>$".number_format( $t_importe, 2 )."</td>
						<td>&nbsp;</td>
					</tr>";
			
		return $datos;
	}
	
?>