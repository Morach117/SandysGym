<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$id_venta	= request_var( 'id_venta', 0 );
	$folio		= request_var( 'folio', 0 );
	$venta		= array();
	$danger		= "";
	$obs		= "";
	$detalle	= "";
	$usuarios	= "";
	
	if( $enviar )
	{
		//la venta
		$query		= "	SELECT		DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) AS movimiento,
									DATE_FORMAT( ven_entrega, '%d-%m-%Y %r' ) AS entrega,
									ROUND( ven_total_efectivo, 2 ) AS efectivo,
									ROUND( ven_total_credito, 2 ) AS credito,
									ROUND( ven_total, 2 ) AS total,
									CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS cliente
						FROM		san_venta
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						WHERE		ven_id_venta = $id_venta
						AND			ven_folio = $folio
						AND			ven_id_empresa = $id_empresa
						AND			ven_status = 'R'";
						
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
				$obs .= "<div class='bg-info text-info'><strong>$fila[movimiento]</strong>: $fila[obs]</div>";
			}
		}
		
		/*CCUANDO SEA CONSORCIO DEBERA CAMBIARSE A CONSORCIO NO TIPO DE EMPRESA*/
		//los usuarios que pudieron haber lavado del mismo tipo de empresa
		$query		= "	SELECT		usua_id_usuario AS id_usuario,
									CONCAT( usua_ape_pat, ' ', usua_ape_mat ) AS apellidos,
									usua_nombres AS nombres
						FROM		san_usuarios
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = usua_id_empresa
						WHERE		usua_id_empresa IN ( SELECT emp_id_empresa FROM san_empresas WHERE emp_id_giro = $id_giro )
						AND			usua_status != 'B'
						AND			coem_id_consorcio = $id_consorcio
						ORDER BY	nombres,
									apellidos";
		
						// WHERE		usua_id_empresa = $id_empresa //
						
		$resultado	= mysqli_query( $conexion, $query );
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $id_usuario == $fila['id_usuario'] )
					$bg = "style='background-color:#CA5844'";
				else
					$bg = "";
					
				$usuarios .= "	<li $bg id='li_$fila[id_usuario]' onclick='seleccionar_usuario( $fila[id_usuario] )'>
									<span class='glyphicon glyphicon-user'></span>
									<h5><strong>$fila[nombres]</strong></h5>
									<span class='touch-class'>$fila[apellidos]</span>
								</li>";
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
				<div class="col-md-10">Total</div>
				<div class="col-md-2">$<?= $venta['total'] ?></div>
			</div>
			
			<div class="row-min text-right h4">
				<div class="col-md-10">Efectivo/Anticipo</div>
				<div class="col-md-2">$<?= $venta['efectivo'] ?></div>
			</div>
			
			<div class="row-min text-right h4 <?= $danger ?>">
				<div class="col-md-10">Por pagar</div>
				<div class="col-md-2">$<?= $venta['credito'] ?></div>
			</div>
			
			<label>Observaciones y Anticipo</label>
			
			<div class="row text-right">
				<div class="col-md-8"><textarea maxlength="100" class="form-control" rows="2" id="observaciones"></textarea></div>
				
				<div class="col-md-2">
					<input type="text" id="pagar_adeudo" class="form-control text-success" value="0" onclick="seleccionar(this)" />
				</div>
				
				<div class="col-md-2" id="guardar_anticipo">
					<button type="button" onclick="cambiar_status( <?= $id_venta.", ".$folio ?>, 'N' )" class="btn btn-primary btn-sm">Guardar</button>
				</div>
			</div>
			
			<label>¿Quién lavó?</label>
			
			<div class="bs-touch-sm">
				<ul class="bs-touch-list pointer">
					<?= $usuarios ?>
				</ul>
			</div>
			
			<?= $obs ?>
		</div>
		
		<div class="modal-footer">
			<input type="hidden" id="id_usuario" value="<?= ( $rol != 'S' ) ? $id_usuario : 0 ?>" />
			<input type="hidden" id="por_pagar" value="<?= $venta['credito'] ?>" />
			
			<label id="msj_procesar">&nbsp;</label>
			<label id="img_procesar">&nbsp;</label>
			
			<label id="btn_procesar">
				<button type="button" onclick="cambiar_status( <?= $id_venta.", ".$folio ?>, 'S' )" class="btn btn-primary">Lavado</button>
			</label>
		</div>
	</div>
</div>