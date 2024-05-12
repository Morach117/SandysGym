<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=prepagos"><span class="glyphicon glyphicon-usd"></span> PrePagos</a>
	<a class="list-group-item" href=".?s=prepagos"><span class="glyphicon glyphicon-home"></span> Lista de PrePagos <?= ( $item == 'index' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=prepagos&i=nuevo"><span class="glyphicon glyphicon-plus-sign"></span> Nuevo <?= ( $item == 'nuevo' ) ? $focus:'' ?></a>
</div>