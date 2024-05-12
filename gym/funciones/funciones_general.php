<?php
	function obtener_detalle_articulo( $id_articulo )
	{
		global $conexion, $id_empresa;
		
		mysqli_autocommit( $conexion, false );//se desactiva la transaccion para que no afecte a la funcion principal
		
		$query		= "	SELECT 
							* 
						FROM 
							san_cat_articulos 
						LEFT JOIN
							san_stock ON stk_id_articulo = art_id_articulo
						AND
							stk_codigo = art_codigo
						AND
							stk_id_empresa = $id_empresa
						WHERE
							art_id_articulo = $id_articulo";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		
		return false;
	}
	
?>