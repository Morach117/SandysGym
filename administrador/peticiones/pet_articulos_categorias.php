<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	
	mysqli_close( $conexion );
	
	if( $enviar )
	{
?>
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h5 class="modal-title text-primary">Nueva Categoria</h5>
				</div>
				
				<div class="modal-body">
					<input type="text" id="cat_descripcion" maxlength="50" class="form-control" />
				</div>
				
				<div class="modal-footer">
					<label id="msj_procesar">&nbsp;</label>
					<label id="img_procesar">&nbsp;</label>
					
					<label id="btn_procesar">
						<button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
						<button type="button" onclick="guardar_categoria()" class="btn btn-primary">Guardar</button>
					</label>
				</div>
			</div>
		</div>
<?php
	}
?>