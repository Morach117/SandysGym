<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar	= isset( $_POST['envio'] ) ? true:false;
	$exito	= array();
	$chk	= false;
	$fecha	= date( 'Y-m-d H:i:s' );
	
	if( $enviar )
	{
			mysqli_autocommit( $conexion, false );
			
			$query		= "	SELECT	ven_id_venta AS id_venta,
									ven_folio AS folio
							FROM	san_venta
							WHERE	ven_id_empresa = $id_empresa
							AND		ven_status = 'R'";
			
			$res_select	= mysqli_query( $conexion, $query );
			
			if( $res_select )
			{
				while( $fila = mysqli_fetch_assoc( $res_select ) )
				{
					$chk = true;
					
					$query		= "	UPDATE	san_venta
									SET		ven_status = 'L',
											ven_total_efectivo = 0,
											ven_fecha = '$fecha',
											ven_id_usuario = $id_usuario
									WHERE	ven_id_venta = $fila[id_venta]
									AND		ven_folio = $fila[folio]
									AND		ven_id_empresa = $id_empresa";
					
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) == 1 )
						{
							$query		= "INSERT INTO san_venta_historico(	venh_id_venta, 
																			venh_folio, 
																			venh_anio, 
																			venh_fecha, 
																			venh_entrega, 
																			venh_total_efectivo, 
																			venh_total_prepago, 
																			venh_total_credito, 
																			venh_total, 
																			venh_tipo_pago, 
																			venh_status, 
																			venh_observaciones, 
																			venh_n_m,
																			venh_id_prepago, 
																			venh_id_socio, 
																			venh_id_usuario, 
																			venh_id_lavador, 
																			venh_id_empresa
																		)
																		( 	SELECT	ven_id_venta, 
																					ven_folio, 
																					ven_anio, 
																					ven_fecha, 
																					ven_entrega, 
																					ven_total_efectivo, 
																					ven_total_prepago, 
																					ven_total_credito, 
																					ven_total, 
																					ven_tipo_pago, 
																					ven_status, 
																					ven_observaciones, 
																					2 AS n_m,
																					ven_id_prepago, 
																					ven_id_socio, 
																					ven_id_usuario, 
																					ven_id_lavador, 
																					ven_id_empresa 
																			FROM 	san_venta 
																			WHERE 	ven_id_venta = $fila[id_venta]
																			AND		ven_folio = $fila[folio]
																			AND		ven_id_empresa = $id_empresa
																		)";
																		
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								if( mysqli_affected_rows( $conexion ) == 1 )
								{
									$exito['num'] = 1;
									$exito['msj'] = "Folios actualizados a LAVADOS.";
								}
								else
								{
									$exito['num'] = 8;
									$exito['msj'] = "No se guardó el historico de un folio RECEPCIONADO.";
									break;
								}
							}
							else
							{
								$exito['num'] = 7;
								$exito['msj'] = "No se puede guardar el historico de un folio RECEPCIONADO. ".mysqli_error( $conexion );
								break;
							}
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "No se actualizó un folio RECEPCIONADO.";
							break;
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "No se puede actualizar un folio RECEPCIONADO. ".mysqli_error( $conexion );
						break;
					}
				}
				
				if( !$chk )
				{
					$exito['num'] = 4;
					$exito['msj'] = "No se encontraron folios RECEPCIONADOS.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se pueden consultar los folios RECEPCIONADOS. ".mysqli_error( $conexion );
			}
	}
	else
	{
		$exito['num'] = 2;
		$exito['msj'] = "No se validó el envió del formulario.";
	}
	
	if( $exito['num'] == 1 )
		mysqli_commit( $conexion );
	else
		mysqli_rollback( $conexion );
	
	mysqli_close( $conexion );
	
	echo json_encode( $exito );
?>