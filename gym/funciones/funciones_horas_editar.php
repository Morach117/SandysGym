<?php
	function actualizar_horario()
	{
		global $conexion, $id_empresa;
		
		$cuota				= obtener_servicio( 'HORA' );
		
		$id_horas			= request_var( 'id_horas', 0 );
		$hor_horas			= request_var( 'hor_horas', '' );
		$h_inicial			= request_var( 'h_inicial', '' );
		$francion			= 0;
		
		$h_inicial			= explode( ':', $h_inicial );
		$horas_minutos		= explode( ':', $hor_horas );
		
		$horas_suma			= (int)$horas_minutos[0];
		$minutos_suma		= (int)$horas_minutos[1];
		
		if( $minutos_suma )
			$francion = 0.5;
		
		$horas				= $h_inicial[0] + $horas_suma;
		$minutos			= $h_inicial[1] + $minutos_suma;
		$segundos			= $h_inicial[2];
		
		$hor_hora_final		= mktime( $horas, $minutos, $segundos, 0, 0, 0 );
		$hor_hora_final		= date( 'H:i:s', $hor_hora_final );
		
		$datos_sql			= array
		(
			'hor_nombre'		=> request_var( 'hor_nombre', '' ),
			'hor_hora_final'	=> $hor_hora_final,
			'hor_horas'			=> $hor_horas,
			'hor_importe'		=> ( $cuota['cuota'] * ( $horas_suma + $francion ) ),
			'hor_turno'			=> '',
			'hor_genero'		=> request_var( 'hor_genero', '' )
		);
		
		$query		= construir_update( 'san_horas', $datos_sql, "hor_id_horas = $id_horas AND hor_id_empresa = $id_empresa" );
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			header( "Location: .?s=horas" );
			exit;
		}
		else
			echo "Error: ".mysqli_error( $conexion );
		
		return false;
	}
?>