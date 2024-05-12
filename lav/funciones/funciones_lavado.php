<?php
	function obtener_lavados()
	{
		Global $conexion, $id_empresa;
		$datos		= '';
		$colspan	= 5;
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "	SELECT		ven_id_venta AS id_venta,
									ven_folio AS folio,
									CONCAT( 'F', LPAD( ven_folio, 7, '0' ) ) AS folio_desc,
									LOWER( DATE_FORMAT( venh_fecha, '%d-%m-%Y %r' ) ) AS primer_m,
									UPPER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) AS cliente,
									ven_observaciones AS obs
						FROM		san_venta
						INNER JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						AND			venh_n_m = 1
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						AND			soc_id_empresa = ven_id_empresa
						WHERE		ven_status = 'R'
						AND			ven_id_empresa = $id_empresa
						ORDER BY	folio ASC";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr onclick='ver_detalle( $fila[id_venta], $fila[folio] )'>
								<td>$i</td>
								<td>".$fila['folio_desc']."</td>
								<td>".$fila['primer_m']."</td>
								<td>".$fila['cliente']."</td>
								<td>".$fila['obs']."</td>
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