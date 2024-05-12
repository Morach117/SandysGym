<?php
	function detalle_servicio( $p_id_servicio )
	{
		global $conexion, $id_consorcio;
		
		$datos		= "";
		$colspan	= 6;
		
		$query		= "	SELECT		emp_descripcion AS sucursal,
									emp_id_empresa AS id_sucursal,
									src_id_scuota AS id_scuota, 
									src_cuota AS cuota, 
									src_promo_cuota AS promo_cuota, 
									src_promo_dia AS promo_dia, 
									src_kg_minimo AS kg, 
									src_mostrar AS mostrar
						FROM		san_consorcio_empresa
						INNER JOIN	san_empresas ON emp_id_empresa = coem_id_empresa
						LEFT JOIN	san_servicios_cuotas ON src_id_empresa = coem_id_empresa
						AND			src_id_servicio = $p_id_servicio
						LEFT JOIN	san_servicios ON ser_id_servicio = src_id_servicio
						AND			ser_id_giro = emp_id_giro
						AND			ser_id_consorcio = coem_id_consorcio
						WHERE		coem_id_consorcio = $id_consorcio
						AND			emp_id_giro = 2";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['mostrar'] == 'S' )
					$chk	= "<input type='checkbox' value='S' name='ar_mostrar[$fila[id_sucursal]]' checked />";
				else
					$chk	= "<input type='checkbox' name='ar_mostrar[$fila[id_sucursal]]' />";
				
				$chk .= "<input type='hidden' name='ar_id_src[$fila[id_sucursal]]' value='$fila[id_scuota]' />";
				
				$datos	.= "<tr>
								<td>$fila[sucursal]</td>
								<td width='60px'><input type='text' class='form-control' name='ar_cuota[$fila[id_sucursal]]' maxlength='5' value='$fila[cuota]' required='required' /></td>
								<td width='90px'><input type='text' class='form-control' name='ar_pcuota[$fila[id_sucursal]]' maxlength='5' value='$fila[promo_cuota]' required='required' /></td>
								<td width='110px'>
									<select name='ar_pdia[$fila[id_sucursal]]' class='form-control'>
										".opciones_dia_semana( $fila['promo_dia'] )."
									<select>
								</td>
								<td width='90px'><input type='text' class='form-control' name='ar_kg[$fila[id_sucursal]]' maxlength='2' value='$fila[kg]' required='required' /></td>
								<td>$chk</td>
							</tr>";
			}
			
			if( !$datos )
				$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>No se pudo obtener el reporte. ".mysqli_error( $conexion )."</td></tr>";
		
		return $datos;
	}
	
	function actualizar_servicio( $p_id_servicio )
	{
		global $conexion, $id_consorcio;
		
		$arreglo	= array();
		$exito		= array();
		
		$servicios	= array(
			'ser_descripcion'	=> request_var( 's_descripcion', '' ),
			'ser_status'		=> request_var( 's_status', '' ),
			'ser_orden'			=> request_var( 's_orden', 0 )
		);
		
		$ides		= sucursales_por_empresa_validacion();
		
		mysqli_autocommit( $conexion, false );
		
		if( $ides )
		{
			foreach( $ides as $id_sucursal )
			{
				if( isset( $_POST['ar_cuota'] ) )
				{
					foreach( $_POST['ar_cuota'] as $ide => $valor )
					{
						if( $id_sucursal == $ide )
						{
							$arreglo[$id_sucursal]['src_cuota'] = $valor;
							break;
						}
					}
				}
				
				if( isset( $_POST['ar_pcuota'] ) )
				{
					foreach( $_POST['ar_pcuota'] as $ide => $valor )
					{
						if( $id_sucursal == $ide )
						{
							$arreglo[$id_sucursal]['src_promo_cuota'] = $valor;
							break;
						}
					}
				}
				
				if( isset( $_POST['ar_pdia'] ) )
				{
					foreach( $_POST['ar_pdia'] as $ide => $valor )
					{
						if( $id_sucursal == $ide )
						{
							$arreglo[$id_sucursal]['src_promo_dia'] = $valor;
							break;
						}
					}
				}
				
				if( isset( $_POST['ar_kg'] ) )
				{
					foreach( $_POST['ar_kg'] as $ide => $valor )
					{
						if( $id_sucursal == $ide )
						{
							$arreglo[$id_sucursal]['src_kg_minimo'] = $valor;
							break;
						}
					}
				}
				
				//solo se usa para comprobar si se actualiza o inserta
				if( isset( $_POST['ar_id_src'] ) )
				{
					foreach( $_POST['ar_id_src'] as $ide => $valor )
					{
						if( $id_sucursal == $ide )
						{
							$arreglo[$id_sucursal]['src_id_scuota'] = $valor;
							break;
						}
					}
				}
			}
			
			foreach( $arreglo as $ide => $mostrar )
			{
				if( isset( $_POST['ar_mostrar'][$ide] ) )
					$arreglo[$ide]['src_mostrar'] = 'S';
				else
					$arreglo[$ide]['src_mostrar'] = 'N';
			}
			
			//comienza la transaccion
			
			$query		= construir_update( "san_servicios", $servicios, "ser_id_servicio = $p_id_servicio AND ser_id_giro = 2 AND ser_id_consorcio = $id_consorcio" );
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( mysqli_affected_rows( $conexion ) <= 1 ) //puede ser que le den guardar sin mover nada
				{
					foreach( $arreglo as $ide => $datos )
					{
						if( $datos['src_id_scuota'] > 0 )
						{
							$id_scuota	= $datos['src_id_scuota'];
							unset( $datos['src_id_scuota'] );
							
							$query		= construir_update( "san_servicios_cuotas", $datos, "src_id_servicio = $p_id_servicio AND src_id_scuota = $id_scuota AND src_id_empresa = $ide" );
						}
						else
						{
							$datos['src_id_servicio']	= $p_id_servicio;
							$datos['src_id_empresa']	= $ide;
							$query						= construir_insert( "san_servicios_cuotas", $datos );
						}
						
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							if( mysqli_affected_rows( $conexion ) <= 1 ) //puede ser que le den guardar sin mover nada
							{
								$exito['num'] = 1;
								$exito['msj'] = "Servicio actualizado.";
							}
							else
							{
								$exito['num'] = 6;
								$exito['msj'] = "No se puede actualizar el servicio seleccionado en una Sucursal.";
							}
						}
						else
						{
							$exito['num'] = 5;
							$exito['msj'] = "Ocurrió un problema técnico al tratar de actualizar un servicio en una Sucursal. ".mysqli_error( $conexion );
						}
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "No se puede actualizar el servicio seleccionado.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Ocurrió un problema técnico al tratar de actualizar los servicios. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se puede realizar la validación de Sucursales.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
?>