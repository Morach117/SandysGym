<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tags"></span> Listado de Proveedores
		</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<a href=".?s=catalogos&i=proveedora" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Agregar</a>
	</div>
</div>

<hr/>

<?php
	$tabla	= obtener_proveedores();
?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6 pointer">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Nombres o RS</th>
					<th>Primer Contacto</th>
					<th>Teléfono</th>
					<th>Segundo Contacto</th>
					<th>Teléfono</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>