<?php
	function eliminar_gasto( $p_id_gasto )
	{
		global $conexion, $id_consorcio;
		
		$exito	= array();
		
		if( $p_id_gasto )
		{
			mysqli_autocommit( $conexion, false );
			
			$query		= "	DELETE
							FROM	san_gastos
							WHERE	gas_id_gasto = $p_id_gasto
							AND		gas_id_empresa IN ( SELECT coem_id_empresa FROM san_consorcio_empresa WHERE coem_id_consorcio = $id_consorcio )";
			
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( mysqli_affected_rows( $conexion ) == 1 )
				{
					$exito['num'] = 1;
					$exito['msj'] = "Gasto eliminado.";
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "No se ha eliminado el gasto seleccionado.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Ocurrio un problema técnico al tratar de eliminar el gasto. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se seleccionó un gasto.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
	function obtener_gasto_detalle( $p_id_gasto )
	{
		global $conexion, $id_consorcio;
		
		$datos		= "";
		
		$query		= "	SELECT		pro_nombres AS proveedor,
									emp_descripcion AS sucursal,
									CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) AS usuario,
									gas_fnota AS fnota,
									DATE_FORMAT( gas_fecha_fnota, '%d-%m-%Y' ) AS fecha_fnota,
									LOWER( DATE_FORMAT( gas_fecha_captura, '%d-%m-%Y %r' ) ) AS fecha_captura,
									ROUND( gas_importe, 2 ) AS importe,
									ROUND( gas_iva, 2 ) AS iva,
									ROUND( gas_descuento, 2 ) AS descuento,
									ROUND( gas_total, 2 ) AS total,
									gas_observaciones AS observaciones
						FROM		san_gastos
						INNER JOIN	san_proveedores ON pro_id_proveedor = gas_id_proveedor
						INNER JOIN	san_empresas ON emp_id_empresa = gas_id_empresa
						INNER JOIN	san_usuarios ON usua_id_usuario = gas_id_usuario
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = gas_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						AND			con_id_consorcio = pro_id_consorcio
						WHERE		gas_id_gasto = $p_id_gasto
						AND			con_id_consorcio = $id_consorcio";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	= "	<div class='row'>
								<label class='col-md-2'>Sucursal</label>
								<div class='col-md-10'>$fila[sucursal]</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Proveedor</label>
								<div class='col-md-4'>$fila[proveedor]</div>
								
								<label class='col-md-4'>No. de Factura o Nota</label>
								<div class='col-md-2'>$fila[fnota]</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Capturó</label>
								<div class='col-md-10'>$fila[usuario]</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Fecha nota</label>
								<div class='col-md-4'>".fecha_generica( $fila['fecha_fnota'] )."</div>
								
								<label class='col-md-2'>Fecha captura</label>
								<div class='col-md-4'>".fecha_generica( $fila['fecha_captura'] )."</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Importe</label>
								<div class='col-md-4'>$".number_format( $fila['importe'], 2 )."</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>IVA</label>
								<div class='col-md-4'>$".number_format( $fila['iva'], 2 )."</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Descuento</label>
								<div class='col-md-4'>$".number_format( $fila['descuento'], 2 )."</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Total</label>
								<div class='col-md-4'>$".number_format( $fila['total'], 2 )."</div>
							</div>
							
							<div class='row'>
								<label class='col-md-2'>Observaciones</label>
								<div class='col-md-10'>$fila[observaciones]</div>
							</div>";
			}
		}
			
		return $datos;
	}
	
	function obtener_gastos( $mes_movimiento, $sucursal )
	{
		global $conexion, $id_consorcio;
		
		$colspan		= 8;
		$datos			= "";
		$condicion		= "";
		$tot_importe	= 0;
		$tot_iva		= 0;
		$tot_descuento	= 0;
		$tot_total		= 0;
		
		if( $sucursal )
			$condicion = "AND coem_id_empresa = $sucursal";
		
		$query		= "	SELECT		gas_id_gasto AS id_gasto,
									emp_descripcion AS sucursal,
									pro_nombres AS proveedor,
									gas_importe AS importe,
									gas_iva AS iva,
									gas_descuento AS descuento,
									gas_total AS total,
									DATE_FORMAT( gas_fecha_fnota, '%d-%m-%Y' ) AS fnota
						FROM		san_gastos
						INNER JOIN	san_empresas ON emp_id_empresa = gas_id_empresa
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = gas_id_empresa
						INNER JOIN	san_proveedores ON pro_id_proveedor = gas_id_proveedor
						WHERE		'$mes_movimiento' = DATE_FORMAT( gas_fecha_fnota, '%Y-%m' )
						AND			coem_id_consorcio = $id_consorcio
									$condicion
						ORDER BY	gas_fecha_fnota,
									pro_nombres";
		
		$resultado		= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr>
								<td>$i</td>
								<td>
									<div class='btn-group pointer'>
										<a class='' dropdown-toggle' data-toggle='dropdown'>
											<span class='glyphicon glyphicon-chevron-down'></span>
										</a>
										<ul class='dropdown-menu'>
											<li><a onclick='mostrar_detalle_gasto( $fila[id_gasto] )'><span class='glyphicon glyphicon-list'></span> Detalles</a></li>
											<li><a onclick='eliminar_detalle_gasto( $fila[id_gasto] )'><span class='glyphicon glyphicon-remove-sign'></span> Eliminar gasto</a></li>
										</ul>
									</div>
								</td>
								<td>".$fila['proveedor']."</td>
								<td class='text-right'>$".number_format( $fila['importe'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['iva'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['descuento'], 2 )."</td>
								<td class='text-right'>$".number_format( $fila['total'], 2 )."</td>
								<td>".$fila['fnota']."</td>
							</tr>";
				
				$tot_importe	+= $fila['importe'];
				$tot_iva		+= $fila['iva'];
				$tot_descuento	+= $fila['descuento'];
				$tot_total		+= $fila['total'];
				$i++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>No se pudo obtener el reporte. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$colspan -= 5;
		$datos	.= "<tr class='success text-bold'>
						<td colspan='$colspan' class='text-right'>Totales</td>
						<td class='text-right'>$".number_format( $tot_importe, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_iva, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_descuento, 2 )."</td>
						<td class='text-right'>$".number_format( $tot_total, 2 )."</td>
						<td>&nbsp;</td>
					</tr>";
			
		return $datos;
	}
	
?>