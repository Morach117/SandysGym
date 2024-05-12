<?php
	function checar_impresion_hora()
	{
		global $conexion, $id_empresa;
		
		$query		= "SELECT foc_tickets FROM san_folios_conf WHERE foc_id_empresa = $id_empresa";
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['foc_tickets'];
		
		return 'N';
	}
	
	function guardar_nuevo_horario()
	{
		global $conexion, $id_usuario, $id_empresa, $gbl_key;
		
		$hor_horas			= request_var( 'hor_horas', '' );
		$cuota				= obtener_servicio( 'HORA' );
		$exito				= array();
		$francion			= 0;
		
		$v_porcentaje_comision	= request_var( 'comision', 0.0 );
		$v_metodo_pago			= request_var( 'm_pago', '' ); // E-T
		
		$v_pag_efectivo			= 0;
		$v_pag_tarjeta			= 0;
		$v_monto_comision		= 0;
		
		$horas_minutos		= explode( ':', $hor_horas );
		
		$horas_suma			= (int)$horas_minutos[0];
		$minutos_suma		= (int)$horas_minutos[1];
		
		if( $minutos_suma )
			$francion = 0.5;
		
		$horas				= date( 'H' ) + $horas_suma;
		$minutos			= date( 'i' ) + $minutos_suma;
		$segundos			= date( 's' );
		
		$hor_hora_final		= mktime( $horas, $minutos, $segundos, 0, 0, 0 );
		$hor_hora_final		= date( 'H:i:s', $hor_hora_final );
		
		$importe			= ( $cuota['cuota'] * ( $horas_suma + $francion ) );
		
		if( $v_metodo_pago == 'E' )
			$v_pag_efectivo = $importe;
		else
		{
			$v_pag_tarjeta		= $importe;
			
			if( $v_porcentaje_comision > 0 )
				$v_monto_comision = $importe * ( $v_porcentaje_comision / 100 );
		}
		
		$datos_sql			= array
		(
			'hor_nombre'		=> request_var( 'hor_nombre', '' ),
			'hor_fecha'			=> date( 'Y-m-d H:i:s' ),
			'hor_hora_inicial'	=> date( 'H:i:s' ),
			'hor_hora_final'	=> $hor_hora_final,
			'hor_horas'			=> $hor_horas,
			'hor_efectivo'		=> $v_pag_efectivo,
			'hor_tarjeta'		=> $v_pag_tarjeta,
			'hor_comision'		=> round( $v_monto_comision, 2 ),
			'hor_importe'		=> round( $importe + $v_monto_comision, 2 ),
			'hor_tipo_pago'		=> $v_metodo_pago,
			'hor_genero'		=> request_var( 'hor_genero', '' ),
			'hor_id_servicio'	=> $cuota['id_servicio'],
			'hor_id_usuario'	=> $id_usuario,
			'hor_id_empresa'	=> $id_empresa
		);
		
		$query		= construir_insert( 'san_horas', $datos_sql );
		
		$resultado	= mysqli_query( $conexion, $query );
		$id_hora	= mysqli_insert_id( $conexion );
		$token		= hash_hmac( 'md5', $id_hora, $gbl_key );
		
		if( $resultado && $id_hora && $token )
		{
			$exito['num'] = 1;
			$exito['msj'] = "Guardado.";
			$exito['IDH'] = $id_hora;
			$exito['tkn'] = $token;
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se ha podido guardar los datos capturados. Intenta nuevamente. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function obtener_hora_seleccionada()
	{
		global $conexion, $id_empresa;
		
		$id_horas	= request_var( 'id_horas', 0 );
		$fecha_mov	= date( 'd-m-Y' );
		$hora_mov	= date( 'H:i:s' );
		
		$query		= "	SELECT 	hor_id_horas AS id_horas,
								hor_nombre AS nombre,
								hor_hora_inicial AS h_inicio,
								hor_hora_final AS h_final,
								hor_horas AS tiempo,
								hor_genero AS sexo
						FROM 	san_horas
						WHERE	'$fecha_mov' = DATE_FORMAT( hor_fecha, '%d-%m-%Y' )
						
						AND		hor_id_empresa = $id_empresa
						AND		hor_id_horas = $id_horas";
		//AND		hor_hora_final >= '$hora_mov'
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		elseif( $error = mysqli_error( $conexion ) )
			echo $error;
		
		return false;
	}
	
	function obtener_servicio( $desc = 'HORA' )
	{
		global $conexion;
		
		$query		= "	SELECT	ser_id_servicio AS id_servicio,
								ser_cuota AS cuota
						FROM	san_servicios
						WHERE	ser_tipo = 'PARCIAL'
						AND		ser_descripcion = '$desc'";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		else
			echo "Error: ".mysqli_error( $conexion );
		
		return false;
	}
	
	function eliminar_horas()
	{
		global $conexion, $id_empresa, $id_usuario;
		
		$id_horas	= request_var( 'id_horas', 0 );
		$exito		= array();
		$fecha_m	= date( 'Y-m-d H:i:s' );
		
		$query		= "	UPDATE	san_horas 
						SET 	hor_status = 'E',
								hor_id_usuario_e = $id_usuario,
								hor_fecha_e = '$fecha_m'
						WHERE	hor_id_horas = $id_horas
						AND		hor_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( mysqli_affected_rows( $conexion ) == 1 )
			{
				$exito['num']	= 1;
				$exito['msj']	= "Hora eliminada";
			}
			else
			{
				$exito['num']	= 3;
				$exito['msj']	= "No se puede quitar la hora seleccionada.";
			}
		}
		else
		{
			$exito['num']	= 2;
			$exito['msj']	= "Ocurrió un problema al tratar de quitar la hora seleccionada.";
		}
		
		return $exito;
	}
	
	function lista_horas( $lista = 'activos' )
	{
		global $conexion, $id_empresa;
		$datos		= '';
		$condicion	= '';
		$opcion		= '';
		$editar		= '';
		$class		= '';
		$colspan	= 5;
		$hora_mov	= date( 'H:i:s' );
		$fecha_mov	= date( 'd-m-Y' );
		
		if( $lista == 'activos' )
			$condicion = "AND hor_hora_final >= '$hora_mov'";
		else
			$condicion = "AND hor_hora_final <= '$hora_mov'";
		
		$query		= "	SELECT 		hor_id_horas AS id_horas,
									hor_status AS status,
									hor_nombre AS nombre,
									LOWER( DATE_FORMAT( hor_hora_inicial, '%h:%i:%s %p' ) ) AS h_inicio,
									LOWER( DATE_FORMAT( hor_hora_final, '%h:%i:%s %p' ) ) AS h_final,
									hor_horas AS tiempo
						FROM 		san_horas
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						AND			ser_tipo = 'PARCIAL'
						AND			ser_descripcion = 'HORA'
						WHERE		'$fecha_mov' = DATE_FORMAT( hor_fecha, '%d-%m-%Y' )
									$condicion
						AND			hor_id_empresa = $id_empresa
						ORDER BY	status,
									hor_hora_final DESC";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$editar	= "location.href='.?s=horas&i=editar&id_horas=$fila[id_horas]'";
				
				if( $fila['status'] == 'A' && $lista == 'activos' )
				{
					$opcion	= "<a href='.?s=horas&id_horas=$fila[id_horas]&eliminar=true'><span class='text-danger glyphicon glyphicon-remove-sign'></span></a>";
					$class	= "pointer";
				}
				else
				{
					$opcion	= '';
					$editar	= '';
					$class	= 'danger';
				}
				
				$datos	.= "<tr class='$class' onclick=\"$editar\">
								<td>$opcion</td>
								<td>$fila[nombre]</td>
								<td>$fila[tiempo]</td>
								<td>$fila[h_inicio]</td>
								<td>$fila[h_final]</td>
							</tr>";
			}
		}
		else
			$datos	.= "<tr><td colspan='$colspan'>No se puede obtener la consulta. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	.= "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
	function validar_registro_hora()
	{
		$validar	= array
		(
			'hor_horas'			=> array( 'tipo' => 'T',	'max' => 8,		'req' => 'S',	'for' => '',	'txt' => 'Tiempo'),
			'hor_genero'		=> array( 'tipo' => 'T',	'max' => 8,		'req' => 'S',	'for' => '',	'txt' => 'Sexo'),
			'hor_nombre'		=> array( 'tipo' => 'T',	'max' => 50,	'req' => 'S',	'for' => '',	'txt' => 'Nombre')
		);
		
		$exito		= validar_php( $validar );
		
		return $exito;
	}
	
?>