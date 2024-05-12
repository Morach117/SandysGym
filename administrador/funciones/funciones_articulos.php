<?php
	function opciones_busqueda( $default = 0 )
	{
		$busqueda		= array(
			1 => 'Con existencia', 
			2 => 'Sin existencia', 
			3 => 'Con monedero',
			4 => 'Sin monedero'
		);
		
		$opc_busqueda	= "";
		
		foreach( $busqueda as $ind => $opcion )
		{
			if( $default == $ind )
				$opc_busqueda .= "<option selected value='$ind'>$opcion</option>";
			else
				$opc_busqueda .= "<option value='$ind'>$opcion</option>";
		}
		
		return $opc_busqueda;
	}
	
	function opciones_status( $default = '' )
	{
		$busqueda		= array( 'A' => 'Activos', 'D' => 'Descontinuados' );
		$opc_busqueda	= "";
		
		foreach( $busqueda as $ind => $opcion )
		{
			if( $default == $ind )
				$opc_busqueda .= "<option selected value='$ind'>$opcion</option>";
			else
				$opc_busqueda .= "<option value='$ind'>$opcion</option>";
		}
		
		return $opc_busqueda;
	}
	
	/*verificar que coincida con los comentarios de la table de articulos columna tipo*/
	function combo_tipos_unidades( $default = '' )
	{
		$unidades	= array
		(
			'C' => 'Caja',
			'J' => 'Juego',
			'L' => 'Litro',
			'M' => 'Metro',
			'P' => 'Pieza',
			'S' => 'Servicio'
		);
		
		$opciones	= "";
		
		foreach( $unidades as $clave => $descripcion )
		{
			if( $clave == $default )
				$opciones .= "<option selected value='$clave'>$descripcion</option>";
			else
				$opciones .= "<option value='$clave'>$descripcion</option>";
		}
		
		return $opciones;
	}
	
	function checar_stock( $id_articulo, $IDE )
	{
		global $conexion;
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "	SELECT	stk_existencia
						FROM	san_stock
						WHERE	stk_id_articulo = $id_articulo
						AND		stk_id_empresa = $IDE";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( mysqli_num_rows( $resultado ) )
				return true;
		
		return false;
	}
	
	function lista_articulos_excel()
	{
		global $conexion, $id_consorcio;
		
		$pag_IDE		= request_var( 'pag_IDE', 0 );//id_empresa
		$v_status		= request_var( 'pag_status', '' );
		$v_proveedores	= request_var( 'pag_proveedores', 0 );
		$v_categorias	= request_var( 'pag_categorias', 0 );
		$v_marcas		= request_var( 'pag_marcas', 0 );
		$v_opciones		= request_var( 'pag_opciones', 0 );
		$v_busqueda		= strtoupper( request_var( 'pag_busqueda', '' ) );
		$v_datos		= array();
		$v_condicion	= "";
		$v_scondicion	= "";
		
		if( $pag_IDE )
			$v_condicion .= "AND stk_id_empresa = $pag_IDE ";
		
		if( $v_status )
			$v_condicion .= "AND art_status = '$v_status' ";
		
		if( $v_proveedores )
			$v_condicion .= "AND art_id_proveedor = $v_proveedores ";
		
		if( $v_categorias )
			$v_condicion .= "AND art_id_categoria = $v_categorias ";
		
		if( $v_marcas )
			$v_condicion .= "AND art_id_marca = $v_marcas ";
		
		if( $v_opciones == 1 )
			$v_condicion .= "AND stk_existencia > 0 ";
		
		if( $v_opciones == 2 )
			$v_condicion .= "AND stk_existencia <= 0 ";
		
		if( $v_opciones == 3 )
			$v_condicion .= "AND art_monedero > 0 ";
		
		if( $v_opciones == 4 )
			$v_condicion .= "AND art_monedero <= 0 ";
		
		if( $v_busqueda )
		{
			$palabras	= explode( ' ', $v_busqueda );
			
			foreach( $palabras as $like )
				$v_scondicion .= "%$like%";
				
			$v_scondicion	= substr( $v_scondicion, 0, -1 );
			$v_condicion	.= " AND ( UPPER( art_codigo ) LIKE UPPER( '%$v_scondicion%' ) OR UPPER( art_descripcion ) LIKE UPPER( '%$v_scondicion%' ) )";
		}
		
		$query		= "	SELECT 		art_codigo AS codigo,
									art_id_consorcio AS id_consorcio,
									art_descripcion AS descripcion,
									art_unidad AS unidad,
									ROUND( SUM( IF( stk_existencia IS NULL, 0, stk_existencia ) ), 2 ) AS existencia,
									ROUND( art_costo, 2 ) AS costo,
									ROUND( art_precio, 2 ) AS precio,
									ROUND( art_mayoreo_1, 2 ) AS mayoreo_1,
									ROUND( art_mayoreo_2, 2 ) AS mayoreo_2
						FROM 		san_articulos
						INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
						WHERE		art_id_consorcio = $id_consorcio
									$v_condicion
						GROUP BY	art_codigo
						ORDER BY	descripcion";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			while( $fila = mysqli_fetch_assoc( $resultado ) )
				array_push( $v_datos, $fila );
		
		return $v_datos;
	}
	
	function lista_articulos( $p_regresar = '' )
	{
		global $conexion, $id_consorcio, $gbl_paginado;
		
		$var_pagina		= ( request_var( 'pag', 1 ) - 1 ) * $gbl_paginado;
		$pag_IDE		= request_var( 'pag_IDE', 0 );//id_empresa
		$var_status		= request_var( 'pag_status', '' );
		$var_proveedors	= request_var( 'pag_proveedores', 0 );
		$var_categorias	= request_var( 'pag_categorias', 0 );
		$var_marcas		= request_var( 'pag_marcas', 0 );
		$var_opciones	= request_var( 'pag_opciones', 0 );
		$var_busqueda	= strtoupper( request_var( 'pag_busqueda', '' ) );
		$var_exito		= array();
		$var_datos		= "";
		$var_condicion	= "";
		$v_scondicion	= "";
		$limit			= "LIMIT $var_pagina, $gbl_paginado";
		$var_colspan	= 8;
		$var_total		= 0;
		
		if( $pag_IDE )
			$var_condicion .= "AND stk_id_empresa = $pag_IDE ";
		
		if( $var_status )
			$var_condicion .= "AND art_status = '$var_status' ";
		
		if( $var_proveedors )
			$var_condicion .= "AND art_id_proveedor = $var_proveedors ";
		
		if( $var_categorias )
			$var_condicion .= "AND art_id_categoria = $var_categorias ";
		
		if( $var_marcas )
			$var_condicion .= "AND art_id_marca = $var_marcas ";
		
		if( $var_opciones == 1 )
			$var_condicion .= "AND stk_existencia > 0 ";
		
		if( $var_opciones == 2 )
			$var_condicion .= "AND stk_existencia <= 0 ";
		
		if( $var_opciones == 3 )
			$var_condicion .= "AND art_monedero > 0 ";
		
		if( $var_opciones == 4 )
			$var_condicion .= "AND art_monedero <= 0 ";
		
		if( $var_busqueda )
		{
			$palabras	= explode( ' ', $var_busqueda );
			
			foreach( $palabras as $like )
				$v_scondicion .= "%$like%";
				
			$v_scondicion	= substr( $v_scondicion, 0, -1 );
			$var_condicion	.= " AND ( UPPER( art_codigo ) LIKE UPPER( '%$v_scondicion%' ) OR UPPER( art_descripcion ) LIKE UPPER( '%$v_scondicion%' ) )";
		}
		
		$query		= "	SELECT 		COUNT(*) AS total
						FROM 		san_articulos
						INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
						WHERE		art_id_consorcio = $id_consorcio
									$var_condicion
						GROUP BY	art_codigo";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			$var_total = mysqli_num_rows( $resultado );
		
		mysqli_free_result( $resultado );
		
		$query		= "	SELECT	a.*,
								fn_trans_total( id_consorcio, $pag_IDE, id_articulo, 'D' ) AS transito,
								fn_trans_total( id_consorcio, $pag_IDE, id_articulo, 'O' ) AS transferencia,
								fn_apartados_total( id_consorcio, $pag_IDE, id_articulo ) AS apartado
						FROM
						(
							SELECT 		art_id_articulo AS id_articulo,
										art_id_consorcio AS id_consorcio,
										art_codigo AS codigo,
										art_descripcion AS descripcion,
										art_unidad AS unidad,
										ROUND( SUM( IF( stk_existencia IS NULL, 0, stk_existencia ) ), 2 ) AS existencia,
										ROUND( art_costo, 2 ) AS costo,
										ROUND( art_precio, 2 ) AS precio,
										ROUND( ( art_monedero * art_precio ) / 100, 2 ) AS mon_monto
							FROM 		san_articulos
							INNER JOIN	san_stock ON stk_id_articulo = art_id_articulo
							WHERE		art_id_consorcio = $id_consorcio
										$var_condicion
							GROUP BY	art_codigo
							ORDER BY	descripcion
										$limit
						) a";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$class			= ( $fila['existencia'] <= 0 && $fila['unidad'] != 'S' ) ? "class='danger'":'';
				$codigo			= strtoupper( $fila['codigo'] );
				$descripcion	= strtoupper( $fila['descripcion'] );
				
				if( $fila['apartado'] == ceil( $fila['apartado'] ) )
					$fila['apartado'] = (int)$fila['apartado'];
				
				if( $fila['transito'] == ceil( $fila['transito'] ) )
					$fila['transito'] = (int)$fila['transito'];
				
				if( $fila['transferencia'] == ceil( $fila['transferencia'] ) )
					$fila['transferencia'] = (int)$fila['transferencia'];
				
				$apartado		= ( $fila['apartado'] > 0 ) ? "<span class='label label-warning'>$fila[apartado]</span> ":"";
				$transito		= ( $fila['transito'] > 0 ) ? "<span class='label label-info'>$fila[transito]</span> ":"";
				$transferencia	= ( $fila['transferencia'] > 0 ) ? "<span class='label label-primary'>$fila[transferencia]</span> ":"";
				
				if( $var_busqueda )
				{
					$palabras	= explode( ' ', $var_busqueda );
			
					foreach( $palabras as $like )
					{
						$codigo			= str_replace( $like, "<span class='label label-success'>$like</span>", $codigo );
						$descripcion	= str_replace( $like, "<span class='label label-success'>$like</span>", $descripcion );
					}
				}
				
				$var_datos	.= "<tr $class >
									<td>".( $var_pagina + $i )."</td>
									<td>
										<div class='btn-group'>
											<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
												<span class='glyphicon glyphicon-chevron-down'></span>
											</a>
											<ul class='dropdown-menu pointer'>
												<li><a href='.?s=articulos&i=editar&id_articulo=$fila[id_articulo]&IDC=$fila[id_consorcio]$p_regresar'><span class='glyphicon glyphicon-list-alt'></span> Inventario</a></li>
												<li><a onclick='agregar_a_transferencia( $fila[id_articulo], 0, \"articulos\" )'><span class='glyphicon glyphicon-refresh'></span> Agregar a transferencia</a></li>
											</ul>
										</div>
									</td>
									<td>$codigo</td>
									<td>$descripcion</td>
									<td class='text-right'>$apartado$transito$transferencia$fila[existencia]</td>
									<td class='text-right'>$$fila[costo]</td>
									<td class='text-right'>$$fila[precio]</td>
									<td class='text-right'>$$fila[mon_monto]</td>
								</tr>";
				$i++;
			}
		}
		else
			$var_datos = "<tr><td colspan='$var_colspan'>Ocurrió un error al tratar de obtener el catálogo de Artículos. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$var_datos )
			$var_datos = "<tr><td colspan='$var_colspan'>No hay datos.</td></tr>";
		
		$var_exito['num'] = $var_total;
		$var_exito['msj'] = $var_datos;
		
		return $var_exito;
	}
	
	function numbre_sucursal( $id_sucursal )
	{
		global $conexion, $id_consorcio;
		
		$query		= "	SELECT		emp_descripcion
						FROM 		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						WHERE		coem_id_consorcio = $id_consorcio
						AND			coem_id_empresa = $id_sucursal";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['emp_descripcion'];
			
		return false;
	}
	
?>