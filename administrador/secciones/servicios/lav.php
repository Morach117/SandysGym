<?php
	$tipo	= request_var( 'tipo', '' );
	$tabla	= obtener_servicios_lav( $tipo );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-bullhorn"></span> Servicios
		</h4>
	</div>
</div>

<div class="row">
	<label class="col-md-2">Tipo</label>
	<label class="col-md-8"><?= $tipo ?></label>
	
	<div class="col-md-2 text-right">
		<a href=".?s=servicios&i=lav_agregar&tipo=<?= $tipo ?>"><span class="glyphicon glyphicon-plus-sign"></span></a>
	</div>
</div>

<hr/>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr>
					<th>#</th>
					<th></th>
					<th>Descripción</th>
					<th>Orden</th>
					<th>Status</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>