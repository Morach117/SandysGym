<?php
	$tipo	= request_var( 'tipo', '' );
	$focus	= "<span style='float:right' class='glyphicon glyphicon-chevron-right'></span>";
?>

<div class="list-group">
	<a class="list-group-item active" href=".?s=servicios"><span class="glyphicon glyphicon-th-large"></span> Servicios</a>
	<a class="list-group-item" href=".?s=servicios&i=gim"><span class="glyphicon glyphicon-file"></span> Gimnasio <?= ( $item == 'gim' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=servicios&i=lav&tipo=LAVANDERIA"><span class="glyphicon glyphicon-file"></span> Lavaderia <?= ( $tipo == 'LAVANDERIA' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=servicios&i=lav&tipo=PLANCHADURIA"><span class="glyphicon glyphicon-file"></span> Planchaduria <?= ( $tipo == 'PLANCHADURIA' ) ? $focus:'' ?></a>
	<a class="list-group-item" href=".?s=servicios&i=lav&tipo=EDREDONES"><span class="glyphicon glyphicon-file"></span> Edredones <?= ( $tipo == 'EDREDONES' ) ? $focus:'' ?></a>
	<a class="list-group-item" href="."><span class="glyphicon glyphicon-home"></span> Inicio</a>
</div>