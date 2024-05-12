<?php
	require_once( "../../../funciones_globales/funciones_conexion.php" );
	require_once( "../../../funciones_globales/funciones_comunes.php" );
	require_once( "../../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$folio		= request_var( 'folio', 0 );
	$id_venta	= request_var( 'id_venta', 0 );
	$venta		= array();
	$danger		= "";
	$detalle	= "";
	$historico	= "";
	
	//verificar que todos los querys regresen datos
	if( $enviar )
	{
		//la venta
		$query		= "	SELECT		DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) AS movimiento,
									DATE_FORMAT( ven_entrega, '%d-%m-%Y %r' ) AS entrega,
									CASE ven_status
										WHEN 'R' THEN 'RECEPCIONADO'
										WHEN 'L' THEN 'LAVADO'
										WHEN 'E' THEN 'ENTREGADO'
										WHEN 'C' THEN 'CANCELADO'
										
										WHEN 'I' THEN 'INACTIVO'
										
										WHEN 'S' THEN 'PARA PLANCHAR'
										WHEN 'T' THEN 'PLANCHADO Y ENTREGADO'
										WHEN 'Z' THEN 'REVISADO'
										
									END AS status,
									ROUND( ven_total_efectivo, 2 ) AS efectivo,
									ROUND( ven_total_credito, 2 ) AS credito,
									ROUND( ven_total, 2 ) AS total,
									CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS cliente
						FROM		san_venta
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						WHERE		ven_id_venta = $id_venta
						AND			ven_folio = $folio
						AND			ven_id_empresa = $id_empresa";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				$venta = $fila;
			
			if( $venta['credito'] > 0 )
				$danger		= "text-danger";
		}
		
		//el detalle de la venta
		$query		= "	SELECT		ser_descripcion AS descripcion,
									ROUND( vense_kilogramo, 2 ) AS kg,
									ROUND( vense_precio, 2 ) AS precio,
									ROUND( vense_importe, 2 ) AS importe
						FROM		san_venta_servicio
						INNER JOIN	san_servicios ON ser_id_servicio = vense_id_servicio
						WHERE		vense_id_venta = $id_venta";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$detalle	.= "<tr>
									<td>".$fila['kg']."</td>
									<td>$fila[descripcion]</td>
									<td class='text-right'>$".$fila['precio']."</td>
									<td class='text-right'>$".$fila['importe']."</td>
								</tr>";
			}
		}
		
		//el historico
		$query		= "	SELECT		LOWER( DATE_FORMAT( venh_fecha, '%d-%m-%Y %r' ) ) AS movimiento,
									a.usua_nombres AS vendedor,
									b.usua_nombres AS lavador,
									ROUND( venh_total_efectivo, 2 ) AS efectivo,
									ROUND( venh_total_credito, 2 ) AS credito,
									ROUND( venh_total, 2 ) AS total,
									venh_status AS statusc,
									CASE venh_status
										WHEN 'R' THEN 'RECEPCIONADO'
										WHEN 'L' THEN 'LAVADO'
										WHEN 'E' THEN 'ENTREGADO'
										WHEN 'C' THEN 'CANCELADO'
										
										WHEN 'I' THEN 'INACTIVO'
										
										WHEN 'S' THEN 'PARA PLANCHAR'
										WHEN 'T' THEN 'PLANCHADO Y ENTREGADO'
										WHEN 'Z' THEN 'REVISADO'
									END AS status,
									venh_observaciones AS obs
						FROM		san_venta_historico
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = venh_id_usuario
						LEFT JOIN	san_usuarios b ON b.usua_id_usuario = venh_id_lavador
						WHERE		venh_id_venta = $id_venta
						AND			venh_folio = $folio
						ORDER BY	venh_id_historico DESC";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$historico	.= "<tr>
									<td>".$fila['movimiento']."</td>
									<td>".$fila['vendedor']."</td>
									<td>".$fila['lavador']."</td>
									<td class='text-right'>$".$fila['efectivo']."</td>
									<td class='text-right'>$".$fila['credito']."</td>
									<td class='text-right'>$".$fila['total']."</td>
									<td>".$fila['status']."</td>
									<td>".$fila['obs']."</td>
								</tr>";
			}
		}
	}
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			
			<div class="row-min">
				<div class="col-md-10">
					<h4 class="modal-title text-info">Detalle del Ticket</h4>
				</div>
			</div>
			
			<div class="row-min">
				<label class="col-md-1">Folio</label>
				<label class="col-md-8"><?= "F".str_pad( $folio, 7, '0', STR_PAD_LEFT )." | ".$venta['status'] ?></label>
			</div>
			
			<div class="row-min">
				<label class="col-md-1">Cliente</label>
				<label class="col-md-8"><?= $venta['cliente'] ?></label>
			</div>
		</div>
		
		<div class="modal-body">
			<div class="row text-info">
				<div class="col-md-10">
					<h5><strong>Detalle de la Venta</strong></h5>
				</div>
			</div>
			
			<table class="table table-hover">
				<thead>
					<tr class="active">
						<th>Kg.</th>
						<th>Servicio</th>
						<th class="text-right">Precio</th>
						<th class="text-right">Importe</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $detalle ?>
				</tbody>
			</table>
			
			<div class="row-min text-right text-bold">
				<div class="col-md-11">Total</div>
				<div class="col-md-1">$<?= $venta['total'] ?></div>
			</div>
			
			<div class="row-min text-right text-bold <?= $danger ?>">
				<div class="col-md-11">Por cobrar</div>
				<div class="col-md-1">$<?= $venta['credito'] ?></div>
			</div>
			
			<div class="row text-info">
				<div class="col-md-12">
					<h5><strong>Historico de Movientos en la Venta</strong></h5>
				</div>
			</div>
			
			<table class="table table-hover">
				<thead>
					<tr class="active">
						<th>Movientos</th>
						<th>Vendedor</th>
						<th>Lavador</th>
						<th class="text-right">Efectivo</th>
						<th class="text-right">Crédito</th>
						<th class="text-right">Total</th>
						<th>Status</th>
						<th>Observaciones</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $historico ?>
				</tbody>
			</table>
		</div>
		
		<div class="modal-footer">
			<label id="msj_ticket">&nbsp;</label>
			<label id="img_ticket">&nbsp;</label>
			<label id="btn_ticket">
				<button type="button" onclick="desactivar_ticket( <?= $folio.", ".$id_venta ?> )" class="btn btn-default">Desactivar Nota</button>
				<button type="button" onclick="eliminar_ticket( <?= $folio.", ".$id_venta ?> )" class="btn btn-danger">Cancelar Nota</button>
			</label>
			
			<button type="button" data-dismiss="modal" class="btn btn-primary">Cerrar</button>
		</div>
	</div>
</div>