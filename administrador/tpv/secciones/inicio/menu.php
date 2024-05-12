<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=reportes"><span class="glyphicon glyphicon-list-alt"></span> Reportes</a>
	<a class="list-group-item" href=".?s=reportes&i=diario"><span class="glyphicon glyphicon-adjust"></span> Corte Diario <?= ( $item == 'diario' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=mensual"><span class="glyphicon glyphicon-tasks"></span> Venta Mensual <?= ( $item == 'mensual' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=anual"><span class="glyphicon glyphicon-tasks"></span> Venta Anual <?= ( $item == 'anual' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=vendidosencero"><span class="glyphicon glyphicon-record"></span> Vendidos en 0 <?= ( $item == 'vendidosencero' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=masvendidos"><span class="glyphicon glyphicon-sort-by-order"></span> Más vendidos <?= ( $item == 'masvendidos' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=inversion"><span class="glyphicon glyphicon-usd"></span> Inversión <?= ( $item == 'inversion' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=tickets"><span class="glyphicon glyphicon-list-alt"></span> Tickets <?= ( $item == 'folios' ) ? $focus:'' ?></a>
</div>