<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-sort-by-order"></span> Más vendidos
		</h4>		
	</div>
</div>

<hr/>

<?php
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$mes_evaluar	= "$mes-$año";
	
	$datos			= obtener_masvendidos( $mes_evaluar );
?>

<div class="row">
	<div class="col-md-12">
		<p>Lista de 20 Articulos que más se han vendido</p>	
	</div>
</div>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-3">Año del movimiento</label>
		<div class="col-md-3">
			<select name="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-3">Mes del movimiento</label>
		<div class="col-md-3">
			<select name="mes_calcular" class="form-control">
				<?= $opciones_mes ?>
			</select>
		</div>
	</div>

	<div class="row">
		<div class="col-md-offset-3 col-md-3">
			<input type="submit" name="enviar" value="Buscar" class="btn btn-primary" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Lista de Articulos más vendidos</h5>
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
					<th class="text-right">Vendidos</th>
					<th class="text-right">Costo</th>
					<th class="text-right">Precio</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $datos ?>
			</tbody>
		</table>
	</div>
</div>
