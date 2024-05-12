<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$envio			= isset( $_POST['envio'] ) ? true:false;
	$id_prepago		= request_var( 'id_prepago', 0 );
	$id_prepago_d	= request_var( 'id_prepago_d', 0 );
	$id_socio		= request_var( 'id_socio', 0 );
	$token			= request_var( 'token', '' );
	$token_chk		= hash_hmac( 'md5', $id_prepago, $gbl_key );
	$datos			= "";
	
	if( $envio )
	{
		if( $token == $token_chk )
		{
			$query		= "	SELECT		ROUND( prep_saldo, 2 ) AS saldo,
										ROUND( pred_importe, 2 ) AS importe,
										pred_descripcion AS descripcion,
										UPPER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) AS cliente
							FROM		san_prepago
							INNER JOIN	san_prepago_detalle ON pred_id_prepago = prep_id_prepago
							INNER JOIN	san_socios ON soc_id_socio = prep_id_socio
							WHERE		prep_id_prepago = $id_prepago
							AND			prep_id_socio = $id_socio
							AND			prep_id_empresa = $id_empresa
							AND			pred_id_pdetalle = $id_prepago_d
							AND			pred_movimiento = 'S'";
							
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$datos = "	<label>Cliente: </label> $fila[cliente] <br/>
								<label>Movimiento: </label> $fila[descripcion] <br/>
								<label>Abono: </label> $$fila[importe] <br/>
								<label>Saldo: </label> $$fila[saldo] <br/>";
				}
			}
		}
		else
		{
			$datos		= "Token no válido.";
			$id_prepago	= 0;
			$id_prepago_d	= 0;
			$id_socio	= 0;
			$token		= "";
		}
	}
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title text-primary">Pago realizado.</h4>
		</div>
		
		<div class="modal-body">
			<?= $datos ?>
		</div>
		
		<div class="modal-footer">
			<label id="msj_procesar">&nbsp;</label>
			<label id="img_procesar">&nbsp;</label>
			
			<label id="btn_procesar">
				<button type="button" onclick="imprimir_ticket_prepago( <?= $id_prepago.", ".$id_prepago_d.", ".$id_socio.", '$token'" ?> )" class="btn btn-primary">Imprimir Ticket</button>
			</label>
		</div>
	</div>
</div>