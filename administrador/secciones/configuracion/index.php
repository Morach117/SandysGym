<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tower"></span> Configuración General de Sucursales
		</h4>
	</div>
</div>

<hr/>

<?php
	$tabla	= lista_sucursales();
?>

<form action=".?s=<?= $seccion ?>&i=editar" method="post">
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr class="active">
						<th>#</th>
						<th></th>
						<th>IDE</th>
						<th>ABR</th>
						<th>Sucursal</th>
						<th>Giro</th>
						<th>Status</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="continuar" value="Editar" class="btn btn-primary" />
		</div>
	</div>
</form>