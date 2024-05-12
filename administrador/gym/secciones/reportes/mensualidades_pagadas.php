<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Mensualidades pagadas
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<p>Pago del Servicio de Mensualidades realizados el día de hoy o en fechas seleccionadas.</p>
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_ini" onchange="buscar_mensualidades()" placeholder="Fecha inicial" />
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_fin" onchange="buscar_mensualidades()" placeholder="Fecha final" />
	</div>
</div>

<hr/>

<?php
	$tabla	= obtener_mensualidades();
?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Socio</th>
					<th>Servicio</th>
					<th>Capturó</th>
					<th>Fecha de Pago</th>
					<th class="text-right">Importe</th>
				</tr>
			</thead>
			
			<tbody id="tabla_mensualidades">
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>