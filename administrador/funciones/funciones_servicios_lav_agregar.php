<?php
	function sucursales_del_giro()
	{
		global $conexion, $id_consorcio;
		
		$datos		= "";
		$colspan	= 6;
		
		$query		= "	SELECT		emp_id_empresa AS id_sucursal,
									emp_descripcion AS sucursal
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						WHERE		emp_id_giro = 2
						AND			coem_id_consorcio = $id_consorcio";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$fila[sucursal]</td>
								<td width='60px'><input type='text' class='form-control' name='ar_cuota[$fila[id_sucursal]]' maxlength='5' required='required' /></td>
								<td width='90px'><input type='text' class='form-control' name='ar_pcuota[$fila[id_sucursal]]' maxlength='5' required='required' value='0' /></td>
								<td width='100px'>
									<select name='ar_pdia[$fila[id_sucursal]]' class='form-control'>
										".opciones_dia_semana()."
									<select>
								</td>
								<td width='60px'><input type='text' class='form-control' name='ar_kg[$fila[id_sucursal]]' maxlength='2' value='0' required='required' /></td>
								<td><input type='checkbox' name='ar_mostrar[$fila[id_sucursal]]' checked /></td>
							</tr>";
			}
			
			if( !$datos )
				$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>No se pudo obtener el reporte. ".mysqli_error( $conexion )."</td></tr>";
		
		return $datos;
	}
	
	function guardar_servicio()
	{
		global $conexion, $id_consorcio;
		
		$arreglo	= array();
		$exito		= array();
		$ser_tipo	= request_var( 'tipo', '' );
		
		$servicios	= array(
			'ser_id_giro'		=> 2,
			'ser_id_consorcio'	=> $id_consorcio,
			'ser_tipo'			=> $ser_tipo,
			'ser_descripcion'	=> request_var( 's_descripcion', '' ),
			'ser_orden'			=> request_var( 's_orden', 0 ),
			'ser_status'		=> 'A'
		);
		
		$ides		= sucursales_por_empresa_validacion();
		
		mysqli_autocommit( $conexion, false );
		
		if( $ser_tipo == 'LAVANDERIA' || $ser_tipo == 'PLANCHADURIA' || $ser_tipo == 'EDREDONES' )
		{
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
				}
				
				foreach( $arreglo as $ide => $mostrar )
				{
					if( isset( $_POST['ar_mostrar'][$ide] ) )
						$arreglo[$ide]['src_mostrar'] = 'S';
					else
						$arreglo[$ide]['src_mostrar'] = 'N';
				}
				
				//comienza la transaccion
				
				$query		= construir_insert( "san_servicios", $servicios );
				$resultado	= mysqli_query( $conexion, $query );
				$id_servicio	= mysqli_insert_id( $conexion );
				
				if( $resultado && $id_servicio )
				{
					if( mysqli_affected_rows( $conexion ) == 1 )
					{
						foreach( $arreglo as $ide => $datos )
						{
							$datos['src_id_servicio']	= $id_servicio;
							$datos['src_id_empresa']	= $ide;
							
							$query		= construir_insert( "san_servicios_cuotas", $datos );
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								if( mysqli_affected_rows( $conexion ) == 1 )
								{
									$exito['id_servicio'] = $id_servicio;
									$exito['num'] = 1;
									$exito['msj'] = "Servicios actualizados.";
								}
								else
								{
									$exito['num'] = 6;
									$exito['msj'] = "No se puede guardar el servicio seleccionado en una Sucursal.";
								}
							}
							else
							{
								$exito['num'] = 5;
								$exito['msj'] = "Ocurrió un problema técnico al tratar de guardar un servicio en una Sucursal. ".mysqli_error( $conexion );
							}
						}
					}
					else
					{
						$exito['num'] = 4;
						$exito['msj'] = "No se puede guardar el servicio seleccionado.";
					}
				}
				else
				{
					$exito['num'] = 3;
					$exito['msj'] = "Ocurrió un problema técnico al tratar de guardar los servicios. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se puede realizar la validación de Sucursales.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se encontró un tipo de giro válido, debe ser LAVANDERIA o PLANCHADURIA.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
?>