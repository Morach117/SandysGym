<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tasks"></span> Informe de ingresos y gastos
		</h4>		
	</div>
</div>

<hr/>

<?php
	$gastos				= array();
	$año				= request_var( 'año_calcular', date( 'Y' ) );
	$mes				= request_var( 'mes_calcular', date( 'm' ) );
	$opciones_año		= combo_años( $año );
	$opciones_mes		= combo_meses( $mes );
	$mes_ganancia		= "$mes-$año";
	
	$v_total		= 0;
	$v_efectivo		= 0;
	$v_tarjeta		= 0;
	$v_comsion		= 0;
	$v_tar_com		= 0;
	$v_utilidad		= 0;
	
	$importe_mens		= obtener_importe_mensualidades( $mes_ganancia, 'M' );
	$importe_hors		= obtener_importe_por_horas( 'HORA', $mes_ganancia, 'M' );
	$importe_hdia		= obtener_importe_por_horas( 'VISITA', $mes_ganancia, 'M' );
	$importe_prep		= obtener_importe_prepago( $mes_ganancia, 'M' );
	$importe_vent		= obtener_importe_venta_efectivo( $mes_ganancia, 'M' );
	$importe_gastos		= obtener_gastos( $mes_ganancia, 'M' );
	
	$lista_ventas		= lista_ventas_del_mes( $mes_ganancia );
	
	if( $importe_mens['num'] != 1 )
		mostrar_mensaje_div( $importe_mens['msj'], 'danger' );
	
	if( $importe_hors['num'] != 1 )
		mostrar_mensaje_div( $importe_hors['msj'], 'danger' );
	
	if( $importe_hdia['num'] != 1 )
		mostrar_mensaje_div( $importe_hors['msj'], 'danger' );
	
	if( $importe_prep['num'] != 1 )
		mostrar_mensaje_div( $importe_prep['msj'], 'danger' );
	
	if( $importe_vent['num'] != 1 )
		mostrar_mensaje_div( $importe_vent['msj'], 'danger' );
	
	//gastos
	if( $importe_gastos['num'] != 1 )
	{
		mostrar_mensaje_div( $importe_gastos['num'].". ".$importe_gastos['msj'], 'danger' );
	}
	
	$v_total	= $importe_mens['total'] + $importe_hors['total'] + $importe_hdia['total'] + $importe_vent['total'] + $importe_prep['total'];
	$v_efectivo	= $importe_mens['efectivo'] + $importe_hors['efectivo'] + $importe_vent['efectivo'];
	$v_tarjeta	= $importe_mens['tarjeta'] + $importe_hors['tarjeta'] + $importe_vent['tarjeta'];
	$v_comsion	= $importe_mens['comision'] + $importe_hors['comision'] + $importe_vent['comision'];
	$v_tar_com	= $v_tarjeta + $v_comsion;
	
	//ganancia
	if( $importe_gastos['num'] == 1 && $v_total )
	{
		$gastos['importe']		= number_format( $importe_gastos['msj']['importe'], 2 );
		$gastos['iva']			= number_format( $importe_gastos['msj']['iva'], 2 );
		$gastos['descuento']	= number_format( $importe_gastos['msj']['descuento'], 2 );
		$gastos['total']		= $importe_gastos['msj']['total'];
		
		$v_utilidad				= $v_total - $gastos['total'];
		$v_utilidad				= number_format( $v_utilidad, 2 );
		$gastos['total']		= number_format( $gastos['total'], 2 );
	}
	else
	{
		$gastos['importe']		= "-/-";
		$gastos['iva']			= "-/-";
		$gastos['descuento']	= "-/-";
		$gastos['total']		= "-/-";
	}
?>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-3">Selecciona el Año</label>
		<div class="col-md-3">
			<select name="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Selecciona el Mes</label>
		<div class="col-md-3">
			<select name="mes_calcular" class="form-control">
				<?= $opciones_mes ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-3 col-md-3">
			<input type="submit" class="btn btn-primary btn-sm" value="Buscar" name="enviar" />
		</div>
	</div>
</form>

<hr/>

<div class="row">
	<div class="col-md-6">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>Descripción</th>
					<th class="text-right">Efectivo</th>
					<th class="text-right">Tarjeta</th>
					<th class="text-right">Importe</th>
				</tr>
			</thead>
			
			<tbody>
				<tr>
					<td>Total en mensualidades</td>
					<td class="text-right"><?= '$'.number_format( $importe_mens['efectivo'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_mens['tar_com'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_mens['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en entradas por horas</td>
					<td class="text-right"><?= '$'.number_format( $importe_hors['efectivo'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_hors['tar_com'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_hors['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en entradas por visitas</td>
					<td class="text-right"><?= '$'.number_format( $importe_hdia['efectivo'], 2 ) ?></td>
					<td class="text-right">-/-</td>
					<td class="text-right"><?= '$'.number_format( $importe_hdia['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en venta de articulos</td>
					<td class="text-right"><?= '$'.number_format( $importe_vent['efectivo'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_vent['tar_com'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_vent['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en prepagos</td>
					<td class="text-right"><?= '$'.number_format( $importe_prep['efectivo'], 2 ) ?></td>
					<td class="text-right">-/-</td>
					<td class="text-right"><?= '$'.number_format( $importe_prep['total'], 2 ) ?></td>
				</tr>
				
				<tr class="success text-bold">
					<td class="text-right">Total de ingresos</td>
					<td class="text-right"><?= '$'.number_format( $v_efectivo, 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $v_tar_com, 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $v_total, 2 ) ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-md-6">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>Descripción</th>
					<th class="text-right">Importe</th>
				</tr>
			</thead>
			
			<tbody>
				<tr>
					<td>Importe</td>
					<td class="text-right">$<?= $gastos['importe'] ?></td>
				</tr>
				
				<tr>
					<td>IVA</td>
					<td class="text-right">$<?= $gastos['iva'] ?></td>
				</tr>

				<tr>
					<td>Descuento</td>
					<td class="text-right">$<?= $gastos['descuento'] ?></td>
				</tr>
				
				<tr>
					<td class="text-right"><strong>Total en Gastos</strong></td>
					<td class="text-right"><strong>$<?= $gastos['total'] ?></strong></td>
				</tr>
			</tbody>
		</table>
		
		<br/>
		<h3 class="text-info"><strong>Utilidad: $<?= $v_utilidad ?></strong></h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Movimiento en ventas de servicios por días del mes.</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">	
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Fecha</th>
					<th class="text-right">Mensualidades</th>
					<th class="text-right">Horas</th>
					<th class="text-right">Visitas</th>
					<th class="text-right">Artículos</th>
					<th class="text-right">Prepagos</th>
					<th class="text-right">Cortes</th>
					<th class="text-right">Total</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_ventas ?>
			</tbody>
		</table>
	</div>
</div>