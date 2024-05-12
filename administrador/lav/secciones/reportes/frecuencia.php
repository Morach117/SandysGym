<?php
	$orden			= request_var( 'fr_orden', 'frecuencia' );
	$limit			= request_var( 'fr_registros', 50 );
	
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$mes_evaluar	= "$año-$mes";
	
	$tabla			= obtener_datos( $orden, $limit, $mes_evaluar );
?>

<div class="row">
	<div class="col-md-9">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-repeat"></span> Lista de Socio que con mayor frecuencia solicitan un servicio
		</h4>
	</div>
</div>

<hr/>

<form action=".?s=reportes&i=frecuencia" method="post">
	<div class="row">
		<label class="col-md-3">Selecciona el Año</label>
		<div class="col-md-3">
			<select name="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Selecciona el Mes</label>
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
		<label class="col-md-3">Registros</label>
		<div class="col-md-3"><input type="text" name="fr_registros" class="form-control" value="<?= $limit ?>" /></div>
	</div>

	<div class="row">
		<div class="col-md-offset-3 col-md-3"><input type="submit" class="btn btn-primary" value="Buscar" name="enviar" maxlength="3" /></div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Socio</th>
					<th class="text-right">Frecuencia</th>
					<th class="text-right">Kilogramos</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>