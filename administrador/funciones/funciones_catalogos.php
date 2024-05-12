<?php
	function obtener_aplicaciones( $id_giro_usuario_editar = 0 )
	{
		global $conexion, $id_consorcio;
		$datos		= array();
		
		/*el tipo_usuario es del actual, cuado se actualiza uno, de toma el id_giro_usuario_editar del que se edita, no del concurrente*/
		if( $id_giro_usuario_editar )
			$id_giro = $id_giro_usuario_editar;
		
		$query		= "	SELECT 		DISTINCT( api_id_api ) AS api,
									api_secciones AS seccion,
									api_item AS item,
									api_item_excepto AS item_excepto,
									gir_descripcion AS descripcion
						FROM 		san_aplicaciones
						INNER JOIN	san_giros ON gir_id_giro = api_id_giro
						INNER JOIN	san_empresas ON emp_id_giro = api_id_giro
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						WHERE		coem_id_consorcio = $id_consorcio
						ORDER BY	descripcion,
									seccion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $datos, $fila );
		}
		else
			echo "Error: ".mysqli_error( $conexion );
			
		return $datos;
	}
	
?>