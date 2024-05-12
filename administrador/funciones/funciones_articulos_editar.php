<?php
	function actualizar_articulo()
	{
		global $conexion, $id_consorcio;
		
		$exito			= array();
		$IDC			= request_var( 'IDC', '' );
		$id_articulo	= request_var( 'id_articulo', '' );
		$art_desc		= strtoupper( request_var( 'art_desc', '' ) );
		$art_detalle	= strtoupper( request_var( 'art_detalle', '' ) );
		$art_costo		= request_var( 'art_costo', 0.0 );
		$art_precio		= request_var( 'art_precio', 0.0 );
		$art_monedero	= request_var( 'art_monedero', 0.0 );
		$art_mayoreo_1	= request_var( 'art_mayoreo_1', 0.0 );
		$art_mayoreo_2	= request_var( 'art_mayoreo_2', 0.0 );
		$art_unidad		= request_var( 'art_unidad', '' );
		$art_status		= request_var( 'art_status', 'A' );
		$art_proveedor	= request_var( 'art_proveedor', 0 );
		$art_categoria	= request_var( 'art_categoria', 0 );
		$art_marca		= request_var( 'art_marca', 0 );
		
		$monto_monedero	= round( $art_precio * ( $art_monedero / 100 ), 2 );
		$utilidad		= $art_precio - $art_costo;
		$jesusgp		= 0;
		
		mysqli_autocommit( $conexion, false );
		
		$art_query	= "	UPDATE	san_articulos
						SET		art_id_proveedor = $art_proveedor,
								art_id_categoria = $art_categoria,
								art_id_marca = $art_marca,
								art_descripcion = '$art_desc',
								art_detalle = '$art_detalle',
								art_costo = ROUND( $art_costo, 2 ),
								art_precio = ROUND( $art_precio, 2 ),
								art_monedero = ROUND( $art_monedero, 2 ),
								art_mayoreo_1 = ROUND( $art_mayoreo_1,2 ),
								art_mayoreo_2 = ROUND( $art_mayoreo_2, 2 ),
								art_unidad = '$art_unidad',
								art_status = '$art_status'
						WHERE	art_id_articulo = $id_articulo
						AND		art_id_consorcio = $id_consorcio";
		
		if( !$art_monedero || ( $art_monedero > 0 && $art_monedero <= 99 ) )
		{
			if( !$art_monedero || ( $monto_monedero < $utilidad ) )
			{
				if( $id_articulo && $art_desc && $IDC == $id_consorcio )
				{
					$resultado	= mysqli_query( $conexion, $art_query );
					
					if( $resultado )
					{
						if( isset( $_POST['stock'] ) )
						{
							$art_stock		= $_POST['stock'];//arrego [IDE][stock]
							$art_ubicacion	= $_POST['ubicacion'];//arrego [IDE][ubicacion]
							
							foreach( $art_stock as $IDE => $agregar ) //IDE=id_empresa, agregar[0]=cant. que se agrega
							{
								$tmp_ubicacion	= "";
								
								foreach( $art_ubicacion as $u_IDE => $u_ubicacion )
								{
									if( $u_IDE == $IDE )
									{
										$tmp_ubicacion = $u_ubicacion;
										break;
									}
								}
								
								if( !$agregar )
									$agregar = 0;
								
								if( is_numeric( $agregar ) )
								{
									if( $art_unidad == 'S' )
										$agregar = 0;
									
									$datos_sql		= array
									(
										'stk_id_articulo'	=> $id_articulo,
										'stk_existencia'	=> round( $agregar, 2 ),
										'stk_id_empresa'	=> $IDE,
										'stk_ubicacion'		=> $tmp_ubicacion
									);
									
									$jesusgp = $agregar;
									
									if( checar_stock( $id_articulo, $IDE ) )
									{
										$query	= "	UPDATE	san_stock 
													SET 	stk_existencia = ROUND( stk_existencia + $agregar, 2 ), 
															stk_ubicacion = '$tmp_ubicacion' 
													WHERE 	stk_id_empresa = $IDE 
													AND 	stk_id_articulo = $id_articulo";
									}
									else
										$query	= construir_insert( 'san_stock', $datos_sql );
									
									$resultado	= mysqli_query( $conexion, $query );
									
									if( $resultado && is_numeric( $agregar ) )
									{
										if( mysqli_affected_rows( $conexion ) <= 1 )
										{
											$exito['num'] = 1;
											$exito['msj'] = "STOCK del articulo actualizado.";
											
											notificar_por_correo( $id_articulo, $jesusgp );
										}
										else
										{
											$exito['num'] = 8;
											$exito['msj'] = "Ningún cambio a guardar.";
										}
									}
									else
									{
										$exito['num'] = 7;
										$exito['msj'] = "No se puede guardar el STOCK. ".mysqli_error( $conexion );
										break;
									}
								}
								else
								{
									$exito['num'] = 6;
									$exito['msj'] = "Se actualizo el articulo, pero no se puede actualizar el STOCK en una sucursal porque no se ingreso un número válido.";
									break;
								}
							}
						}
						else
						{
							$exito['num'] = 1;
							$exito['msj'] = "Articulo actualizado.";
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "No se pudo actualizar los datos del articulo.";
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "Descripción del Articulo inválido.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "El porcentaje del monedero genera un monto mayor o igual al de la utilidad. El monto del monedero debe ser menor a la utilidad.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "El porcentaje para el monedero debe ser mayor a 0 y menor o igual a 99.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function obtener_detalle_articulo( $id_articulo, $IDC )
	{
		global $conexion, $id_consorcio;
		
		$query		= "	SELECT		art_id_articulo AS id_articulo,
									art_codigo AS codigo,
									art_id_consorcio AS id_consorcio,
									art_id_proveedor AS id_proveedor,
									art_id_categoria AS id_categoria,
									art_id_marca AS id_marca,
									art_descripcion AS descripcion,
									art_detalle AS detalle,
									ROUND( art_costo, 2 ) AS costo,
									ROUND( art_precio, 2 ) AS precio,
									ROUND( art_monedero, 2 ) AS monedero,
									ROUND( art_mayoreo_1, 2 )AS mayoreo_1,
									ROUND( art_mayoreo_2, 2 )AS mayoreo_2,
									art_unidad AS unidad,
									art_status AS status
						FROM		san_articulos
						INNER JOIN	san_consorcios ON con_id_consorcio = art_id_consorcio
						WHERE		art_id_articulo = $id_articulo
						AND			art_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $id_articulo && $IDC == $id_consorcio )
			if( $resultado )
				if( $fila = mysqli_fetch_assoc( $resultado ) )
					return $fila;
			
		return false;
	}
	
	function obtener_stock( $id_articulo )
	{
		global $conexion, $id_consorcio, $id_giro;
		
		$datos		= "";
		$colspan	= 4;
		
		$query		= "	SELECT		emp_descripcion AS sucursal,
									emp_id_empresa AS id_empresa,
									ROUND( IF( stk_existencia IS NULL, 0, stk_existencia ), 2 ) AS existencia,
									stk_ubicacion AS ubicacion
						FROM		san_articulos
						INNER JOIN	san_consorcio_empresa ON coem_id_consorcio = art_id_consorcio
						INNER JOIN	san_empresas ON emp_id_empresa = coem_id_empresa
						LEFT JOIN	san_stock ON stk_id_articulo = art_id_articulo
						AND			stk_id_empresa = coem_id_empresa
						WHERE		art_id_articulo = $id_articulo
						AND			art_id_consorcio = $id_consorcio
						AND			emp_id_giro IN ( 1, 3 )";//1=GYM, 3=TPV
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos .= "	<tr>
								<td>$i</td>
								<td>$fila[sucursal]</td>
								<td class='text-right'>$fila[existencia]</td>
								<td width='130px'><input type='text' name='stock[$fila[id_empresa]]' class='form-control' autocomplete='off' /></td>
								<td width='100px'><input type='text' name='ubicacion[$fila[id_empresa]]' class='form-control' autocomplete='off' maxlength='10' value='$fila[ubicacion]' /></td>
							</tr>";
				$i++;
			}
		}
		else
			$datos = "<tr><td colspan='$colspan'>".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
	function eliminar_articulo()
	{
		global $conexion, $id_consorcio;
		
		$exito			= array();
		$id_articulo	= request_var( 'id_articulo', '' );
		$IDC			= request_var( 'IDC', '' );
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "	SELECT		art_codigo
						FROM		san_articulos
						INNER JOIN	san_consorcio_empresa ON coem_id_consorcio = art_id_consorcio
						INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
						AND			stk_id_empresa = coem_id_empresa
						WHERE		art_id_articulo = $id_articulo
						AND			stk_existencia > 0
						AND			coem_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado && $IDC == $id_consorcio )
		{
			if( !mysqli_num_rows( $resultado ) )
			{
				$query		= "	SELECT		*
								FROM		san_venta
								INNER JOIN	san_venta_detalle ON vende_id_venta = ven_id_venta
								INNER JOIN	san_consorcio_empresa ON coem_id_empresa = ven_id_empresa
								WHERE		vende_id_articulo = $id_articulo
								AND			coem_id_consorcio = $id_consorcio";
								
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					if( !mysqli_num_rows( $resultado ) )
					{
						$query		= "	DELETE FROM	san_stock 
										WHERE		stk_id_articulo = $id_articulo
										AND 		stk_existencia <= 0 
										AND 		stk_id_empresa IN ( SELECT coem_id_empresa FROM san_consorcio_empresa WHERE coem_id_consorcio = $id_consorcio )";
										
						$resultado	= mysqli_query( $conexion, $query );
						
						if( $resultado && mysqli_affected_rows( $conexion ) )
						{
							$query		= "DELETE FROM san_articulos WHERE art_id_articulo = $id_articulo AND art_id_consorcio = $id_consorcio";
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								if( mysqli_affected_rows( $conexion ) == 1 )
								{
									$exito['num'] = 1;
									$exito['msj'] = "Artículo eliminado.";
								}
								else
								{
									$exito['num'] = 7;
									$exito['msj'] = "No se ha eliminado ningún Artículo.";
								}
							}
							else
							{
								$exito['num'] = 6;
								$exito['msj'] = "Ocurrió un problema al tratar de Eliminar este Artículo. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "Ocurrió un problema al tratar de Eliminar este Artículo del STOCK. ".mysqli_error( $conexion );
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "Mientras que haya Ventas de este Artículo no podrá ser eliminado. Se sugiere sea Descontinuado.";
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "Ocurrió un problema al tratar de consultar las Ventas de este Artículo. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Mientras que haya existencias de este Artículo no podrá ser eliminado.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Ocurrió un problema al tratar de consultar el STOCK de este Artículo. ".mysqli_error( $conexion );
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	// solo para el gym de jesus
	function notificar_por_correo( $id_articulo, $cant_movimiento )
	{
		global $conexion, $id_consorcio;
		
		$lst_stock	= "";
		$para		= "";
		
		if( $id_consorcio == 1 )
		{
			$query		= "SELECT emp_correo FROM san_empresas WHERE emp_id_empresa = 1";
			$resultado	= mysqli_query( $conexion, $query );
			list( $para ) = mysqli_fetch_row( $resultado );
			
			if( $para )
			{
				$query		= "	SELECT		emp_descripcion AS sucursal,
											emp_id_empresa AS id_empresa,
											art_id_articulo AS id_articulo,
											art_codigo AS codigo,
											art_descripcion AS articulo,
											stk_existencia AS stock
								FROM		san_articulos
								INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
								INNER JOIN	san_empresas ON emp_id_empresa = stk_id_empresa
								WHERE		art_id_consorcio = $id_consorcio
								AND			art_status = 'A'
								AND			art_id_articulo = $id_articulo
								ORDER BY	emp_descripcion";
				
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					while( $fila = mysqli_fetch_assoc( $resultado ) )
					{
						$lst_stock .= "	<tr>
											<td>$fila[sucursal]</td>
											<td>$fila[codigo]</td>
											<td>$fila[articulo]</td>
											<td>$fila[stock]</td>
											<td>$cant_movimiento</td>
										</tr>";
					}
					
					$fecha		= fecha_generica( $fila['fecha'] );
					$titulo		= 'Movimiento en Inventario | SERGYM';
					
					$mensaje	= "	<html>
										<head>
											<title>Movimiento de caja</title>
										</head>
										
										<body>
											<p>Movimiento registrado</p>
											
											<table>
												<tr>
													<th>Sucursal</td>
													<th>Código</td>
													<th>Articulo</td>
													<th>Actual</td>
													<th>Movimiento</td>
												</tr>
												
												$lst_stock
											</table>
										</body>
									</html>";
					
					$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
					$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					
					mail( $para, $titulo, utf8_decode( $mensaje ), $cabeceras );
				}
			}
		}
	}
	
?>