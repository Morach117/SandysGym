<?php
	function lista_cortes_del_dia( $fecha_mov )
	{
		global $conexion, $id_empresa;
		
		$datos		= "";
		$condicion	= "";
		$colspan	= 7;
		
		$query		= "	SELECT		IF( DATE_FORMAT( cor_fecha, '%d-%m-%Y' ) = DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' ), 1, 2 ) AS tipo,
									LOWER( DATE_FORMAT( cor_fecha, '%d-%m-%Y %r' ) ) AS movimiento,
									a.usua_nombres AS usuario,
									IF( cor_id_cajero > 0, b.usua_nombres, 'No Seleccionado' ) AS cajero,
									CASE cor_tipo_corte
										WHEN 3 THEN 'APERTURA'
										WHEN 4 THEN 'CIERRE'
										ELSE cor_tipo_corte
									END AS tipo_mov,
									cor_importe AS importe,
									cor_diferencia AS diferencia,
									cor_caja AS caja,
									cor_observaciones AS notas
						FROM		san_corte
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = cor_id_usuario
						LEFT JOIN	san_usuarios b ON b.usua_id_usuario = cor_id_cajero
						WHERE		(
										'$fecha_mov' = DATE_FORMAT( cor_fecha, '%d-%m-%Y' )
										OR
										'$fecha_mov' = DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' )
									)
						AND			cor_id_empresa = $id_empresa
						AND			cor_tipo_corte IN ( 3, 4 )
						ORDER BY	cor_id_corte";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$class_tr	= ( $fila['tipo'] == 2 ) ? 'info':'';
				
				if( $fila['diferencia'] > 0 )
					$class_td	= 'warning';
				elseif( $fila['diferencia'] < 0 )
					$class_td	= 'danger';
				else
					$class_td	= '';
				
				$datos	.= "<tr class='$class_tr'>
								<td>$i</td>
								<td>".$fila['movimiento']."</td>
								<td>".$fila['usuario']."</td>
								<td>".$fila['cajero']."</td>
								<td class='text-right'>$".number_format( $fila['caja'], 2 )."</td>
								<td>".$fila['tipo_mov']."</td>
								<td>".$fila['notas']."</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
			
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
	function realizar_corte()
	{
		global $conexion, $id_usuario, $id_empresa;
		
		$exito			= array();
		$cor_importe	= request_var( 'cor_importe', 0.0 );
		$cor_obs		= request_var( 'cor_observaciones', '' );
		
		if( $cor_importe )
		{
			$datos_sql		= array
			(
				'cor_id_usuario'	=> $id_usuario,
				'cor_id_cajero'		=> $id_usuario,
				'cor_id_empresa'	=> $id_empresa,
				'cor_tipo_corte'	=> 5,
				'cor_fecha'			=> date( 'Y-m-d H:i:s' ),
				'cor_fecha_venta'	=> date( 'Y-m-d' ),
				'cor_importe'		=> $cor_importe,
				'cor_diferencia'	=> 0,
				'cor_caja'			=> 0,
				'cor_observaciones'	=> request_var( 'cor_observaciones', '' )
			);
			
			$query		= construir_insert( 'san_corte', $datos_sql );
			$resultado	= mysqli_query( $conexion, $query );
			$id_corte	= mysqli_insert_id( $conexion );
			
			if( $resultado )
			{
				notificar_por_correo( $id_corte );
				$exito['num'] = 1;
				$exito['msj'] = "Corte realizado exitosamente por $".number_format( $cor_importe, 2 );
				$exito['IDC'] = $id_corte;
				$exito['IDU'] = $id_usuario;
			}
			else
			{
				$exito['num'] = 4;
				$exito['msj'] = "Ocurrió un error al procesar la petición y no se realizó el corte. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se escribió la cantidad por la que se desea hacer el corte.";
		}
		
		return $exito;
	}
	
	function registrar_caja()
	{
		global $conexion, $id_usuario, $id_empresa;
		
		$v_monto	= request_var( 'v_monto', 0.0 );
		$v_obs		= request_var( 'v_obs', '' );
		$v_tipo		= request_var( 'v_tipo', 0 );
		$exito		= array();
		
		if( $v_monto )
		{
			if( $v_tipo == 3 || $v_tipo == 4 )
			{
				$datos_sql		= array
				(
					'cor_id_usuario'	=> $id_usuario,
					'cor_id_cajero'		=> $id_usuario,
					'cor_id_empresa'	=> $id_empresa,
					'cor_tipo_corte'	=> $v_tipo,
					'cor_fecha'			=> date( 'Y-m-d H:i:s' ),
					'cor_fecha_venta'	=> date( 'Y-m-d' ),
					'cor_importe'		=> 0,
					'cor_diferencia'	=> 0,
					'cor_caja'			=> $v_monto,
					'cor_observaciones'	=> request_var( 'v_obs', '' ),
				);
				
				$query		= construir_insert( 'san_corte', $datos_sql );
				$resultado	= mysqli_query( $conexion, $query );
				$id_corte	= mysqli_insert_id( $conexion );
				
				if( $resultado )
				{
					notificar_por_correo( $id_corte );
					$exito['num'] = 1;
					$exito['msj'] = "";
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "Ocurrió un problema técnico al tratar de registrar la caja. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se seleccionó tipo de movimiento";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se escribió el monto con que se apertura caja";
		}
		
		return $exito;
	}
	
	function notificar_por_correo( $p_id_corte )
	{
		global $conexion, $id_consorcio, $id_empresa;
		
		$lst_stock	= "";
		$query		= "	SELECT		date_format( cor_fecha, '%d-%m-%Y %r' ) AS fecha,
									CASE cor_tipo_corte
										WHEN 3 THEN 'Apertura de caja'
										WHEN 4 THEN 'Cierre de caja'
										WHEN 5 THEN 'Corte de caja'
										ELSE 'Desconocido'
									END AS tipo_corte,
									CASE cor_tipo_corte
										WHEN 3 THEN ROUND( cor_caja, 2 )
										WHEN 4 THEN ROUND( cor_caja, 2 )
										WHEN 5 THEN ROUND( cor_importe, 2 )
										ELSE 0
									END AS monto,
									CONCAT( usua_nombres, ' ', usua_ape_pat, ' ', usua_ape_mat ) AS cajero,
									cor_observaciones,
									emp_descripcion,
									emp_correo,
									emp_not_corte
						FROM		san_corte
						INNER JOIN	san_empresas ON emp_id_empresa = cor_id_empresa
						INNER JOIN	san_usuarios ON usua_id_usuario = cor_id_cajero
						WHERE		cor_id_corte = $p_id_corte";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['emp_not_corte'] == 'S' )
				{
					$aaa_query		= "	SELECT		art_codigo,
													art_descripcion,
													stk_existencia
										FROM		san_articulos
										INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
										WHERE		art_id_consorcio = $id_consorcio
										AND			art_status = 'A'
										AND			stk_id_empresa = $id_empresa
										ORDER BY	art_descripcion";
					
					$aaa_resultado	= mysqli_query( $conexion, $aaa_query );
					
					if( $aaa_resultado )
					{
						while( $aaa_fila = mysqli_fetch_assoc( $aaa_resultado ) )
						{
							$lst_stock .= "	<tr>
												<td>$aaa_fila[art_codigo]</td>
												<td>$aaa_fila[art_descripcion]</td>
												<td>$aaa_fila[stk_existencia]</td>
											</tr>";
						}
					}
					
					if( $fila['emp_correo'] )
					{
						$fecha		= fecha_generica( $fila['fecha'] );
						$para		= $fila['emp_correo'];
						$titulo		= 'Movimiento de caja | SERGYM';
						
						$mensaje	= "	<html>
											<head>
												<title>Movimiento de caja</title>
											</head>
											
											<body>
												<p>Movimiento de caja detectado en operativo!</p>
												
												<table>
													<tr>
														<td>Sucursal</td>
														<td>$fila[emp_descripcion]</td>
													</tr>
													
													<tr>
														<td>Fecha</td>
														<td>$fecha</td>
													</tr>
													
													<tr>
														<td>Cajero</td>
														<td>$fila[cajero]</td>
													</tr>
													
													<tr>
														<td>Movimiento</td>
														<td>$fila[tipo_corte]</td>
													</tr>
													<tr>
														<td>Monto</td>
														<td>$$fila[monto]</td>
													</tr>
													
													<tr>
														<td>Observaciones</td>
														<td>$fila[cor_observaciones]</td>
													</tr>
												</table>
												
												<p>Artículos registrados al hacer el movimiento</p>
												
												<table>
													<tr>
														<th>Código</td>
														<th>Articulo</td>
														<th>Existencia</td>
													</tr>
													
													$lst_stock
												</table>
											</body>
										</html>";
						
						$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
						$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						
						mail( $para, $titulo, $mensaje, $cabeceras );
					}
				}
			}
		}
	}
	
?>