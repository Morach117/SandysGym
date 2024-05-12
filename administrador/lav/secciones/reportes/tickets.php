<?php
	$año			= request_var( 'año_calcular', date( 'Y' ) );
	$mes			= request_var( 'mes_calcular', date( 'm' ) );
	$busqueda		= request_var( 'busqueda', 'credito' );
	$cliente		= request_var( 'nombre_cliente', '' );
	$opciones_año	= combo_años( $año );
	$opciones_mes	= combo_meses( $mes );
	$link			= "";
	$mes_evaluar	= "";
	
	// z
	$eliminar		= isset( $_POST['eliminar'] ) ? true:false;
	$opcion_z		= request_var( 'z', '' );
	if( $eliminar )
		eliminar_por_opcion_z();
	
	if( $opcion_z == 'E' )
		$accion_z = "<div class='row'><div class='col-md-12'><input type='submit' class='btn btn-danger' value='Eliminar' name='eliminar' /></div></div>";
	else
		$accion_z = "";
	
	if( $año && $mes )
		$mes_evaluar	= "$año-$mes";
	
	//se construye el link para descargar archivo en base a submit
	
	if( $año )
		$link .= "&anio=$año";
	
	if( $busqueda )
		$link .= "&busqueda=$busqueda";
	
	if( $mes_evaluar )
		$link .= "&mes_evaluar=$mes_evaluar";
	
	if( $cliente )
		$link .= "&nombre_cliente=$cliente";
	
	$tabla	= obtener_tickets( $busqueda, $año, $mes_evaluar, $cliente, $opcion_z );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-credit-card"></span> Listado de tickets para consultar detalles
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>Un ticket solo podrá ser CANCELADO cuando este con status de RECEPCIONADO, si se paso como Lavado o Entregado, ya no se podrá cancelar.</p>
	</div>
</div>

<hr/>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-2">Año-mes</label>
		<div class="col-md-2">
			<select name="año_calcular" id="año_calcular" class="form-control">
				<?= $opciones_año ?>
			</select>
		</div>
		
		<div class="col-md-2">
			<select name="mes_calcular" id="mes_calcular" class="form-control">
				<option value="">Todos</option>
				<?= $opciones_mes ?>
			</select>
		</div>
		
		<div class="col-md-6">Si se selecciona <b>Todos los meses</b>, tardará el cargar la lista</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Búsqueda</label>
		<div class="col-md-4">
			<select name="busqueda" id="busqueda" class="form-control">
				<option <?= ( $busqueda == '' ) ? 'selected':'' ?> value="">Todos</option>
				<option <?= ( $busqueda == 'credito' ) ? 'selected':'' ?> value="credito">Pendientes por cobrar</option>
				<option <?= ( $busqueda == 'LS' ) ? 'selected':'' ?> value="LS">Inventario (Lavados y por planchar)</option>
				<option <?= ( $busqueda == 'R' ) ? 'selected':'' ?> value="R">Notas 'Recepcionados'</option>
				<option <?= ( $busqueda == 'L' ) ? 'selected':'' ?> value="L">Notas 'Lavados'</option>
				<option <?= ( $busqueda == 'E' ) ? 'selected':'' ?> value="E">Notas 'Entregados'</option>
				<option <?= ( $busqueda == 'C' ) ? 'selected':'' ?> value="C">Notas 'Cancelados'</option>
				<option <?= ( $busqueda == 'I' ) ? 'selected':'' ?> value="I">Notas 'Inactivos'</option>
				
				<option <?= ( $busqueda == 'S' ) ? 'selected':'' ?> value="S">Notas 'Para planchar'</option>
				<option <?= ( $busqueda == 'T' ) ? 'selected':'' ?> value="T">Notas 'Planchado y entregado'</option>
				<option <?= ( $busqueda == 'Z' ) ? 'selected':'' ?> value="Z">Notas 'Revisadas'</option>
			</select>
		</div>
		
		<div class="col-md-6">Si se selecciona <b>Todos los status</b>, tardará el cargar la lista</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Cliente</label>
		<div class="col-md-4">
			<input type="text" name="nombre_cliente" class="form-control" maxlength="10" value="<?= $cliente ?>" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" class="btn btn-primary" value="Buscar" name="enviar" />
			<a class="link" target="_blank" href=".?s=reportes&i=tickets&d=excel<?= $link ?>">Descargar lista actual</a>
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
						<th></th>
						<th>Folio</th>
						<th>Movimiento</th>
						<th class="text-right">Efectivo</th>
						<th class="text-right">Tarjeta</th>
						<th class="text-right">Por cobrar</th>
						<th class="text-right">Total</th>
						<th>Cliente</th>
						<th>Usuario</th>
					</tr>
				</thead>
				
				<tbody id="lista_tickets">
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<?= $accion_z ?>
</form>