<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_entrega.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$id_venta	= request_var( 'id_venta', 0 );
	$folio		= request_var( 'folio', 0 );
	$pagando	= request_var( 'pagando', 0.0 );
	$por_pagar	= request_var( 'por_pagar', 0.0 );
	$js_status	= request_var( 'b_status', '' );
	$js_cliente	= request_var( 'b_cliente', '' );
	$js_lista	= request_var( 'b_lista', '' ); //1=pagado pendiente|2=pagados
	$js_orden	= request_var( 'b_orden', '' );
	$js_corte	= request_var( 'b_corte', '' );
	$cambio		= 0;
	$credito	= 0;
	$obs		= request_var( 'obs', '' );
	$c_status	= request_var( 'status', 'N' );//si se cambia o no el status, o solo guardar la observacion cuando es N
	$n_mov		= 0;
	$datos		= "";
	$condicion	= "";
	$commit		= false;
	$error		= ":(";
	
	if( $rol == 'S' )
		$fecha		= $js_corte." ".date( 'H:i:s' );
	else
		$fecha		= date( 'Y-m-d H:i:s' );
	
	if( $enviar )
	{
		if( ( $rol == 'S' && validar_fecha( $js_corte, 'YYYY-MM-DD' ) ) || ( $rol != 'S' ) )
		{
			if( $folio && $id_venta )
			{
				mysqli_autocommit( $conexion, false );
				
				if( $c_status == 'S' )
				{
					if( $js_status == 'L' )
					{
						$condicion	= "ven_status = 'E',";
						$n_mov		= 3;
					}
					elseif( $js_status == 'S' )
					{
						$condicion	= "ven_status = 'T',";
						$n_mov		= 2;
					}
				}
					
				if( $pagando && $por_pagar )
				{
					$cambio		= $pagando - $por_pagar;
					
					if( $cambio > 0 || $cambio == 0 )
						$credito = $por_pagar;
					elseif( $cambio < 0 )
						$credito = $pagando;
				}
				
				$credito	= round( $credito, 2 );
				
				$query		= "	UPDATE	san_venta
								SET		ven_observaciones = '$obs',
										ven_total_efectivo = $credito,
										ven_total_credito = round( ven_total_credito - $credito, 2 ),
										$condicion
										ven_fecha = '$fecha',
										ven_id_usuario = $id_usuario
								WHERE	ven_id_venta = $id_venta
								AND		ven_folio = $folio
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
																		venh_id_empresa
																	)
																	( 	SELECT 	ven_id_venta,
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
																				$n_mov AS n_mov,
																				ven_id_prepago,
																				ven_id_socio,
																				ven_id_usuario,
																				ven_id_empresa 
																		FROM 	san_venta 
																		WHERE 	ven_id_venta = $id_venta
																		AND		ven_folio = $folio 
																		AND		ven_id_empresa = $id_empresa
																	)";
																	
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							if( mysqli_affected_rows( $conexion ) == 1 )
							{
								$commit = true;
							}
							else
								$error = "No se pudo guardar el histórico.";
						}
						else
							$error = mysqli_error( $conexion );
					}
					else
						$error = "No se puede actualizar el STATUS.";
				}
				else
					$error = mysqli_error( $conexion );
			}
			else
				$error = "Folio inválido.";
		}
		else
			$error = "Fecha de corte no valido. $fecha - $js_corte";
	}
	else
		$error = "No se valido en envio del formulario.";
	
	if( $commit )
	{
		mysqli_commit( $conexion );
		
		$datos = obtener_lista_para_entrega( $js_orden, $js_lista, $js_cliente, $js_status );
		
		echo $datos;
	}
	else
	{
		mysqli_rollback( $conexion );
		
		echo "$error";
	}
?>