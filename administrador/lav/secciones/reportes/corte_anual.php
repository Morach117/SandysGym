<?php
	$importes			= array();
	$gastos				= array();
	$año				= request_var( 'año_calcular', date( 'Y' ) );
	$opciones_año		= combo_años( $año );
	
	$total_sistema		= 0;
	$ganancia			= 0;
	
	$lista_ventas		= lista_ventas_del_mes( $año );
	$importe_vent		= obtener_total_venta_efectivo( $año);
	$importe_gastos		= obtener_gastos( $año );
	
	if( $importe_vent['num'] == 1 )
	{
		$total_sistema += $importe_vent['msj'];
		$importes['vent'] = number_format( $importe_vent['msj'], 2 );
	}
	else
	{
		$importes['vent'] = 0;
		mostrar_mensaje_div( $importe_vent['msj'], 'danger' );
	}
	
	//gastos
	if( $importe_gastos['num'] != 1 )
	{
		mostrar_mensaje_div( $importe_gastos['num'].". ".$importe_gastos['msj'], 'danger' );
	}
	
	//ganancia
	if( $importe_gastos['num'] == 1 && $total_sistema )
	{
		$gastos['importe']		= number_format( $importe_gastos['msj']['importe'], 2 );
		$gastos['iva']			= number_format( $importe_gastos['msj']['iva'], 2 );
		$gastos['descuento']	= number_format( $importe_gastos['msj']['descuento'], 2 );
		$gastos['total']		= $importe_gastos['msj']['total'];
		
		$ganancia				= $total_sistema - $gastos['total'];
		$ganancia				= number_format( $ganancia, 2 );
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

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-calendar"></span> Informe de ingresos y gastos
		</h4>		
	</div>
</div>

<hr/>

<form method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-3">Selecciona el Año</label>
		<div class="col-md-3">
			<select name="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-3 col-md-3"><input type="submit" class="btn btn-primary" value="Buscar" name="enviar" /></div>
	</div>
</form>

<hr/>

<div class="row">
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
					<td>Total en venta de Servicios</td>
					<td class="text-right">$<?= $importes['vent'] ?></td>
				</tr>
				
				<tr>
					<td class="text-right"><strong>Total en Ingresos</strong></td>
					<td class="text-right"><strong>$<?= number_format( $total_sistema, 2 ) ?></strong></td>
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
		<h3 class="text-info"><strong>Utilidad: $<?= $ganancia ?></strong></h3>
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
					<th>Mes</th>
					<th class="text-right">Total generado</th>
					<th class="text-right">Efectivo</th>
					<th class="text-right">Tarjeta</th>
					<th class="text-right">Crédito generado</th>
					<th class="text-right">Por cobrar</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_ventas ?>
			</tbody>
		</table>
	</div>
</div>