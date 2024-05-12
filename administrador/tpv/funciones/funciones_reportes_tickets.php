<?php
	function opciones_status_folios( $default = '' )
	{
		$busqueda		= array( 'V' => 'Vendidos', 'C' => 'Devoluciones Completas', 'P' => 'Devoluciones Parciales'  );
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
	
	function lista_folios_cancelados( $mes_evaluar, $status = '' )
	{
		global $conexion, $id_empresa, $gbl_paginado;
		
		$var_pagina		= ( request_var( 'pag', 1 ) - 1 ) * $gbl_paginado;
		$exito		= array();
		$colspan	= 6;
		$filas		= 0;
		$class		= "";
		$datos		= "";
		$condicion	= "";
		
		if( $status )
			$condicion .= " AND ven_status = '$status' ";
		
		//el total de filas para el paginado
		$query		= "	SELECT		COUNT(*) AS total
						FROM		san_venta
						INNER JOIN	san_usuarios a ON usua_id_usuario = ven_id_usuario
						WHERE		ven_id_empresa = $id_empresa
						AND			DATE_FORMAT( ven_fecha, '%m-%Y' ) = '$mes_evaluar'
									$condicion";
		
		$resultado	= mysqli_query( $conexion, $query );
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				$filas = $fila['total'];
		
		//el detalle de los folios
		$query		= "	SELECT		ven_id_venta AS id_venta,
									ven_folio AS folio,
									ven_status AS status,
									LOWER( DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) ) AS fecha,
									usua_nombres AS usuario,
									ven_total AS total,
									ven_observaciones AS obs
						FROM		san_venta
						INNER JOIN	san_usuarios a ON usua_id_usuario = ven_id_usuario
						WHERE		ven_id_empresa = $id_empresa
						AND			DATE_FORMAT( ven_fecha, '%m-%Y' ) = '$mes_evaluar'
									$condicion
						ORDER BY	fecha DESC
						LIMIT		$var_pagina, $gbl_paginado";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				switch( $fila['status'] )
				{
					case 'C':	$class = "danger";	break;
					case 'P':	$class = "warning";	break;
					default:	$class = '';
				}
				
				$datos	.= "<tr class='$class' onclick='mostrar_detalle_cancelacion( $fila[id_venta], $fila[folio] )'>
								<td>".( $var_pagina + $i )."</td>
								<td>".$fila['folio']."</td>
								<td>".$fila['fecha']."</td>
								<td>".$fila['usuario']."</td>
								<td class='text-right'>$".number_format( $fila['total'], 2 )."</td>
								<td>".$fila['obs']."</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos .= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos .= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$exito['num'] = $filas;
		$exito['msj'] = $datos;
		
		return $exito;
	}
	
?>