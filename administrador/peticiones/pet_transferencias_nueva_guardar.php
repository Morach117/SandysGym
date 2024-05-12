<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$js_origen	= request_var( 't_origen', 0 );
	$js_destino	= request_var( 't_destino', 0 );
	$js_entrega	= request_var( 't_entrega', '' );
	$id_transf	= 0;
	$fecha_mov	= date( 'Y-m-d H:i:s' );
	$exito		= array();
	
	if( $enviar )
	{
		if( $js_origen && $js_destino )
		{
			if( $js_origen != $js_destino )
			{
				if( validar_fecha( $js_entrega, 'YYYY-MM-DD' ) )
				{
					mysqli_autocommit( $conexion, false );	//se comnienza la transaccion
					
					$san_transfer	= array(
						'trans_id_usuario'		=> $id_usuario,
						'trans_id_consorcio'	=> $id_consorcio,
						'trans_id_origen'		=> $js_origen,
						'trans_id_destino'		=> $js_destino,
						'trans_status'			=> 'A',
						'trans_fecha'			=> $fecha_mov,
						'trans_fecha_entrega'	=> $js_entrega
					);
					
					$query		= construir_insert( 'san_transferencia', $san_transfer );
					$resultado	= mysqli_query( $conexion, $query );
					$id_transf	= mysqli_insert_id( $conexion );
					
					if( $resultado && $id_transf )
					{
						$san_transferh	= array(
							'tranh_id_transferencia'	=> $id_transf,
							'tranh_id_usuario'			=> $id_usuario,
							'tranh_status'				=> 'A',
							'tranh_fecha'				=> $fecha_mov
						);
						
						$query		= construir_insert( 'san_transferencia_historico', $san_transferh );
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							$t_desc	= "TRA".str_pad( $id_transf, 7, '0', STR_PAD_LEFT );
							$exito['num'] = 1;
							$exito['msj'] = "Transferencia $t_desc creado.";
						}
						else
						{
							$exito['num'] = 7;
							$exito['msj'] = "Error. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 6;
						$exito['msj'] = "Error. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 5;
					$exito['msj'] = "Fecha incorrecta.";
				}
			}
			else
			{
				$exito['num'] = 4;
				$exito['msj'] = "No se debe seleccionar mismo origen-destino.";
			}
		}
		else
		{
			$exito['num'] = 3;
			$exito['msj'] = "Se debe seleccionar origen y destino.";
		}
	}
	else
	{
		$exito['num'] = 2;
		$exito['msj'] = "Acción no permitida.";
	}
	
	if( $exito['num'] == 1 )
		mysqli_commit( $conexion );
	else
		mysqli_rollback( $conexion );
	
	mysqli_close( $conexion );
	
	echo json_encode( $exito );
?>