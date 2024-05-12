<?php
	session_start();
	date_default_timezone_set('america/mexico_city');
	ob_start();
	$conexion		= obtener_conexion();
	$seccion		= request_var( 's', 'inicio' );
	$item			= request_var( 'i', 'index' );
	$enviar			= isset( $_POST['enviar'] ) ? true:false;
	$continuar		= isset( $_POST['continuar'] ) ? true:false;
	$administrador	= "";
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
			$id_secundario	= $_SESSION['sans_id_secundario'];
			$id_consorcio	= $_SESSION['sans_id_consorcio'];
			$apellidos		= $_SESSION['sans_apellidos'];
			$nombres		= $_SESSION['sans_nombres'];
			$apis			= $_SESSION['sans_aplicaciones'];
			$id_giro		= $_SESSION['sans_id_giro'];		//cambiar en cambio de empresa
			$empresa_desc	= $_SESSION['sans_empresa_desc'];	//cambiar en cambio de empresa
			$empresa_abr	= $_SESSION['sans_empresa_abr'];	//cambiar en cambio de empresa
			$rol			= $_SESSION['sans_rol'];
			
			//para LAV el giro permitido es 2
			if( $id_giro == 2 )
			{
				if( $rol == 'S' || $rol == 'R' )
					$administrador .= "<li><a href='../administrador'><span class='glyphicon glyphicon-star'></span> Ir al Administrador</a></li>";
				
				if( $rol != 'S' )
				{
					$query			= "	SELECT 		api_secciones AS seccion,
													api_item AS item,
													api_item_excepto AS item_exepto
										FROM 		san_aplicaciones 
										INNER JOIN	san_giros ON gir_id_giro = api_id_giro
										AND			gir_carpeta = 'LAV'
										WHERE 		api_id_giro = $id_giro
										AND 		api_id_api IN( $apis )";
					
					$resultado		= mysqli_query( $conexion, $query );
					
					if( $resultado )
					{
						while( $fila = mysqli_fetch_assoc( $resultado ) )
						{
							$array_item			= explode( ',', $fila['item'] );
							$array_item_exepto	= explode( ',', $fila['item_exepto'] );
							
							//al index del item siempre se tendra acceso, de ahi que se manda al "else"
							if( $item && $item != 'index' && ( $fila['item'] || $fila['item_exepto'] ) )
							{
								if( $fila['item'] )
								{
									if( $seccion == $fila['seccion'] && in_array( $item, $array_item ) && !in_array( $item, $array_item_exepto ) )
									{
										$permitir = true;
										break;
									}
								}
								elseif( $seccion == $fila['seccion'] && !in_array( $item, $array_item_exepto ) )
								{
									$permitir = true;
									break;
								}
							}
							else
							{
								if( $seccion == $fila['seccion'] )
								{
									$permitir = true;
									break;
								}
							}
						}
						
						if( !$permitir )
							acceso_denegado( $seccion, $item );
					}
					else
						redireccionar( 11 );
				}
			}
			else
				redireccionar( 13 );
		}
		else
			redireccionar( 9 );
	}
	else
		redireccionar( 8 );
	
	function redireccionar( $error )
	{
		session_destroy();
		
		header( "Location: ../acceso/.?error=$error&d=2" );
		exit;
	}
	
	//se pasan por referencia para que se reescriban
	function acceso_denegado( &$seccion, &$item )
	{
		$seccion	= 'inicio';
		$item		= 'denegado';
	}
?>