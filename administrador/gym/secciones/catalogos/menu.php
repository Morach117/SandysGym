<?php
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=catalogos"><span class="glyphicon glyphicon-list"></span> Catalogos</a>
	<a class="list-group-item" href=".?s=catalogos&i=servicios"><span class="glyphicon glyphicon-bullhorn"></span> Servicios <?= ( $item == 'servicios' ) ? $focus:'' ?></a>
</div>