<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$js_id_articulo	= request_var( 'id_articulo', 0 );
	$js_id_transfer	= request_var( 'folio', 0 );
	$js_seccion		= request_var( 'seccion', '' );
	$datos			= "";
	$encabezado		= "";
	$condicion		= "";
	$bandera		= true;
	
	//detalle del articulo
	
	$query		= "	SELECT	art_codigo AS codigo,
							art_descripcion AS descripcion,
							art_costo AS costo,
							art_precio AS precio
					FROM	san_articulos
					WHERE	art_id_articulo = $js_id_articulo
					AND		art_id_consorcio = $id_consorcio";
	
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $resultado )
	{
		if( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$encabezado	.= "<div class='row-min'>
								<label class='col-md-2'>Código</label>
								<div class='col-md-4'>$fila[codigo]</div>
								
								<label class='col-md-1'>Costo</label>
								<div class='col-md-2'>$".number_format( $fila['costo'], 2 )."</div>
								
								<label class='col-md-1'>Precio</label>
								<div class='col-md-2'>$".number_format( $fila['precio'], 2 )."</div>
							</div>
							
							<div class='row-min'>
								<label class='col-md-2'>Descripción</label>
								<div class='col-md-10'>$fila[descripcion]</div>
							</div>";
		}
		
		mysqli_free_result( $resultado );
	}
	
	//detalle de la transferencia
	
	if( $js_id_transfer )
		$condicion = "AND trans_id_transferencia = $js_id_transfer";
	
	$query		= "	SELECT		trans_id_transferencia AS id_tranfer,
								trans_id_origen AS id_origen,
								trans_id_destino AS id_destino,
								a.stk_existencia AS en_origen,
								b.stk_existencia AS en_destino,
								trand_cantidad AS en_tranferencia,
								IF( trand_cantidad IS NULL, 'I', 'U' ) AS movimiento,
								CONCAT( 'TRA', LPAD( trans_id_transferencia, 7, 0 ) ) AS folio_desc,
								x.emp_descripcion AS origen,
								y.emp_descripcion AS destino,
								DATE_FORMAT( trans_fecha_entrega, '%d-%m-%Y' ) AS entrega
					FROM		san_articulos
					INNER JOIN	san_transferencia ON trans_id_consorcio = art_id_consorcio
					AND			trans_status = 'A'
					INNER JOIN	san_empresas x ON x.emp_id_empresa = trans_id_origen
					INNER JOIN	san_empresas y ON y.emp_id_empresa = trans_id_destino
					INNER JOIN	san_stock a ON a.stk_id_empresa = trans_id_origen
					AND			a.stk_id_articulo = art_id_articulo
					LEFT JOIN	san_stock b ON b.stk_id_empresa = trans_id_destino
					AND			b.stk_id_articulo = art_id_articulo
					LEFT JOIN	san_transferencia_detalle ON trand_id_transferencia = trans_id_transferencia
					AND			trand_id_articulo = art_id_articulo
					WHERE		art_id_consorcio = $id_consorcio
					AND			art_id_articulo = $js_id_articulo
								$condicion
					ORDER BY	trans_fecha_entrega";
	
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $resultado )
	{
		while( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$fecha	= fecha_generica( $fila['entrega'] );
			
			if( $bandera )
			{
				$bg_class	= "bg-info";
				$bandera	= false;
			}
			else
			{
				$bg_class	= "bg-danger";
				$bandera	= true;
			}
			
			$datos	.= "<div class='$bg_class'>
							<div class='row-min'>
								<label class='col-md-2'>Folio</label>
								<div class='col-md-4'>$fila[folio_desc]</div>
							
								<label class='col-md-2'>Entrega</label>
								<div class='col-md-4'>$fecha</div>
							</div>
							
							<div class='row-min'>
								<label class='col-md-2'>Origen</label>
								<div class='col-md-4'>$fila[origen]</div>
								
								<label class='col-md-2'>Destino</label>
								<div class='col-md-4'>$fila[destino]</div>
							</div>
							
							<div class='row-min'>
								<label class='col-md-2'>En origen</label>
								<div class='col-md-1'>".number_format( $fila['en_origen'], 2 )."</div>
								
								<label class='col-md-2'>En destino</label>
								<div class='col-md-1'>".number_format( $fila['en_destino'], 2 )."</div>
								
								<label class='col-md-2'>En tránsito</label>
								<div class='col-md-1'>".number_format( $fila['en_tranferencia'], 2 )."</div>
								
								<label class='col-md-2'>A transferir</label>
								<div class='col-md-1'>
									<input type='text' id='tcan_$fila[id_tranfer]' class='form-control' maxlength='4' placeholder='+/-' />
									<input type='hidden' id='tfol_$fila[id_tranfer]' value='$fila[id_tranfer]' />
									<input type='hidden' id='tmov_$fila[id_tranfer]' value='$fila[movimiento]' />
								</div>
							</div>
						</div>";
		}
		
		if( !$datos )
			$datos	= "	<div class='row'><div class='col-md-12'>No hay transferencias ABIERTAS.</div></div>";
		
		mysqli_free_result( $resultado );
	}
	else
		$datos	= "	<div class='row'><div class='col-md-12'>Ocurrió un problema técnico al obtener las transferencias ABIERTAS. ".mysqli_error( $conexion )."</div></div>";
	
	mysqli_close( $conexion );
	
?>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h5 class="modal-title text-primary">Transferencias activas de artículos</h5>
			<br/>
			
			<?= $encabezado ?>
		</div>
		
		<div class="modal-body">
			<?= $datos ?>
		</div>
		
		<div class="modal-footer">
			<div id="btn_procesar" class="pull-right">
				<button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
				<button type="button" onclick="guardar_transferencia_articulo( <?= "$js_id_articulo, $js_id_transfer, '$js_seccion'" ?> )" class="btn btn-primary">Agregar</button>
			</div>
			
			<div id="msj_procesar" class="pull-right page-header">Las cantidades a transferir serán descontados del origen al momento de agregar</div>
			<div id="img_procesar" class="pull-right">&nbsp;</div>
		</div>
	</div>
</div>