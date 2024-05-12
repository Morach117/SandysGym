<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-adjust"></span> Corte diario
		</h4>
	</div>
</div>

<hr/>

<?php
	$v_total		= 0;
	$v_efectivo		= 0;
	$v_tarjeta		= 0;
	$v_comsion		= 0;
	$v_tar_com		= 0;
	$por_retirar	= 0;
	$fecha_mov		= request_var( 'fecha_mov', date( 'd-m-Y' ) );
	$v_id_cajero	= request_var( 'cajero', 0 );
	$accion			= request_var( 'accion', '' );
	
	if( $accion == 'e' )
		eliminar_corte();
	
	if( $enviar )
	{
		$guardar = realizar_corte( $id_empresa, $fecha_mov );
		
		if( $guardar['num'] == 1 )
		{
			header( "Location: .?s=$seccion&i=$item&fecha_mov=$fecha_mov" );
			exit;
		}
		else
			mostrar_mensaje_div( $guardar['num'].". ".$guardar['msj'], 'danger' );
	}
	
	$importe_mens		= obtener_importe_mensualidades( $fecha_mov, 'D', $v_id_cajero );
	$importe_hors		= obtener_importe_por_horas( 'HORA', $fecha_mov, 'D', $v_id_cajero );
	$importe_hdia		= obtener_importe_por_horas( 'VISITA', $fecha_mov, 'D', $v_id_cajero );
	$importe_vent		= obtener_importe_venta_efectivo( $fecha_mov, 'D', $v_id_cajero );
	$importe_prep		= obtener_importe_prepago( $fecha_mov, 'D', $v_id_cajero );
	
	if( $importe_mens['num'] != 1 )
		mostrar_mensaje_div( $importe_mens['msj'], 'danger' );
	
	if( $importe_hors['num'] != 1 )
		mostrar_mensaje_div( $importe_hors['msj'], 'danger' );
	
	if( $importe_hdia['num'] != 1 )
		mostrar_mensaje_div( $importe_hdia['msj'], 'danger' );
	
	if( $importe_vent['num'] != 1 )
		mostrar_mensaje_div( $importe_vent['msj'], 'danger' );
	
	if( $importe_prep['num'] != 1 )
		mostrar_mensaje_div( $importe_prep['msj'], 'danger' );
	
	$lista_cortes	= lista_cortes_del_dia( $fecha_mov );
	$importe_cortes	= total_importe_corte_del_dia( $fecha_mov, $v_id_cajero );
	$cmb_cajeros	= combo_cajeros( $v_id_cajero );
	
	$v_total	= $importe_mens['total'] + $importe_hors['total'] + $importe_hdia['total'] + $importe_vent['total'] + $importe_prep['total'];
	$v_efectivo	= $importe_mens['efectivo'] + $importe_hors['efectivo'] + $importe_vent['efectivo'];
	$v_tarjeta	= $importe_mens['tarjeta'] + $importe_hors['tarjeta'] + $importe_vent['tarjeta'];
	$v_comsion	= $importe_mens['comision'] + $importe_hors['comision'] + $importe_vent['comision'];
	$v_tar_com	= $v_tarjeta + $v_comsion;
	
	if( $v_efectivo )
		$por_retirar = $v_efectivo - $importe_cortes;
?>

<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
	<div class="row">
		<label class="col-md-2">Fecha</label>
		<div class="col-md-4">
			<input type="text" name="fecha_mov" value="<?= $fecha_mov ?>" class="form-control" id="pag_fecha_pago" />
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
		<label class="col-md-10"><?= fecha_generica( $fecha_mov ); ?></label>
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
	<div class="col-md-6">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>Descripción</th>
					<th class="text-right">Efectivo</th>
					<th class="text-right">Tarjeta</th>
					<th class="text-right">Importe</th>
				</tr>
			</thead>
			
			<tbody>
				<tr>
					<td>Total en mensualidades</td>
					<td class="text-right"><?= '$'.number_format( $importe_mens['efectivo'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_mens['tar_com'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_mens['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en entradas por horas</td>
					<td class="text-right"><?= '$'.number_format( $importe_hors['efectivo'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_hors['tar_com'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_hors['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en entradas por visitas</td>
					<td class="text-right"><?= '$'.number_format( $importe_hdia['efectivo'], 2 ) ?></td>
					<td class="text-right">-/-</td>
					<td class="text-right"><?= '$'.number_format( $importe_hdia['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en venta de articulos</td>
					<td class="text-right"><?= '$'.number_format( $importe_vent['efectivo'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_vent['tar_com'], 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $importe_vent['total'], 2 ) ?></td>
				</tr>
				
				<tr>
					<td>Total en prepagos</td>
					<td class="text-right"><?= '$'.number_format( $importe_prep['efectivo'], 2 ) ?></td>
					<td class="text-right">-/-</td>
					<td class="text-right"><?= '$'.number_format( $importe_prep['total'], 2 ) ?></td>
				</tr>
				
				<tr class="success text-bold">
					<td class="text-right">Total en sistema</td>
					<td class="text-right"><?= '$'.number_format( $v_efectivo, 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $v_tar_com, 2 ) ?></td>
					<td class="text-right"><?= '$'.number_format( $v_total, 2 ) ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="col-md-6">
		<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
			<div class="row">
				<div class="col-md-2">Importe</div>
				<div class="col-md-4">
					<input type="text" name="cor_importe" class="form-control" required="required" maxlength="8" />
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-2">Notas</div>
				<div class="col-md-10">
					<input type="text" name="cor_observaciones" class="form-control" maxlength="100" />
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-2">&nbsp;</div>
				<div class="col-md-10">
					<input type="hidden" name="continuar" value="true" />
					<input type="hidden" name="fecha_mov" value="<?= $fecha_mov ?>" />
					<input type="submit" name="enviar" class="btn btn-primary" value="Procesar" />
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
		<h5 class="text-primary"><strong>Cortes de caja realizados</strong></h5>
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