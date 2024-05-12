<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$cmb_sucursales	= combo_sucursales();
	$fecha_mov		= date( 'Y-m-d' );
	$js_seccion		= request_var( 'seccion', '' );
	
	mysqli_close( $conexion );
	
?>
<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h5 class="modal-title text-primary">Nueva transferencia</h5>
		</div>
		
		<div class="modal-body">
			<div class="row">
				<label class="col-md-3">Origen</label>
				<div class="col-md-9">
					<select id="t_origen" class="form-control" >
						<option value="">Sucursal de origen...</option>
						<?= $cmb_sucursales ?>
					</select>
				</div>
			</div>
				
			<div class="row">
				<label class="col-md-3">Destino</label>
				<div class="col-md-9">
					<select id="t_destino" class="form-control" >
						<option value="">Sucursal de destino...</option>
						<?= $cmb_sucursales ?>
					</select>
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Entrega</label>
				<div class="col-md-9"><input type="date" id="t_entrega" class="form-control" value="<?= $fecha_mov ?>" /></div>
			</div>
		</div>
		
		<div class="modal-footer">
			<label id="msj_procesar">&nbsp;</label>
			<label id="img_procesar">&nbsp;</label>
			
			<label id="btn_procesar">
				<button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
				<button type="button" onclick="guardar_transferencia( '<?= $js_seccion ?>' )" class="btn btn-primary">Guardar</button>
			</label>
		</div>
	</div>
</div>