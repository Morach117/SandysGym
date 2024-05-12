<?php
	function lista_historico( $p_id_transfer )
	{
		global $conexion, $id_consorcio;
		$datos		= '';
		$class		= '';
		$colspan	= 4;
		$contador	= 1;
		
		$query		= "	SELECT		CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) AS usuario,
									CASE tranh_status
										WHEN 'A' THEN 'ABIERTO'
										WHEN 'R' THEN 'RECIBIDO'
										WHEN 'C' THEN 'CANCELADO'
										ELSE tranh_status
									END AS status_desc,
									DATE_FORMAT( tranh_fecha, '%d-%m-%Y %r' ) AS movimiento
						FROM		san_transferencia
						INNER JOIN	san_transferencia_historico ON tranh_id_transferencia = trans_id_transferencia
						INNER JOIN	san_usuarios ON usua_id_usuario = tranh_id_usuario
						WHERE		trans_id_transferencia = $p_id_transfer
						AND			trans_id_consorcio = $id_consorcio
						ORDER BY	tranh_id_thistorico";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$fecha	= fecha_generica( $fila['movimiento'] );
				
				$datos	.= "<tr class='$class'>
								<td>$contador</td>
								<td>$fila[usuario]</td>
								<td>$fila[status_desc]</td>
								<td>$fecha</td>
							</tr>";
				
				$contador++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
			
		return $datos;
	}
	
	function lista_transferencias( $p_corte )
	{
		global $conexion, $id_consorcio, $id_empresa;
		$datos		= '';
		$class		= '';
		$colspan	= 8;
		$contador	= 1;
		
		$query		= "	SELECT		CONCAT( 'TRA', LPAD( trans_id_transferencia, 7, 0 ) ) AS folio_desc,
									trans_id_transferencia AS id_transfer,
									trans_id_origen AS id_origen,
									trans_id_destino AS id_destino,
									CASE trans_status
										WHEN 'A' THEN 'Abierto'
										WHEN 'R' THEN 'Recibido'
										WHEN 'C' THEN 'Cancelado'
										ELSE trans_status
									END AS status_desc,
									IF( trans_id_destino = $id_empresa, 'Tránsito', 'Transferencia' ) AS nota,
									trans_status AS status,
									a.emp_descripcion AS origen,
									b.emp_descripcion AS destino,
									DATE_FORMAT( trans_fecha_entrega, '%d-%m-%Y' ) AS entrega
						FROM		san_transferencia
						INNER JOIN	san_empresas a ON a.emp_id_empresa = trans_id_origen
						INNER JOIN	san_empresas b ON b.emp_id_empresa = trans_id_destino
						WHERE		DATE_FORMAT( trans_fecha_entrega, '%m-%Y' ) = '$p_corte'
						AND			trans_id_consorcio = $id_consorcio
						ORDER BY	trans_fecha_entrega";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['status'] == 'R' )
					$class = "success";
				elseif( $fila['status'] == 'C' )
					$class = "danger";
				else
					$class = "";
				
				$fecha	= fecha_generica( $fila['entrega'] );
				
				$datos	.= "<tr class='$class'>
								<td>$contador</td>
								<td>
									<div class='btn-group'>
										<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
											<span class='glyphicon glyphicon-chevron-down'></span>
										</a>
										<ul class='dropdown-menu'>
											<li><a href='.?s=transferencias&i=detalle&folio=$fila[id_transfer]'><span class='glyphicon glyphicon-list-alt'></span> Detalles y lista de artículos</a></li>
											<li><a href='.?s=transferencias&i=detalle&folio=$fila[id_transfer]&m=eliminar'><span class='glyphicon glyphicon-remove-sign'></span> Eliminar</a></li>
											<li><a href='.?s=transferencias&i=detalle&folio=$fila[id_transfer]&m=cancelar'><span class='glyphicon glyphicon-ban-circle'></span> Cancelar</a></li>
										</ul>
									</div>
								</td>
								<td>$fila[folio_desc]</td>
								<td>$fila[status_desc]</td>
								<td>$fila[nota]</td>
								<td>$fila[origen]</td>
								<td>$fila[destino]</td>
								<td>$fecha</td>
							</tr>";
				
				$contador++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
			
		return $datos;
	}
	
	function lista_articulos_en_transferencia( $p_id_transfer )
	{
		global $conexion, $id_consorcio;
		$datos		= '';
		$colspan	= 10;
		$contador	= 1;
		$c_costos	= 0;
		$c_importe	= 0;
		$c_transito	= 0;
		$c_stock	= 0;
		
		$query		= "	SELECT		art_descripcion AS articulo,
									art_codigo AS codigo,
									trans_status AS status,
									trand_actual AS actual,
									trand_cantidad AS transito,
									trand_costo AS costo,
									ROUND( trand_cantidad * trand_costo, 2 ) AS costos,
									trand_precio AS precio,
									ROUND( trand_cantidad * trand_precio, 2 ) AS importe,
									trand_id_articulo AS id_articulo,
									trand_id_transferencia AS id_transfer
						FROM		san_transferencia
						INNER JOIN	san_transferencia_detalle ON trand_id_transferencia = trans_id_transferencia
						INNER JOIN	san_articulos ON art_id_articulo = trand_id_articulo
						AND			art_id_consorcio = trans_id_consorcio
						WHERE		trans_id_transferencia = $p_id_transfer
						AND			trans_id_consorcio = $id_consorcio
						ORDER BY	art_descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['transito'] <= 0 )
					$class_td = 'danger';
				else
					$class_td = '';
				
				$stock	= ( $fila['status'] == 'R' ) ? number_format( $fila['actual'], 2 ) : "-/-";
				
				$datos	.= "<tr>
								<td>$contador</td>
								<td>
									<div class='btn-group'>
										<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
											<span class='glyphicon glyphicon-chevron-down'></span>
										</a>
										<ul class='dropdown-menu pointer'>
											<li><a href='.?s=transferencias&i=quitar&folio=$fila[id_transfer]&id_articulo=$fila[id_articulo]'><span class='glyphicon glyphicon-remove-sign'></span> Eliminar de la transferencia</a></li>
											<li><a onclick='agregar_a_transferencia( $fila[id_articulo], $fila[id_transfer], \"transferencias\" )'><span class='glyphicon glyphicon-refresh'></span> Agregar o quitar</a></li>
										</ul>
									</div>
								</td>
								<td>$fila[codigo]</td>
								<td>$fila[articulo]</td>
								<td class='text-right'>$stock</td>
								<td class='text-right $class_td'>".number_format( $fila['transito'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['costo'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['costos'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['precio'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['importe'], 2 )."</td>
							</tr>";
				
				$c_costos	+= $fila['costos'];
				$c_importe	+= $fila['importe'];
				$c_transito	+= $fila['transito'];
				$c_stock	+= $fila['actual'];
				$contador++;
			}
			
			if( !$datos )
				$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
			
			$datos	.= "<tr class='success text-bold'>
							<td colspan='5' class='text-right'>".number_format( $c_stock, 2 )."</td>
							<td colspan='1' class='text-right'>".number_format( $c_transito, 2 )."</td>
							<td colspan='2' class='text-right'>$".number_format( $c_costos, 2 )."</td>
							<td colspan='2' class='text-right'>$".number_format( $c_importe, 2 )."</td>
						</tr>";
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		return $datos;
	}
	
	function transferencia_detalle( $id_transfer )
	{
		global $conexion, $id_consorcio, $id_empresa;
		
		$datos		= "";
		
		$query		= "	SELECT		CONCAT( 'TRA', LPAD( trans_id_transferencia, 7, 0 ) ) AS folio_desc,
									CASE trans_status
										WHEN 'A' THEN 'ABIERTO'
										WHEN 'R' THEN 'RECIBIDO'
										WHEN 'C' THEN 'CANCELADO'
										ELSE trans_status
									END AS status_desc,
									CASE trans_status
										WHEN 'A' THEN 'label label-info'
										WHEN 'R' THEN 'label label-success'
										WHEN 'C' THEN 'label label-danger'
									END AS status_class,
									IF( trans_id_destino = $id_empresa, 'Tránsito', 'Transferencia' ) AS nota,
									a.emp_descripcion AS origen,
									b.emp_descripcion AS destino,
									DATE_FORMAT( trans_fecha_entrega, '%d-%m-%Y' ) AS entrega
						FROM		san_transferencia
						INNER JOIN	san_empresas a ON a.emp_id_empresa = trans_id_origen
						INNER JOIN	san_empresas b ON b.emp_id_empresa = trans_id_destino
						WHERE		trans_id_transferencia = $id_transfer
						AND			trans_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$fecha	= fecha_generica( $fila['entrega'] );
				
				$datos	= "	<div class='row'>
								<label class='col-md-2'>Folio</label>
								<div class='col-md-4'>$fila[folio_desc]</div>
								
								<label class='col-md-2'>Status</label>
								<div class='col-md-4'><span class='$fila[status_class]'>$fila[status_desc]</span></div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Entrega</label>
								<div class='col-md-4'>$fecha</div>
								
								<label class='col-md-2'>Nota</label>
								<div class='col-md-4'>$fila[nota]</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Origen</label>
								<div class='col-md-4'>$fila[origen]</div>
								
								<label class='col-md-2'>Destino</label>
								<div class='col-md-4'>$fila[destino]</div>
							</div>";
			}
		}
		
		return $datos;
	}
	
	function transferencia_articulo_detalle( $p_id_transfer, $p_id_articulo )
	{
		global $conexion, $id_consorcio;
		
		$query		= "	SELECT		trand_cantidad AS transito,
									trans_status AS status,
									CASE trans_status
										WHEN 'A' THEN 'Abierto'
										WHEN 'R' THEN 'Recibido'
										WHEN 'C' THEN 'Cancelado'
										ELSE trans_status
									END AS status_desc,
									art_descripcion AS articulo
						FROM		san_transferencia
						INNER JOIN	san_transferencia_detalle ON trand_id_transferencia = trans_id_transferencia
						INNER JOIN	san_articulos ON art_id_articulo = trand_id_articulo
						AND			art_id_consorcio = trans_id_consorcio
						WHERE		trans_id_transferencia = $p_id_transfer
						AND			trans_id_consorcio = $id_consorcio
						AND			trand_id_articulo = $p_id_articulo";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		
		return false;
	}
	
	function quitar_articulo( $p_id_transfer, $p_id_articulo )
	{
		global $conexion, $id_consorcio;
		
		$exito		= array();
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "	SELECT		trans_id_origen AS id_origen,
									trans_status AS status,
									trand_cantidad AS cantidad
						FROM		san_transferencia
						INNER JOIN	san_transferencia_detalle ON trand_id_transferencia = trans_id_transferencia
						WHERE		trans_id_transferencia = $p_id_transfer
						AND			trans_id_consorcio = $id_consorcio
						AND			trand_id_articulo = $p_id_articulo";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['status'] == 'A' )
				{
					$query		= "DELETE FROM san_transferencia_detalle WHERE trand_id_transferencia = $p_id_transfer AND trand_id_articulo = $p_id_articulo";
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) == 1 )
						{
							if( $fila['cantidad'] > 0 )
							{
								$query		= "UPDATE san_stock SET stk_existencia = stk_existencia + $fila[cantidad] WHERE stk_id_articulo = $p_id_articulo AND stk_id_empresa = $fila[id_origen]";
								$resultado	= mysqli_query( $conexion, $query );
								
								if( $resultado )
								{
									if( mysqli_affected_rows( $conexion ) == 1 )
									{
										$exito['num'] = 1;
										$exito['msj'] = "Se quito el articulo de la transferencia.";
									}
									else
									{
										$exito['num'] = 8;
										$exito['msj'] = "No se puede actualizar el stock.";
									}
								}
								else
								{
									$exito['num'] = 7;
									$exito['msj'] = "Ocurrió un problema técnico al tratar de actualizar el stock. ".mysqli_error( $conexion );
								}
							}
							else
							{
								$exito['num'] = 1;
								$exito['msj'] = "Se quito el articulo de la transferencia.";
							}
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "No se puede quitar el articulo de la transferencia.";
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "Ocurrió un problema técnico al tratar de quitar el articulo de la transferencia. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "Únicamente los artículos que se encuentran en transferencias con status de ABIERTO se pueden quitar.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se encontró el articulo en la transferencia seleccionada.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Ocurrió un problema técnico al tratar de consultar el detalle de la transferencia. ".mysqli_error( $conexion );
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function eliminar_transferencia( $p_id_transfer )
	{
		global $conexion, $id_consorcio;
		
		$exito			= array();
		$chk_transfer	= chk_transferencia( $p_id_transfer );
		
		mysqli_autocommit( $conexion, false );
		
		if( $chk_transfer )
		{
			if( $chk_transfer['articulos'] == 0 )
			{
				if( $chk_transfer['status'] != 'R' )
				{
					$query		= "DELETE FROM san_transferencia_historico WHERE tranh_id_transferencia = $p_id_transfer";
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						$query		= "DELETE FROM san_transferencia WHERE trans_id_transferencia = $p_id_transfer AND trans_id_consorcio = $id_consorcio AND trans_status != 'R'";
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							if( mysqli_affected_rows( $conexion ) == 1 )
							{
								$exito['num'] = 1;
								$exito['msj'] = "Transferencia eliminada.";
							}
							else
							{
								$exito['num'] = 7;
								$exito['msj'] = "No se puede eliminar la transferencia.";
							}
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "Ocurrió un problema técnico al tratar de eliminar la transferencia. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "No se pueden eliminar los movimientos históricos de la transferencia. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "La transferencia ya ha sido recibida.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Se encontraron artículos en la transferencia.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se puede checar el detalle de la transferencia.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function cancelar_transferencia( $p_id_transfer )
	{
		global $conexion, $id_consorcio, $id_usuario;
		
		$exito			= array();
		$fecha_mov		= date( 'Y-m-d H:i:s' );
		$chk_transfer	= chk_transferencia( $p_id_transfer );
		
		mysqli_autocommit( $conexion, false );
		
		if( $chk_transfer )
		{
			if( $chk_transfer['status'] == 'A' )
			{
				$query		= "UPDATE san_transferencia SET trans_status = 'C' WHERE trans_id_transferencia = $p_id_transfer AND trans_id_consorcio = $id_consorcio";
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					if( mysqli_affected_rows( $conexion ) == 1 )
					{
						$historico	= array(
							'tranh_id_transferencia'	=> $p_id_transfer,
							'tranh_id_usuario'			=> $id_usuario,
							'tranh_status'				=> 'C',
							'tranh_fecha'				=> $fecha_mov
						);
						
						$query		= construir_insert( "san_transferencia_historico", $historico );
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado )
						{
							if( mysqli_affected_rows( $conexion ) == 1 )
							{
								if( $chk_transfer['articulos'] > 0 )
								{
									$query		= "	UPDATE		san_stock
													INNER JOIN	san_transferencia ON trans_id_origen = stk_id_empresa
													INNER JOIN	san_transferencia_detalle ON trand_id_transferencia = trans_id_transferencia
													AND			trand_id_articulo = stk_id_articulo
													SET			stk_existencia = stk_existencia + trand_cantidad
													WHERE		trans_id_transferencia = $p_id_transfer
													AND			trans_id_consorcio = $id_consorcio
													AND			trans_status = 'C'";
									
									$resultado	= mysqli_query( $conexion, $query );
									
									if( $resultado )
									{
										if( mysqli_affected_rows( $conexion ) == $chk_transfer['articulos'] )
										{
											$exito['num'] = 1;
											$exito['msj'] = "Se cancelo la transferencia, se recuperaron los artículos en tránsito a la sucursal de origen.";
										}
										else
										{
											$exito['num'] = 7;
											$exito['msj'] = "No se pueden recuperar los artículos en tránsito.";
										}
									}
									else
									{
										$exito['num'] = 6;
										$exito['msj'] = "Ocurrió un problema técnico al tratar de recuperar los artículos en tránsito. ".mysqli_error( $conexion );
									}
								}
								else
								{
									$exito['num'] = 1;
									$exito['msj'] = "Se cancelo la transferencia, pero no se recuperaron artículos porque no habían agregados.";
								}
							}
							else
							{
								$exito['num'] = 7;
								$exito['msj'] = "No se puede guardar el histórico de la transferencia.";
							}
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "Ocurrió un problema técnico al tratar de guardar el histórico de la transferencia. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "No se puede actualizar la transferencia.";
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "Ocurrió un problema técnico al tratar de cancelar la transferencia. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se puede cancelar la transferencia porque el status no es de ABIERTO.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se puede checar el detalle de la transferencia.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function chk_transferencia( $p_id_transfer )
	{
		global $conexion, $id_consorcio;
		
		$query		= "	SELECT		trans_id_transferencia,
									trans_status AS status,
									COUNT( trand_id_tdetalle ) AS articulos
						FROM		san_transferencia a
						LEFT JOIN	san_transferencia_detalle b ON trand_id_transferencia = trans_id_transferencia
						WHERE		trans_id_transferencia = $p_id_transfer
						AND			trans_id_consorcio = $id_consorcio
						GROUP BY	trans_id_transferencia,
									trans_status";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		
		return false;
	}
	
?>