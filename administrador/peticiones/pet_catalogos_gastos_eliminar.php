<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	
	require_once( "../funciones/sesiones.php" );
	require_once( "../funciones/funciones_catalogos_gastos.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$id_gasto	= request_var( 'id_gasto', 0 );
	$datos		= "";
	
	if( $enviar )
		$datos	= obtener_gasto_detalle( $id_gasto );
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h5 class="modal-title"><span class='glyphicon glyphicon-remove-sign'></span> Eliminar este gasto</h5>
		</div>
		
		<div class="modal-body">
			<?= $datos ?>
		</div>
		
		<div class="modal-footer">
			<div class="pull-right" id="btn_procesar">
				<button type="button" onclick="eliminar_detalle_gasto_commit( <?= $id_gasto ?> )" class="btn btn-danger">Si. Eliminar</button>
				<button type="button" data-dismiss="modal" class="btn btn-default">No. Salir</button>
			</div>
			
			<div class="pull-right page-header" id="msj_procesar">¿Estas seguro de eliminar este gasto?</div>
			<div class="pull-right" id="img_procesar">&nbsp;</div>
		</div>
	</div>
</div>