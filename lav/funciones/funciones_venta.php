<?php
	function preocesar_venta( $array_cant_id, $total_a_pagar, $efectivo, $credito, $id_socio, $obs, $f_entrega, $tipo, $p_iva_por, $p_iva_monto, $forzar_promo = 'N', $p_metodo_pago, $p_tot_tarjeta, $p_monto_comision )
	{
		global $conexion, $id_usuario, $id_empresa, $id_consorcio, $rol;
		
		$continuar	= false;
		$socio_deta	= array();
		$artic_deta	= array();
		$folio		= array();
		$id_venta	= 0;
		$exito		= array();
		$movimiento	= date( 'Y-m-d H:i:s' );
		$ven_status	= "";
		$condicion1	= "";
		$condicion2	= "";
		$v_tipo_pago= "";
		
		//comienza la transaccion
		mysqli_autocommit( $conexion, false );
		
		$entrega	= fecha_entrega( $tipo, $f_entrega );
		$folio		= nuevo_folio();
		
		if( $forzar_promo == 'S' )
		{
			if( $rol == 'S' )
				$condicion1 = " OR 1 = 1 ";
			else
				$condicion2 = " AND 1 = 2 ";
		}
		
		if( $tipo == 'LAVANDERIA' || $tipo == 'EDREDONES' )
			$ven_status = 'R';
		elseif( $tipo == 'PLANCHADURIA' )
			$ven_status = 'S';
		
		$efectivo			= round( $efectivo, 2 );
		$total_a_pagar		= round( $total_a_pagar, 2 );
		$credito			= round( $credito, 2 );
		$p_tot_tarjeta		= round( $p_tot_tarjeta, 2 );
		$p_monto_comision	= round( $p_monto_comision, 2 );
		
		if( $credito > 0 )
			$v_tipo_pago = 'C';
		else
			$v_tipo_pago = $p_metodo_pago;
		
		/*se inserta la venta*/
		$datos_sql	= array
		(
			'ven_folio'				=> $folio['folio'],
			'ven_anio'				=> $folio['anio'],
			'ven_fecha'				=> $movimiento,
			'ven_entrega'			=> $entrega['fechag']." ".$entrega['horag'],
			'ven_total_efectivo'	=> $efectivo,
			'ven_total_tarjeta'		=> $p_tot_tarjeta,
			'ven_comision'			=> $p_monto_comision,
			'ven_total_credito'		=> $credito,
			'ven_iva_por'			=> $p_iva_por,
			'ven_iva_monto'			=> $p_iva_monto,
			'ven_total'				=> round( $total_a_pagar + $p_monto_comision, 2 ),
			'ven_tipo_pago'			=> $v_tipo_pago,
			'ven_status'			=> $ven_status,
			'ven_observaciones'		=> $obs,
			'ven_id_socio'			=> $id_socio,
			'ven_id_usuario'		=> $id_usuario,
			'ven_id_empresa'		=> $id_empresa
		);
		
		if( $tipo == 'LAVANDERIA' || $tipo == 'PLANCHADURIA' || $tipo == 'EDREDONES' )
		{
			$query		= construir_insert( 'san_venta', $datos_sql );
			$resultado	= mysqli_query( $conexion, $query );
			$id_venta	= mysqli_insert_id( $conexion );
			
			if( $resultado && $folio['folio'] && $folio['anio'] && $id_venta )
			{
				$datos_sql	= array
				(
					'venh_id_venta'			=> $id_venta,
					'venh_folio'			=> $folio['folio'],
					'venh_anio'				=> $folio['anio'],
					'venh_fecha'			=> $movimiento,
					'venh_entrega'			=> $entrega['fechag']." ".$entrega['horag'],
					'venh_total_efectivo'	=> $efectivo,
					'venh_total_tarjeta'	=> $p_tot_tarjeta,
					'venh_comision'			=> $p_monto_comision,
					'venh_total_credito'	=> $credito,
					'venh_iva_por'			=> $p_iva_por,
					'venh_iva_monto'		=> $p_iva_monto,
					'venh_total'			=> round( $total_a_pagar + $p_monto_comision, 2 ),
					'venh_tipo_pago'		=> $v_tipo_pago,
					'venh_status'			=> $ven_status,
					'venh_observaciones'	=> $obs,
					'venh_n_m'				=> 1,
					'venh_id_socio'			=> $id_socio,
					'venh_id_usuario'		=> $id_usuario,
					'venh_id_empresa'		=> $id_empresa
				);
				
				/*se inserta el historico de la venta*/
				$query		= construir_insert( 'san_venta_historico', $datos_sql );
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					foreach( $array_cant_id as $cant_id )
					{
						list( $cantidad, $id_servicio ) = explode( '-', $cant_id );
						
						$query		= "	SELECT		src_kg_minimo AS minimo,
													ROUND( IF( DAYOFWEEK( CURDATE() ) = src_promo_dia $condicion1, src_promo_cuota, src_cuota  ), 2 ) AS precio
										FROM		san_servicios
										INNER JOIN	san_servicios_cuotas ON src_id_servicio = ser_id_servicio
										AND			src_id_empresa = $id_empresa
										WHERE		ser_id_servicio = $id_servicio
										AND			ser_id_giro = 2
										AND			ser_tipo = '$tipo'
										AND			ser_id_consorcio = $id_consorcio
										AND			ser_status = 'A'
													$condicion2";
										
										$x = $query;
						$resultado	= mysqli_query( $conexion, $query );
						list( $minimo, $precio ) = mysqli_fetch_row( $resultado );
						
						$datos_sql	= array
						(
							'vense_id_venta'	=> $id_venta,
							'vense_kilogramo'	=> $cantidad,
							'vense_precio'		=> $precio,
							'vense_importe'		=> ( ( $minimo > $cantidad ) ? $minimo:$cantidad ) * $precio,
							'vense_id_servicio'	=> $id_servicio,
						);
						
						$query		= construir_insert( 'san_venta_servicio', $datos_sql );
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							$exito['num']	= 1;
							$exito['msj']	= $folio['folio'];
							$exito['anio']	= $folio['anio'];
							$exito['tkt']	= $folio['ticket'];
							$exito['imp']	= $folio['impresora'];
							$exito['soc']	= $id_socio;
							$exito['idv']	= $id_venta;
						}
						else
						{
							$exito['num']	= 6;
							$exito['msj']	= "No se ha terminado la venta.  No se puede guardar el detalle de la Venta. ".mysqli_error( $conexion );
							break;
						}
					}
				}
				else
				{
					$exito['num']	= 7;
					$exito['msj']	= "No se ha terminado la venta. No se pudo guardar el historico. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num']	= 8;
				$exito['msj']	= "No se ha terminado la venta. No se pudo guardar la Venta o no se pudo obtener el Folio de la Venta. ".mysqli_error( $conexion );
			}
		}
		else
		{
			
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function fecha_entrega( $p_tipo = '', $f_entrega = '' )
	{
		$resultado	= array();
		
		//dias de la semana 0=domingo, 3=miercoles, 6=sabado
		$dia_actual	= date( 'w' );
		
		if( $f_entrega ) // cuando se selecciona una fecha, la hora de entrega sera:
		{
			$resultado['fechag']	= date( 'Y-m-d', strtotime( $f_entrega ) );
			$dia_actual				= date( 'w', strtotime( $f_entrega ) );
			
			if( $dia_actual == 6 )
			{
				$resultado['hora']		= '6:00 pm';
				$resultado['horag']		= '18:00:00';
			}
			else
			{
				$resultado['hora']		= '7:00 pm';
				$resultado['horag']		= '19:00:00';
			}
		}
		else // entrando como no hay fecha se calcula ambos
		{
			$resultado['hora']		= '7:00 pm';
			$resultado['horag']		= '19:00:00';
			
			if( $dia_actual == 3 && ( $p_tipo == 'EDREDONES' || $p_tipo == 'PEDREDONES' ) )//recepcion de miercoles en edredones se va a lunes
			{
				$resultado['fecha']		= date( 'd-m-Y', strtotime( '+5 day' ) );
				$resultado['fechag']	= date( 'Y-m-d', strtotime( '+5 day' ) );
			}
			elseif( $dia_actual >= 0 && $dia_actual <= 4 )//recepcion de domingo a jueves
			{
				$resultado['fecha']		= date( 'd-m-Y', strtotime( '+1 day' ) );
				$resultado['fechag']	= date( 'Y-m-d', strtotime( '+1 day' ) );
			}
			elseif( $dia_actual == 5 ) //recepcion de viernes se entrega sabado
			{
				$resultado['fecha']		= date( 'd-m-Y', strtotime( '+1 day' ) );
				$resultado['fechag']	= date( 'Y-m-d', strtotime( '+1 day' ) );
				
				$resultado['hora']		= '6:00 pm';
				$resultado['horag']		= '18:00:00';
			}
			elseif( $dia_actual == 6 )//recepcion de sabado se entrega lunes
			{
				$resultado['fecha']		= date( 'd-m-Y', strtotime( '+2 day' ) );
				$resultado['fechag']	= date( 'Y-m-d', strtotime( '+2 day' ) );
			}
		}
		
		return $resultado;
	}
	
	function obtener_servicios( $p_tipo, $forzar_promo = 'N' )
	{
		Global $conexion, $id_giro, $id_consorcio, $id_empresa, $rol;
		
		$datos		= "";
		$condicion1	= "";
		$condicion2	= "";
		
		if( $forzar_promo == 'S' )
		{
			if( $rol == 'S' )
				$condicion1 = " OR 1 = 1 ";
			else
				$condicion2 = " AND 1 = 2 ";
		}
		
		$query		= "	SELECT 		ser_id_servicio AS id_servicio,
									ser_descripcion AS descripcion,
									ser_orden AS orden,
									IF( 
										src_kg_minimo > 1, 
											CONCAT( 'MINIMO: ', ROUND( src_kg_minimo, 2 ), IF(
																								ser_tipo = 'PLANCHADURIA', 
																									' PZS', 
																									' KGS'
																								) ), 
											'' 
										) AS notas,
									IF( DAYOFWEEK( CURDATE() ) = src_promo_dia $condicion1, 'S', 'N' ) AS promo,
									ROUND( src_cuota, 2 ) AS cuota,
									ROUND( IF( DAYOFWEEK( CURDATE() ) = src_promo_dia $condicion1, src_promo_cuota, src_cuota  ), 2 ) AS prom_cuota
						FROM		san_servicios
						INNER JOIN	san_servicios_cuotas ON src_id_servicio = ser_id_servicio
						AND			src_id_empresa = $id_empresa
						WHERE		ser_id_giro = 2
						AND			ser_status = 'A'
						AND			ser_tipo = '$p_tipo'
						AND			ser_id_consorcio = $id_consorcio
									$condicion2
						ORDER BY	orden,
									descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['promo'] == 'S' )
				{
					$datos .= "	<li onclick='agregar_servicio_venta( $fila[id_servicio] )'>
									<span class='glyphicon glyphicon-glass'></span>
									<h4>
										<s>$$fila[cuota]</s> <strong>$$fila[prom_cuota]</strong>
									</h4>
									<span class='touch-class'>$fila[descripcion]</span>
									<span class='touch-class'>$fila[notas]</span>
								</li>";
				}
				else
				{
					$datos .= "	<li onclick='agregar_servicio_venta( $fila[id_servicio] )'>
									<span class='glyphicon glyphicon-glass'></span>
									<h4><strong>$$fila[cuota]</strong></h4>
									<span class='touch-class'>$fila[descripcion]</span>
									<span class='touch-class'>$fila[notas]</span>
								</li>";
				}
				
				$i++;
			}
		}
		else
		{
			$datos	= "	<li>
							<span class='glyphicon glyphicon-question-sign'></span>
							<h4><strong>$00.00</strong></h4>
							<span class='touch-class'>Ocurrió un error al realizar la consulta.</span>
							<span class='touch-class'>".mysqli_error( $conexion )."</span>
						</li>";
		}
		
		if( !$datos )
		{
			$datos	= "	<li>
							<span class='glyphicon glyphicon-question-sign'></span>
							<h4><strong>$00.00</strong></h4>
							<span class='touch-class'>No hay datos.</span>
							<span class='touch-class'>:/</span>
						</li>";
		}
		
		return $datos;
	}
	
	function obtener_por_iva()
	{
		global $conexion, $id_consorcio;
		
		$iva	= 0;
		
		$query		= "SELECT con_iva FROM san_consorcios WHERE con_id_consorcio = $id_consorcio";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			list( $iva ) = mysqli_fetch_row( $resultado );
		
		return $iva;
	}
	
?>