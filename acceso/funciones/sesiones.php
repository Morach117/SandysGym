<?php
	session_start();
	date_default_timezone_set('america/mexico_city');
	$conexion	= obtener_conexion();
	$enviar		= isset( $_POST['enviar'] ) ? true:false;
	
	$san_correo	= request_var( 'san_correo', '' );
	$san_pass	= request_var( 'san_pass', '' );
	$error		= request_var( 'error', 1 );
	
	if( $conexion )
	{
		if( isset( $_SESSION['sans_id_usuario'] ) && isset( $_SESSION['sans_id_giro'] )  )
			redireccionar( $_SESSION['sans_id_giro'] );
		
		if( $enviar )
		{
			if( $san_correo && $san_pass )
			{
				if( strpos( $san_correo, '@' ) )
				{
					$query		= "	SELECT		b.usua_id_usuario AS validar,
												a.usua_id_usuario AS id_usuario,
												a.usua_id_empresa AS id_empresa,
												a.usua_id_empresa_sec AS id_secundario,
												coem_id_consorcio AS id_consorcio,
												CONCAT( a.usua_ape_pat, ' ', a.usua_ape_mat ) as apellidos,
												a.usua_nombres AS nombres,
												a.usua_aplicaciones AS apis,
												emp_id_giro AS id_giro,
												emp_descripcion AS emp_descripcion,
												emp_abreviatura AS abr_empresa,
												a.usua_rol AS rol
									FROM		san_usuarios a
									INNER JOIN	san_empresas ON emp_id_empresa = a.usua_id_empresa
									INNER JOIN	san_consorcio_empresa ON coem_id_empresa = a.usua_id_empresa
									LEFT JOIN	san_usuarios b ON b.usua_correo = a.usua_correo
									AND			b.usua_pass_md5 = MD5( '$san_pass' )
									WHERE		LOWER( a.usua_correo ) = LOWER( '$san_correo' )
									AND			a.usua_status = 'A'
									AND			emp_status = 'A' ";
					
					
					$resultado	= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						if( $fila = mysqli_fetch_assoc( $resultado ) )
						{
							if( $fila['validar'] )
							{
								$_SESSION['sans_id_usuario']	= $fila['id_usuario'];
								$_SESSION['sans_id_empresa']	= $fila['id_empresa'];
								$_SESSION['sans_id_secundario']	= $fila['id_secundario'];
								$_SESSION['sans_id_consorcio']	= $fila['id_consorcio'];
								$_SESSION['sans_apellidos']		= $fila['apellidos'];
								$_SESSION['sans_nombres']		= $fila['nombres'];
								$_SESSION['sans_aplicaciones']	= $fila['apis'];
								$_SESSION['sans_id_giro']		= $fila['id_giro'];
								$_SESSION['sans_empresa_desc']	= $fila['emp_descripcion'];
								$_SESSION['sans_empresa_abr']	= $fila['abr_empresa'];
								$_SESSION['sans_rol']			= $fila['rol'];
								
								redireccionar( $_SESSION['sans_id_giro'] );
							}
							else
								$error = 7;
						}
						else
							$error = 6;
					}
					else
						$error = 5;
				}
				else
					$error = 4;
			}
			else
				$error = 3;
		}
	}
	else
		$error = 2;
	
	/*solo se redirecciona cuando hay una sesion valida*/
	function redireccionar( $id_giro )
	{
		if( ( $_SESSION['sans_rol'] == 'S' || $_SESSION['sans_rol'] == 'R' ) && $_SESSION['sans_id_consorcio'] )
			$id_giro = 99;
		
		$empresas[1]	= "gym";
		$empresas[2]	= "lav";
		$empresas[3]	= "tpv";
		$empresas[99]	= "administrador";
		
		header( "Location: ../$empresas[$id_giro]" );
		exit;
	}
	
?>