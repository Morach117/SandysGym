<?php
	function agregar_usuario()
	{
		global $conexion;
		// falta verificar que esta sucursal pertenezca al consorcio
		$u_apis			= isset( $_POST['api'] ) ? $_POST['api'] : false;
		$u_sucursal		= request_var( 'u_sucursal', 0 );
		$u_empresa_sec	= request_var( 'u_empresa_sec', 0 );
		$u_correo		= request_var( 'u_correo', '' );
		$u_pass			= request_var( 'u_pass', '' );
		
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
							'usua_rol'			=> request_var( 'u_rol', 'O' ),
							'usua_correo'		=> $u_correo,
							'usua_pass_md5'		=> md5( $u_pass ),
							'usua_pass_web'		=> $u_pass,
							'usua_aplicaciones'	=> $aplicaciones
						);
						
						if( !existe_correo_usuario( $u_correo ) && $u_correo )
						{
							$query		= construir_insert( 'san_usuarios', $datos_sql );
							
							$resultado	= mysqli_query( $conexion, $query );
							
							if( $resultado )
							{
								$exito['num'] = 1;
								$exito['msj'] = "Usuario registrado correctamente.";
							}
							else
							{
								$exito['num'] = 6;
								$exito['msj'] = "Ocurrió un error al tratar de registrar el nuevo Usuario. ".mysqli_error( $conexion );
							}
						}
						else
						{
							$exito['num'] = 5;
							$exito['msj'] = "El correo ingresado ya ha sido registrado para otro Usuario, se debe de cambiar para poder continuar con el registro.";
						}
					}
					else
					{
						$exito['num'] = 4;
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
	
?>