<?php
	session_start();
	date_default_timezone_set('america/mexico_city');
	$conexion		= obtener_conexion();
	$seccion		= request_var( 's', 'reportes' );
	$item			= request_var( 'i', 'index' );
	$descargar		= request_var( 'd', '' );
	$enviar			= isset( $_POST['enviar'] ) ? true:false;
	$continuar		= isset( $_POST['continuar'] ) ? true:false;
	$gbl_paginado	= 20;
	$permitir		= false;
	
	if( $seccion == 'salir' )
		redireccionar( 10 );
	
	if( $conexion )
	{
		if( isset( $_SESSION['sans_id_usuario'] ) )
		{
			$id_usuario		= $_SESSION['sans_id_usuario'];
			$id_empresa		= $_SESSION['sans_id_empresa'];		//cambiar en cambio de empresa
			$id_consorcio	= $_SESSION['sans_id_consorcio'];	
			$apellidos		= $_SESSION['sans_apellidos'];
			$nombres		= $_SESSION['sans_nombres'];
			$apis			= $_SESSION['sans_aplicaciones'];
			$id_giro		= $_SESSION['sans_id_giro'];		//cambiar en cambio de empresa
			$empresa_desc	= $_SESSION['sans_empresa_desc'];	//cambiar en cambio de empresa
			$empresa_abr	= $_SESSION['sans_empresa_abr'];	//cambiar en cambio de empresa
			$rol			= $_SESSION['sans_rol'];
			
			if( $rol == 'S' || $rol == 'R' )
			{
				//para LAV el giro permitido es 2
				if( $id_giro != 2 )
					redireccionar( 13 );
			}
			else
				redireccionar( 12 );
		}
		else
			redireccionar( 9 );
	}
	else
		redireccionar( 8 );
	
	function redireccionar( $error )
	{
		session_destroy();
		
		header( "Location: ../../acceso/.?error=$error&d=2" );
		exit;
	}
	
	if( $seccion == 'reportes' && $rol == 'R' )
		if( $item == 'corte_diario' || $item == 'corte_mensual' || $item == 'corte_anual' )
			acceso_denegado( $seccion, $item );
	
	//se pasan por referencia para que se reescriban
	function acceso_denegado( &$seccion, &$item )
	{
		$seccion	= 'inicio';
		$item		= 'denegado';
	}
?>