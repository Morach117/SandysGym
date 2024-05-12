<?php
	$datos		= obtener_lavados();
?>

<div class="row">
	<div class="col-md-9">
		<h4 class="text-info"><span class="glyphicon glyphicon-ok-sign"></span> Lavado</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<button type="button" onclick="cambiar_status_todos()" class="btn btn-primary">Cambiar STATUS</button>
	</div>
</div>

<div class="row">
	<div class="col-md-12">Lista de Clientes con ropa Recepcionada y lista para ser Lavada o que ya esta Lavado.</div>
</div>

<hr/>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-condensed pointer">
			<thead>
				<tr>
					<th>#</th>
					<th>Folio</th>
					<th>Primer Movimiento</th>
					<th>Cliente</th>
					<th>Ultima Observación</th>
				</tr>
			</thead>
			
			<tbody id="lista_lavados">
				<?= $datos ?>
			</tbody>
		</table>
	</div>
</div>