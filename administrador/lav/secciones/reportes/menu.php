<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=reportes"><span class="glyphicon glyphicon-list-alt"></span> Reportes</a>
	<a class="list-group-item" href=".?s=reportes&i=corte_diario"><span class="glyphicon glyphicon-adjust"></span> Corte Diario <?= ( $item == 'corte_diario' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=corte_mensual"><span class="glyphicon glyphicon-tasks"></span> Corte Mensual <?= ( $item == 'corte_mensual' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=corte_anual"><span class="glyphicon glyphicon-calendar"></span> Corte Anual <?= ( $item == 'corte_anual' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=tickets"><span class="glyphicon glyphicon-barcode"></span> Tickets <?= ( $item == 'tickets' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=comisiones"><span class="glyphicon glyphicon-stats"></span> Comisiones <?= ( $item == 'comisiones' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=frecuencia"><span class="glyphicon glyphicon-repeat"></span> Frecuencia Clientes <?= ( $item == 'frecuencia' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=frecuenciaservis"><span class="glyphicon glyphicon-repeat"></span> Frecuencia Servicios <?= ( $item == 'frecuenciaservis' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=revision"><span class="glyphicon glyphicon-info-sign"></span> Revisión de tickets <?= ( $item == 'revision' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=reportes&i=planchaduria"><span class="glyphicon glyphicon-list"></span> Planchaduria <?= ( $item == 'planchaduria' ) ? $focus:'' ?></a>
</div>