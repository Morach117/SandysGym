<?php
	$categorias	= obtener_lista_categorias();
?>

<div class="row">
	<div class="col-md-9">
		<h4 class="text-info"><span class="glyphicon glyphicon-flag"></span> Lista de Categorias</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<button onclick="mostrar_modal_categorias()" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Agregar</button>
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
			
			<tbody id="tabla_categorias">
				<?= $categorias ?>
			</tbody>
		</table>
	</div>
</div>