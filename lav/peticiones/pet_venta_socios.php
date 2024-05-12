<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$tabla			= "";
	$mensaje		= "";
	
	if( $enviar )
	{
		$query		= "	SELECT		soc_id_socio AS id_socio,
									soc_apepat AS apepat,
									soc_apemat AS apemat,
									soc_nombres AS nombres,
									soc_descuento AS descuento
						FROM		san_socios
						WHERE		soc_id_empresa = $id_empresa
						ORDER BY	apepat,
									apemat,
									nombres
						LIMIT		0, 100";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		while( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$tabla .= "	<tr onclick='seleccionar_socio( $fila[id_socio], $fila[descuento], \"$fila[apepat] $fila[apemat] $fila[nombres]\" )'>
							<td>$fila[apepat]</td>
							<td>$fila[apemat]</td>
							<td>$fila[nombres]</td>
						</tr>";
		}
	}
	else
	{
		$tabla .= "	<tr>
						<td colspan='3'>No se valido el envio del formulario.</td>
					</tr>";
	}
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title text-primary">Selecciona un Cliente de la lista</h4>
		</div>
		
		<div class="modal-body">
			<div class="row">
				<div class="col-md-10"><input type="text" id="nombre_cliente" class="form-control" placeholder="Escribe el nombre de un Cliente para buscar" autofocus /></div>
				<div class="col-md-2"><button type="button" class="btn btn-sm btn-primary" onclick="buscar_clientes()">&nbsp;Buscar&nbsp;</button></div>
			</div>
			
			<div class="row">
				<div class="col-md-3"><input type="text" id="soc_nombres" class="form-control" placeholder="Nombres" /></div>
				<div class="col-md-2"><input type="text" id="soc_apepat" class="form-control" placeholder="A Paterno" /></div>
				<div class="col-md-2"><input type="text" id="soc_apemat" class="form-control" placeholder="A Materno" /></div>
				<div class="col-md-3"><input type="text" id="soc_correo" class="form-control" placeholder="Correo" /></div>
				
				<div class="col-md-2"><button type="button" class="btn btn-sm btn-default" onclick="agregar_cliente()">Agregar</button></div>
			</div>
			
			<table class="table table-hover pointer">
				<thead>
					<tr class="active">
						<th>A Paterno</th>
						<th>A Materno</th>
						<th>Nombres</th>
					</tr>
				</thead>
				
				<tbody id="lista_clientes">
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
		
		<div class="modal-footer">
			<label class="text-danger" id="modal_venta_mensajes"></label>
			<button type="button" data-dismiss="modal" class="btn btn-primary">Cerrar</button>
		</div>
	</div>
</div>