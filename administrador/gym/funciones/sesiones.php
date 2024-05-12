<?php
	session_start();
	date_default_timezone_set('america/mexico_city');
	$conexion		= obtener_conexion();
	$seccion		= request_var( 's', 'inicio' );
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
				//para GYM el giro permitido es 1
				if( $id_giro != 1 )
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
		if( $item == 'diario' || $item == 'mensual' || $item == 'anual' || $item == 'mensualidades_pagadas' )
			acceso_denegado( $seccion, $item );
	
	//se pasan por referencia para que se reescriban
	function acceso_denegado( &$seccion, &$item )
	{
		$seccion	= 'inicio';
		$item		= 'denegado';
	}
?>