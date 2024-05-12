<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=configuracion"><span class="glyphicon glyphicon-cog"></span> Configuraciónes</a>
	<a class="list-group-item" href=".?s=configuracion&i=general"><span class="glyphicon glyphicon-cog"></span> General<?= ( $item == 'general' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=configuracion"><span class="glyphicon glyphicon-list-alt"></span> Sucursales <?= ( $item == 'index' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=configuracion&i=articulos"><span class="glyphicon glyphicon-file"></span> Artículos <?= ( $item == 'articulos' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=configuracion&i=folios"><span class="glyphicon glyphicon-list"></span> Folios <?= ( $item == 'folios' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=configuracion&i=tickets"><span class="glyphicon glyphicon-list"></span> Tickets <?= ( $item == 'tickets' ) ? $focus:'' ?></a>
	<a class="list-group-item" href="."><span class="glyphicon glyphicon-home"></span> Inicio</a>
</div>