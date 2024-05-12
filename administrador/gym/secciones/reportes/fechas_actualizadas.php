<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-calendar"></span> Cambios en las fechas de los Socios
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<p>Fechas en pagos de Mensualidades actualizados el día de hoy o en fechas seleccionadas.</p>
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_ini" onchange="buscar_fechasa()" placeholder="Fecha inicial" />
	</div>
	
	<div class="col-md-2">
		<input type="text" class="form-control" id="rango_fin" onchange="buscar_fechasa()" placeholder="Fecha final" />
	</div>
</div>	

<hr/>

<?php
	$tabla	= obtener_mensualidades_actualizadas();
?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>Socio</th>
					<th>Anterior</th>
					<th>Nuevo</th>
					<th>Capt./Modif.</th>
					<th>Modificación</th>
					<th>Comentario</th>
				</tr>
			</thead>
			
			<tbody id="tabla_fechasa">
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>