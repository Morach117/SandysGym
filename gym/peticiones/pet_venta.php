<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$id_articulo	= request_var( 'id_articulo', '' );
	$descripcion	= '';
	$tabla			= '';
	
	if( $enviar )
	{
		$query		= "	SELECT		art_id_articulo AS id_articulo,
									art_codigo AS codigo,
									art_descripcion AS descripcion,
									ROUND( art_precio, 2 ) AS precio
						FROM		san_articulos
						LEFT JOIN	san_stock ON stk_id_articulo = art_id_articulo
						AND			stk_id_empresa = $id_empresa
						AND			art_id_consorcio = $id_consorcio
						WHERE		art_id_articulo = $id_articulo";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$descripcion	= $fila['descripcion'];
				
				$tabla	= "	<tr id='art_$id_articulo'>
								<td onclick='quitar_de_lista( $id_articulo )' class='text-danger pointer'>
									<span class='glyphicon glyphicon-remove-sign'></span>
								</td>
								<td style='width:20px'>
									<input type='text' id='can_$id_articulo' name='can_$id_articulo' class='form-control' value='1' required='required' maxlength='2' onKeyUp='calcular_importe( $id_articulo )' />
									<input type='hidden' name='art_$id_articulo' value='$id_articulo' />
									<input type='hidden' id='pre_$id_articulo' value='$fila[precio]' />
								</td>
								<td>$descripcion</td>
								<td class='text-right'>$$fila[precio]</td>
								<td class='text-right' id='imp_$id_articulo'>$$fila[precio]</td>
							</tr>";
			}
		}
		else
			$tabla = "<tr><td colspan='5'>Error encontrado: ".mysqli_error( $conexion )."</td></tr>";
	}
	
	echo $tabla;
	
	mysqli_close( $conexion );
?>