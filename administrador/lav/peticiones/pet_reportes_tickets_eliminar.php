<?php
	require_once( "../../../funciones_globales/funciones_conexion.php" );
	require_once( "../../../funciones_globales/funciones_comunes.php" );
	require_once( "../../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$folio		= request_var( 'folio', 0 );
	$id_venta	= request_var( 'id_venta', 0 );
	$fecha_mov	= date( 'Y-m-d H:i:s' );
	$commit		= false;
	
	mysqli_autocommit( $conexion, false );
	
	if( $enviar )
	{
		if( $folio && $id_venta )
		{
			$query		= "	UPDATE	san_venta 
							SET		ven_status = 'C',
									ven_fecha = '$fecha_mov',
									ven_id_usuario = $id_usuario
							WHERE 	ven_id_venta = $id_venta
							AND		ven_folio = $folio 
							AND 	ven_status IN ( 'R', 'S' )
							AND 	ven_id_empresa = $id_empresa";
			
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
																			4 AS n_mov,
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
							$commit = true;
						else
							echo "No se ha guardado el histórico.";
					}
					else
						echo "No se puede guardar el histórico. ".mysqli_error( $conexion );
				}
				else
					echo "No hay filas para CANCELAR en la venta. Verifica que el folio este con status de RECEPCIONADO.";
			}
			else
				echo "No se ha podido CANCELAR la venta de servicios. ".mysqli_error( $conexion );

		}
		else
			echo "Folio inválido, intenta nuevamente.";
	}
	
	if( $commit )
		mysqli_commit( $conexion );
	else
		mysqli_rollback( $conexion );
	
	mysqli_close( $conexion );
?>