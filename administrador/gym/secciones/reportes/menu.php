<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=reportes"><span class="glyphicon glyphicon-list-alt"></span> Reportes</a>
	<a class="list-group-item" href=".?s=reportes&i=diario"><span class="glyphicon glyphicon-adjust"></span> Corte Diario <?= ( $item == 'diario' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=mensual"><span class="glyphicon glyphicon-tasks"></span> Venta Mensual <?= ( $item == 'mensual' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=anual"><span class="glyphicon glyphicon-tasks"></span> Venta Anual <?= ( $item == 'anual' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=horas_eliminadas"><span class="glyphicon glyphicon-time"></span> Horas eliminadas <?= ( $item == 'horas_eliminadas' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=mensualidades_eliminadas"><span class="glyphicon glyphicon-usd"></span> Mensualidades eliminadas <?= ( $item == 'mensualidades_eliminadas' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=mensualidades_pagadas"><span class="glyphicon glyphicon-usd"></span> Mensualidades pagadas <?= ( $item == 'mensualidades_pagadas' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=fechas_actualizadas"><span class="glyphicon glyphicon-calendar"></span> Fechas actualizadas <?= ( $item == 'fechas_actualizadas' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=socios_vencidos"><span class="glyphicon glyphicon-remove-circle"></span> Socios vencidos <?= ( $item == 'socios_vencidos' ) ? $focus:'' ?></a>
</div>
