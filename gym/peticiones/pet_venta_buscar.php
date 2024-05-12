<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$criterio		= request_var( 'criterio', '' );
	$tabla			= "";
	$condicion		= "";
	
	if( $enviar )
	{
		if( $criterio )
			$condicion	= "AND ( art_codigo like '%$criterio%' OR lower( art_descripcion ) like lower( '%$criterio%' ) )";
		
		$query		= "	SELECT		art_id_articulo AS id_articulo,
									art_codigo AS codigo,
									art_descripcion AS descripcion,
									stk_existencia AS existencia,
									ROUND( art_precio, 2 ) AS precio
						FROM		san_articulos
						INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
						AND			stk_id_empresa = $id_empresa
						AND			art_id_consorcio = $id_consorcio
									$condicion
						ORDER BY	existencia DESC,
									descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$class	= '';
				if( $fila['existencia'] <= 0 )
					$class = "danger";
				
				$tabla	.= "<tr onclick='agregar_articulo_venta( $fila[id_articulo] )' class='$class'>
								<td>".$fila['descripcion']."</td>
								<td class='text-right'>".$fila['existencia']."</td>
								<td class='text-right'>$".$fila['precio']."</td>
							</tr>";
			}
		}
		else
			$tabla	= "	<tr><td colspan='3'>".mysqli_error( $conexion )."</td></tr>";
	}
	
	if( !$tabla )
		$tabla	= "	<tr><td colspan='3'>No hay datos.</td></tr>";
	
	echo $tabla;
	
	mysqli_close( $conexion );
?>