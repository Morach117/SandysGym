<?php
	$lav	= obtener_servicios( 'LAVANDERIA' );
	$pla	= obtener_servicios( 'PLANCHADURIA' );
	$edr	= obtener_servicios( 'EDREDONES' );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-primary">
			<span class="glyphicon glyphicon-glass"></span> Servicios
		</h4>
	</div>
</div>

<hr/>

<div class="row">
	<div class="col-md-12"><h4 class="text-info">LAVANDERIA</h4></div>
</div>

<div class="bs-touch">
    <ul class="bs-touch-list">
		<?= $lav ?>
	</ul>
</div>

<div class="row">
	<div class="col-md-12"><h4 class="text-info">PLANCHADURIA</h4></div>
</div>

<div class="bs-touch">
    <ul class="bs-touch-list">
		<?= $pla ?>
	</ul>
</div>

<div class="row">
	<div class="col-md-12"><h4 class="text-info">EDREDONES</h4></div>
</div>

<div class="bs-touch">
    <ul class="bs-touch-list">
		<?= $edr ?>
	</ul>
</div>

<div class="bs-touch">
    <ul class="bs-touch-list">
		<a href=".?s=inicio">
			<li>
				<span class="glyphicon glyphicon-home"></span>
				<h4><strong>Inicio</strong></h4>
				<span class="touch-class">Ir a la página principal</span>
			</li>
		</a>
	</ul>
</div>