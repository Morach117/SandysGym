<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$envio		= isset( $_POST['envio'] ) ? true:false;
	$id_pago	= request_var( 'id_pago', 0 );
	$token		= request_var( 'token', '' );
	$token_chk	= hash_hmac( 'md5', $id_pago, $gbl_key );
	$datos		= "";
	
	if( $envio )
	{
		if( $token == $token_chk )
		{
			$query		= "	SELECT		DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini,
										DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin,
										CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS cliente,
										ser_descripcion AS descripcion,
										ROUND( pag_importe, 2 ) AS importe
							FROM		san_pagos
							INNER JOIN	san_socios ON soc_id_socio = pag_id_socio
							INNER JOIN	san_servicios ON pag_id_servicio = ser_id_servicio
							WHERE		pag_id_pago = $id_pago
							AND			pag_id_empresa = $id_empresa
							AND			pag_status = 'A'";
							
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$datos = "	<label>Cliente: </label> $fila[cliente] <br/>
								<label>Inicio: </label>	".fecha_generica( $fila['fecha_ini'], true )."<br/>
								<label>Fin: </label> ".fecha_generica( $fila['fecha_fin'], true )." <br/>
								<label>Servicio: </label> $fila[descripcion] <br/>
								<label>Importe: </label> $$fila[importe] <br/>";
				}
			}
		}
		else
		{
			$datos		= "Token no válido.";
			$id_pago	= 0;
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
				<button type="button" data-dismiss="modal" class="btn btn-default">No imprimir</button>
				<button type="button" onclick="imprimir_ticket_pago( <?= $id_pago.", '$token'" ?> )" class="btn btn-primary">Imprimir Ticket</button>
			</label>
		</div>
	</div>
</div>