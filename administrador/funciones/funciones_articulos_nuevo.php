<?php
	function guardar_articulo()
	{
		global $conexion, $id_consorcio;
		
		$exito			= array();
		$codigo			= strtoupper( request_var( 'art_codigo', '' ) );
		$art_desc		= strtoupper( request_var( 'art_descripcion', '' ) );
		$art_costo		= round( request_var( 'art_costo', 0.0 ), 2 );
		$art_precio		= round( request_var( 'art_precio', 0.0 ), 2 );
		$art_monedero	= round( request_var( 'art_monedero', 0.0 ), 2 );
		$art_mayoreo_1	= round( request_var( 'art_mayoreo_1', 0.0 ), 2 );
		$art_mayoreo_2	= round( request_var( 'art_mayoreo_2', 0.0 ), 2 );
		$art_unidad		= request_var( 'art_unidad', '' );
		
		mysqli_autocommit( $conexion, false );
		
		$datos_sql	= array
		(
			'art_codigo'		=> $codigo,
			'art_id_consorcio'	=> $id_consorcio,
			'art_id_proveedor'	=> request_var( 'art_proveedor', 0.0 ),
			'art_id_categoria'	=> request_var( 'art_categoria', 0.0 ),
			'art_id_marca'		=> request_var( 'art_marca', 0.0 ),
			'art_descripcion'	=> $art_desc,
			'art_detalle'		=> strtoupper( request_var( 'art_detalle', '' ) ),
			'art_costo'			=> $art_costo,
			'art_precio'		=> $art_precio,
			'art_monedero'		=> $art_monedero,
			'art_mayoreo_1'		=> $art_mayoreo_1,
			'art_mayoreo_2'		=> $art_mayoreo_2,
			'art_unidad'		=> $art_unidad
		);
		
		if( $art_desc && $codigo )
		{
			if( !checar_articulo( $codigo ) && $codigo )
			{
				$query			= construir_insert( 'san_articulos', $datos_sql );
				$resultado		= mysqli_query( $conexion, $query );
				$id_articulo	= mysqli_insert_id( $conexion );
				
				if( $resultado && $id_articulo )
				{
					if( isset( $_POST['stock'] ) )
					{
						$art_stock = $_POST['stock'];
						
						foreach( $art_stock as $IDE => $agregar ) //IDE=id_empresa, agregar[0]=lo que se agrega
						{
							if( $art_unidad == 'S' )
								$agregar[0] = 0;
							
							$datos_sql		= array
							(
								'stk_id_articulo'	=> $id_articulo,
								'stk_existencia'	=> round( $agregar[0], 2 ),
								'stk_id_empresa'	=> $IDE
							);
							
							$query		= construir_insert( 'san_stock', $datos_sql );
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								if( mysqli_affected_rows( $conexion ) )
								{
									$exito['num'] = 1;
									$exito['msj'] = "Articulo y STOCK guardados.";
								}
							}
							else
							{
								$exito['num'] = 4;
								$exito['msj'] = "No se puede guardar el STOCK. ".mysqli_error( $conexion );
								break;
							}
						}
					}
					else
					{
						$exito['num'] = 1;
						$exito['msj'] = "Articulo Guardado.";
					}
				}
				else
				{
					$exito['num']	= 3;
					$exito['msj']	= "No se ha podido guardar los datos del Articulo. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 2;
				$exito['msj'] = "No se ha podido guardar los datos capturados porque exite un articulo con el código ingresado. Para continuar verifica bien los datos y escribe un nuevo código.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Descripción del Articulo inválido.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function checar_articulo( $codigo )
	{
		global $conexion, $id_consorcio;
		
		$query		= "SELECT art_codigo FROM san_articulos WHERE UPPER( art_codigo ) = UPPER( '$codigo' ) AND art_id_consorcio = $id_consorcio";
		$resultado	= mysqli_query( $conexion, $query );
		
		mysqli_autocommit( $conexion, false );
		
		if( $resultado )
			if( mysqli_num_rows( $resultado ) )
				return true;
		
		return false;
	}
	
	function obtener_empresas_para_stock()
	{
		global $conexion, $id_consorcio, $id_giro;
		
		$datos		= "";
		$colspan	= 3;
		
		$query		= "	SELECT		emp_descripcion AS sucursal,
									emp_id_empresa AS id_empresa
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						WHERE		coem_id_consorcio = $id_consorcio
						AND			emp_id_giro IN ( 1, 3 )";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos .= "	<tr>
								<td>$i</td>
								<td>$fila[sucursal]</td>
								<td width='100px'><input type='text' name='stock[$fila[id_empresa]][]' class='form-control' autocomplete='off' /></td>
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
	
?>