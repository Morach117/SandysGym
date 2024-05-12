<?php
	function obtener_servicios_lav( $p_tipo )
	{
		global $conexion, $id_consorcio;
		
		$datos		= '';
		$class		= '';
		$colspan	= 5;
		
		$query		= "	SELECT 		ser_id_servicio AS id_servicio,
									ser_descripcion AS descripcion,
									ser_orden AS orden,
									ser_status AS status,
									ser_tipo AS tipo,
									CASE ser_status
										WHEN 'A' THEN 'Activo'
										WHEN 'D' THEN 'Descontinuado'
									END AS status_desc 
						FROM		san_servicios 
						WHERE		ser_id_giro = 2 
						AND			ser_id_consorcio = $id_consorcio
						AND			ser_tipo = '$p_tipo'
						ORDER BY	status,
									orden,
									descripcion";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$class = ( $fila['status'] == 'D' ) ? 'danger':'';
				
				$datos	.= "<tr class='$class'>
								<td>$i</td>
								<td>
									<div class='btn-group'>
										<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
											<span class='glyphicon glyphicon-chevron-down'></span>
										</a>
										<ul class='dropdown-menu'>
											<li><a href='.?s=servicios&i=lav_editar&id_servicio=$fila[id_servicio]&tipo=$fila[tipo]'><span class='glyphicon glyphicon-edit'></span> Editar servicio</a></li>
											<li><a href='.?s=servicios&i=lav_quitar&id_servicio=$fila[id_servicio]&tipo=$fila[tipo]'><span class='glyphicon glyphicon-remove-sign'></span> Eliminar servicio</a></li>
										</ul>
									</div>
								</td>
								<td>$fila[descripcion]</td>
								<td>$fila[orden]</td>
								<td>$fila[status_desc]</td>
							</tr>";
				$i++;
			}
			
			if( !$datos )
				$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>No se pudo obtener el reporte. ".mysqli_error( $conexion )."</td></tr>";
			
		return $datos;
	}
	
?>