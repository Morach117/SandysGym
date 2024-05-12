<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-info-sign"></span> Revisión de tickets</h4>
	</div>
</div>

<hr/>

<?php
	$b_status	= request_var( 'b_status', 'L' );
	$b_cliente	= strtoupper( request_var( 'b_cliente', '' ) );
	$b_orden	= request_var( 'b_orden', 0 );
	
	$lista		= obtener_lista_para_entrega( $b_orden, $b_cliente, $b_status );
	$lista_oby	= lista_orden_lavado( $b_orden );
?>

<div class="row">
	<div class="col-md-12">
		<p>El movimiento en este módulo, solo cambia el status del ticket a REVISADO.</p>
	</div>
</div>

<form action=".?s=reportes&i=revision" method="post">
	<div class="row">
		<label class="col-md-2">Tipo</label>
		<div class="col-md-10">
			<input type="radio" name="b_status" id="b_status_1" value="L" <?= ( $b_status == 'L' ) ? 'checked':'' ?> >Lavanderia 
			<input type="radio" name="b_status" id="b_status_2" value="S" <?= ( $b_status == 'S' ) ? 'checked':'' ?>>Planchaduria
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Folio o Cliente</label>
		<div class="col-md-4"><input type="text" name="b_cliente" id="b_cliente" class="form-control" value="<?= $b_cliente ?>" placeholder="Nombre o 321" /></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Ordenar por</label>
		<div class="col-md-4">
			<select name="b_orden" id="b_orden" class="form-control" >
				<?= $lista_oby ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-2 col-md-4"><input type="submit" name="enviar" class="btn btn-primary" value="Buscar" /></div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr>
					<th>#</th>
					<th>Folio</th>
					<th>Primer movimiento</th>
					<th>Cliente</th>
					<th class="text-right">Por cobrar</th>
					<th>Teléfono</th>
				</tr>
			</thead>
			
			<tbody class="pointer" id="lista_entrega">
				<?= $lista ?>
			</tbody>
		</table>
	</div>
</div>