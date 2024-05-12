<?php
	function obtener_detalle_empresa()
	{
		global $conexion, $id_consorcio;
		
		$editar_id_sucursal	= request_var( 'editar_id_sucursal', 0 );
		
		mysqli_autocommit( $conexion, false );
		
		$query		= "	SELECT		emp_id_empresa AS id_sucursal,
									gir_id_giro AS id_giro,
									emp_descripcion AS sucursal,
									gir_descripcion AS giro,
									emp_abreviatura AS abr,
									emp_direccion AS direccion,
									emp_colonia AS colonia,
									emp_ciudad AS ciudad,
									emp_telefono AS telefono,
									emp_correo AS correo,
									emp_status AS status,
									emp_not_corte AS not_correo,
									CASE emp_status
										WHEN 'A' THEN 'Activo'
										WHEN 'I' THEN 'Inactivo'
										WHEN 'B' THEN 'Baja'
									END AS status_desc
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						INNER JOIN	san_giros ON gir_id_giro = emp_id_giro
						WHERE		emp_id_empresa = $editar_id_sucursal
						AND			con_id_consorcio = $id_consorcio
						AND			con_status IN ( 'A', 'I' )
						ORDER BY	id_sucursal";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
		}
		
		return false;
	}
	
	function actualizar_datos()
	{
		global $conexion;
		
		$exito				= array();
		$editar_id_sucursal	= request_var( 'editar_id_sucursal', 0 );
		$editar_id_giro		= request_var( 'editar_id_giro', 0 );
		
		mysqli_autocommit( $conexion, false );
		
		$validar			= obtener_detalle_empresa();
		
		if( $validar )
		{
			$datos_sql	= array
			(
				'emp_descripcion'	=> request_var( 'e_descripcion', '' ),
				'emp_abreviatura'	=> request_var( 'e_abr', '' ),
				'emp_direccion'		=> request_var( 'e_direccion', '' ),
				'emp_colonia'		=> request_var( 'e_colonia', '' ),
				'emp_ciudad'		=> request_var( 'e_ciudad', '' ),
				'emp_telefono'		=> request_var( 'e_telefono', '' ),
				'emp_not_corte'		=> request_var( 'e_not_corte', '' ),
				'emp_correo'		=> request_var( 'e_correo', '' )
			);
			
			$query		= construir_update( 'san_empresas', $datos_sql, "emp_id_empresa = $editar_id_sucursal AND emp_id_giro = $editar_id_giro" );
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				if( mysqli_affected_rows( $conexion ) == 1 )
				{
					$exito['num'] = 1;
					$exito['msj'] = "Datos actualizados de manera correcta.";
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "No se guardo ningún cambio.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Ocurrió un problema al actualizar la información. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se validó la información capturada para la Sucursal seleccionada.";
		}
		
		if( $exito['num'] == 1 )
			mysqli_commit( $conexion );
		else
			mysqli_rollback( $conexion );
		
		return $exito;
	}
	
?>