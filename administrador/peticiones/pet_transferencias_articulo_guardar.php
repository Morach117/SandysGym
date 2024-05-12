<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$js_id_articulo	= request_var( 'id_articulo', 0 );
	$js_cadena		= request_var( 'cadena', '' );
	$exito			= array();
	$salida			= "";
	$transferidos	= 0;
	
	$arreglo		= explode( ',', $js_cadena );
	
	mysqli_autocommit( $conexion, false );
	
	foreach( $arreglo as $fila )
	{
		list( $v_cantidad, $v_id_transfer, $v_movimiento ) = explode( ':', $fila );
		
		if( is_numeric( $v_cantidad ) )
		{
			if( $v_cantidad != 0 )
			{
				$query		= "	SELECT		stk_existencia AS stk,
											art_precio AS precio,
											art_costo AS costo,
											CONCAT( 'TRA', LPAD( trans_id_transferencia, 7, 0 ) ) AS folio_desc
								FROM		san_articulos
								INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
								INNER JOIN	san_transferencia ON trans_id_consorcio = art_id_consorcio
								AND			trans_id_origen = stk_id_empresa
								AND			trans_id_transferencia = $v_id_transfer
								WHERE		art_id_articulo = $js_id_articulo
								AND			art_id_consorcio = $id_consorcio";
				
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					if( $fila = mysqli_fetch_assoc( $resultado ) )
					{
						if( $v_cantidad <= $fila['stk'] )
						{
							if( $v_movimiento == 'I' )
							{
								if( $v_cantidad > 0 )
								{
									$trand_datos	= array(
										'trand_id_transferencia'	=> $v_id_transfer,
										'trand_id_articulo'			=> $js_id_articulo,
										'trand_cantidad'			=> $v_cantidad,
										'trand_costo'				=> $fila['costo'],
										'trand_precio'				=> $fila['precio']
									);
									
									$query	= construir_insert( 'san_transferencia_detalle', $trand_datos );
								}
								else
								{
									$exito['num'] = 9;
									$exito['msj'] = "No se puede quitar porque no hay artículos en tránsito.";
									break;
								}
							}
							else
							{
								$condicion = "";
								
								if( $v_cantidad < 0 )
									$condicion = "AND trand_cantidad >= ( $v_cantidad * -1 )";
								
								$query	= "	UPDATE	san_transferencia_detalle 
											SET		trand_cantidad = trand_cantidad + $v_cantidad 
											WHERE	trand_id_transferencia = $v_id_transfer 
											AND 	trand_id_articulo = $js_id_articulo
													$condicion";
							}
							
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								if( mysqli_affected_rows( $conexion ) == 1 )
								{
									$query		= "	UPDATE	san_stock
													SET		stk_existencia = stk_existencia - $v_cantidad
													WHERE	stk_id_articulo = $js_id_articulo
													AND		stk_id_empresa IN (	SELECT	trans_id_origen
																				FROM	san_transferencia
																				WHERE	trans_id_transferencia = $v_id_transfer
																				AND		trans_id_consorcio = $id_consorcio )";
									
									$resultado	= mysqli_query( $conexion, $query );
									
									if( $resultado )
									{
										if( mysqli_affected_rows( $conexion ) == 1 )
										{
											$transferidos += $v_cantidad;
											
											$exito['num'] = 1;
											
											if( $transferidos == -1 )
												$exito['msj'] = "Se quitó ".( $transferidos * -1 )." articulo de la transferencia";
											elseif( $transferidos < 0 )
												$exito['msj'] = "Se quitaron ".( $transferidos * -1 )." artículos de la transferencia";
											elseif( $transferidos > 1 )
												$exito['msj'] = "$transferidos artículos transferidos";
											else
												$exito['msj'] = "$transferidos articulo transferido";
										}
										else
										{
											$exito['num'] = 8;
											$exito['msj'] = "No se puede actualizar el stock";
										}
									}
									else
									{
										$exito['num'] = 6;
										$exito['msj'] = "Ocurrió un problema técnico al tratar de actualizar el stock. ".mysqli_error( $conexion );
									}
								}
								else
								{
									$exito['num'] = 7;
									$exito['msj'] = "No se puede realizar la transferencia del articulo del folio $fila[folio_desc]";
								}
							}
							else
							{
								$exito['num'] = 6;
								$exito['msj'] = "Ocurrió un problema técnico al tratar de guardar la transferencia del folio $fila[folio_desc]. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num'] = 5;
							$exito['msj'] = "Solo hay $fila[stk] de $v_cantidad que se quiere transferir en el folio $fila[folio_desc]";
						}
					}
					else
					{
						$exito['num'] = 4;
						$exito['msj'] = "No se puede verificar el stock del articulo seleccionado";
					}
				}
				else
				{
					$exito['num'] = 3;
					$exito['msj'] = "Ocurrió un problema técnico al tratar de verificar el stock. ".mysqli_error( $conexion );
				}
			}
		}
		
		if( $exito )
			if( $exito['num'] != 1 )
				break;
	}
	
	if( !$exito )
	{
		$exito['num'] = 2;
		$exito['msj'] = "No se escribieron cantidades para la transferencia";
	}
	
	if( $exito['num'] == 1 )
		mysqli_commit( $conexion );
	else
		mysqli_rollback( $conexion );
	
	mysqli_close( $conexion );
	
	echo json_encode( $exito );
?>