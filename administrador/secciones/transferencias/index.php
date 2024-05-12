<div class="row">
	<div class="col-md-9">
		<h4 class="text-info"><span class="glyphicon glyphicon-refresh"></span> Transferencia de artículos</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
			Acciones <span class="caret"></span>
			</button>
			<ul class="dropdown-menu pointer" role="menu">
				<li><a onclick="nueva_transferencia( 'transferencias' )"><span class="glyphicon glyphicon-plus-sign"></span> Nueva transferencia</a></li>
			</ul>
		</div>
	</div>
</div>

<hr/>

<?php
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$corte			= "$mes-$año";
	
	$datos		= lista_transferencias( $corte );
?>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-2">Año de entrega</label>
		<div class="col-md-4">
			<select name="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Mes de entrega</label>
		<div class="col-md-4">
			<select name="mes_calcular" class="form-control">
				<?= $opciones_mes ?>
			</select>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="enviar" value="Buscar" class="btn btn-primary" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th></th>
					<th>Folio</th>
					<th>Status</th>
					<th>Nota</th>
					<th>Origen</th>
					<th>Destino</th>
					<th>Entrega</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $datos ?>
			</tbody>
		</table>
	</div>
</div>