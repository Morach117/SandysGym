<?php
	function obtener_detalle_pago( $id_socio, $id_pago )
	{
		Global $conexion, $id_empresa, $id_usuario, $id_consorcio;
		
		$exito		= array();
		$fecha_mov	= date( 'Y-m-d H:i:s' );
		
		$query		= "	SELECT 	DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y %r' ) AS f_pago,
								DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ) AS f_ini,
								DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) AS f_fin,
								pag_importe As importe
						FROM 	san_pagos 
						WHERE 	pag_id_pago = $id_pago 
						AND 	pag_id_socio = $id_socio 
						AND 	pag_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		
		return false;
	}
	
	function eliminar_pago_socio( $id_socio, $id_pago )
	{
		Global $conexion, $id_empresa, $id_usuario, $id_consorcio;
		
		$exito		= array();
		$fecha_mov	= date( 'Y-m-d H:i:s' );
		
		$query		= "	UPDATE 	san_pagos
						SET		pag_status = 'E',
								pag_id_usuario_e = $id_usuario,
								pag_fecha_e = '$fecha_mov'
						WHERE	pag_id_pago = $id_pago
						AND		pag_id_socio = $id_socio
						AND		pag_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( mysqli_affected_rows( $conexion ) )
			{
				$exito['num']	= 1;
				$exito['msj']	= "Pago eliminando";
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se ha podido guardar el detalle del registro que se esta eliminando.";
			}
		}
		else
		{
			$exito['num']	= 2;
			$exito['msj']	= "No se puede iniciar la transacción para eliminar. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
?>