<?php
	$duplicados	= lista_socios_duplicados();
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-pause"></span> Lista de Socios Duplicados
		</h4>
	</div>
</div>

<hr/>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Nombre Completo</th>
					<th class="text-right">Cantidad</th>
				</tr>
			</thead>
			
			<tbody id="lista_socios">
				<?= $duplicados ?>
			</tbody>
		</table>
	</div>
</div>