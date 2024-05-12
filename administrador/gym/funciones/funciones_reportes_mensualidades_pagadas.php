<?php
	function obtener_mensualidades( $rango_ini = '', $rango_fin = '' )
	{
		global $conexion, $id_empresa;
		
		$fecha_mov	= date( 'd-m-Y' );
		$contador	= 1;
		
		if( $rango_ini && $rango_fin )
		{
			$condicion	= "	DATE_FORMAT( pag_fecha_pago, '%Y-%m-%d' )
							BETWEEN DATE_FORMAT( '$rango_ini', '%Y-%m-%d' )
							AND		DATE_FORMAT( '$rango_fin', '%Y-%m-%d' )";
		}
		else
			$condicion = "'$fecha_mov' = DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y' )";
		
		$datos		= '';
		$query		= "	SELECT		CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS socio,
									ser_descripcion AS servicio,
									usua_nombres AS capturo,
									LOWER( DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y %r' ) ) AS fecha_pago,
									ROUND( pag_importe, 2 ) AS importe
						FROM		san_pagos
						INNER JOIN	san_socios ON soc_id_socio = pag_id_socio
						INNER JOIN	san_servicios ON pag_id_servicio = ser_id_servicio
						INNER JOIN	san_usuarios ON usua_id_usuario = pag_id_usuario
						WHERE		$condicion
						AND			pag_status != 'E'
						AND			pag_id_empresa = $id_empresa
						ORDER BY	pag_fecha_pago DESC";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$contador</td>
								<td>".$fila['socio']."</td>
								<td>".$fila['servicio']."</td>
								<td>".$fila['capturo']."</td>
								<td>".$fila['fecha_pago']."</td>
								<td class='text-right'>$".$fila['importe']."</td>
							</tr>";
				$contador++;
			}
		}
		else
		{
			$error	= mysqli_error( $conexion );
			$datos	= "	<tr>
							<td colspan='6'>No se pudo obtener el reporte. $error</td>
						</tr>";
		}
		
		if( !$datos )
		{
			$datos	= "	<tr>
							<td colspan='6'>No hay datos.</td>
						</tr>";
		}
			
		return $datos;
	}
	
?>