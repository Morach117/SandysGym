<?php
	function obtener_lista_marcas()
	{
		global $conexion, $id_consorcio;
		
		$datos		= "";
		$colspan	= 0;
		
		$query		= "	SELECT		mar_id_marca AS id_marca,
									mar_id_consorcio AS id_consorcio,
									mar_descripcion AS descripcion
						FROM		san_marcas
						WHERE		mar_id_consorcio = $id_consorcio
						ORDER BY	descripcion";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$i</td>
								<td>$fila[id_marca]</td>
								<td>$fila[descripcion]</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
?>