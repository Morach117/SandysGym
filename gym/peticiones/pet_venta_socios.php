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
									prep_id_prepago AS id_prepago,
									soc_apepat AS apepat,
									soc_apemat AS apemat,
									soc_nombres AS nombres,
									prep_saldo AS saldo
						FROM		san_socios
						INNER JOIN	san_prepago ON prep_id_socio = soc_id_socio
						AND			prep_id_empresa = soc_id_empresa
						WHERE		prep_id_empresa = $id_empresa
						ORDER BY	saldo DESC,
									apepat,
									apemat,
									nombres";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		while( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$saldo	= number_format( $fila['saldo'], 2 );
			
			if( $fila['saldo'] )
				$tabla .= "	<tr onclick='seleccionar_socio( $fila[id_socio], $fila[id_prepago], $fila[saldo], \"$fila[apepat] $fila[apemat] $fila[nombres]\" )'>";
			else
				$tabla .= "	<tr>";
			
			$tabla .= "		<td>$fila[apepat]</td>
							<td>$fila[apemat]</td>
							<td>$fila[nombres]</td>
							<td class='text-right'>$$saldo</td>
						</tr>";
		}
	}
	else
		$mensaje	= "<li>Operación inválida.</li>";
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title text-primary">Socios con Prepago.</h4>
		</div>
		
		<div class="modal-body">
			<ul><?= $mensaje ?></ul>

			<table class="table table-hover pointer">
				<thead>
					<tr class="active">
						<th>A Paterno</th>
						<th>A Materno</th>
						<th>Nombres</th>
						<th>Saldo</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
		
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-primary">Cerrar</button>
		</div>
	</div>
</div>