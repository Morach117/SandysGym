<?php
	function obtener_horas_eliminadas( $rango_ini = '', $rango_fin = '' )
	{
		global $conexion, $id_empresa;
		
		$fecha_mov		= date( 'Y-m-d' );
		
		if( $rango_ini && $rango_fin )
		{
			$condicion	= "	DATE_FORMAT( hor_fecha_e, '%Y-%m-%d' )
							BETWEEN DATE_FORMAT( '$rango_ini', '%Y-%m-%d' )
							AND		DATE_FORMAT( '$rango_fin', '%Y-%m-%d' )";
		}
		else
			$condicion = "'$fecha_mov' = DATE_FORMAT( hor_fecha_e, '%Y-%m-%d' )";
		
		$datos		= '';
		
		$query		= "	SELECT		hor_nombre AS cliente,
									CONCAT( a.usua_nombres ) as capturo,
									CONCAT( b.usua_nombres ) as elimino,
									LOWER( DATE_FORMAT( hor_fecha, '%r' ) ) AS captura,
									IF( ser_descripcion = 'VISITA', 'N/A', hor_horas ) AS tiempo,
									IF( ser_descripcion = 'VISITA', 'N/A', CONCAT( LOWER( DATE_FORMAT( hor_hora_inicial, '%r' ) ), ' a ', LOWER( DATE_FORMAT( hor_hora_final, '%r' ) ) ) ) AS periodo,
									LOWER( DATE_FORMAT( hor_fecha_e, ' %d-%m-%Y %r' ) ) AS eliminacion,
									ser_descripcion AS servicio
						FROM		san_horas
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = hor_id_usuario
						INNER JOIN	san_usuarios b ON b.usua_id_usuario = hor_id_usuario_e
						WHERE		$condicion
						AND			hor_id_empresa = $id_empresa
						AND			hor_status = 'E'
						ORDER BY	eliminacion DESC";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$class	= '';
				if( $fila['servicio'] == 'VISITA' )
					$class = "class='info'";
				
				$datos	.= "<tr $class>
								<td>".$fila['cliente']."</td>
								<td>".$fila['capturo']."/".$fila['elimino']."</td>
								<td>".$fila['captura']."</td>
								<td>".$fila['tiempo']."</td>
								<td>".$fila['periodo']."</td>
								<td>".$fila['eliminacion']."</td>
							</tr>";
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