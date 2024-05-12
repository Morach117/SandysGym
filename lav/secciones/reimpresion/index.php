<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-print"></span> Reimpresión de Ticket</h4>
	</div>
</div>

<hr/>

<?php
	$folio	= request_var( 'folio', '' );
	$tabla	= ultimos_tickets( $folio );
?>

<form role="form" method="post" action=".?s=reimpresion" >
	<div class="row">
		<div class="col-md-offset-2 col-md-10">Ingresa únicamente los últimos dígitos (F000000<strong>99</strong>) para continuar.</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Folio</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="folio" maxlength="6" required="required" placeholder="Ingresa el número de Folio" value="<?= $folio ?>" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-2 col-md-10">
			<input type="submit" name="enviar" value="Buscar" class="btn btn-primary" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Últimos 20 tickets generados en la venta</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr>
					<th>#</th>
					<th></th>
					<th>Folio</th>
					<th>Último Movimiento</th>
					<th>Status</th>
					<th>Cliente</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $tabla ?>
			</tbody>
		</table>
	</div>
</div>