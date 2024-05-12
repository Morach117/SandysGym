<?php
	function inversion()
	{
		global $conexion, $id_empresa, $id_consorcio;
		
		$query		= "	SELECT	a.cantidad,
								a.costos,
								a.precios,
								( a.precios - a.costos ) AS diferencia
						FROM
						(
							SELECT		ROUND( SUM( stk_existencia ), 2 ) AS cantidad,
										ROUND( SUM( art_costo * stk_existencia ), 2 ) AS costos,
										ROUND( SUM( art_precio * stk_existencia ), 2 ) AS precios
							FROM		san_stock
							INNER JOIN	san_articulos ON art_id_articulo = stk_id_articulo
							WHERE		stk_id_empresa = $id_empresa
							AND			art_id_consorcio = $id_consorcio
						) a";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		else
			"Error L28: ".mysqli_error( $conexion );
		
		return false;
	}
	
?>