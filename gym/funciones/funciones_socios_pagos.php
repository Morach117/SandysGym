<?php
	function obtener_servicios( $default = '' )
	{
		global $conexion, $id_consorcio, $id_giro;
		
		$datos		= "<option value=''>Selecciona...</option>";
		
		$query		= "	SELECT 	ser_id_servicio AS id_servicio, 
								ser_clave AS clave,
								ser_descripcion AS descripcion,
								ROUND( ser_cuota, 2 ) AS cuota,
								ser_meses AS meses
						FROM 	san_servicios 
						WHERE	ser_tipo = 'PERIODO'
						AND		ser_id_consorcio = $id_consorcio
						AND		ser_id_giro = $id_giro";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$servicio	= $fila['id_servicio'].'-'.$fila['meses'];
				
				if( $default == $servicio )
					$datos	.= "<option selected value='$servicio'>$fila[descripcion] - $$fila[cuota]</option>";
				else
					$datos	.= "<option value='$servicio'>$fila[descripcion] - $$fila[cuota]</option>";
			}
		}
		else
			echo "Error: ".mysqli_error( $conexion );
		
		return $datos;
	}
	
	function obtener_servicio( $id_servicio )
	{
		global $conexion, $id_consorcio, $id_giro;
		
		$query		= "	SELECT 	ser_id_servicio AS id_servicio, 
								ser_clave AS clave,
								ser_descripcion AS descripcion,
								ROUND( ser_cuota, 2 ) AS cuota,
								ser_meses AS meses
						FROM 	san_servicios 
						WHERE 	ser_id_servicio = $id_servicio
						AND		ser_id_consorcio = $id_consorcio
						AND		ser_id_giro = $id_giro";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		
		return false;
	}
	
	function lista_pagos_socio()
	{
		Global $conexion, $id_empresa, $id_consorcio, $id_giro;
		
		$datos		= "";
		$colspan	= 6;
		$fecha_mov	= date( 'Y-m-d' );
		$id_socio	= request_var( 'id_socio', 0 );
		
		$query		= "	SELECT 		pag_id_pago,
									pag_id_socio,
									pag_status AS status,
									ser_descripcion,
									LOWER( DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y %r' ) ) AS fecha_pago,
									DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini,
									DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin,
									ROUND( pag_importe, 2 ) AS importe,
									IF( '$fecha_mov' > pag_fecha_fin, 'VENCIDO', 'VIGENTE' ) AS vigencia
						FROM 		san_pagos 
						INNER JOIN	san_servicios ON ser_id_servicio = pag_id_servicio
						WHERE 		pag_id_socio = $id_socio
						AND			pag_id_empresa = $id_empresa
						AND			ser_id_consorcio = $id_consorcio
						AND			ser_id_giro = $id_giro
						ORDER BY 	pag_id_pago DESC";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['vigencia'] == 'VIGENTE' && $fila['status'] == 'A' )
					$opciones = "<a href='.?s=socios&i=eliminarp&id_pago=$fila[pag_id_pago]&id_socio=$fila[pag_id_socio]'><span class='text-danger glyphicon glyphicon-remove-sign'></span></a>";
				else
					$opciones = "";
				
				$class	= ( $fila['status'] == 'E' ) ? 'danger':'';
				
				$datos	.= "<tr class='$class'>
								<td>$opciones</td>
								<td>$fila[ser_descripcion]</td>
								<td>$fila[fecha_pago]</td>
								<td>$fila[fecha_ini]</td>
								<td>$fila[fecha_fin]</td>
								<td class='text-right'>$$fila[importe]</td>
							</tr>";
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>Ocurrió un error al obtener los datos. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
	function guardar_pago_socio()
	{
		Global $conexion, $id_usuario, $id_empresa, $gbl_key;
		
		$exito							= array();
		$pag_fecha_pago					= fecha_formato_mysql( request_var( 'pag_fecha_pago', date( 'd-m-Y' ) ) );
		$pag_fecha_ini					= fecha_formato_mysql( request_var( 'pag_fecha_ini', '' ) );
		$pag_fecha_fin					= fecha_formato_mysql( request_var( 'pag_fecha_fin', '' ) );
		list( $id_servicio, $meses )	= explode( '-', request_var( 'servicio', '' ) );
		$importe						= request_var( 'pag_importe', 0.0 ); // cuando el admin escribe el monto preferente
		$fecha_mov						= $pag_fecha_pago." ".date( 'H:i:s' );
		$id_socio						= request_var( 'id_socio', 0 );
		$v_porcentaje_comision			= request_var( 'comision', 0.0 );
		$v_metodo_pago					= request_var( 'm_pago', '' ); // E-T
		
		$v_pag_efectivo					= 0;
		$v_pag_tarjeta					= 0;
		$v_monto_comision				= 0;
		
		if( $pag_fecha_ini && $pag_fecha_fin )
		{
			if( $id_servicio && $meses && $id_socio )
			{
				$servicio = obtener_servicio( $id_servicio );
				
				if( $servicio )
				{
					if( ( $servicio['clave'] == 'MEN PARCIAL' && $importe ) ||  $servicio['clave'] != 'MEN PARCIAL' )
					{
						if( $servicio['clave'] != 'MEN PARCIAL' )
							$importe = $servicio['cuota'];
						
						if( $importe )
						{
							if( $importe > 0 )
							{
								if( $v_metodo_pago == 'E' )
									$v_pag_efectivo = $importe;
								else
								{
									$v_pag_tarjeta		= $importe;
									
									if( $v_porcentaje_comision > 0 )
										$v_monto_comision = $importe * ( $v_porcentaje_comision / 100 );
								}
							}
							
							$datos_sql		= array
							(
								'pag_id_socio'		=> $id_socio,
								'pag_fecha_pago'	=> $fecha_mov,
								'pag_id_servicio'	=> $id_servicio,
								'pag_fecha_ini'		=> $pag_fecha_ini,
								'pag_fecha_fin'		=> $pag_fecha_fin,
								'pag_efectivo'		=> $v_pag_efectivo,
								'pag_tarjeta'		=> $v_pag_tarjeta,
								'pag_comision'		=> round( $v_monto_comision, 2 ),
								'pag_importe'		=> round( $importe + $v_monto_comision, 2 ),
								'pag_tipo_pago'		=> $v_metodo_pago,
								'pag_id_usuario'	=> $id_usuario,
								'pag_id_empresa'	=> $id_empresa
							);
							
							$query		= construir_insert( 'san_pagos', $datos_sql );
							$resultado	= mysqli_query( $conexion, $query );
							$id_pago	= mysqli_insert_id( $conexion );
							$token		= hash_hmac( 'md5', $id_pago, $gbl_key );
							
							if( $resultado && $id_pago && $token )
							{
								$foto = subir_fotografia();
								
								//operacion exitosa
								$exito['num'] = 1;
								$exito['msj'] = "Pago y fechas guardados correctamente. ";
								$exito['IDS'] = $id_socio;
								$exito['IDP'] = $id_pago;
								$exito['tkn'] = $token;
							}
							else
							{
								$exito['num']	= 8;
								$exito['msj']	= "No se ha podido guardar la información de este socio. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num']	= 6;
							$exito['msj']	= "No se puede obtener el importe del servicio seleccionado.";
						}
					}
					else
					{
						$exito['num']	= 5;
						$exito['msj']	= "Se ha detectado Servicio Parcial pero no se ha indicado el importe a pagar.";
					}
				}
				else
				{
					$exito['num']	= 4;
					$exito['msj']	= "No se puede identificar el tipo de servicio seleccionado.";
				}
			}
			else
			{
				$exito['num']	= 3;
				$exito['msj']	= "Faltan datos importantes para guardar el pago.";
			}
		}
		else
		{
			$exito['num']	= 2;
			$exito['msj']	= "Fechas inválidas.";
		}
		
		return $exito;
	}
	
?>