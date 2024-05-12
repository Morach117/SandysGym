<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$like			= request_var( 'criterio', '' );
	$tabla			= "";
	
	if( $enviar )
	{
		$query		= "	SELECT		soc_id_socio AS id_socio,
									soc_apepat AS apepat,
									soc_apemat AS apemat,
									soc_nombres AS nombres,
									soc_descuento AS descuento
						FROM		san_socios
						WHERE		soc_id_empresa = $id_empresa
						AND			(
										LOWER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) LIKE LOWER( '%$like%' )
									)
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
	
	echo $tabla;
	
	mysqli_close( $conexion );
?>