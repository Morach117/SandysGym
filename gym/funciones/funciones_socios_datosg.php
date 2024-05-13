<?php
	function actualizar_socio()
	{
		Global $conexion, $id_empresa;
		
		$mensaje		= array();
		$soc_id_socio	= request_var( 'id_socio', 0 );
		
		$datos_sql		= array
		(
			'soc_nombres'			=> request_var( 'soc_nombres', '' ),
			'soc_apepat'			=> request_var( 'soc_apepat', '' ),
			'soc_apemat'			=> request_var( 'soc_apemat', '' ),
			'soc_genero'			=> request_var( 'soc_genero', '' ),
			'soc_turno'				=> request_var( 'soc_turno', '' ),
			'soc_direccion'			=> request_var( 'soc_direccion', '' ),
			'soc_colonia'			=> request_var( 'soc_colonia', '' ),
			'soc_tel_fijo'			=> request_var( 'soc_tel_fijo', '' ),
			'soc_tel_cel'			=> request_var( 'soc_tel_cel', '' ),
			'soc_correo'			=> request_var( 'soc_correo', '' ),
			'soc_emer_nombres'		=> request_var( 'soc_emer_nombres', '' ),
			'soc_emer_parentesco'	=> request_var( 'soc_emer_parentesco', '' ),
			'soc_emer_direccion'	=> request_var( 'soc_emer_direccion', '' ),
			'soc_emer_tel'			=> request_var( 'soc_emer_tel', '' ),
			'soc_observaciones'		=> request_var( 'soc_observaciones', '' ),
			'soc_descuento'			=> request_var( 'soc_descuento', '' ),
			'soc_fecha_nacimiento'	=> request_var( 'soc_fecha_nacimiento', '' )
		);
		
		$query		= construir_update( 'san_socios', $datos_sql, "soc_id_socio = $soc_id_socio AND soc_id_empresa = $id_empresa" );
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$mensaje['num']		= 1;
			$mensaje['msj']		= "Registro actualizado correctamente.";
		}
		else
		{
			$mensaje['num']		= 2;
			$mensaje['msj']	= "No se ha podido actualizar la información de este socio.";
		}
		
		return $mensaje;
	}
	
?>
