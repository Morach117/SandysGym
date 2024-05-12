<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-bullhorn"></span> Servicios que se ofrecen en este tipo de Sucursal
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-12 text-right">
		<a href=".?s=catalogos&i=servicios_agregar"><span class="glyphicon glyphicon-plus-sign"></span></a>
	</div>
</div>

<hr/>

<?php
	$tabla	= obtener_servicios();
?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6 pointer">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>CLAVE</th>
					<th>Descripción</th>
					<th>Tipo</th>
					<th>STATUS</th>
					<th class="text-right">Cuota</th>
					<th class="text-right">Días</th>
					<th class="text-right">Meses</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>