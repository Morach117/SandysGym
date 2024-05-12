<?php
	function eliminar_socio()
	{
		global $conexion, $id_empresa;
		
		$id_socio	= request_var( 'id_socio', 0 );
		$exito		= array();
		$e_pagos	= false;
		$e_prepagos	= false;
		$e_ventas	= false;
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "	SELECT 		soc_id_socio,
									COUNT( pag_id_socio ) AS pagos,
									COUNT( prep_id_socio ) AS prepagos,
									COUNT( ven_id_socio ) AS ventas
						FROM 		san_socios
						LEFT JOIN	san_pagos ON pag_id_socio = soc_id_socio
						LEFT JOIN	san_prepago ON prep_id_socio = soc_id_socio
						LEFT JOIN	san_venta ON ven_id_socio = soc_id_socio
						WHERE		soc_id_socio = $id_socio
						AND			soc_id_empresa = $id_empresa
						GROUP BY	soc_id_socio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				//para eliminar mensualidades pagadas
				if( $fila['pagos'] > 0 )
				{
					$query		= "DELETE FROM san_pagos_actualizados WHERE pag_id_pago IN (	SELECT	pag_id_pago 
																								FROM 	san_pagos 
																								WHERE 	pag_id_socio = $id_socio 
																								AND 	pag_id_empresa = $id_empresa)";
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						$query		= "DELETE FROM san_pagos WHERE pag_id_socio = $id_socio AND pag_id_empresa = $id_empresa";
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
							$e_pagos = true;
						else
						{
							$exito['num'] = 5;
							$exito['msj'] = "Ha ocurrido un error al tratar de Eliminar las vigencias de este Socio. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 4;
						$exito['msj'] = "Ha ocurrido un error al tratar de Eliminar las vigencias actualizadas de este Socio. ".mysqli_error( $conexion );
					}
				}
				else
					$e_pagos = true;
				
				//para eliminar prepagos
				if( $fila['prepagos'] > 0 )
				{
					$query		= "DELETE FROM san_prepago_detalle WHERE pred_id_prepago IN (	SELECT	prep_id_prepago
																								FROM	san_prepago
																								WHERE	prep_id_socio = $id_socio
																								AND		prep_id_empresa = $id_empresa )";
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						$query		= "DELETE FROM san_prepago WHERE prep_id_socio = $id_socio AND prep_id_empresa = $id_empresa";
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
							$e_prepagos = true;
						else
						{
							$exito['num'] = 7;
							$exito['msj'] = "Ha ocurrido un error al tratar de Eliminar los Prepagos de este Socio. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 6;
						$exito['msj'] = "Ha ocurrido un error al tratar de Eliminar el detalle de Prepagos de este Socio. ".mysqli_error( $conexion );
					}
				}
				else
					$e_prepagos = true;
				
				//se eliminan las ventas de este socio
				if( $fila['ventas'] > 0 )
				{
					$query		= "DELETE FROM san_venta_historico WHERE venh_id_usuario = $id_socio AND venh_id_empresa = $id_empresa";
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						$query		= "DELETE FROM san_venta_detalle WHERE vende_id_venta IN (	SELECT	ven_id_venta
																								FROM	san_venta
																								WHERE 	ven_id_usuario = $id_socio 
																								AND 	ven_id_empresa = $id_empresa )";
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							$query		= "DELETE FROM san_venta WHERE ven_id_usuario = $id_socio AND ven_id_empresa = $id_empresa";
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								$e_ventas = true;
							}
							else
							{
								$exito['num'] = 10;
								$exito['msj'] = "Ha ocurrido un error al tratar de Eliminar las Ventas de este Socio. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num'] = 9;
							$exito['msj'] = "Ha ocurrido un error al tratar de Eliminar el detalle de Ventas de este Socio. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 8;
						$exito['msj'] = "Ha ocurrido un error al tratar de Eliminar el histórico de Ventas de este Socio. ".mysqli_error( $conexion );
					}
				}
				else
					$e_ventas = true;
				
				if( $e_ventas && $e_pagos && $e_prepagos )
				{
					$query		= "DELETE FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
					$resultado	= mysqli_query( $conexion, $query );
				
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) == 1 )
						{
							$exito['num'] = 1;
							$exito['msj'] = "Socio Eliminado.";
						}
						else
						{
							$exito['num'] = 12;
							$exito['msj'] = "No se Elimino ningun Socio.";
						}
					}
					else
					{
						$exito['num'] = 11;
						$exito['msj'] = "Ha ocurrio un error al tratar de Eliminar este Socio. ".mysqli_error( $conexion );
					}
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Número de Socio no válido.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Ha ocurrio un error al tratar de verificar este Socio. ".mysqli_error( $conexion );
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
?>