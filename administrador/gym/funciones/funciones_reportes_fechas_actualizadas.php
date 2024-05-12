<?php
	function obtener_mensualidades_actualizadas( $rango_ini = '', $rango_fin = '' )
	{
		global $conexion, $id_empresa;
		
		$datos		= '';
		$colspan	= 6;
		$fecha_mov	= date( 'Y-m-d' );
		
		if( $rango_ini && $rango_fin )
		{
			$condicion	= "	DATE_FORMAT( a.pag_fecha_a, '%Y-%m-%d' )
							BETWEEN DATE_FORMAT( '$rango_ini', '%Y-%m-%d' )
							AND		DATE_FORMAT( '$rango_fin', '%Y-%m-%d' )";
		}
		else
			$condicion = "'$fecha_mov' = DATE_FORMAT( a.pag_fecha_a, '%Y-%m-%d' )";
		
		$query		= "	SELECT		soc_nombres AS socio,
									DATE_FORMAT( a.pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini_ant,
									DATE_FORMAT( a.pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin_ant,
									d.usua_nombres AS capturo,
									b.usua_nombres AS actualizo,
									DATE_FORMAT( c.pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini_nvo,
									DATE_FORMAT( c.pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin_nvo,
									LOWER( DATE_FORMAT( a.pag_fecha_a, '%d-%m-%Y %r' ) ) AS modificacion,
									a.pag_comentario AS comentario
						FROM		san_pagos_actualizados a
						INNER JOIN	san_usuarios b ON b.usua_id_usuario = a.pag_id_usuario_a
						INNER JOIN	san_pagos c ON c.pag_id_pago = a.pag_id_pago
						INNER JOIN	san_usuarios d ON d.usua_id_usuario = c.pag_id_usuario
						INNER JOIN	san_socios ON soc_id_socio = c.pag_id_socio
						WHERE		$condicion
						AND			pag_id_empresa = $id_empresa
						ORDER BY	a.pag_id_pago_a DESC";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>".$fila['socio']."</td>
								<td>".$fila['fecha_ini_ant']." al ".$fila['fecha_fin_ant']."</td>
								<td>".$fila['fecha_ini_nvo']." al ".$fila['fecha_fin_nvo']."</td>
								<td>".$fila['capturo']."/".$fila['actualizo']."</td>
								<td>".$fila['modificacion']."</td>
								<td>".$fila['comentario']."</td>
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