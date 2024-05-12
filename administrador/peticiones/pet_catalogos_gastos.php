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
			<h5 class="modal-title"><span class='glyphicon glyphicon-list'></span> Detalles del gasto</h5>
		</div>
		
		<div class="modal-body">
			<?= $datos ?>
		</div>
		
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-default">Salir</button>
		</div>
	</div>
</div>