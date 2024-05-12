<?php
	$fecha			= request_var( 'fecha', date( 'd-m-Y' ) );
	$v_id_cajero	= request_var( 'cajero', 0 );
	$accion			= request_var( 'accion', '' );
	
	if( $accion == 'e' )
		eliminar_corte();
	
	$importes		= array();
	$total_sistema	= 0;
	$por_retirar	= 0;
	$importe_vent	= obtener_importe_venta_efectivo( $fecha, $v_id_cajero );
	$lista_cortes	= lista_cortes_del_dia( $fecha );
	$lista_ventas	= lista_ventas_del_dia( $fecha, true, $v_id_cajero );
	$lista_ventast	= lista_ventas_del_dia( $fecha, false, $v_id_cajero );
	$importe_cortes	= total_importe_corte_del_dia( $fecha, $v_id_cajero );
	$cmb_cajeros	= combo_cajeros( $v_id_cajero );
	
	if( $importe_vent['num'] == 1 )
	{
		$total_sistema += $importe_vent['msj'];
		$importes['vent'] = number_format( $importe_vent['msj'], 2 );
	}
	else
	{
		$importes['vent'] = 0;
		mostrar_mensaje_div( $importe_vent['msj'], 'danger' );
	}
	
	if( $enviar )
	{
		$validar	= validar_corte_caja();
		
		if( $validar['num'] == 1 )
		{
			$guardar = realizar_corte( $fecha );
			
			if( $guardar['num'] == 1 )
			{
				header( "Location: .?s=$seccion&i=$item&cajero=$v_id_cajero" );
				exit;
			}
			else
				mostrar_mensaje_div( $guardar['msj'], 'danger' );
		}
		else
			mostrar_mensaje_div( $validar['msj'], 'warning' );
	}
	
	if( $total_sistema )
		$por_retirar = $total_sistema - $importe_cortes;
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-adjust"></span> Corte diario
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>El corte de caja puede realizarse aun si la venta es de otras fechas.</p>
		<p>Si se hacen cortes de caja de venta de otras fechas, el corte aparecerá en la lista del día de la venta y en la del día que fue realizado.</p>
		<p>El monto de Cortes realizados y Pendiente de retirar, se calcula de acuerdo a la fecha del movimiento seleccionado.</p>
		<p>Cada que se realice un corte de otra fecha, se regresa al día actual.</p>
	</div>
</div>

<hr/>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-2">Fecha</label>
		
		<div class="col-md-4">
			<input type="text" name="fecha" value="<?= $fecha ?>" class="form-control" id="f_actual" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Cajero</label>
		
		<div class="col-md-4">
			<select name="cajero" class="form-control">
				<option value="">Todos...</option>
				<?= $cmb_cajeros ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Actual</label>
		<label class="col-md-10"><?= fecha_generica( $fecha ); ?></label>
	</div>

	<div class="row">
		<div class="col-md-offset-2 col-md-4">
			<input type="submit" name="buscar" value="Buscar" class="btn btn-primary" />
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Información para el corte de caja</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>Si se desea realizar el corte de caja por cajero, primero se debe seleccionar el cajero y dar en <b>Buscar</b> para ver el monto de que le corresponde, posteriormente se puede <b>Procesar</b>.</p>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>Descripción</th>
					<th>Importe</th>
				</tr>
			</thead>
			
			<tbody>
				<tr>
					<td>Total en venta de Servicios</td>
					<td class="text-right">$<?= $importes['vent'] ?></td>
				</tr>
				
				<tr>
					<td class="text-right"><strong>Total en sistema</strong></td>
					<td class="text-right"><strong>$<?= number_format( $total_sistema, 2 ) ?></strong></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-md-6">
		<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
			<div class="row">
				<label class="col-md-2">Importe</label>
				<div class="col-md-4">
					<input type="text" name="cor_importe" class="form-control" required="required" maxlength="8" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Notas</label>
				<div class="col-md-10">
					<textarea name="cor_observaciones" class="form-control" maxlength="100" rows="2"></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-offset-2 col-md-10">
					<input type="hidden" name="cajero" value="<?= $v_id_cajero ?>" />
					<input type="hidden" name="fecha" value="<?= $fecha ?>" />
					<input type="submit" name="enviar" class="btn btn-primary" value="Procesar" />
					<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=reportes&i=corte&si=diario'" />
				</div>
			</div>
		</form>
		
		<div class="row">
			<div class="col-md-6">Cortes realizados</div>
			<div class="col-md-3 text-right">$<?= number_format( $importe_cortes, 2 ) ?></div>
		</div>
		
		<div class="row ">
			<div class="col-md-6"><strong>Pendiente de retirar</strong></div>
			<div class="col-md-3 text-right"><strong>$<?= number_format( $por_retirar, 2 ) ?></strong></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Cortes de caja realizados.</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">	
			<thead>
				<tr class="active">
					<th>#</th>
					<th></th>
					<th>Movimiento</th>
					<th>Venta</th>
					<th>Usuario</th>
					<th>Cajero</th>
					<th>Tipo</th>
					<th class="text-right">Caja</th>
					<th class="text-right">Importe</th>
					<th>Observaciones</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_cortes ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Movimiento en ventas de servicios del día donde se realizaron pagos en efectivo.</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">	
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Folio</th>
					<th>Status</th>
					<th class="text-right">Efectivo</th>
					<th class="text-right">Tarjeta</th>
					<th class="text-right">Por cobrar</th>
					<th class="text-right">Total</th>
					<th>Usuario</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_ventas ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary"><strong>Notas generadas en este día.</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">	
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Folio</th>
					<th>Status</th>
					<th class="text-right">Efectivo</th>
					<th class="text-right">Tarjeta</th>
					<th class="text-right">Por cobrar</th>
					<th class="text-right">Total</th>
					<th>Usuario</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_ventast ?>
			</tbody>
		</table>
	</div>
</div>