<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-record"></span> Vendidos en 0
		</h4>		
	</div>
</div>

<hr/>

<?php
	$importes		= array();
	$gastos			= array();
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$mes_evaluar	= "$mes-$año";
	
	$datos			= obtener_agotados( $mes_evaluar );
?>

<div class="row">
	<div class="col-md-12">
		<p>Lista de Articulos que se han vendido pero que se han agotado</p>	
	</div>
</div>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-3">Año del movimiento</label>
		<div class="col-md-3">
			<select name="año_calcular" class="form-control">
				<option value="">Año...</option>
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Mes del movimiento</label>
		<div class="col-md-3">
			<select name="mes_calcular" class="form-control">
				<option value="">Mes...</option>
				<?= $opciones_mes ?>
			</select>
		</div>
	</div>

	<div class="row">
		<div class="col-md-offset-3 col-md-3">
			<input type="submit" name="enviar" value="Buscar" class="btn btn-primary" />
			<input type="button" onclick="location.href='.?s=reportes&i=vendidosencero&d=excel&mesevaluar=<?= $mes_evaluar ?>'" value="Descargar" class="btn btn-default" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Lista de Articulos con existencia en 0</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th></th>
					<th>Código</th>
					<th>Descripción</th>
					<th class="text-right">Costo</th>
					<th class="text-right">Precio</th>
					<th class="text-right">Cantidad</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $datos ?>
			</tbody>
		</table>
	</div>
</div>
