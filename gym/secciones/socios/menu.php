<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=socios"><span class="glyphicon glyphicon-user"></span> Socios</a>
	<a class="list-group-item" href=".?s=socios"><span class="glyphicon glyphicon-user"></span> Lista de Socios <?= ( $item == 'index' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=socios&i=lista_vigentes"><span class="glyphicon glyphicon-user"></span> Socios vigentes <?= ( $item == 'lista_vigentes' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=socios&i=lista_vencidos"><span class="glyphicon glyphicon-user"></span> Socios vencidos <?= ( $item == 'lista_vencidos' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=socios&i=duplicados"><span class="glyphicon glyphicon-pause"></span> Duplicados <?= ( $item == 'duplicados' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=socios&i=nuevo"><span class="glyphicon glyphicon-plus-sign"></span> Nuevo Socio <?= ( $item == 'nuevo' ) ? $focus:'' ?></a>
</div>