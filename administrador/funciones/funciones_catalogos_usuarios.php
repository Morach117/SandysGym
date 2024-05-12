<?php
	function obtener_usuarios()
	{
		global $conexion, $id_consorcio;
		$datos		= '';
		$class		= '';
		$colspan	= 7;
		$contador	= 1;
		
		$query		= "	SELECT 		usua_id_usuario AS id_usuario,
									usua_id_empresa AS id_empresa,
									usua_status AS status,
									CASE usua_status
										WHEN 'A' THEN 'Activo'
										WHEN 'B' THEN 'Baja'
									END AS status_desc,
									CASE usua_rol
										WHEN 'S' THEN 'Administrador'
										WHEN 'O' THEN 'Operativo'
										WHEN 'R' THEN 'Supervisor'
										WHEN 'M' THEN 'Operativo Multisucursal'
										ELSE usua_rol
									END as rol_desc,
									usua_rol AS rol,
									emp_descripcion AS empresa,
									CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) AS nombres,
									usua_correo AS correo,
									usua_pass_web AS pass
						FROM 		san_usuarios
						INNER JOIN	san_empresas ON emp_id_empresa = usua_id_empresa
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = usua_id_empresa
						WHERE		coem_id_consorcio = $id_consorcio
						ORDER BY	status,
									empresa,
									nombres";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$class	= ( $fila['status'] == 'B' ) ? 'danger':'';
				
				if( $fila['rol'] == 'S' )
				{
					$datos	.= "<tr class='info'>
									<td>$contador</td>
									<td>$fila[empresa]</td>
									<td>$fila[nombres]</td>
									<td>$fila[status_desc]</td>
									<td>$fila[correo]</td>
									<td>&nbsp;</td>
									<td>$fila[rol_desc]</td>
								</tr>";
				}
				else
				{
					$datos	.= "<tr class='pointer $class' onclick='location.href=\".?s=catalogos&i=usuario&usuarioe=$fila[id_usuario]$fila[id_empresa]\"'>
									<td>$contador</td>
									<td>$fila[empresa]</td>
									<td>$fila[nombres]</td>
									<td>$fila[status_desc]</td>
									<td>$fila[correo]</td>
									<td>$fila[pass]</td>
									<td>$fila[rol_desc]</td>
								</tr>";
				}
				
				$contador++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
			
		return $datos;
	}
	
?>