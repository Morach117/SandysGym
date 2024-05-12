<?php
	function eliminar_usuario( $usuarioe )
	{
		global $conexion;
		
		$exito		= array();
		$query		= "DELETE FROM san_usuarios WHERE CONCAT( usua_id_usuario, usua_id_empresa ) = $usuarioe";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$exito['num'] = 1;
			$exito['msj'] = "Usuario eliminado.";
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se eliminó al usuario. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function actualizar_datos()
	{
		global $conexion;
		
		$u_apis			= isset( $_POST['api'] ) ? $_POST['api'] : false;
		$u_sucursal		= request_var( 'u_empresa', 0 );
		$u_empresa_sec	= request_var( 'u_empresa_sec', 0 );
		$u_correo		= request_var( 'u_correo', '' );
		$u_pass			= request_var( 'u_pass', '' );
		$id_usuario		= request_var( 'id_usuario', 0 );
		
		$aplicaciones	= '';
		$exito			= array();
		
		if( $u_apis )
		{
			foreach( $u_apis as $api )
				$aplicaciones .= $api.',';
			
			$aplicaciones	= substr( $aplicaciones, 0, -1 );
		}
		
		if( $aplicaciones )
		{
			if( $u_sucursal )
			{
				if( $u_sucursal != $u_empresa_sec )
				{
					if( $u_correo && $u_pass && filter_var( $u_correo, FILTER_VALIDATE_EMAIL ) )
					{
						$datos_sql	= array
						(
							'usua_id_empresa'	=> $u_sucursal,
							'usua_id_empresa_sec'	=> $u_empresa_sec,
							'usua_ape_pat'		=> request_var( 'u_ape_pat', '' ),
							'usua_ape_mat'		=> request_var( 'u_ape_mat', '' ),
							'usua_nombres'		=> request_var( 'u_nombres', '' ),
							'usua_status'		=> request_var( 'u_status', '' ),
							'usua_rol'			=> request_var( 'u_rol', 'O' ),
							'usua_correo'		=> $u_correo,
							'usua_pass_md5'		=> md5( $u_pass ),
							'usua_pass_web'		=> $u_pass,
							'usua_aplicaciones'	=> $aplicaciones
						);
						
						if( !existe_correo_usuario( $u_correo, $id_usuario ) && $u_correo )
						{
							$query		= construir_update( 'san_usuarios', $datos_sql, "usua_id_usuario = $id_usuario" );
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								$exito['num'] = 1;
								$exito['msj'] = "Datos actualizados de manera correcta.";
							}
							else
							{
								$exito['num'] = 7;
								$exito['msj'] = "Ocurrió un error al tratar de actualizar el registro. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num'] = 6;
							$exito['msj'] = "El correo ingresado ya ha sido registrado para otro Usuario, se debe de cambiar para poder continuar con la actualización de los datos de Usuario.";
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "Se debe ingresar un correo y contraseña para la cuenta del Usuario, y debe ser un correo válido.";
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "No se debe seleccionar misma sucursal principal y secundario.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "Se debe seleccionar la Sucursal a la que el Usuario pertenece.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "Se deben seleccionar las Aplicaciones a las que el Usuario tendrá acceso, de lo contrario, no podrá realizar ningún movimiento en su cuenta.";
		}
		
		return $exito;
	}
	
	function obtener_datos_usuario( $usuarioe )
	{
		global $conexion;
		
		$query		= "	SELECT 		usua_id_usuario AS id_usuario,
									usua_id_empresa AS id_empresa,
									gir_id_giro AS id_giro,
									emp_descripcion AS empresa,
									usua_ape_pat AS ape_pat,
									usua_ape_mat AS ape_mat,
									usua_nombres AS nombres,
									usua_status AS status,
									usua_correo AS correo,
									usua_pass_web AS pass,
									usua_aplicaciones AS apis,
									usua_rol AS rol,
									usua_id_empresa_sec AS id_secundario
						FROM		san_usuarios
						INNER JOIN	san_empresas ON emp_id_empresa = usua_id_empresa
						INNER JOIN 	san_giros ON gir_id_giro = emp_id_giro
						WHERE		CONCAT( usua_id_usuario, usua_id_empresa ) = $usuarioe
						AND			usua_rol != 'S'";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila;
			
		return false;
	}
	
?>