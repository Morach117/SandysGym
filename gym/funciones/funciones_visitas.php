<?php
	function obtener_servicio( $desc = 'VISITA' )
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
	//ser_clave en luar de ser_descripcion
	function lista_horas_visitas()
	{
		global $conexion, $id_empresa;
		$datos		= "";
		$class		= "";
		$opcion		= "";
		$colspan	= 3;
		$fecha_mov	= date( 'd-m-Y' );
		
		$query		= "	SELECT		hor_id_horas AS id_horas,
									hor_nombre AS nombre,
									hor_status AS status,
									LOWER( DATE_FORMAT( hor_fecha, '%r' ) ) AS h_inicio
						FROM 		san_horas
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						AND			ser_tipo = 'PARCIAL'
						AND			ser_descripcion = 'VISITA'
						WHERE		'$fecha_mov' = DATE_FORMAT( hor_fecha, '%d-%m-%Y' )
						AND			hor_id_empresa = $id_empresa
						ORDER BY	status,
									h_inicio DESC";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				if( $fila['status'] == 'A' )
				{
					$class	= "";
					$opcion	= "<a href='.?s=visitas&id_horas=$fila[id_horas]&eliminar=true'><span class='text-danger glyphicon glyphicon-remove-sign'></span></a>";
				}
				else
				{
					$class	= "danger";
					$opcion	= "";
				}
				
				$datos	.= "<tr class='$class'>
								<td>$opcion</td>
								<td>$fila[nombre]</td>
								<td>$fila[h_inicio]</td>
							</tr>";
			}
		}
		else
			$datos	.= "<tr><td colspan='$colspan'>No se puede obtener la consulta. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	.= "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
	function validar_registro_dia()
	{
		$validar	= array
		(
			'hor_nombre'		=> array( 'tipo' => 'T',	'max' => 50,	'req' => 'S',	'for' => '',	'txt' => 'Nombre')
		);
		
		$exito		= validar_php( $validar );
		
		return $exito;
	}
	
?>