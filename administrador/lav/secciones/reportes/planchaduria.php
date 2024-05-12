<?php
	$rango_ini		= request_var( 'rango_ini', date( 'd-m-Y' ) );
	$rango_fin		= request_var( 'rango_fin', date( 'd-m-Y' ) );
	$id_usuario		= request_var( 'id_usuario', 0 );
	$status			= request_var( 'status', 'S' );
	
	$cmb_cajeros	= combo_cajeros( $id_usuario );
	$tabla			= obtener_planchado( $rango_ini, $rango_fin, $status, $id_usuario );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-list"></span> Listado de planchado por empleado
		</h4>
	</div>
</div>

<hr/>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-2">Recepción inicio</label>
		<div class="col-md-4"><input type="text" name="rango_ini" id="f1" class="form-control" maxlength="12" value="<?= $rango_ini ?>" /></div>
	</div>
		
	<div class="row">
		<label class="col-md-2">Recepción fin</label>
		<div class="col-md-4"><input type="text" name="rango_fin" id="f2" class="form-control" maxlength="12" value="<?= $rango_fin ?>" /></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Empleado</label>
		<div class="col-md-4">
			<select name="id_usuario" class="form-control">
				<option value="">Todos...</option>
				<?= $cmb_cajeros ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Status Ticket</label>
		<div class="col-md-4">
			<select name="status" class="form-control">
				<option <?= ( $status == 'S' ) ? 'selected':'' ?> value="S">Notas 'Para planchar'</option>
				<option <?= ( $status == 'T' ) ? 'selected':'' ?> value="T">Notas 'Planchado y entregado'</option>
				<option <?= ( $status == 'Z' ) ? 'selected':'' ?> value="Z">Notas 'Revisadas'</option>
			</select>
		</div>
		
		<div class="col-md-6">Si se selecciona <b>Todos los status</b>, tardará el cargar la lista</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" class="btn btn-primary" value="Buscar" name="enviar" />
		</div>
	</div>
</form>

<hr/>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary text-bold">Lista actual seleccionada</h5>
	</div>
</div>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr>
						<th>#</th>
						<th>Folio</th>
						<th>Primer movimiento</th>
						<th class="text-right">Piezas</th>
						<th class="text-right">Precio</th>
						<th class="text-right">Importe</th>
						<th>Servicio</th>
					</tr>
				</thead>
				
				<tbody id="lista_tickets">
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
	</div>
</form>