<?php
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$mes_evaluar	= "$año-$mes";
	$orden			= request_var( 'fr_orden', 'frecuencia' );
	$datetime	= strtotime( $mes_evaluar );
	$dias_mes	= date( 't', $datetime );
	
	$tabla	= obtener_datos( $mes_evaluar, $orden, $dias_mes );
?>

<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-repeat"></span> Lista de Servicios y frecuencia de venta
		</h4>
	</div>
</div>

<hr/>

<form action=".?s=reportes&i=frecuenciaservis" method="post">
	<div class="row">
		<label class="col-md-3">Selecciona el Año</label>
		<div class="col-md-3">
			<select name="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Selecciona el Mes (<?= $dias_mes ?> días)</label>
		<div class="col-md-3">
			<select name="mes_calcular" class="form-control">
				<?= $opciones_mes ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Ordenar por</label>
		<div class="col-md-3">
			<select name="fr_orden" class="form-control">
				<option <?= ( $orden == 'frecuencia' ) ? 'selected':'' ?> value="frecuencia">Frecuencia</option>
				<option <?= ( $orden == 'kgs' ) ? 'selected':'' ?> value="kgs">Kilogramos</option>
			</select>
		</div>
	</div>

	<div class="row">
		<label class="col-md-3">&nbsp;</label>
		<div class="col-md-3"><input type="submit" class="btn btn-primary" value="Buscar" name="enviar" maxlength="3" /></div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Servicios</th>
					<th class="text-right">Frecuencia</th>
					<th class="text-right">Kilogramos</th>
					<th class="text-right">Kgs. cliente</th>
					<th class="text-right">Kgs. día</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>