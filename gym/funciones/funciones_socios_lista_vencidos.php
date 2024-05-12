<?php
	function lista_socios_fechas( $rango_ini, $rango_fin, $pag_busqueda )
	{
		Global $conexion, $id_empresa, $gbl_paginado;
		
		$datos		= "";
		$condicion	= "";
		$colspan	= 6;
		$contador	= 1;
		$exito		= array();
		$pagina		= ( request_var( 'pag', 1 ) - 1 ) * $gbl_paginado;
		$bloque		= request_var( 'blq', 0 );
		$pag		= request_var( 'pag', 0 );
		
		$parametros	= "&pag_fechai=$rango_ini&pag_fechaf=$rango_fin&item=lista_vencidos";
		
		if( $pag_busqueda )
			$parametros .= "&pag_busqueda=$pag_busqueda";
		
		if( $bloque )
			$parametros .= "&blq=$bloque";
		
		if( $pag )
			$parametros .= "&pag=$pag";
		
		$rango_ini	= fecha_formato_mysql( $rango_ini );
		$rango_fin	= fecha_formato_mysql( $rango_fin );
		
		if( $pag_busqueda )
		{
			$condicion	= "AND	(
									LOWER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) LIKE LOWER( '%$pag_busqueda%' )
								)";
		}
		
		$query		= "	SELECT		soc_id_socio AS id_socio
						FROM 		san_socios
						INNER JOIN	san_pagos ON pag_id_socio = soc_id_socio
						AND			DATE_FORMAT( pag_fecha_fin, '%Y-%m-%d' )
						BETWEEN 	DATE_FORMAT( '$rango_ini', '%Y-%m-%d' )
						AND			DATE_FORMAT( '$rango_fin', '%Y-%m-%d' )
						AND			pag_status = 'A'
						AND			pag_fecha_fin = ( 	SELECT		pag_fecha_fin
														FROM		san_pagos
														WHERE		pag_id_socio = soc_id_socio
														AND			pag_status = 'A'
														ORDER BY	pag_fecha_fin DESC 
														LIMIT		0, 1 )
						WHERE		soc_id_empresa = $id_empresa
									$condicion
						GROUP BY	soc_id_socio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			$exito['num'] = mysqli_num_rows( $resultado );
		
		$query		= "	SELECT		soc_id_socio AS id_socio,
									pag_id_pago AS id_pago,
									soc_nombres AS nombres,
									CONCAT( soc_apepat, ' ', soc_apemat ) AS apellidos,
									IF( pag_id_pago > 0, CONCAT( DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ), ' al ', DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) ), 'Pago Vencido' ) AS status_pago,
									IF( soc_imagen IS NULL OR soc_imagen = '', 'Sin nombre de archivo', soc_imagen ) AS img
						FROM 		san_socios
						INNER JOIN	san_pagos ON pag_id_socio = soc_id_socio
						AND			DATE_FORMAT( pag_fecha_fin, '%Y-%m-%d' )
						BETWEEN 	DATE_FORMAT( '$rango_ini', '%Y-%m-%d' )
						AND			DATE_FORMAT( '$rango_fin', '%Y-%m-%d' )
						AND			pag_status = 'A'
						AND			pag_fecha_fin = ( 	SELECT		pag_fecha_fin
														FROM		san_pagos
														WHERE		pag_id_socio = soc_id_socio
														AND			pag_status = 'A'
														ORDER BY	pag_fecha_fin DESC 
														LIMIT		0, 1 )
						WHERE		soc_id_empresa = $id_empresa
									$condicion
						GROUP BY	soc_id_socio
						ORDER BY	pag_fecha_fin DESC
						LIMIT 		$pagina, $gbl_paginado";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( file_exists( "../imagenes/avatar/$fila[id_socio].jpg" ) )
					$fotografia	= "<img src='../imagenes/avatar/$fila[id_socio].jpg' class='img-responsive' alt='$fila[id_socio]' />";
				else
					$fotografia	= "<img src='../imagenes/avatar/noavatar.jpg' class='img-responsive' width='124px' alt='noavatar' />";
				
				if( $contador == 1 )
					$datos .= "<div class='row'>";
				
				$datos .= "	<div class='col-xs-6 col-md-3'>
								<a href='.?s=socios&i=pagos&id_socio=$fila[id_socio]$parametros' class='thumbnail'>
									$fotografia
									
									<div class='caption text-center'>
										<h6>".strtoupper( $fila['apellidos'] )."</h6>
										<h6>".strtoupper( $fila['nombres'] )."</h6>
										<h6>$fila[status_pago]</h6>
										<h6>$fila[img]</h6>
									</div>
								</a>
							</div>";
				
				$contador++;
				
				if( $contador == 5 )
				{
					$datos		.= "</div>";
					$contador	= 1;
				}
			}
			
			if( $datos && $contador != 1 )
				$datos .= "</div>";
			
			if( isset( $exito['num'] ) )
			{
				if( $exito['num'] == 0 )
				{
					$datos = "	<div class='row'>
									<div class='col-sm-6 col-md-4'>
										<div class='thumbnail'>
											<img src='../imagenes/avatar/noavatar.jpg' class='img-responsive' width='124px' alt='noavatar' />
											
											<div class='caption'>
												<h5>No hay datos</h5>
											</div>
										</div>
									</div>
								</div>";
				}
			}
		}
		else
		{
			$datos .= "	<div class='row'>
							<div class='col-sm-6 col-md-4'>
								<div class='thumbnail'>
									<img src='../imagenes/avatar/noavatar.jpg' class='img-responsive' width='124px' alt='noavatar' />
									
									<div class='caption'>
										<h5>Error: ". mysqli_error( $conexion ) ."</h5>
									</div>
								</div>
							</div>
						</div>";
		}
		
		$exito['msj'] = $datos;
		
		return $exito;
	}
	
?>