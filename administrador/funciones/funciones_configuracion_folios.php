<?php
	function lista_folios_consecutivos()
	{
		global $conexion, $id_consorcio;
		
		$tabla		= "";
		$colspan	= 6;
		
		$query		= "	SELECT		foc_id_empresa AS id_empresa,
									emp_abreviatura AS abr,
									emp_descripcion AS descripcion,
									foc_conse_anual AS conse_anual,
									foc_tkt_letra AS tkt_letra
						FROM		san_folios_conf
						INNER JOIN	san_empresas ON emp_id_empresa = foc_id_empresa
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = foc_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						WHERE		con_id_consorcio = $id_consorcio
						AND			con_status IN ( 'A', 'I' )
						ORDER BY	id_empresa";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$check_S	= ( $fila['conse_anual'] == 'S' ) ? 'checked':'';
				$check_N	= ( $fila['conse_anual'] == 'N' ) ? 'checked':'';
				
				$tabla	.= "<tr>
								<td>$i</td>
								<td>".$fila['id_empresa']."</td>
								<td>".$fila['abr']."</td>
								<td>".$fila['descripcion']."</td>
								
								<td style='width:50px'>
									<input type='text' name='tkt_letra[$fila[id_empresa]]' class='form-control' maxlength='1' value='$fila[tkt_letra]' />
								</td>
								
								<td>
									<input type='radio' name='conse_anual[$fila[id_empresa]]' value='S' $check_S  />
									El Número de Folio se reinicia cada año
									
									<br/>
									<input type='radio' name='conse_anual[$fila[id_empresa]]' value='N' $check_N />
									El Número de Folio es infinito
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
	
	function actualizar_conf_folios()
	{
		global $conexion, $id_consorcio;
		
		//se utiliza para validar que los id_empresas de un session pertenezcan al consorcio
		$ides	= sucursales_por_empresa_validacion();
		$exito	= array();
		$datos	= array();
		
		mysqli_autocommit( $conexion, false );
		
		if( $ides )
		{
			if( isset( $_POST['conse_anual'] ) )
			{
				foreach( $_POST['conse_anual'] as $IDE => $valor )
				{
					foreach( $ides as $ide_suc )
					{
						if( $ide_suc == $IDE )
						{
							$datos[$ide_suc]['foc_conse_anual'] = $valor;
							break;
						}
					}
				}
			}
			
			if( isset( $_POST['tkt_letra'] ) )
			{
				foreach( $_POST['tkt_letra'] as $IDE => $valor )
				{
					foreach( $ides as $ide_suc )
					{
						if( $ide_suc == $IDE )
						{
							$datos[$ide_suc]['foc_tkt_letra'] = $valor;
							break;
						}
					}
				}
			}
			
			if( $datos )
			{
				foreach( $datos as $IDE => $arreglo )
				{
					$query		= construir_update( 'san_folios_conf', $arreglo, "foc_id_empresa = $IDE" );
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) <= 1 )
						{
							$exito['num'] = 1;
							$exito['msj'] = "Configuración de Folios actualizado.";
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