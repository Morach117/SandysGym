<?php
	function realizar_corte( $id_empresa_corte, $fecha_venta )
	{
		global $conexion, $id_usuario;
		
		$cor_importe	= request_var( 'cor_importe', 0.0 );
		$fecha_mov		= date( 'Y-m-d H:i:s' );
		$fecha_venta	= fecha_formato_mysql( $fecha_venta );
		$exito			= array();
		
		if( $fecha_venta )
		{
			$datos_sql		= array
			(
				'cor_fecha'			=> $fecha_mov,
				'cor_fecha_venta'	=> $fecha_venta,
				'cor_id_usuario'	=> $id_usuario,
				'cor_id_empresa'	=> $id_empresa_corte,
				'cor_importe'		=> $cor_importe,
				'cor_observaciones'	=> request_var( 'cor_observaciones', '' )
			);
			
			$query		= construir_insert( 'san_corte', $datos_sql );
			
			if( $cor_importe )
			{
				$resultado	= mysqli_query( $conexion, $query );
				
				if( $resultado )
				{
					$exito['num'] = 1;
					$exito['msj'] = "Corte realizado exitosamente.";
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "No se puedo precesar la petición. ".mysqli_error( $conexion );
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se puede retirar más de lo que se ha indicado que se tiene.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se seleccionó fecha de venta.";
		}
		
		return $exito;
	}
	
	function lista_cortes_del_dia( $fecha_movimiento )
	{
		global $conexion, $id_empresa;
		
		list( $d, $m, $Y )	= explode( '-', $fecha_movimiento );
		$datos		= "";
		$colspan	= 10;
		$total		= 0;
		$contador	= 1;
		$fecha		= request_var( 'fecha', date( 'd-m-Y' ) );
		$v_id_cajero= request_var( 'cajero', 0 );
		
		$query		= "	SELECT		LOWER( DATE_FORMAT( cor_fecha, '%d-%m-%Y %r' ) ) AS movimiento,
									LOWER( DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' ) ) as fecha_venta,
									a.usua_nombres AS usuario,
									b.usua_nombres AS cajero,
									cor_id_corte AS id_corte,
									cor_importe AS importe,
									cor_caja AS caja,
									CASE cor_tipo_corte
										WHEN 3 THEN 'APERTURA'
										WHEN 4 THEN 'CIERRE'
										WHEN 5 THEN 'RETIRO'
										ELSE 'CORTE'
									END as tipo_mov,
									cor_observaciones AS notas
						FROM		san_corte
						INNER JOIN	san_usuarios a ON a.usua_id_usuario = cor_id_usuario
						LEFT JOIN	san_usuarios b ON b.usua_id_usuario = cor_id_cajero
						WHERE		(
										'$Y-$m-$d' = DATE_FORMAT( cor_fecha, '%Y-%m-%d' )
										OR
										'$Y-$m-$d' = DATE_FORMAT( cor_fecha_venta, '%Y-%m-%d' )
									)
						AND			cor_id_empresa = $id_empresa
						ORDER BY	movimiento DESC";
		
		if( checkdate( $m, $d, $Y ) )
		{
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				while( $fila = mysqli_fetch_assoc( $resultado ) )
				{
					$total += $fila['importe'];
					
					if( substr( $fila['movimiento'], 0, 10 ) != $fila['fecha_venta'] )
						$class = "warning";
					else
						$class = "";
					
					$datos	.= "<tr class='$class'>
									<td>$contador</td>
									<td>
										<div class='btn-group'>
											<a class='pointer' dropdown-toggle' data-toggle='dropdown'>
												<span class='glyphicon glyphicon-chevron-down'></span>
											</a>
											<ul class='dropdown-menu'>
												<li><a href='.?s=reportes&i=diario&idc=$fila[id_corte]&accion=e&fecha=$fecha&cajero=$v_id_cajero'><span class='glyphicon glyphicon-remove-sign'></span> Eliminar</a></li>
											</ul>
										</div>
									</td>
									<td>".$fila['movimiento']."</td>
									<td>".$fila['fecha_venta']."</td>
									<td>".$fila['usuario']."</td>
									<td>".$fila['cajero']."</td>
									<td>".$fila['tipo_mov']."</td>
									<td class='text-right'>$".number_format( $fila['caja'], 2 )."</td>
									<td class='text-right'>$".number_format( $fila['importe'], 2 )."</td>
									<td>".$fila['notas']."</td>
								</tr>";
					
					$contador++;
				}
			}
			else
				$datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener la información. ".mysqli_error( $conexion )."</td></tr>";
		}
		else
			$datos = "<tr><td colspan='$colspan'>Fecha inválida seleccionada.</td></tr>";
			
		if( !$datos )
			$datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		$colspan -= 2;
		$datos	.= "<tr class='success text-bold'>
								<td colspan='$colspan' class='text-right'>Total en retiros del día</td>
								<td class='text-right'>$".number_format( $total, 2 )."</td>
								<td>&nbsp;</td>
							</tr>";
		
		return $datos;
	}
	
	function total_importe_corte_del_dia( $fecha_movimiento, $p_id_cajero = 0 )
	{
		global $conexion, $id_empresa;
		
		list( $d, $m, $Y )	= explode( '-', $fecha_movimiento );
		
		$condicion	= "";
		
		if( $p_id_cajero )
			$condicion = "AND cor_id_cajero = $p_id_cajero";
		
		$query		= "	SELECT	SUM( cor_importe ) AS total_dia
						FROM	san_corte
						WHERE	'$Y-$m-$d' = DATE_FORMAT( cor_fecha_venta, '%Y-%m-%d' )
								$condicion
						AND		cor_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['total_dia'];
		
		return 0;
	}
	
	function eliminar_corte()
	{
		global $conexion, $id_empresa;
		
		$v_fecha	= request_var( 'fecha_mov', '' );
		$v_id_cajero= request_var( 'cajero', 0 );
		$v_id_corte	= request_var( 'idc', 0 );
		
		if( $v_id_corte )
		{
			$query		= "DELETE FROM san_corte WHERE cor_id_corte = $v_id_corte AND cor_id_empresa = $id_empresa";
			$resultado	= mysqli_query( $conexion, $query );
		}
		
		header( "location: .?s=reportes&i=diario&fecha_mov=$v_fecha&cajero=$v_id_cajero" );
		exit;
	}
	
?>