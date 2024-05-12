<?php
	require_once( "../../../funciones_globales/funciones_conexion.php" );
	require_once( "../../../funciones_globales/funciones_comunes.php" );
	require_once( "../../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$folio		= request_var( 'folio', 0 );
	$id_venta	= request_var( 'id_venta', 0 );
	$venta		= array();
	$entregado	= false;
	$mensaje	= "";
	$boton		= "";
	
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
										ELSE ven_status
									END AS status_desc,
									ven_status AS status,
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
			
			if( $venta['status'] == 'E' || $venta['status'] == 'T' )
				$entregado = true;
			
			if( !$entregado )
			{
				$mensaje = "<div class='row'>
								<div class='col-md-12'>
									<p class='alert alert-danger'>No se puede hacer devolución de un folio que no ha sido entregado</p>
								</div>
							</div>";
			}
			else
			{
				$boton = "<button type='button' onclick='cambiar_status_commit( $folio, $id_venta )' class='btn btn-primary'>Confirmar</button>";
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
				<div class="col-md-10">
					<h4 class="modal-title text-info">Ticket seleccionado</h4>
				</div>
			</div>
		</div>
		
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">Si se confirma la cancelación, se regresará al estado enterior de la nota.</div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Folio</label>
				<label class="col-md-8"><?= "F".str_pad( $folio, 7, '0', STR_PAD_LEFT ) ?></label>
			</div>
			
			<div class="row">
				<label class="col-md-2">Cliente</label>
				<label class="col-md-8"><?= $venta['cliente'] ?></label>
			</div>
			
			<div class="row">
				<label class="col-md-2">STATUS</label>
				<label class="col-md-10"><?= $venta['status_desc'] ?></label>
			</div>
			
			<?= $mensaje ?>
		</div>
		
		<div class="modal-footer">
			<label id="msj_ticket">&nbsp;</label>
			<label id="img_ticket">&nbsp;</label>
			<label id="btn_ticket">
				<?= $boton ?>
				<button type="button" data-dismiss="modal" class="btn btn-default">Salir</button>
			</label>
		</div>
	</div>
</div>