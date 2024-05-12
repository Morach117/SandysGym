<?php
	function lista_configuracion_tickets()
	{
		global $conexion, $id_consorcio;
		
		$tabla		= "";
		$colspan	= 4;
		
		$query		= "	SELECT		foc_id_empresa AS id_empresa,
									emp_abreviatura AS abr,
									emp_descripcion AS descripcion,
									foc_tickets AS tickets,
									foc_impresora AS impresora,
									foc_tkt_font_size AS font_size,
									foc_tkt_encabezado AS encabezado,
									foc_tkt_pie_pagina AS pie_pagina,
									foc_tkt_ver_sucursal AS sucursal,
									foc_tkt_ver_direccion AS direccion,
									foc_tkt_ver_colonia AS colonia,
									foc_tkt_ver_ciudad AS ciudad,
									foc_tkt_ver_telefono AS tel,
									foc_tkt_ver_rfc AS rfc
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
				$check_tkt	= ( $fila['tickets'] == 'S' ) ? 'checked':'';
				
				$check_S	= ( $fila['sucursal'] == 'S' ) ? 'checked':'';
				$check_Y	= ( $fila['sucursal'] == 'Y' ) ? 'checked':'';
				$check_T	= ( $fila['sucursal'] == 'T' ) ? 'checked':'';
				$check_N	= ( $fila['sucursal'] == 'N' ) ? 'checked':'';
				
				$check_l8	= ( $fila['font_size'] == 8 ) ? 'checked':'';
				$check_l9	= ( $fila['font_size'] == 9 ) ? 'checked':'';
				$check_l10	= ( $fila['font_size'] == 10 ) ? 'checked':'';
				
				$check_iL	= ( $fila['impresora'] == 'L' ) ? 'checked':'';
				$check_iT	= ( $fila['impresora'] == 'T' ) ? 'checked':'';
				
				$check_dir	= ( $fila['direccion'] == 'S' ) ? 'checked':'';
				$check_col	= ( $fila['colonia'] == 'S' ) ? 'checked':'';
				$check_ciu	= ( $fila['ciudad'] == 'S' ) ? 'checked':'';
				$check_tel	= ( $fila['tel'] == 'S' ) ? 'checked':'';
				$check_rfc	= ( $fila['rfc'] == 'S' ) ? 'checked':'';
				
				$tabla	.= "<tr>
								<td>$i</td>
								
								<td>
									<strong>$fila[descripcion]</strong>
									
									<br/>
									<input type='checkbox' name='hab_tickets[$fila[id_empresa]]' $check_tkt />
									Habilitar la Impresión de Tickets
									
									<hr/>
									<small>Solo en lavanderia</small><br/>
									
									<input type='radio' name='ver_impresara[$fila[id_empresa]]' value='L' $check_iL  />
									Impresora de tinta/toner
									
									<br/>
									<input type='radio' name='ver_impresara[$fila[id_empresa]]' value='T' $check_iT  />
									Impresora Termica
									
									<hr/>
									
									<input type='radio' name='ver_font_size[$fila[id_empresa]]' value='8' $check_l8  />
									Tamaño de la letra en 8px
									
									<br/>
									<input type='radio' name='ver_font_size[$fila[id_empresa]]' value='9' $check_l9  />
									Tamaño de la letra en 9px
									
									<br/>
									<input type='radio' name='ver_font_size[$fila[id_empresa]]' value='10' $check_l10  />
									Tamaño de la letra en 10px
								</td>
								
								<td>
									Encabezado: (Máximo 200 caracteres)
									<textarea name='ver_encabezado[$fila[id_empresa]]' class='form-control' maxlength='200' rows='4'>$fila[encabezado]</textarea>
									
									<br/>
									Pie de página: (Máximo 200 caracteres)
									<textarea name='ver_pie_pagina[$fila[id_empresa]]' class='form-control' maxlength='200' rows='5'>$fila[pie_pagina]</textarea>
								</td>
								
								<td>
									<input type='radio' name='ver_sucursal[$fila[id_empresa]]' value='S' $check_S  />
									Mostrar el nombre de la Sucursal
									
									<br/>
									<input type='radio' name='ver_sucursal[$fila[id_empresa]]' value='Y' $check_Y  />
									Mostrar el nombre de la Empresa
									
									<br/>
									<input type='radio' name='ver_sucursal[$fila[id_empresa]]' value='T' $check_T />
									Mostrar nombre de Sucursal y Empresa
									
									<br/>
									<input type='radio' name='ver_sucursal[$fila[id_empresa]]' value='N' $check_N />
									No mostrar nombre de Sucursal o Empresa
									
									<hr/>
									
									<input type='checkbox' name='ver_direccion[$fila[id_empresa]]' $check_dir />
									Mostrar Direccion de la Sucursal
									
									<br/>
									<input type='checkbox' name='ver_colonia[$fila[id_empresa]]' $check_col />
									Mostrar Colonia de la Sucursal
									
									<br/>
									<input type='checkbox' name='ver_ciudad[$fila[id_empresa]]' $check_ciu />
									Mostrar Ciudad de la Sucursal
									
									<br/>
									<input type='checkbox' name='ver_telefono[$fila[id_empresa]]' $check_tel />
									Mostrar Teléfono de la Sucursal
									
									<br/>
									<input type='checkbox' name='ver_RFC[$fila[id_empresa]]' $check_rfc />
									Mostrar RFC de la Empresa
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
		global $conexion;
		
		//se utiliza para validar que los id_empresas de un session pertenezcan al consorcio
		$ides	= sucursales_por_empresa_validacion();
		$datos	= array();
		$exito	= array();
		
		mysqli_autocommit( $conexion, false );
		
		if( $ides )
		{
			//texarea y radio -> $ide_suc == $IDE id_suc del consorcio == ide_suc que se recibe
			
			if( isset( $_POST['ver_encabezado'] ) )
			{
				foreach( $_POST['ver_encabezado'] as $IDE => $valor )
				{
					foreach( $ides as $ide_suc )
					{
						if( $ide_suc == $IDE )
						{
							$datos[$ide_suc]['foc_tkt_encabezado'] = $valor;
							break;
						}
					}
				}
			}
			
			if( isset( $_POST['ver_pie_pagina'] ) )
			{
				foreach( $_POST['ver_pie_pagina'] as $IDE => $valor )
				{
					foreach( $ides as $ide_suc )
					{
						if( $ide_suc == $IDE )
						{
							$datos[$ide_suc]['foc_tkt_pie_pagina'] = $valor;
							break;
						}
					}
				}
			}
			
			if( isset( $_POST['ver_impresara'] ) )
			{
				foreach( $_POST['ver_impresara'] as $IDE => $valor )
				{
					foreach( $ides as $ide_suc )
					{
						if( $ide_suc == $IDE )
						{
							$datos[$ide_suc]['foc_impresora'] = $valor;
							break;
						}
					}
				}
			}
			
			if( isset( $_POST['ver_font_size'] ) )
			{
				foreach( $_POST['ver_font_size'] as $IDE => $valor )
				{
					foreach( $ides as $ide_suc )
					{
						if( $ide_suc == $IDE )
						{
							$datos[$ide_suc]['foc_tkt_font_size'] = $valor;
							break;
						}
					}
				}
			}
			
			if( isset( $_POST['ver_sucursal'] ) )
			{
				foreach( $_POST['ver_sucursal'] as $IDE => $valor )
				{
					foreach( $ides as $ide_suc )
					{
						if( $ide_suc == $IDE )
						{
							$datos[$ide_suc]['foc_tkt_ver_sucursal'] = $valor;
							break;
						}
					}
				}
			}
			
			//checkbox ide_suc = ide_suc del consorcio
			
			foreach( $ides as $ide_suc )
			{
				if( isset( $_POST['hab_tickets'][$ide_suc] ) )
					$datos[$ide_suc]['foc_tickets'] = 'S';
				else
					$datos[$ide_suc]['foc_tickets'] = 'N';
				
				if( isset( $_POST['ver_direccion'][$ide_suc] ) )
					$datos[$ide_suc]['foc_tkt_ver_direccion'] = 'S';
				else
					$datos[$ide_suc]['foc_tkt_ver_direccion'] = 'N';
				
				if( isset( $_POST['ver_colonia'][$ide_suc] ) )
					$datos[$ide_suc]['foc_tkt_ver_colonia'] = 'S';
				else
					$datos[$ide_suc]['foc_tkt_ver_colonia'] = 'N';
				
				if( isset( $_POST['ver_ciudad'][$ide_suc] ) )
					$datos[$ide_suc]['foc_tkt_ver_ciudad'] = 'S';
				else
					$datos[$ide_suc]['foc_tkt_ver_ciudad'] = 'N';
				
				if( isset( $_POST['ver_telefono'][$ide_suc] ) )
					$datos[$ide_suc]['foc_tkt_ver_telefono'] = 'S';
				else
					$datos[$ide_suc]['foc_tkt_ver_telefono'] = 'N';
				
				if( isset( $_POST['ver_RFC'][$ide_suc] ) )
					$datos[$ide_suc]['foc_tkt_ver_rfc'] = 'S';
				else
					$datos[$ide_suc]['foc_tkt_ver_rfc'] = 'N';
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
							$exito['msj'] = "Configuración de Tickets actualizados.";
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