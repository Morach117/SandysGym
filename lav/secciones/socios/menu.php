<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=socios"><span class="glyphicon glyphicon-user"></span> Socios</a>
	<a class="list-group-item" href=".?s=socios"><span class="glyphicon glyphicon-user"></span> Lista de socios <?= ( $item == 'index' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=socios&i=nuevo"><span class="glyphicon glyphicon-plus-sign"></span> Nuevo <?= ( $item == 'nuevo' ) ? $focus:'' ?></a>
</div>