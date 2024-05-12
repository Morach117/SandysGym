<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$cat_desc	= request_var( 'cat_descripcion', '' );
	$exito		= array();
	
	if( $enviar )
	{
		if( $cat_desc )
		{
			$query		= "INSERT INTO san_categorias( cat_id_consorcio, cat_descripcion ) VALUES( $id_consorcio, '$cat_desc' )";
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Categoría guardado.";
			}
			else
			{
				$exito['num'] = 4;
				$exito['msj'] = "Error al guardar. ".mysqli_error( $conexion );
			}
		}
		else
		{
			$exito['num'] = 3;
			$exito['msj'] = "No se escribio una Categoría.";
		}
	}
	else
	{
		$exito['num'] = 2;
		$exito['msj'] = "Acción no permitido.";
	}
	
	echo json_encode( $exito );
	
	mysqli_close( $conexion );
?>