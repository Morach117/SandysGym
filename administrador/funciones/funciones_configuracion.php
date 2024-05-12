<?php
	function lista_sucursales()
	{
		global $conexion, $id_consorcio, $id_empresa;
		
		$tabla		= "";
		$class		= "";
		$colspan	= 7;
		
		$query		= "	SELECT  	emp_id_empresa AS id_empresa,
									emp_descripcion AS descripcion,
									emp_abreviatura AS abr,
									gir_descripcion AS giro,
									emp_status AS status,
									CASE emp_status
										WHEN 'A' THEN 'Activo'
										WHEN 'I' THEN 'Inactivo'
										WHEN 'B' THEN 'Baja'
									END AS status_desc
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						INNER JOIN	san_giros ON gir_id_giro = emp_id_giro
						WHERE		con_id_consorcio = $id_consorcio
						AND			con_status IN ( 'A', 'I' )";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				switch( $fila['status'] )
				{
					case 'A': $class = "";			break;
					case 'I': $class = "warning";	break;
					case 'B': $class = "danger";	break;
				}
				
				$check	= ( $fila['id_empresa'] == $id_empresa ) ? 'checked':'';
				
				$tabla	.= "<tr class='$class'>
								<td>$i</td>
								<td><input type='radio' name='editar_id_sucursal' value='$fila[id_empresa]' $check /></td>
								<td>".$fila['id_empresa']."</td>
								<td>".$fila['abr']."</td>
								<td>".$fila['descripcion']."</td>
								<td>".$fila['giro']."</td>
								<td>".$fila['status_desc']."</td>
							</tr>";
				
				$i++;
			}
		}
		else
			$tabla	= "	<tr><td colspan='$colspan'>Ocurrió un error al tratar de obtener la información. ".mysqli_error( $conexion )."</td></tr>";
			
		if( !$tabla )
			$tabla	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $tabla;
	}
	
?>