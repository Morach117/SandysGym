<?php
	function obtener_pagos_eliminados( $rango_ini = '', $rango_fin = '' )
	{
		global $conexion, $id_empresa;
		
		$datos		= '';
		$colspan	= 6;
		$fecha_mov	= date( 'Y-m-d' );
		
		if( $rango_ini && $rango_fin )
		{
			$condicion	= "	DATE_FORMAT( pag_fecha_e, '%Y-%m-%d' )
							BETWEEN DATE_FORMAT( '$rango_ini', '%Y-%m-%d' )
							AND		DATE_FORMAT( '$rango_fin', '%Y-%m-%d' )";
		}
		else
			$condicion = "'$fecha_mov' = DATE_FORMAT( pag_fecha_e, '%Y-%m-%d' )";
		
		$query		= "	SELECT		LOWER( DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y %r' ) ) AS fecha_pago,
									soc_nombres AS socio,
									DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini,
									DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin,
									ser_descripcion AS servicio,
									a.usua_nombres AS capturo,
									b.usua_nombres AS elimino,
									LOWER( DATE_FORMAT( pag_fecha_e, '%Y-%m-%d %r' ) ) AS eliminacion
						FROM		san_pagos
						INNER JOIN	san_socios ON soc_id_socio = pag_id_socio
						INNER JOIN	san_servicios ON ser_id_servicio = pag_id_servicio
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = pag_id_usuario
						INNER JOIN	san_usuarios b ON b.usua_id_usuario = pag_id_usuario_e
						WHERE		pag_id_empresa = $id_empresa
						AND			$condicion
						AND			pag_status = 'E'
						GROUP BY	pag_fecha_e DESC";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>".$fila['fecha_pago']."</td>
								<td>".$fila['socio']."</td>
								<td>".$fila['fecha_ini']." al ".$fila['fecha_fin']."</td>
								<td>".$fila['capturo']."/".$fila['elimino']."</td>
								<td>".$fila['servicio']."</td>
								<td>".$fila['eliminacion']."</td>
							</tr>";
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>No se pudo obtener el reporte. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
			
		return $datos;
	}
	
?>