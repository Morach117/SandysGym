<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=visitas"><span class="glyphicon glyphicon-time"></span> Control de Visitas</a>
	<a class="list-group-item" href=".?s=visitas"><span class="glyphicon glyphicon-time"></span> Lista de Visitas <?= ( $item == 'index' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=visitas&i=nuevo"><span class="glyphicon glyphicon-plus-sign"></span> Captura de Visitas <?= ( $item == 'nuevo' ) ? $focus:'' ?></a>
</div>