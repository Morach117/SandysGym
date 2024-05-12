<?php
	function lista_conf_articulos()
	{
		global $conexion, $id_consorcio;
		
		$tabla		= "";
		$colspan	= 5;
		
		//3 = solo para la TPV
		$query		= "	SELECT		emp_id_empresa AS id_empresa,
									emp_abreviatura AS abr,
									emp_descripcion AS descripcion,
									emp_descuento AS articulos_desc,
									emp_monedero AS monedero
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						WHERE		con_id_consorcio = $id_consorcio
						AND			emp_id_giro = 3
						AND			con_status IN ( 'A', 'I' )
						ORDER BY	id_empresa";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$check_desc		= ( $fila['articulos_desc'] == 'S' ) ? 'checked':'';
				$check_mon		= ( $fila['monedero'] == 'S' ) ? 'checked':'';
				
				$tabla	.= "<tr>
								<td>$i</td>
								<td>".$fila['id_empresa']."</td>
								<td>".$fila['abr']."</td>
								<td>".$fila['descripcion']."</td>
								<td>
									<input type='checkbox' name='art_desc[$fila[id_empresa]]' value='S' $check_desc>
									Habilitar el Descuento desde la TPV
									
									<br/>
									
									<input type='checkbox' name='art_mon[$fila[id_empresa]]' value='S' $check_mon>
									Habilitar el uso del monedero electrónico
								</td>
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
	
	function actualizar_conf_articulos()
	{
		global $conexion, $id_consorcio;
		
		//se utiliza para validar que los id_empresas de un session pertenezcan al consorcio
		$ides	= sucursales_por_empresa_validacion();
		$exito	= array();
		$datos	= array();
		
		mysqli_autocommit( $conexion, false );
		
		if( $ides )
		{
			foreach( $ides as $ide_suc )
			{
				if( isset( $_POST['art_desc'][$ide_suc] ) )
					$datos[$ide_suc]['emp_descuento'] = 'S';
				else
					$datos[$ide_suc]['emp_descuento'] = 'N';
				
				if( isset( $_POST['art_mon'][$ide_suc] ) )
					$datos[$ide_suc]['emp_monedero'] = 'S';
				else
					$datos[$ide_suc]['emp_monedero'] = 'N';
			}
			
			if( $datos )
			{
				foreach( $datos as $IDE => $arreglo )
				{
					$query		= construir_update( 'san_empresas', $arreglo, "emp_id_empresa = $IDE" );
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) <= 1 )
						{
							$exito['num'] = 1;
							$exito['msj'] = "Configuración de Artículos actualizado.";
						}
						else
						{
							$exito['num'] = 5;
							$exito['msj'] = "No se puede cambiar la configuración de una Sucursal.";
							break;
						}
					}
					else
					{
						$exito['num'] = 4;
						$exito['msj'] = "Ocurrió un problema al tratar de actualizar los datos de una Sucursal.";
						break;
					}
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No hay datos para guardar.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se pueden obtener las Sucursales de la Empresa.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
?>