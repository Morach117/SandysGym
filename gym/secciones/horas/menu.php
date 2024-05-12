<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=horas"><span class="glyphicon glyphicon-time"></span> Control de Horas</a>
	<!--a class="list-group-item" href=".?s=horas"><span class="glyphicon glyphicon-time"></span> Lista de Horas </a-->
	<a class="list-group-item" href=".?s=horas"><span class="glyphicon glyphicon-plus-sign"></span> Captura de Hora <?= ( $seccion == 'horas' ) ? $focus:'' ?></a>
</div>