<?php
	function obtener_proveedores()
	{
		global $conexion, $id_consorcio;
		
		$datos		= '';
		$colspan	= 6;
		
		$query		= "	SELECT		pro_id_proveedor AS id_proveedor,
									pro_nombres AS nombres,
									pro_direccion AS direccion,
									pro_contacto_1 AS contacto_1,
									pro_tel_fijo_1 AS telefono_1,
									pro_tel_ext_1 AS ext_1,
									pro_tel_cel_1 AS telcel_1,
									pro_correo_1 AS correo_1,
									pro_contacto_2 AS contacto_2,
									pro_tel_fijo_2 AS telefono_2,
									pro_tel_ext_2 AS ext_2,
									pro_tel_cel_2 AS telcel_2,
									pro_correo_2 AS correo_2
						FROM		san_proveedores
						WHERE		pro_id_consorcio = $id_consorcio
						ORDER BY	nombres";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$i = 1;
				$datos	.= "<tr onclick='location.href=\".?s=catalogos&i=proveedore&id_proveedor=$fila[id_proveedor]\"'>
								<td>$i</td>
								<td>".$fila['nombres']."</td>
								<td>".$fila['contacto_1']."</td>
								<td>".$fila['telefono_1']."</td>
								<td>".$fila['contacto_2']."</td>
								<td>".$fila['telefono_2']."</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>No se pudo obtener el Catalogo. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
			
		return $datos;
	}
	
?>