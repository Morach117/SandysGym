<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-time"></span> Horas eliminadas del día
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<p>Reporte que corresponde a Horas o Visitas Eliminadas del día o fecha seleccionada. Las filas que se marcan en azul, corresponden a entradas por visitas.</p>
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_ini" onchange="buscar_horase()" placeholder="Fecha inicial" />
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_fin" onchange="buscar_horase()" placeholder="Fecha final" />
	</div>
</div>

<hr/>

<?php
	$tabla	= obtener_horas_eliminadas();
?>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>Cliente</th>
					<th>Capt./Elim.</th>
					<th>Captura</th>
					<th>Tiempo</th>
					<th>Periodo</th>
					<th>Eliminación</th>
				</tr>
			</thead>
			
			<tbody id="tabla_horase">
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>