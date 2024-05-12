<?php
	require_once( "../../../funciones_globales/funciones_conexion.php" );
	require_once( "../../../funciones_globales/funciones_comunes.php" );
	require_once( "../../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$folio		= request_var( 'folio', 0 );
	$id_venta	= request_var( 'id_venta', 0 );
	$commit		= false;
	
	mysqli_autocommit( $conexion, false );
	
	if( $enviar )
	{
		if( $folio && $id_venta )
		{
			$query		= "	DELETE 
							FROM 	san_venta_historico
							WHERE	venh_id_empresa = $id_empresa
							AND		venh_id_venta = $id_venta
							AND		venh_folio = $folio
							AND		venh_status IN ( 'E', 'T' )";
			
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( mysqli_affected_rows( $conexion ) == 1 )
				{
					$query		= "	UPDATE		san_venta
									INNER JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
									SET			ven_total_efectivo = venh_total_efectivo,
												ven_total_credito = venh_total_credito,
												ven_fecha = venh_fecha,
												ven_observaciones = venh_observaciones,
												ven_status = venh_status
									WHERE		venh_id_historico = (	SELECT	MAX( venh_id_historico )
																		FROM	san_venta_historico
																		WHERE	venh_id_empresa = $id_empresa
																		AND		venh_id_venta = $id_venta
																		AND		venh_folio = $folio )";
					
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) == 1 )
							$commit = true;
						else
							echo "No se ha actualizado la venta al estado anterior.";
					}
					else
						echo "No se puede actualizar la venta al estado anterior. ".mysqli_error( $conexion );
				}
				else
					echo "No hay filas eliminadas en el historico.";
			}
			else
				echo "No se ha podido hacer la devolución del servicio. ".mysqli_error( $conexion );
		}
		else
			echo "Folio inválido, intenta nuevamente.";
	}
	
	if( $commit )
	{
		mysqli_commit( $conexion );
		echo "Ticket actualizado";
	}
	else
		mysqli_rollback( $conexion );
	
	mysqli_close( $conexion );
?>