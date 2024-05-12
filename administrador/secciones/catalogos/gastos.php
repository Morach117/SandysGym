<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Gastos capturado por mes
		</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<a href=".?s=catalogos&i=gastosa" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Agregar</a>
	</div>
</div>

<hr/>

<?php
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$sucursal		= request_var( 'sucursal', 0 );
	
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$mes_movimiento	= "$año-$mes";
	
	$sucursales		= combo_sucursales( $sucursal );
	$tabla			= obtener_gastos( $mes_movimiento, $sucursal );
?>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-2">Año</label>
		<div class="col-md-4">
			<select name="año_calcular" id="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Mes</label>
		<div class="col-md-4">
			<select name="mes_calcular" id="mes_calcular" class="form-control">
				<?= $opciones_mes ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Sucursal</label>
		<div class="col-md-4">
			<select name="sucursal" id="sucursal" class="form-control">
				<option value="">Todas...</option>
				<?= $sucursales ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-2 col-md-4"><input type="submit" class="btn btn-primary btn-sm" value="Buscar" name="enviar" /></div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr>
					<th>#</th>
					<th></th>
					<th>Proveedor</th>
					<th class="text-right">Importe</th>
					<th class="text-right">IVA</th>
					<th class="text-right">Descuento</th>
					<th class="text-right">Total</th>
					<th>Fecha Fac. Nota</th>
				</tr>
			</thead>
			
			<tbody id="tabla_gastos">
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>