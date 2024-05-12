<?php
	$socios	= "";
	$datos	= obtener_socios();
?>

<h4 class="text-info">
	<span class="glyphicon glyphicon-picture"></span> Socios
</h4>

<hr/>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover">
			<thead>
				<tr>
					<th></th>
					<th>Id</th>
					<th>Nombres</th>
					<th>Vigencia</th>
					<th>Foto</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $datos ?>
			</tbody>
		</table>
	</div>
</div>