<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href="."><span class="glyphicon glyphicon-th-large"></span> Administración General</a>
	<a class="list-group-item" href=".?s=catalogos&i=gastos"><span class="glyphicon glyphicon-usd"></span> Gastos <?= ( $item == 'gastos' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=catalogos&i=proveedores"><span class="glyphicon glyphicon-tags"></span> Proveedores <?= ( $item == 'proveedores' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=catalogos&i=usuarios"><span class="glyphicon glyphicon-user"></span> Usuarios <?= ( $item == 'usuarios' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=articulos"><span class="glyphicon glyphicon-file"></span> Artículos</a>
	<a class="list-group-item" href=".?s=transferencias"><span class="glyphicon glyphicon-refresh"></span> Transferencias <?= ( $seccion == 'transferencias' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=servicios"><span class="glyphicon glyphicon-file"></span> Servicios</a>
	<a class="list-group-item" href=".?s=configuracion"><span class="glyphicon glyphicon-cog"></span> Configuración General</a>
</div>