<?php
	function lista_orden_lavado( $default = 0 )
	{
		$ordenes	= array
		(
			1	=> 'Folio',
			2	=> 'Primer movimiento(recepción)',
			3	=> 'Cliente'
		);
		
		$lista	= "";
		
		foreach( $ordenes as $ind => $orden )
		{
			if( $ind == $default )
				$lista .= "<option selected value='$ind'>$orden</option>";
			else
				$lista .= "<option value='$ind'>$orden</option>";
		}
		
		return $lista;
	}
	
	function obtener_lista_para_entrega( $p_order_by, $p_buscar = '', $p_status = '' )
	{
		Global $conexion, $id_empresa;
		$datos		= "";
		$class		= "";
		$condicion	= "";
		$oby		= "";
		$colspan	= 5;
		
		mysqli_autocommit( $conexion, false );
		
		switch( $p_order_by )
		{
			case 1: $oby = "ORDER BY folio"; break;
			case 2: $oby = "ORDER BY primer_m"; break;
			case 3: $oby = "ORDER BY cliente"; break;
		}
		
		if( $p_buscar )
		{
			if( is_numeric( $p_buscar ) )
				$condicion .= "	AND ( ven_folio LIKE '%$p_buscar%' )";
			else
			{
				$condicion .= "	AND (	CONCAT( soc_apepat, ' ', soc_apemat ) LIKE '%$p_buscar%'
										OR
										soc_apepat LIKE '%$p_buscar%'
										OR
										soc_apemat LIKE '%$p_buscar%'
										OR
										soc_nombres LIKE '%$p_buscar%'
									)";
			}
		}
		
		if( $p_status == 'L' ) //se busca lo que se ha ENTREGADO E
			$condicion .= " AND ven_status = 'E'";
		elseif( $p_status == 'S' ) //se busca para plancar, ENTREGADO T
			$condicion .= " AND ven_status = 'T'";
		
		$query		= "	SELECT		ven_id_venta AS id_venta,
									ven_status AS status,
									ven_folio AS folio,
									CONCAT( 'F', LPAD( ven_folio, 7, '0' ) ) AS folio_desc,
									LOWER( DATE_FORMAT( venh_fecha, '%d-%m-%Y %r' ) ) AS primer_m,
									UPPER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) AS cliente,
									ROUND( ven_total_credito, 2 ) AS credito,
									soc_tel_cel AS telcel
						FROM		san_venta
						INNER JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						AND			venh_n_m = 1
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						AND			soc_id_empresa = ven_id_empresa
						WHERE		ven_id_empresa = $id_empresa
									$condicion
									$oby";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$n_cliente	= $fila['cliente'];
				$n_folio	= $fila['folio_desc'];
				
				if( $p_buscar )
				{
					$n_cliente	= str_replace( $p_buscar, "<span class='label label-success'>$p_buscar</span>", $n_cliente );
					$n_folio	= str_replace( $p_buscar, "<span class='label label-success'>$p_buscar</span>", $n_folio );
				}
				
				if( $fila['status'] != 'L' && $fila['credito'] > 0 )
					$class = "danger";
				else
					$class = "";
				
				$datos	.= "<tr class='$class' onclick='ver_detalle( $fila[id_venta], $fila[folio] )'>
								<td>".( $i )."</td>
								<td>$n_folio</td>
								<td style='width:170px'>".$fila['primer_m']."</td>
								<td>$n_cliente</td>
								<td class='text-right'>$".$fila['credito']."</td>
								<td>".$fila['telcel']."</td>
							</tr>";
				
				$i++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener los datos. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
?>