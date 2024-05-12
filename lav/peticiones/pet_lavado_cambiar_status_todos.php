<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	require_once( "../funciones/funciones_lavado.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$lavados	= "";
	
	if( $enviar )
		$lavados	= obtener_lavados();
?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			
			<div class="row-min">
				<h4 class="col-md-12">¿Estas seguro de cambiar el status a LAVADO de los siguientes folios recepcionados?</h4>
			</div>
		</div>
		
		<div class="modal-body">
			<table class="table table-hover">
				<thead>
					<tr class="active">
						<th>#</th>
						<th>Primer movimiento</th>
						<th>Cliente</th>
						<th>Ultima observación</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $lavados ?>
				</tbody>
			</table>
		</div>
		
		<div class="modal-footer">
			<label id="msj_procesar">&nbsp;</label>
			<label id="img_procesar">&nbsp;</label>
			
			<label id="btn_procesar">
				<button type="button" onclick="cambiar_status_todos_commit()" class="btn btn-primary">Confirmar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
			</label>
		</div>
	</div>
</div>