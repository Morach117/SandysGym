<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_venta.php" );
	
	/*desde javascript*/
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$id_servicios	= request_var( 'id_servicios', '' );
	$id_socio		= request_var( 'id_socio', 0 );
	$kilos			= request_var( 'kilos', '' );
	$obs			= request_var( 'obs', '' );
	$f_entrega		= request_var( 'f_entrega', '' );
	$v_efectivo		= request_var( 'efectivo', 0.0 );/*dinero dado en efectivo*/
	$js_tot_a_pagar	= request_var( 'total_a_pagar', 0.0 );/*CANTIDAD A PAGAR*/
	$js_descuento	= request_var( 'descuento', 0.0 );
	$commit			= request_var( 'commit', 'N' );/*indica si se termina o no la venta*/
	$tipo			= request_var( 'tipo', '' ); /*LAVANDERIA o PLANCHADURIA, PEDREDONES*/
	
	$js_metodo_pago	= request_var( 'chk_tipo_pago', '' );
	$js_pag_tarjeta	= request_var( 'pag_tarjeta', 0.0 );
	$js_pag_comision= request_var( 'pag_comision', 0.0 );
	$v_comision		= 0;
	
	$js_iva_cobrar	= request_var( 'iva_cobro', 'N' );
	$js_iva_monto	= request_var( 'iva_monto', 0.0 );
	$v_iva_por		= obtener_por_iva();
	$v_iva_monto	= 0;
	
	/*propias del codigo del modal*/
	$pet_cambio		= 0;
	$folio			= 0;
	$v_credito		= 0;
	$v_aux_desc		= 0;
	$v_subtotal		= 0;
	$v_total		= 0;
	$v_tarjeta		= 0;
	$v_t_servicios	= 0;
	$mensaje		= "";
	$tabla			= "";
	$condicion		= "";
	$forzar_promo	= "N";
	$etiqueta		= "text-success";
	$continuar		= false;
	$servicios		= array();
	$exito			= array(); //se declara para el json_encode
	
	$array_ids		= explode( ',', $id_servicios );
	$array_cant_id	= explode( ',', $kilos );
	
	$count_ids		= count( $array_ids );
	$count_cants	= count( $array_cant_id );
	
	if( $enviar )
	{
		if( $id_socio )
		{
			if( $tipo == 'PEDREDONES' && $rol == 'S' )
			{
				$condicion	= " OR 1 = 1 ";
				$tipo		= "EDREDONES";
				$forzar_promo	= "S";
			}
			
			if( $count_ids == $count_cants && is_numeric( $v_efectivo ) )
			{
				$query			= " SELECT		ser_id_servicio AS id,
												ser_descripcion AS descripcion,
												IF( src_kg_minimo > 0, src_kg_minimo, 1 ) AS minimo,
												ROUND( IF( DAYOFWEEK( CURDATE() ) = src_promo_dia $condicion, src_promo_cuota, src_cuota  ), 2 ) AS precio
									FROM		san_servicios
									INNER JOIN	san_servicios_cuotas ON src_id_servicio = ser_id_servicio
									AND			src_id_empresa = $id_empresa
									WHERE		ser_id_servicio IN ( $id_servicios )
									AND			ser_id_giro = 2
									AND			ser_tipo = '$tipo'
									AND			ser_id_consorcio = $id_consorcio
									AND			ser_status = 'A'";
				
				$resultado		= mysqli_query( $conexion, $query );
				
				while( $fila = mysqli_fetch_assoc( $resultado ) )
					array_push( $servicios, $fila );
				
				foreach( $array_cant_id as $cant_id )
				{
					list( $cantidad, $id ) = explode( '-', $cant_id );
					
					foreach( $servicios as $servicio )
					{
						if( $servicio['id'] == $id )
						{
							if( $servicio['minimo'] > $cantidad )
								$importe			= $servicio['precio'] * $servicio['minimo'];
							else
								$importe			= $servicio['precio'] * $cantidad;
							
							// $v_total		+= $importe;
							$v_subtotal		+= $importe;
							$v_t_servicios	+= $cantidad;
							
							if( $js_descuento )
							{
								$v_aux_desc	= $v_subtotal * ( $js_descuento / 100 );
								$v_total	= $v_subtotal - $v_aux_desc;
							}
							else
								$v_total = $v_subtotal;
							
							$tabla .= "	<tr>
											<td>$cantidad</td>
											<td>$servicio[descripcion]</td>
											<td class='text-right'>$$servicio[precio]</td>
											<td class='text-right'>$".( number_format( $importe, 2 ) )."</td>
										</tr>";
							
							break;
						}
					}
				}
				
				if( ( $js_iva_cobrar == 'N' && !$js_iva_monto ) || ( $js_iva_cobrar == 'S' && $js_iva_monto ) )
				{
					if( $js_iva_cobrar == 'S' && $v_iva_por )
					{
						$v_iva_monto	= $v_total * ( $v_iva_por / 100 );
						$v_total 		+= $v_iva_monto;
					}
					
					if( $js_tot_a_pagar == $v_total && $js_iva_monto == $v_iva_monto )
					{
						if( $js_metodo_pago == 'E' )
						{
							if( $v_efectivo >= $js_tot_a_pagar )
								$pet_cambio = $v_efectivo - $js_tot_a_pagar;
							else
								$v_credito = $js_tot_a_pagar - $v_efectivo;
						}
						else
							$v_efectivo	= 0;
						
						if( ( ( $v_efectivo > 0 || $v_credito > 0 || $js_descuento > 0 ) && $js_metodo_pago == 'E' ) || ( $js_metodo_pago == 'T' && !$v_credito ) )
						{
							if( $js_metodo_pago == 'T' )
							{
								$v_comision 	= obtener_p_comision_tarjeta();
								
								if( $v_comision )
									$tmp_tarjeta	= round( $v_total + ( $v_total * ( $v_comision / 100 ) ), 2 );
								
								$v_tarjeta	= round( $js_pag_tarjeta + $js_pag_comision, 2 );
								$v_credito	= 0;
							}
							
							if( ( $js_metodo_pago == 'T' && $v_tarjeta == $tmp_tarjeta && !$v_credito ) || $js_metodo_pago == 'E' )
							{
								if( $commit == 'S' )
								{
									$exito = preocesar_venta( $array_cant_id, $js_tot_a_pagar, $v_efectivo - $pet_cambio, $v_credito, $id_socio, $obs, $f_entrega, $tipo, $v_iva_por, $v_iva_monto, $forzar_promo, $js_metodo_pago, $js_pag_tarjeta, $js_pag_comision );
									
									//si num => 2 solo msj
									$exito['tipo_pago']	= $js_metodo_pago;
									$exito['efectivo']	= $v_efectivo;
									$exito['tarjeta']	= $v_tarjeta;
									
									echo json_encode( $exito );
								}
								else
								{
									$exito['num']	= 1;
									$exito['msj']	= "Se puede terminar la venta.";
								}
							}
							else
								$mensaje	.= "<li>Verificación del monto a pagar falló.</li>";
						}
						else
							$mensaje	.= "<li>Pago inválido.</li>";
					}
					else
						$mensaje .= "<li>El total de la venta no coincide con la validación.</li>";
				}
				else
					$mensaje .= "<li>Hay un problema con la selección del IVA.</li>";
			}
			else
				$mensaje .= "<li>No se escribieron cantidades correctas para los Servicios seleccionados.</li>";
		}
		else
			$mensaje .= "<li>Selecciona un Cliente para poder continuar.</li>";
	}
	else
		$mensaje .= "<li>No se validó el envío del formulario.</li>";
	
	if( $mensaje )
	{
?>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title text-warning">Venta detenida.</h4>
				</div>
				
				<div class="modal-body">
					<ul>
						<?= $mensaje ?>
					</ul>
				</div>
				
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-danger">Cerrar</button>
				</div>
			</div>
		</div>
<?php
	}
	elseif( $exito['num'] == 1 && $commit == 'N' )////////////para confirmar el COMMIT
	{
?>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-primary">Servicios vendidos</h4>
				</div>
				
				<div class="modal-body">
					<table class="table table-hover">
						<thead>
							<tr class="active">
								<th>Cantidad</th>
								<th>Descripción</th>
								<th class="text-right">Precio</th>
								<th class="text-right">Importe</th>
							</tr>
						</thead>
						
						<tbody>
							<?= $tabla ?>
						</tbody>
					</table>
					
					<div class="row-min text-right h4">
						<div class="col-md-10">Subtotal</div>
						<div class="col-md-2">$<?= number_format( $v_subtotal, 2 ) ?></div>
					</div>
					
					<div class="row-min text-right h4">
						<div class="col-md-10">Descuento</div>
						<div class="col-md-2">$<?= number_format( $v_aux_desc, 2 ) ?></div>
					</div>
					
					<div class="row-min text-right text-danger h4">
						<div class="col-md-10">IVA</div>
						<div class="col-md-2">$<?= number_format( $js_iva_monto, 2 ) ?></div>
					</div>
					
					<div class="row-min text-right text-success text-info h4">
						<div class="col-md-10"><?= $v_t_servicios ?> servicios por un total de</div>
						<div class="col-md-2">$<?= number_format( $v_total, 2 ) ?></div>
					</div>
					
					<div class="row-min text-right text-success text-info h4">
						<div class="col-md-10">Comisión</div>
						<div class="col-md-2">$<?= number_format( $js_pag_comision, 2 ) ?></div>
					</div>
					
					<div class="row">
						<div class="col-md-12 text-right">
							<h4 class="text-info"><strong>Método de pago</strong></h4>
						</div>
					</div>
					
					<div class="row-min text-right h4">
						<div class="col-md-10">Tarjeta</div>
						<div class="col-md-2">$<?= number_format( $v_tarjeta, 2 ) ?></div>
					</div>
					
					<div class="row-min text-right h4">
						<div class="col-md-10">Efectivo</div>
						<div class="col-md-2">$<?= number_format( $v_efectivo, 2 ) ?></div>
					</div>
					
					<div class="row-min text-right h4">
						<div class="col-md-10">Cambio</div>
						<div class="col-md-2">$<?= number_format( $pet_cambio, 2 ) ?></div>
					</div>
					
					<div class="row text-right text-danger h4">
						<div class="col-md-10">Por pagar</div>
						<div class="col-md-2">$<?= number_format( $v_credito, 2 ) ?></div>
					</div>
					
					<h4 class="text-right text-success">
						<?= $exito['msj'] ?>
					</h4>
				</div>
				
				<div class="modal-footer">
					<label id="msj_procesar">&nbsp;</label>
					<label id="img_procesar">&nbsp;</label>
					
					<label id="btn_procesar">
						<button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
						<button type="button" onclick="checar_servicios( 'S' )" class="btn btn-primary">Realizar la Venta</button>
					</label>
				</div>
			</div>
		</div>
<?php
	}
	
	mysqli_close( $conexion );
?>