<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-user"></span> Usuarios
		</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<a href=".?s=catalogos&i=agregaru" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Agregar</a>
	</div>
</div>

<hr/>

<?php
	$usuarios	= obtener_usuarios();
?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Sucursal</th>
					<th>Nombre completo</th>
					<th>Status</th>
					<th>Correo</th>
					<th>Contraseña</th>
					<th>Rol</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $usuarios ?>
			</tbody>
		</table>
	</div>
</div>