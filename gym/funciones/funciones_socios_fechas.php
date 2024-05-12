<?php
	function guardar_fechas()
	{
		global $conexion, $id_usuario, $id_empresa;
		
		$exito		= array();
		$id_pago	= request_var( 'id_pago', 0 );
		$id_socio	= request_var( 'id_socio', 0 );
		$fecha_pago	= explode( '-', request_var( 'pag_fecha_pago', '' ) );//dd-mm-yyyy
		$fecha_ini	= explode( '-', request_var( 'pag_fecha_ini', '' ) );//dd-mm-yyyy
		$fecha_fin	= explode( '-', request_var( 'pag_fecha_fin', '' ) );
		$comentario	= request_var( 'pag_comentario', '' );
		
		$fecha_pago_aux	= "$fecha_pago[2]-$fecha_pago[1]-$fecha_pago[0] ".date( 'H:i:s' );//yyyy-mm-dd
		$fecha_ini_aux	= "$fecha_ini[2]-$fecha_ini[1]-$fecha_ini[0]";//yyyy-mm-dd
		$fecha_fin_aux	= "$fecha_fin[2]-$fecha_fin[1]-$fecha_fin[0]";
		
		mysqli_autocommit( $conexion, false );
		
		if( checkdate( $fecha_pago[1], $fecha_pago[0], $fecha_pago[2] ) && checkdate( $fecha_ini[1], $fecha_ini[0], $fecha_ini[2] ) && checkdate( $fecha_fin[1], $fecha_fin[0], $fecha_fin[2] ) )
		{
			$query		= "	INSERT INTO san_pagos_actualizados( pag_id_pago, pag_fecha_pago, pag_fecha_ini, pag_fecha_fin, pag_id_usuario_a, pag_comentario )
							(
								SELECT	pag_id_pago,
										pag_fecha_pago,
										pag_fecha_ini,
										pag_fecha_fin,
										$id_usuario AS id_usuario,
										'$comentario' AS comentario
								FROM	san_pagos
								WHERE	pag_id_pago = $id_pago
								AND		pag_id_socio = $id_socio
								AND		pag_id_empresa = $id_empresa
							)";
							
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( mysqli_affected_rows( $conexion ) )
				{
					$query		= "	UPDATE	san_pagos
									SET		pag_fecha_pago = '$fecha_pago_aux',
											pag_fecha_ini = '$fecha_ini_aux',
											pag_fecha_fin = '$fecha_fin_aux'
									WHERE	pag_id_pago = $id_pago
									AND		pag_id_socio = $id_socio
									AND		pag_id_empresa = $id_empresa";
									
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) )
						{
							$exito['num'] = 1;
							$exito['msj'] = "Cambio confirmado.";
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "No se actualizo ningún dato porque talvez no se modificaron las fechas.";
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "Ocurrió un problema al tratar de actualizar las fechas. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "No hay datos que guardar en el histórico.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Ocurrió un problema al iniciar la transacción. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Fechas inválidas.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function obtener_fecha_seccionada( $id_socio, $id_pago )
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT		soc_id_socio AS id_socio,
									pag_id_pago AS id_pago,
									soc_nombres AS nombres,
									CONCAT( soc_apepat, ' ', soc_apemat ) AS apellidos,
									DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y' ) AS fecha_pago,
									DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini,
									DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin
						FROM		san_socios
						INNER JOIN	san_pagos ON pag_id_socio = soc_id_socio
						WHERE		soc_id_empresa = $id_empresa
						AND			soc_id_socio = $id_socio
						AND			pag_id_pago = $id_pago";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		else
			echo "Error: ".mysqli_error( $conexion );
		
		return false;
	}
	
	function validar_fechas()
	{
		$validar	= array
		(
			'pag_fecha_ini'		=> array( 'tipo' => 'F',	'max' => 10,	'req' => 'S',	'for' => 'DD-MM-YYYY',	'txt' => 'Cambiar inicio a'),
			'pag_fecha_fin'		=> array( 'tipo' => 'F',	'max' => 10,	'req' => 'S',	'for' => 'DD-MM-YYYY',	'txt' => 'Cambiar vencimiento a')
		);
		
		$exito		= validar_php( $validar );
		
		return $exito;
	}
	
?>