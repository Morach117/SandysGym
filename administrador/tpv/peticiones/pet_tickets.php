<?php
	require_once( "../../../funciones_globales/funciones_conexion.php" );
	require_once( "../../../funciones_globales/funciones_comunes.php" );
	require_once( "../../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$id_venta	= request_var( 'id_venta', '' );
	$folio		= request_var( 'folio', '' );
	$encabezado	= array();
	$detalle	= "";
	$class		= "";
	$dev_fecha	= "";
	$tot_dev	= 0;
	$tot_venta	= 0;
	
	if( $enviar )
	{
		//san_venta
		
		$query		= "	SELECT		ven_id_venta AS id_venta,
									ven_folio AS folio,
									IF( venh_fecha IS NULL, DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ), DATE_FORMAT( venh_fecha, '%d-%m-%Y %r' ) ) AS fecha_venta,
									ven_total AS total,
									ven_status AS status,
									CASE ven_status
										WHEN 'V' THEN 'Vendido'
										WHEN 'C' THEN 'Cancelación Total'
										WHEN 'P' THEN 'Cancelación Parcial'
										ELSE 'Desconocido'
									END AS status_desc,
									IF ( ven_observaciones IS NULL OR '', 'No Cancelado', ven_observaciones ) AS obs,
									CONCAT( a.usua_ape_pat, ' ', a.usua_ape_mat, ' ', a.usua_nombres ) AS cajero,
									IF( venh_fecha IS NULL, 'No cancelado', DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) ) AS dev_fecha,
									IF( venh_fecha IS NULL, 'No cancelado', CONCAT( b.usua_ape_pat, ' ', b.usua_ape_mat, ' ', b.usua_nombres ) ) AS dev_usuario
						FROM		san_venta
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = ven_id_usuario
						LEFT JOIN	san_usuarios b ON b.usua_id_usuario = ven_id_usuario
						LEFT JOIN	san_venta_historico ON venh_id_venta = ven_id_venta
						WHERE		ven_id_venta = $id_venta
						AND			ven_folio = $folio
						AND			ven_id_empresa = $id_empresa";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$encabezado = $fila;
				
				if( $fila['status'] == 'C' || $fila['status'] == 'P' )
					$dev_fecha	= fecha_generica( $fila['dev_fecha'] );
				else
					$dev_fecha = $fila['dev_fecha'];
				
				mysqli_free_result( $resultado );
			}
		}
		
		//san_venta_detalle
		
		$query		= "	SELECT		art_codigo AS codigo,
									art_descripcion AS descripcion,
									ROUND( vende_cantidad, 2 ) AS cantidad,
									vende_descuento AS descuento,
									vende_precio AS precio,
									ROUND( vende_cantidad * vende_precio, 2 ) AS importe,
									vende_status AS status
						FROM		san_venta
						INNER JOIN	san_venta_detalle a ON vende_id_venta = ven_id_venta
						INNER JOIN	san_articulos ON art_id_articulo = vende_id_articulo
						AND			art_id_consorcio = $id_consorcio
						WHERE		ven_id_venta = $id_venta
						AND			ven_folio = $folio
						AND			ven_id_empresa = $id_empresa";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['status'] == 'D' )
				{
					$class		= "danger";
					$tot_dev	+= $fila['importe'];
				}
				else
				{
					$class		= "";
					$tot_venta	+= $fila['importe'];
				}
				
				$detalle .= "	<tr class='$class'>
									<td>$i</td>
									<td>$fila[codigo]</td>
									<td>$fila[descripcion]</td>
									<td class='text-right'>".number_format( $fila['cantidad'], 2 )."</td>
									<td class='text-right'>$".number_format( $fila['descuento'], 2 )."</td>
									<td class='text-right'>$".number_format( $fila['precio'], 2 )."</td>
									<td class='text-right'>$".number_format( $fila['importe'], 2 )."</td>
								</tr>";
				$i++;
			}
		}
	}
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h5 class="modal-title text-primary text-bold">Detalle del Folio: <?= $folio ?></h5>
		</div>
		
		<div class="modal-body">
			<div class="row-min">
				<div class="col-md-2">Status</div>
				<label class="col-md-4"><?= $encabezado['status_desc'] ?></label>
				
				<div class="col-md-2">Cajero</div>
				<label class="col-md-4"><?= $encabezado['cajero'] ?></label>
			</div>
			
			<div class="row-min">
				<div class="col-md-2">Venta</div>
				<label class="col-md-10"><?= fecha_generica( $encabezado['fecha_venta'] ) ?></label>
			</div>
			
			<div class="row-min">
				<div class="col-md-12">
					<h5 class="text-info text-bold">Cancelación del Ticket</h5>
				</div>
			</div>
			
			<div class="row-min">
				<div class="col-md-2">Fecha</div>
				<label class="col-md-4"><?= $dev_fecha ?></label>
				
				<div class="col-md-2">Usuario</div>
				<label class="col-md-4"><?= $encabezado['dev_usuario'] ?></label>
			</div>
			
			<div class="row-min">
				<div class="col-md-2">Observaciones</div>
				<label class="col-md-10"><?= $encabezado['obs'] ?></label>
			</div>
			
			<div class="row-min">
				<div class="col-md-12">
					<h5 class="text-info text-bold">Detalle de la Venta</h5>
				</div>
			</div>
			
			<div class="row-min">
				<div class="col-md-12">
					<table class="table table-hover h6">
						<thead>
							<tr class="active">
								<th>#</th>
								<th>Código</th>
								<th>Descripción</th>
								<th class="text-right">Cant.</th>
								<th class="text-right">$Desc.</th>
								<th class="text-right">Precio</th>
								<th class="text-right">Importe</th>
							</tr>
						</thead>
						
						<tbody>
							<?= $detalle ?>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="row-min">
				<div class="col-md-2">Total del Tiket</div>
				<label class="col-md-1 text-right">$<?= number_format( $encabezado['total'], 2 ) ?></label>
			</div>
			
			<div class="row-min">
				<div class="col-md-2">Total Cancelado</div>
				<label class="col-md-1 text-right text-danger">$<?= number_format( $tot_dev, 2 ) ?></label>
			</div>
			
			<div class="row-min">
				<div class="col-md-2">Total Activo</div>
				<label class="col-md-1 text-right">$<?= number_format( $tot_venta, 2 ) ?></label>
			</div>
		</div>
		
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
</div>