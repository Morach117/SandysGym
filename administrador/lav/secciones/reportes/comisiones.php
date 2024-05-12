<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-stats"></span> Comisiones
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>Las comisiones solo se toman en cuenta de aquellas notas que han cubierto todo el Total.</p>
		<p>Únicamente se debe <strong>procesar aquellos folios que tengan STATUS de Entregado( E )</strong>, ya que si se procesan folios con status de Recepcionado y Lavado, se afectarán los filtros en la seccion de Lavado y Entregado del nivel Operativo, y ya no se podrán marcar esos folios a otro STATUS.</p>
	</div>
</div>

<hr/>

<?php
	if( $enviar )
	{
		$exito = procesar_comisiones();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$fecha				= request_var( 'fecha', date( 'd-m-Y' ) );
	$lista_comisiones	= lista_comisiones_del_dia( 'N', $fecha );//Recepcionado, Lavado, Entregado
	$lista_c_pagadas	= lista_comisiones_del_dia( 'S', $fecha );//Pagado
	$comision			= obtener_comision();
?>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-2">Fecha</label>
		
		<div class="col-md-4">
			<input type="text" name="fecha" value="<?= $fecha ?>" class="form-control" id="f_actual" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Actual</label>
		<label class="col-md-10"><?= fecha_generica( $fecha ); ?></label>
	</div>
	
	<div class="row">
		<label class="col-md-2">Comsión</label>
		<label class="col-md-10"><?= $comision ?> %</label>
	</div>

	<div class="row">
		<div class="col-md-offset-2 col-md-4">
			<input type="submit" name="buscar" value="Buscar" class="btn btn-primary" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Comisiones pendientes de procesar.</strong></h5>
	</div>
</div>

<form action=".?s=reportes&i=comisiones" method="post" enctype='multipart/form-data'>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">	
				<thead>
					<tr>
						<th>#</th>
						<th></th>
						<th>Folio</th>
						<th>STATUS</th>
						<th>Último mov.</th>
						<th class="text-right">Pagado</th>
						<th class="text-right">Total</th>
						<th class="text-right">Comisión A</th>
						<th class="text-right">Comisión B</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $lista_comisiones ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
		</div>
	</div>
</form>

<hr/>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Comisiones procesadas.</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">	
			<thead>
				<tr>
					<th></th>
					<th>#</th>
					<th>Folio</th>
					<th>STATUS</th>
					<th>Último mov.</th>
					<th class="text-right">Pagado</th>
					<th class="text-right">Total</th>
					<th class="text-right">Comisión A</th>
					<th class="text-right">Comisión B</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_c_pagadas ?>
			</tbody>
		</table>
	</div>
</div>