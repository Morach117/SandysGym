<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tasks"></span> Informe de Ingresos y Egresos Anual
		</h4>		
	</div>
</div>

<hr/>

<?php
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$opciones_año	= combo_años( $año );
	
	$total_venta	= total_venta( $año );
	$total_gastos	= total_gastos( $año );
	$total_costos	= total_venta_costos( $año );
	$desglose		= deglose_por_dia( $año );
	$utilidad		= $total_venta - ( $total_gastos + $total_costos );
?>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-3">Año del movimiento</label>
		<div class="col-md-3">
			<select name="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>

	<div class="row">
		<div class="col-md-offset-3 col-md-3">
			<input type="submit" name="enviar" value="Buscar" class="btn btn-primary" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Información de los movimientos Anuales</h5>
	</div>
</div>

<div class="row">
	<label class="col-md-4">Total en venta de Artículos</label>
	<label class="col-md-2 text-right">$<?= number_format( $total_venta, 2 ) ?></label>
</div>

<div class="row">
	<label class="col-md-4">Total en gastos</label>
	<label class="col-md-2 text-danger text-right">$<?= number_format( $total_gastos, 2 ) ?></label>
</div>

<div class="row">
	<label class="col-md-4">Total en costos</label>
	<label class="col-md-2 text-danger text-right">$<?= number_format( $total_costos, 2 ) ?></label>
</div>

<div class="row">
	<label class="col-md-4">Utilidad</label>
	<label class="col-md-2 text-success text-right">$<?= number_format( $utilidad, 2 ) ?></label>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Detalle de los movimientos</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>El <label>$ Desc.</label> representa el monto de Descuentos aplicado a las <label>Ventas</label>. La <label>Utilidad X</label> representa la difrencia entre <label>Ventas</label> y <label>Costos</label>, mientras que la <label>Utilidad Neta</label> representa la diferencia entre <label>Ventas</label> respecto a <label>Gastos</label> y <label>Costos</label>.</p>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Mes</th>
					<th class="text-right">$Dto.</th>
					<th class="text-right">Ventas</th>
					<th class="text-right">Gastos</th>
					<th class="text-right">Costos</th>
					<th class="text-right">Utilidad X</th>
					<th class="text-right">Utilidad Neta</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $desglose ?>
			</tbody>
		</table>
	</div>
</div>
