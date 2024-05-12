<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_lavado.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$id_venta	= request_var( 'id_venta', 0 );
	$folio		= request_var( 'folio', 0 );
	$pagando	= request_var( 'pagando', 0.0 );
	$por_pagar	= request_var( 'por_pagar', 0.0 );
	$cambio		= 0;
	$credito	= 0;
	$lavador	= request_var( 'lavador', 0 );
	$obs		= request_var( 'obs', '' );
	$status		= request_var( 'status', 'N' );//si se cambia o no el status o solo se guarda la observacion
	$n_mov		= 0;
	$datos		= "";
	$condicion	= "";
	$commit		= false;
	$error		= ":(";
	$fecha		= date( 'Y-m-d H:i:s' );
	
	if( $enviar )
	{
		if( $folio && $id_venta )
		{
			mysqli_autocommit( $conexion, false );
			
			if( $status == 'S' )
			{
				$condicion	= "ven_status = 'L', ";
				$n_mov		= 2;
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
									ven_id_lavador = $lavador,
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
																			$n_mov AS n_m,
																			ven_id_prepago, 
																			ven_id_socio, 
																			ven_id_usuario, 
																			ven_id_lavador, 
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
							$error = "1. No se guardo el histórico.";
					}
					else
						$error = "2. ".mysqli_error( $conexion );
				}
				else
					$error = "3. No se realizó ningún cambio.";
			}
			else
				$error = "4. ".mysqli_error( $conexion );
		}
		else
			$error = "5. Folio inválido.";
	}
	else
		$error = "6. No se valido el envio del formulario.";
	
	if( $commit )
	{
		mysqli_commit( $conexion );
		$datos = obtener_lavados();
		
		echo $datos;
	}
	else
	{
		mysqli_rollback( $conexion );
		echo $error;
	}
	
	mysqli_close( $conexion );
?>