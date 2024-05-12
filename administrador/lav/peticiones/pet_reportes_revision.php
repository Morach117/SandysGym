<?php
	require_once( "../../../funciones_globales/funciones_conexion.php" );
	require_once( "../../../funciones_globales/funciones_comunes.php" );
	require_once( "../../../funciones_globales/funciones_phpBB.php" );
	
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$id_venta	= request_var( 'id_venta', 0 );
	$folio		= request_var( 'folio', 0 );
	$venta		= array();
	$ocultar	= "hide";
	$obs		= "";
	$detalle	= "";
	
	//falta validar que las tres consultas generen un resultado y si no fuera asi denegar la informacion
	
	if( $enviar )
	{
		//la venta L=lav y S=planch
		$query		= "	SELECT		DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) AS movimiento,
									DATE_FORMAT( ven_entrega, '%d-%m-%Y %r' ) AS entrega,
									ROUND( ven_total - ven_total_credito ) AS efectivo,
									ROUND( ven_total_credito, 2 ) AS credito,
									ROUND( ven_total, 2 ) AS total,
									CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS cliente
						FROM		san_venta
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						WHERE		ven_id_venta = $id_venta
						AND			ven_folio = $folio
						AND			ven_id_empresa = $id_empresa
						AND			ven_status IN ( 'E','T' )";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				$venta = $fila;
			
			if( $venta['credito'] > 0 )
				$ocultar = "";
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
		
		//las observaciones
		$query		= "	SELECT		LOWER( DATE_FORMAT( venh_fecha, '%d-%m-%Y %r' ) ) AS movimiento,
									venh_observaciones AS obs
						FROM		san_venta_historico
						WHERE		venh_id_venta = $id_venta
						AND			venh_folio = $folio 
						AND			venh_observaciones != ''
						ORDER BY	venh_id_historico DESC";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$obs .= "<div class='bg-info'><strong>$fila[movimiento]</strong>: $fila[obs]</div>";
			}
		}
	}
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			
			<div class="row-min">
				<label class="col-md-1">Folio</label>
				<h4 class="col-md-8"><?= "F".str_pad( $folio, 7, '0', STR_PAD_LEFT ) ?></h4>
			</div>
			
			<div class="row-min">
				<label class="col-md-1">Cliente</label>
				<h4 class="col-md-8"><?= $venta['cliente'] ?></h4>
			</div>
		</div>
		
		<div class="modal-body">
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
			
			<div class="row-min text-right h4">
				<div class="col-md-10">Total:</div>
				<div class="col-md-2">$<?= $venta['total'] ?></div>
			</div>
			
			<div class="row-min text-right h4">
				<div class="col-md-10">Efectivo/Anticipo</div>
				<div class="col-md-2">$<?= $venta['efectivo'] ?></div>
			</div>
			
			<div class="row-min text-right h4 text-danger">
				<div class="col-md-10">Por pagar</div>
				<div class="col-md-2">$<?= $venta['credito'] ?></div>
			</div>
			
			<label>Observaciones</label>
			
			<div class="row">
				<div class="col-md-12"><textarea maxlength="100" class="form-control" rows="2" id="observaciones"></textarea></div>
			</div>
			
			<?= $obs ?>
		</div>
		
		<div class="modal-footer">
			<label id="msj_procesar">¿Deseas cambiar el status del ticket a REVISADO?</label>
			<label id="img_procesar">&nbsp;</label>
			
			<label id="btn_procesar">
				<button type="button" onclick="cambiar_status( <?= $id_venta.", ".$folio ?> )" class="btn btn-primary">Si</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
			</label>
		</div>
	</div>
</div>