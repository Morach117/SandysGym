<?php
	$fecha_mov		= request_var( 'fecha', date( 'd-m-Y' ) );
	$cajero			= request_var( 'cajero', 0 );
	
	$cor_b_1000		= request_var( 'cor_b_1000', 0 );
	$cor_b_500		= request_var( 'cor_b_500', 0 );
	$cor_b_200		= request_var( 'cor_b_200', 0 );
	$cor_b_100		= request_var( 'cor_b_100', 0 );
	$cor_b_50		= request_var( 'cor_b_50', 0 );
	$cor_b_20		= request_var( 'cor_b_20', 0 );
	$cor_m_20		= request_var( 'cor_m_20', 0 );
	$cor_m_10		= request_var( 'cor_m_10', 0 );
	$cor_m_5		= request_var( 'cor_m_5', 0 );
	$cor_m_2		= request_var( 'cor_m_2', 0 );
	$cor_m_1		= request_var( 'cor_m_1', 0 );
	$cor_c_50		= request_var( 'cor_c_50', 0 );
	
	$cor_importe	= request_var( 'cor_importe', 0.0 );
	$cor_obs		= request_var( 'cor_observaciones', '' );
	
	if( $enviar )
	{
		$exito = realizar_corte( $fecha_mov, $cajero );
		
		if( $exito['num'] == 1 )
		{
			mostrar_mensaje_div( $exito['msj'], 'success' );
			
			$cor_b_1000		= 0;
			$cor_b_500		= 0;
			$cor_b_200		= 0;
			$cor_b_100		= 0;
			$cor_b_50		= 0;
			$cor_b_20		= 0;
			$cor_m_20		= 0;
			$cor_m_10		= 0;
			$cor_m_5		= 0;
			$cor_m_2		= 0;
			$cor_m_1		= 0;
			$cor_c_50		= 0;
			
			$cor_importe	= '';
			$cor_obs		= '';
			
			// json_encode( $exito );
			echo "<script>mostrar_modal_corte( $exito[IDC], $exito[IDU], $exito[CTC] )</script>";
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	/*nuevos*/
	$ven_ventas		= obtener_vendidos_total( $fecha_mov, $cajero );
	$ven_ventas_may	= obtener_vendidos_total_mayoreo( $fecha_mov, $cajero );
	$ven_devs_dia	= obtener_devoluciones( $fecha_mov, 1, $cajero );
	$ven_devs_otros	= obtener_devoluciones( $fecha_mov, 2, $cajero );
	$ven_pag_mon	= obtener_pagos_monedero( $fecha_mov, $cajero );
	
	$sa_ventas_a	= obtener_sa_vendidos_total( $fecha_mov, 'A', $cajero );	//ventas por entregar
	$sa_ventas_p	= obtener_sa_vendidos_total( $fecha_mov, 'P', $cajero );	//ventas entregadas
	$sa_cancel		= obtener_sa_cancelaciones( $fecha_mov, $cajero );
	
	$total_sa		= ( $sa_ventas_a + $sa_ventas_p + $ven_ventas_may ) - $sa_cancel;
	$total_ventas	= $ven_ventas - ( $ven_devs_dia + $ven_devs_otros + $ven_pag_mon );
	$total_neto		= $total_ventas + $total_sa;
	
	$total_gastos	= total_gastos( $fecha_mov );
	$total_costos	= total_venta_costos( $fecha_mov, $cajero );
	$total_utilidad	= total_venta_utilidad( $fecha_mov, $cajero );
	/*viejos*/
	
	$detalle		= lista_ventas_diaria( $fecha_mov, $cajero );
	$cajeros		= combo_cajeros( $cajero );
	$tot_corte		= total_importe_corte_del_dia( $fecha_mov, $cajero );
	
	$utilidad_n		= $total_utilidad - ( $total_gastos + $ven_pag_mon );
	
	$lista_cortes	= lista_cortes_del_dia( $fecha_mov );
?>
<div class="row">
	<div class="col-md-12">
		<h4 class="text-primary">
			<span class="glyphicon glyphicon-adjust"></span> REPORTE DE MOVIMIENTOS DEL DÍA
		</h4>
	</div>
</div>

<hr/>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-2">Actual</label>
		<label class="col-md-4"><?= fecha_generica( $fecha_mov ) ?></label>
	</div>
	
	<div class="row">
		<label class="col-md-2">Cajero</label>
		<div class="col-md-4">
			<select class="form-control" name="cajero">
				<option value="">Todos...</option>
				<?= $cajeros ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Fecha</label>
		<div class="col-md-4">
			<input type="text" name="fecha" value="<?= $fecha_mov ?>" class="form-control" id="fecha_actual" />
		</div>
	</div>

	<div class="row">
		<div class="col-md-offset-2 col-md-4">
			<input type="submit" name="buscar" value="Buscar" class="btn btn-primary" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Corte de caja</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<table class="table table-hover h6">
			<thead>
				<tr>
					<th>Descripción</th>
					<th class="text-right">Importe</th>
				</tr>
			</thead>
			
			<tbody>
				<tr>
					<td>Total del día en caja de Venta de Artículos</td>
					<td class="text-right">$<?= number_format( $total_neto, 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Cortes realizados</td>
					<td class="text-right">$<?= number_format( $tot_corte, 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Pendiente de retirar</td>
					<td class="text-right">$<?= number_format( $total_neto - $tot_corte, 2 ) ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-md-6">
		<form action=".?s=reportes&i=diario" method="post">
			<div class="row">
				<div class="col-md-12">
						 <input type="radio" name="cor_tipo" value="1" onclick="tipo_corte( 't1' )" checked />Introducir el Importe
					<br/><input type="radio" name="cor_tipo" value="2" onclick="tipo_corte( 't2' )" />Calcular el Importe
				</div>
			</div>
			
			<!--MOSTRAR ESTE O EL OTRO-->
			<div class="row" id="mos_tipo_1" style="display:none">
				<div class="col-md-12">
					<table class="table table-hover h6">
						<thead>
							<tr class="active">
								<th width="90px">Billetes</th>
								<th>Cantidad</th>
								<th width="90px">Monedas</th>
								<th>Cantidad</th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>de $1,000</td>
								<td><input type="text" name="cor_b_1000" id="cor_b_1000" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_b_1000 ?>" /></td>
								<td>de $20</td>
								<td><input type="text" name="cor_m_20" id="cor_m_20" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_m_20 ?>" /></td>
							</tr>
							<tr>
								<td>de $500</td>
								<td><input type="text" name="cor_b_500" id="cor_b_500" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_b_500 ?>" /></td>
								<td>de $10</td>
								<td><input type="text" name="cor_m_10" id="cor_m_10" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_m_10 ?>" /></td>
							</tr>
							<tr>
								<td>de $200</td>
								<td><input type="text" name="cor_b_200" id="cor_b_200" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_b_200 ?>" /></td>
								<td>de $5</td>
								<td><input type="text" name="cor_m_5" id="cor_m_5" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_m_5 ?>" /></td>
							</tr>
							<tr>
								<td>de $100</td>
								<td><input type="text" name="cor_b_100" id="cor_b_100" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_b_100 ?>" /></td>
								<td>de $2</td>
								<td><input type="text" name="cor_m_2" id="cor_m_2" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_m_2 ?>" /></td>
							</tr>
							<tr>
								<td>de $50</td>
								<td><input type="text" name="cor_b_50" id="cor_b_50" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_b_50 ?>" /></td>
								<td>de $1</td>
								<td><input type="text" name="cor_m_1" id="cor_m_1" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_m_1 ?>" /></td>
							</tr>
							<tr>
								<td>de $20</td>
								<td><input type="text" name="cor_b_20" id="cor_b_20" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_b_20 ?>" /></td>
								<td>de $0.5</td>
								<td><input type="text" name="cor_c_50" id="cor_c_50" class="form-control" onkeyup="calcular_importe()" value="<?= $cor_c_50 ?>" /></td>
							</tr>
						</tbody>
					</table>
					<label id="cal_importe">Importe: $<?= $cor_importe ?></label>
				</div>
			</div>
			
			<!--MOSTRAR ESTE O EL OTRO-->
			<div class="row" id="mos_tipo_2">
				<label class="col-md-2">Importe</label>
				<div class="col-md-4">
					<input type="text" name="cor_importe" id="cor_importe" class="form-control" maxlength="8" value="<?= $cor_importe ?>" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Notas</label>
				<div class="col-md-10">
					<textarea name="cor_observaciones" class="form-control" maxlength="100" rows="2"><?= $cor_obs ?></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-offset-2 col-md-10">
					<input type="hidden" name="pendiente_retirar" value="<?= $total_neto - $tot_corte ?>">
					<input type="hidden" name="total_dia" value="<?= $total_neto ?>">
					<input type="hidden" name="fecha" value="<?= $fecha_mov ?>">
					<input type="hidden" name="cajero" value="<?= $cajero ?>">
					<input type="submit" name="enviar" class="btn btn-primary" value="Procesar">
				</div>
			</div>
		</form>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Lista de Cortes con movimientos del día de hoy, tambien se incluyen de ventas de otros días</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Movimiento</th>
					<th>Fecha Venta</th>
					<th>Procesó</th>
					<th>Cajero</th>
					<th class="text-right">Importe</th>
					<th>Observaciones</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_cortes ?>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-12">
				<h5 class="text-info text-bold">Ventas al Público en General</h5>
			</div>
		</div>
		
		<div class="row">
			<label class="col-md-9">Total en folios generados</label>
			<label class="col-md-3 text-info text-right">$<?= number_format( $ven_ventas, 2 ) ?></label>
		</div>
		
		<div class="row">
			<label class="col-md-9">Devoluciones de ventas del día <abbr title="Pueden ser de ventas al público en general o de clientes"><span class="glyphicon glyphicon-question-sign"></span></abbr></label>
			<label class="col-md-3 text-danger text-right">$<?= number_format( $ven_devs_dia, 2 ) ?></label>
		</div>
		
		<div class="row">
			<label class="col-md-9">Devoluciones de ventas otros días <abbr title="Pueden ser de ventas al público en general o de clientes"><span class="glyphicon glyphicon-question-sign"></span></abbr></label>
			<label class="col-md-3 text-danger text-right">$<?= number_format( $ven_devs_otros, 2 ) ?></label>
		</div>
		
		<div class="row">
			<label class="col-md-9">Pagos con monedero electrónico</label>
			<label class="col-md-3 text-danger text-right">$<?= number_format( $ven_pag_mon, 2 ) ?></label>
		</div>
		
		<div class="row text-right">
			<label class="col-md-9">Total VPG</label>
			<label style="border-top: 1px solid #d4d4d4" class="col-md-3">$<?= number_format( $total_ventas, 2 ) ?></label>
		</div>
		
		<div class="row text-right">
			<label class="col-md-9">Total del día</label>
			<label class="col-md-3 text-success" style="border-top: 1px solid #d4d4d4">$<?= number_format( $total_neto, 2 ) ?></label>
		</div>
	</div>
	
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-12">
				<h5 class="text-info text-bold">Ventas a Clientes, Sistema de Apartado y Mayoreo</h5>
			</div>
		</div>

		<div class="row">
			<label class="col-md-9">Total en artículos por Entregar <abbr title="Mientras no se termine la venta, no se considera artículo vendido"><span class="glyphicon glyphicon-question-sign"></span></abbr></label>
			<label class="col-md-3 text-right text-info">$<?= number_format( $sa_ventas_a, 2 ) ?></label>
		</div>
		
		<div class="row">
			<label class="col-md-9">Total en artículos Entregados <abbr title="Si un artículo tiene un abono anterior, únicamente se contabiliza el de hoy, si es que hubiera"><span class="glyphicon glyphicon-question-sign"></span></abbr></label>
			<label class="col-md-3 text-right text-info">$<?= number_format( $sa_ventas_p, 2 ) ?></label>
		</div>
		
		<div class="row">
			<label class="col-md-9">Ventas por Mayoreo</label>
			<label class="col-md-3 text-right text-info">$<?= number_format( $ven_ventas_may, 2 ) ?></label>
		</div>
		
		<div class="row">
			<label class="col-md-9">Cancelaciones</label>
			<label class="col-md-3 text-right text-danger">$<?= number_format( $sa_cancel, 2 ) ?></label>
		</div>
		
		<div class="row text-right">
			<label class="col-md-9">Total VSA y VPM</label>
			<label style="border-top: 1px solid #d4d4d4" class="col-md-3 text-right">$<?= number_format( $total_sa, 2 ) ?></label>
		</div>
	</div>
</div>

<div class="row">
	<label class="col-md-4">Gastos</label>
	<div class="col-md-2 text-bold text-right">$<?= number_format( $total_gastos, 2 ) ?></div>
</div>

<div class="row">
	<label class="col-md-4">Costos <abbr title="Del Total en folios generados + Total en artículos Entregados, aún se haya hecho abonos en otro día, se contabiliza el día que se termina la venta"><span class="glyphicon glyphicon-question-sign"></span></abbr></label>
	<div class="col-md-2 text-bold text-right">$<?= number_format( $total_costos, 2 ) ?></div>
</div>

<div class="row">
	<label class="col-md-4">Utilidad por Artículos <abbr title="Utilidad por la venta de artículos, aún se haya hecho abonos en otro día, se contabiliza el día que se termina la venta"><span class="glyphicon glyphicon-question-sign"></span></abbr></label>
	<div class="col-md-2 text-success text-bold text-right">$<?= number_format( $total_utilidad, 2 ) ?></div>
</div>

<div class="row">
	<label class="col-md-4">Utilidad Neta <abbr title="Utilidad por Artículos - ( Gastos + pagos con menedero electrónico )"><span class="glyphicon glyphicon-question-sign"></span></abbr></label>
	<div class="col-md-2 text-success text-bold text-right">$<?= number_format( $utilidad_n, 2 ) ?></div>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Acumulado de Artículos vendidos durante el día</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>Lista de artículos vendidos al público en general o ventas terminadas de clientes. Se registran ventas de clientes terminados aún cuando se ha abonado en días anteriores a la fecha seleccionada.</p>
		<p>Si el "Importe" no es igual a "Total en folios generados", es porque Total en folios generados solo contabiliza las ventas al público en general sin devoluciones e Importe incluye las ventas de clientes que se terminaron en la fecha seleccionada.</p>
		</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover small" cellspacing="0" cellpadding="0" id="tbl_datos">
			<thead>
				<tr class="active">
					<th>Folio</th>
					<th>Código</th>
					<th>Descripción</th>
					<th class="text-right sum">Cant.</th>
					<th class="text-right">Costo</th>
					<th class="text-right sum">Costo Importe</th>
					<th class="text-right sum">$Dto.</th>
					<th class="text-right sum">$Mon.</th>
					<th class="text-right">Precio</th>
					<th class="text-right sum">Importe</th>
					<th class="text-right sum">Utilidad</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $detalle ?>
			</tbody>
			
			<tfoot id="tbl_foot">
				<tr class="success text-bold">
					<th>TOTALES:</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th class="text-right">&nbsp;</th>
					<th class="text-right">&nbsp;</th>
					<th class="text-right">&nbsp;</th>
					<th class="text-right">&nbsp;</th>
					<th class="text-right">&nbsp;</th>
					<th class="text-right">&nbsp;</th>
					<th class="text-right">&nbsp;</th>
					<th class="text-right">&nbsp;</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>