<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Mensualidades eliminadas
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<p>Reporte que corresponde a Mensualidades Eliminadas del día o fecha seleccionada.</p>
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_ini" onchange="buscar_pagose()" placeholder="Fecha inicial" />
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_fin" onchange="buscar_pagose()" placeholder="Fecha final" />
	</div>
</div>

<hr/>

<?php
	$tabla	= obtener_pagos_eliminados();
?>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>Captura</th>
					<th>Socio</th>
					<th>Periodo</th>
					<th>Capt./Elim.</th>
					<th>Servicio</th>
					<th>Eliminación</th>
				</tr>
			</thead>
			
			<tbody id="tabla_pagose">
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>