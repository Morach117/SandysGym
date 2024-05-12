<?php
	function cambiar_contraseña()
	{
		global $conexion, $id_usuario;
		
		$exito		= array();
		$pass_a		= request_var( 'pass_a', '' );
		$pass_n		= request_var( 'pass_n', '' );
		$pass_r		= request_var( 'pass_r', '' );
		
		if( $pass_a && $pass_n && $pass_r )
		{
			if( $pass_n == $pass_r )
			{
				if( checar_pass( $pass_a ) )
				{
					$query		= "	UPDATE	san_usuarios
									SET		usua_pass_md5 = md5( '$pass_n' ),
											usua_pass_web = '$pass_n'
									WHERE	usua_id_usuario = $id_usuario";
										
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) > 0 )
						{
							$exito['num'] = 1;
							$exito['msj'] = 'Cambio de contraseña confirmado.';
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = 'No se cambio ningun dato porque no se encontro información para actualizar';
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = 'No se pudo actualizar la contraseña. '.mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = 'La contraseña actual no coincide con el de la BD.';
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = 'Las nuevas contraseñas no coinciden.';
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = 'Escribe contraseña para continuar.';
		}
		
		return $exito;
	}
	
	function checar_pass( $pass )
	{
		global $conexion, $id_usuario;
		
		$query		= "	SELECT	usua_id_usuario
						FROM	san_usuarios
						WHERE	usua_id_usuario = $id_usuario
						AND		usua_pass_md5 = md5( '$pass' )";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return true;
		}
		else
			echo "Error: ".mysqli_error( $conexion );
		
		return false;
	}
	
	function cambiar_correo()
	{
		global $conexion, $id_usuario;
		
		$exito		= array();
		$correo_n	= request_var( 'correo_n', '' );
		$correo_r	= request_var( 'correo_r', '' );
		
		if( $correo_n && $correo_r && strpos( $correo_n, '@' ) )
		{
			if( $correo_n == $correo_r )
			{
				if( !existe_correo( $correo_n ) )
				{
					$query		= "	UPDATE	san_usuarios
									SET		usua_correo = LOWER( '$correo_n' )
									WHERE	usua_id_usuario = $id_usuario";
										
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) > 0 )
						{
							$exito['num'] = 1;
							$exito['msj'] = 'Cambio de correo confirmado.';
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = 'No se cambio ningun dato porque no se encontro información para actualizar.';
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = 'No se pudo actualizar el correo. '.mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = 'Ya existe un usuario que esta utilizando este correo.';
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = 'Los correos escritos no coinciden.';
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = 'No se escribieron correos o no son valido.';
		}
		
		return $exito;
	}
	
	function obtener_datos_usuario()
	{
		global $conexion, $id_usuario;
		
		$exito		= array();
		
		$query		= "SELECT		emp_descripcion AS empresa,
									usua_ape_pat AS ape_pat,
									usua_ape_mat AS ape_mat,
									usua_nombres AS nombres,
									usua_correo AS correo,
									CONCAT( substring( usua_pass_md5, 1, 10 ), '...' ) AS pass
						FROM		san_usuarios
						INNER JOIN	san_empresas ON emp_id_empresa = usua_id_empresa
						WHERE		usua_id_usuario = $id_usuario";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$exito['num'] = 1;
				$exito['msj'] = $fila;
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = 'Mmm algo salio mal, no se pudo obtener la información de tu cuenta.';
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = 'Ocurrio un problema. '.mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
?>