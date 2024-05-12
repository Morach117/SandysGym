<?php
	function opciones_dia_semana( $dia = 0 )
	{
		$opciones	= "<option value=''>Ninguno</option>";
		$semana		= array
		(
			1	=> 'Domingo',
			2	=> 'Lunes',
			3	=> 'Martes',
			4	=> 'Miercoles',
			5	=> 'Jueves',
			6	=> 'Viernes',
			7	=> 'Sábado'
		);
		
		foreach( $semana as $i_dia => $d_dia )
		{
			if( $i_dia == $dia )
				$opciones .= "<option selected value='$i_dia'>$d_dia</option>";
			else
				$opciones .= "<option value='$i_dia'>$d_dia</option>";
		}
		
		return $opciones;
	}
	
	function datos_servicio_lav( $p_id_servicio )
	{
		global $conexion, $id_consorcio;
		
		$query		= "	SELECT 		ser_id_servicio AS id_servicio,
									ser_descripcion AS descripcion,
									ser_orden AS orden,
									ser_status AS status,
									ser_tipo AS tipo,
									CASE ser_status 
										WHEN 'A' THEN 'Activo'
										WHEN 'D' THEN 'Descontinuado'
									END AS status_desc
						FROM		san_servicios 
						WHERE		ser_id_giro = 2 
						AND			ser_id_consorcio = $id_consorcio
						AND			ser_id_servicio = $p_id_servicio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
			
		return false;
	}
	
?>