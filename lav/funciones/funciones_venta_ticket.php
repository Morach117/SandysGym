<?php
	function detalle_empresa()
	{
		global $conexion, $id_empresa;
		
		$query		= "	SELECT	emp_id_empresa AS id_empresa,
								emp_descripcion AS descripcion,
								emp_abreviatura AS abr,
								emp_direccion AS direccion,
								emp_colonia AS colonia,
								emp_ciudad AS ciudad,
								emp_telefono AS telefono
						FROM	san_empresas
						WHERE	emp_id_empresa = $id_empresa
						AND		emp_status = 'A'";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		
		return false;
	}
	
	function detalle_ticket( $folio, $id_venta )
	{
		global $conexion, $id_empresa, $id_consorcio;
		$datos		= array();
		
		$query		= "	SELECT		ser_descripcion AS descripcion,
									ROUND( vense_kilogramo, 2 ) AS kilo,
									ROUND( vense_precio, 2 ) AS precio,
									ROUND( vense_importe, 2 ) AS importe
						FROM		san_venta
						INNER JOIN	san_venta_servicio ON vense_id_venta = ven_id_venta
						INNER JOIN	san_servicios ON ser_id_servicio = vense_id_servicio
						WHERE		ven_id_venta = $id_venta
						AND			ven_folio = $folio
						AND			ven_id_empresa = $id_empresa
						AND			ser_id_consorcio = $id_consorcio";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		}
		else
		{
			echo mysqli_error( $conexion );
			die();
		}
		
		return $datos;
	}
	
	function venta_ticket( $folio, $id_venta )
	{
		global $conexion, $id_empresa, $id_consorcio;
		
		$query		= "	SELECT		soc_id_socio AS id_socio,
									DATE_FORMAT( ven_fecha, '%d-%m-%Y' ) AS f_recepcion,
									DATE_FORMAT( ven_entrega, '%d-%m-%Y' ) AS f_entrega,
									DATE_FORMAT( ven_fecha, '%r' ) AS h_recepcion,
									DATE_FORMAT( ven_entrega, '%r' ) AS h_entrega,
									UPPER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) AS nombres,
									IF( soc_tel_fijo, soc_tel_fijo, '-/-') AS telfijo,
									IF( soc_tel_cel, soc_tel_cel, '-/-') AS telcel,
									ven_iva_monto AS iva_monto,
									ven_tipo_pago AS tipo_pago,
									ROUND( ven_total_efectivo, 2 ) AS efectivo,
									ROUND( ven_total_tarjeta + ven_comision, 2 ) AS tar_com,
									ROUND( ven_total_credito, 2 ) AS credito,
									ROUND( ven_total, 2 ) AS total,
									ven_observaciones AS obs
						FROM		san_venta
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = ven_id_empresa
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						AND			soc_id_empresa = ven_id_empresa
						WHERE		ven_id_venta = $id_venta
						AND			ven_folio = $folio
						AND			ven_id_empresa = $id_empresa
						AND			coem_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		else
		{
			echo mysqli_error( $conexion );
			die();
		}
		
		return false;
	}
	
?>