<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-remove-circle"></span> Reporte de Tickets generados durante el mes
		</h4>
	</div>
</div>

<hr/>

<?php
	$pag_status		= request_var( 'pag_status', '' );
	
	$var_status		= opciones_status_folios( $pag_status );
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$mes_evaluar	= "$mes-$año";
	
	$exito			= lista_folios_cancelados( $mes_evaluar, $pag_status );
	$var_paginas	= paginado( $exito['num'], 'reportes', 'tickets' );
?>

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
		<label class="col-md-3">Status</label>
		<div class="col-md-3">
			<select name="pag_status" class="form-control">
				<option value="">Todos...</option>
				<?= $var_status ?>
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
		<h5 class="text-info text-bold">Información de los movimientos mensuales</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>Lista de Tickets generados durante este mes. Los Tickets que aparecen marcados en rojo son de <span class="label label-danger">Cancelación Total</span> y los que aparecen en amarillo son de <span class="label label-warning">Cancelación Parcial</span>.</p>
		
		<p>Si un Folio esta Cancelado, en la columna Movimiento se muestra la fecha en que fue cancelado, caso contrario se muestra la fecha de Venta.</p>
	</div>
</div>
	
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6 pointer">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Folio</th>
					<th>Movimiento</th>
					<th>Usuario</th>
					<th class="text-right">Total</th>
					<th>Observaciones</th>
				</tr>
			</thead>
			
			<tbody id="tabla_devoluciones">
				<?= $exito['msj'] ?>
			</tbody>
		</table>
	</div>
</div>

<?= $var_paginas ?>