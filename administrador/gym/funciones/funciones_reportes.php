<?php
	function obtener_importe_mensualidades( $mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$exito['total']		= 0;
		$exito['efectivo']	= 0;
		$exito['tar_com']	= 0;
		$exito['tarjeta']	= 0;
		$exito['comision']	= 0;
		
		if( $tipo_corte == 'A' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pag_fecha_pago, '%Y' )";
		elseif( $tipo_corte == 'M' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pag_fecha_pago, '%m-%Y' )";
		else
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y' )";
		
		if( $p_id_cajero )
			$condicion .= " AND pag_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT	ROUND( total, 2 ) AS total,
								ROUND( efectivo, 2 ) AS efectivo,
								ROUND( tarjeta + comision, 2 ) AS tar_com,
								ROUND( tarjeta, 2 ) AS tarjeta,
								ROUND( comision, 2 ) AS comision
						FROM
						(
							SELECT 		SUM( pag_importe ) AS total,
										SUM( pag_efectivo ) AS efectivo,
										SUM( pag_tarjeta ) AS tarjeta,
										SUM( pag_comision ) AS comision
							FROM 		san_pagos
							INNER JOIN	san_servicios ON ser_id_servicio = pag_id_servicio
							WHERE 		pag_status = 'A'
										$condicion
							AND			pag_id_empresa = $id_empresa
						) a";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total']		= $fila['total'];
				$exito['efectivo']	= $fila['efectivo'];
				$exito['tar_com']	= $fila['tar_com'];
				$exito['tarjeta']	= $fila['tarjeta'];
				$exito['comision']	= $fila['comision'];
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe de las mensualidades.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el importe de las mensualidades. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_importe_por_horas( $desc = 'HORA', $mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$condicion	= "";
		$exito		= array();
		$exito['total']		= 0;
		$exito['efectivo']	= 0;
		$exito['tar_com']	= 0;
		$exito['tarjeta']	= 0;
		$exito['comision']	= 0;
		
		if( $desc == 'HORA' )
			$condicion .= " AND ser_clave = 'HORA' ";
		else
			$condicion .= " AND ser_clave = 'VISITA' ";
		
		if( $tipo_corte == 'A' )
			$condicion .= " AND '$mes_ganancia' = DATE_FORMAT( hor_fecha, '%Y' ) ";
		elseif( $tipo_corte == 'M' )
			$condicion .= " AND '$mes_ganancia' = DATE_FORMAT( hor_fecha, '%m-%Y' ) ";
		else
			$condicion .= " AND '$mes_ganancia' = DATE_FORMAT( hor_fecha, '%d-%m-%Y' ) ";
		
		if( $p_id_cajero )
			$condicion .= " AND hor_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT		ROUND( SUM( hor_importe ), 2 ) AS total,
									ROUND( SUM( hor_efectivo ), 2 ) AS efectivo,
									ROUND( SUM( hor_tarjeta + hor_comision ), 2 ) AS tar_com,
									ROUND( SUM( hor_tarjeta ), 2 ) AS tarjeta,
									ROUND( SUM( hor_comision ), 2 ) AS comision
						FROM		san_horas
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						AND			ser_tipo = 'PARCIAL'
						AND			ser_descripcion = '$desc'
						WHERE		hor_status = 'A'
									$condicion
						AND			hor_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total']		= $fila['total'];
				$exito['efectivo']	= $fila['efectivo'];
				$exito['tar_com']	= $fila['tar_com'];
				$exito['tarjeta']	= $fila['tarjeta'];
				$exito['comision']	= $fila['comision'];
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe por horas.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el importe por horas. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_importe_prepago($mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0) {
		global $conexion, $id_empresa;
	
		$exito = array();
		$exito['total'] = 0;
		$exito['efectivo'] = 0;
		$exito['tar_com'] = 0;
		$exito['tarjeta'] = 0;
		$exito['comision'] = 0;
	
		if ($tipo_corte == 'A')
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT(pred_fecha, '%Y')";
		elseif ($tipo_corte == 'M')
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT(pred_fecha, '%m-%Y')";
		else
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT(pred_fecha, '%d-%m-%Y')";
	
		if ($p_id_cajero)
			$condicion .= " AND pred_id_usuario = $p_id_cajero";
	
		$query = "SELECT ROUND(IF(total > 0, total, 0)) AS total
				  FROM (
					  SELECT SUM(IF(pred_movimiento = 'S', pred_importe, 0)) - SUM(IF(pred_movimiento = 'R', pred_importe, 0)) AS total
					  FROM san_prepago_detalle
					  WHERE pred_id_socio IN (
						  SELECT prep_id_socio
						  FROM san_prepago
						  WHERE prep_id_empresa = $id_empresa
					  )
					  $condicion
				  ) a";
							
		$resultado = mysqli_query($conexion, $query);
	
		if ($resultado) {
			if ($fila = mysqli_fetch_assoc($resultado)) {
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total'] = $fila['total'];
				$exito['efectivo'] = $fila['total'];
			} else {
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe por prepago.";
			}
		} else {
			$exito['num'] = 3;
			$exito['msj'] = "Ocurrió un problema al tratar de obtener el importe por prepago. " . mysqli_error($conexion);
		}
	
		return $exito;
	}
	
	
	function obtener_importe_venta_efectivo( $mes_ganancia = '', $tipo_corte = 'D', $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$exito['total']		= 0;
		$exito['efectivo']	= 0;
		$exito['tar_com']	= 0;
		$exito['tarjeta']	= 0;
		$exito['comision']	= 0;
		
		if( $tipo_corte == 'A' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( ven_fecha, '%Y' )";
		elseif( $tipo_corte == 'M' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( ven_fecha, '%m-%Y' )";
		else
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( ven_fecha, '%d-%m-%Y' )";
		
		if( $p_id_cajero )
			$condicion .= " AND ven_id_usuario = $p_id_cajero ";
		
		$query		= "	SELECT	ROUND( SUM( ven_total ), 2 ) AS total,
								ROUND( SUM( ven_total_efectivo ), 2 ) AS efectivo,
								ROUND( SUM( ven_total_tarjeta + ven_comision ), 2 ) AS tar_com,
								ROUND( SUM( ven_total_tarjeta ), 2 ) AS tarjeta,
								ROUND( SUM( ven_comision ), 2 ) AS comision
						FROM	san_venta
						WHERE	ven_status = 'V'
						AND		ven_id_empresa = $id_empresa
								$condicion";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Se obtuvieron los datos del corte.";
				$exito['total']		= $fila['total'];
				$exito['efectivo']	= $fila['efectivo'];
				$exito['tar_com']	= $fila['tar_com'];
				$exito['tarjeta']	= $fila['tarjeta'];
				$exito['comision']	= $fila['comision'];
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el importe por la Venta en Efectivo.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener el importe por la Venta en Efectivo. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_gastos( $mes_ganancia = '', $tipo_corte = 'D' )
	{
		global $conexion, $id_empresa;
		
		$exito		= array();
		$condicion	= '';
		
		if( $tipo_corte == 'A' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( gas_fecha_fnota, '%Y' )";
		elseif( $tipo_corte == 'M' )
			$condicion = "AND '$mes_ganancia' = DATE_FORMAT( gas_fecha_fnota, '%m-%Y' )";
		
		$query		= "	SELECT	SUM( gas_importe ) AS importe,
								SUM( gas_iva ) AS iva,
								SUM( gas_descuento ) AS descuento,
								SUM( gas_total ) AS total
						FROM	san_gastos
						WHERE	gas_id_empresa = $id_empresa
								$condicion";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = $fila;
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se pudo obtener el total de gastos.";
			}
		}
		else
		{
			$exito['num']	= 3;
			$exito['msj']	= "Ocurrió un problema al tratar de obtener los gastos. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}

	
	function nombre_archivo_imagen( $id_socio )
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT soc_imagen FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				if( $fila['soc_imagen'] )
					return $fila['soc_imagen'];
			
		return 'Sin nombre de imagen...';
	}
		
	function obtener_datos_socio()
	{
		Global $conexion, $id_empresa;
		
		$id_socio	= request_var( 'id_socio', 0 );
		
		$query		= "SELECT * FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		
		return false;
	}
	
	/*
	tipo	-> T=texto, N=numerico, C=correo, F=fecha
	max		-> longitud maxima del campo
	txt		-> texto o descripcion para mostrar un mensaje acerca de este campo
	req		-> obligatoriedad(S,N)
	*/
	
	function subir_fotografia()
	{
		global $conexion, $id_empresa;
		
		$id_socio			= request_var( 'id_socio', 0 );
		
		$dir_ponencias		= "../imagenes/avatar/";
		$extenciones		= "/^\.(jpg){1}$/i";
		$tamaño_maximo		= 2 * 1024 * 1024;
		$exito				= array();
		$imagen_guardada	= "";
		
		if( isset( $_FILES['avatar'] ) && $_FILES['avatar']['name'] && $id_socio )
		{
			$extencion_archivo	= tipo_archivo( $_FILES['avatar']['type'] );
			$nombre_archivo		= $id_socio.$extencion_archivo;
			$valido				= is_uploaded_file($_FILES['avatar']['tmp_name']); 
			
			if( $valido )
			{
				$safe_filename = preg_replace( array( "/\s+/", "/[^-\.\w]+/" ), array( "_", "" ), trim( $_FILES['avatar']['name'] ) );
				
				if( $extencion_archivo && $_FILES['avatar']['size'] <= $tamaño_maximo && preg_match( $extenciones, strrchr( $safe_filename, '.' ) ) )
				{
					if( move_uploaded_file ( $_FILES['avatar']['tmp_name'], $dir_ponencias.$nombre_archivo ) )
					{
						$query		= "SELECT soc_id_socio FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							list( $bandera ) = mysqli_fetch_row( $resultado );
							$imagen_nombre	= $_FILES['avatar']['name'];
							
							if( $bandera )
								$query	= "UPDATE san_socios SET soc_imagen = '$imagen_nombre' WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
							else
								$query	= "INSERT INTO san_socios ( soc_imagen ) VALUES ( '$imagen_nombre' )";
							
							$resultado	= mysqli_query( $conexion, $query );
						}
						
						$exito['num'] = 1;
						$exito['msj'] = 'Fotografía guardada.';
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = 'La fotografía no se ha guardado.<br/>';
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = 'La fotografía no es del tipo solicitado o excede el tamaño permitido.';
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = 'No es archivo válido.';
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = 'No se selecciono un archivo para la Fotografía.';
		}
		
		return $exito;
	}
	
	function eliminar_fotografia()
	{
		global $id_socio;
		
		if( file_exists( "../../imagenes/avatar/$id_socio.jpg" ) )
			if( unlink( "../../imagenes/avatar/$id_socio.jpg" ) )
				return true;
		
		return false;
	}
	
?>