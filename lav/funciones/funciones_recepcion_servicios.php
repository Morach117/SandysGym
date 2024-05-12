<?php
	function obtener_servicios( $p_tipo )
	{
		Global $conexion, $id_giro, $id_consorcio, $id_empresa;
		
		$datos		= "";
		$query		= "	SELECT 		ser_id_servicio AS id_servicio,
									ser_descripcion AS descripcion,
									ser_orden AS orden,
									IF( 
										src_kg_minimo > 1, 
											CONCAT( 'MINIMO: ', ROUND( src_kg_minimo, 2 ), IF(
																								ser_tipo = 'LAVANDERIA', 
																									' KGS',
																									' PZS' 
																								) ), 
											'' 
										) AS notas,
									IF( DAYOFWEEK( CURDATE() ) = src_promo_dia, 'S', 'N' ) AS promo,
									ROUND( src_cuota, 2 ) AS cuota,
									ROUND( IF( DAYOFWEEK( CURDATE() ) = src_promo_dia, src_promo_cuota, src_cuota  ), 2 ) AS prom_cuota
						FROM		san_servicios
						INNER JOIN	san_servicios_cuotas ON src_id_servicio = ser_id_servicio
						AND			src_id_empresa = $id_empresa
						WHERE		ser_id_giro = 2
						AND			ser_status = 'A'
						AND			ser_tipo = '$p_tipo'
						AND			ser_id_consorcio = $id_consorcio
						ORDER BY	orden,
									descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['promo'] == 'S' )
				{
					$datos .= "	<li>
									<span class='glyphicon glyphicon-glass'></span>
									<h4>
										<s>$$fila[cuota]</s> <strong>$$fila[prom_cuota]</strong>
									</h4>
									<span class='touch-class'>$fila[descripcion]</span>
									<span class='touch-class text-bold'>$fila[notas]</span>
								</li>";
				}
				else
				{
					$datos .= "	<li>
									<span class='glyphicon glyphicon-glass'></span>
									<h4><strong>$$fila[cuota]</strong></h4>
									<span class='touch-class'>$fila[descripcion]</span>
									<span class='touch-class'>$fila[notas]</span>
								</li>";
				}
				
				$i++;
			}
		}
		else
		{
			$error	= mysqli_error( $conexion );
			$datos	= "	<li>
							<span class='glyphicon glyphicon-question-sign'></span>
							<h4><strong>$00.00</strong></h4>
							<span class='touch-class'>ERROR.</span>
							<span class='touch-class'>$error</span>
						</li>";
		}
		
		if( !$datos )
		{
			$datos	= "	<li>
							<span class='glyphicon glyphicon-question-sign'></span>
							<h4><strong>$00.00</strong></h4>
							<span class='touch-class'>No hay datos.</span>
							<span class='touch-class'>:/</span>
						</li>";
		}
		
		return $datos;
	}
	
?>