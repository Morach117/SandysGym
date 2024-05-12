<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=articulos"><span class="glyphicon glyphicon-shopping-cart"></span> Artículos</a>
	<a class="list-group-item" href=".?s=articulos"><span class="glyphicon glyphicon-list-alt"></span> Artículos <?= ( $seccion == 'articulos' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=catalogos&i=proveedores"><span class="glyphicon glyphicon-flag"></span> Proveedores <?= ( $item == 'proveedores' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=articulos&i=categorias"><span class="glyphicon glyphicon-flag"></span> Categorias <?= ( $item == 'categorias' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=articulos&i=marcas"><span class="glyphicon glyphicon-flag"></span> Marcas <?= ( $item == 'marcas' ) ? $focus:'' ?></a>
	<a class="list-group-item" href="."><span class="glyphicon glyphicon-home"></span> Inicio</a>
</div>