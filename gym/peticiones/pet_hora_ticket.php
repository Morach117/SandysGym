<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$envio		= isset( $_POST['envio'] ) ? true:false;
	$id_hora	= request_var( 'id_hora', 0 );
	$token		= request_var( 'token', '' );
	$chk_token	= hash_hmac( 'md5', $id_hora, $gbl_key );
	$datos		= "";
	
	if( $envio )
	{
		if( $chk_token == $token )
		{
			$query		= "	SELECT		UPPER( hor_nombre ) AS cliente,
										DATE_FORMAT( hor_fecha, '%d-%m-%Y %r' ) AS fecha,
										hor_horas AS horas,
										hor_hora_inicial AS inicio,
										hor_hora_final AS fin,
										ser_descripcion AS modalidad,
										ROUND( hor_importe, 2 ) AS importe,
										UPPER( CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) ) AS cajero
							FROM 		san_horas
							INNER JOIN	san_usuarios ON usua_id_usuario = hor_id_usuario
							INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
							WHERE		hor_id_horas = $id_hora
							AND			hor_id_empresa = $id_empresa
							AND			hor_status = 'A'";
							
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$datos = "	<label>Cajero: </label> $fila[cajero] <br/>
								<label>Cliente: </label> $fila[cliente] <br/>
								<label>Fecha: </label> ".fecha_generica( $fila['fecha'], true )." <br/>
								<label>Horas: </label> $fila[horas] <br/>
								<label>Entrada: </label> $fila[inicio] <br/>
								<label>Salida: </label> $fila[fin] <br/>
								<label>Importe: </label> $$fila[importe] <br/>
								<label>Modalidad: </label> $fila[modalidad] <br/>";
				}
			}
		}
		else
		{
			$datos		= "Token no válido.";
			$id_visita	= 0;
			$token		= '';
		};
	}
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title text-primary">Venta de Servicios.</h4>
		</div>
		
		<div class="modal-body">
			<?= $datos ?>
		</div>
		
		<div class="modal-footer">
			<label id="msj_procesar">&nbsp;</label>
			<label id="img_procesar">&nbsp;</label>
			
			<label id="btn_procesar">
				<button type="button" data-dismiss="modal" class="btn btn-default">No imprimir</button>
				<button type="button" onclick="imprimir_ticket_hora( <?= $id_hora.", '".$token."'" ?> )" class="btn btn-primary">Imprimir Ticket</button>
			</label>
		</div>
	</div>
</div>