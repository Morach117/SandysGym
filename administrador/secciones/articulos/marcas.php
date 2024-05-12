<?php
	$marcas	= obtener_lista_marcas();
?>

<div class="row">
	<div class="col-md-9">
		<h4 class="text-info"><span class="glyphicon glyphicon-flag"></span> Lista de Marcas</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<button onclick="mostrar_modal_marcas()" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Agregar</button>
	</div>
</div>

<hr/>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>ID</th>
					<th>Descripción</th>
				</tr>
			</thead>
			
			<tbody id="tabla_marcas">
				<?= $marcas ?>
			</tbody>
		</table>
	</div>
</div>