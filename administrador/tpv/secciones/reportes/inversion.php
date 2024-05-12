<?php
	$inversion	= inversion();
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Inversion
		</h4>
	</div>
</div>

<hr/>

<div class="row">
	<label class="col-md-4">Cantidad de Articulos</label>
	<div class="col-md-2 text-bold"><?= number_format( $inversion['cantidad'], 2 ) ?></div>
</div>

<div class="row">
	<label class="col-md-4">Total en precios</label>
	<div class="col-md-2 text-info text-bold">$<?= number_format( $inversion['precios'], 2 ) ?></div>
</div>

<div class="row">
	<label class="col-md-4">Total en costos</label>
	<div class="col-md-2 text-info text-bold">$<?= number_format( $inversion['costos'], 2 ) ?></div>
</div>

<div class="row">
	<label class="col-md-4">Utilidad</label>
	<div class="col-md-2 text-success text-bold">$<?= number_format( $inversion['diferencia'], 2 ) ?></div>
</div>