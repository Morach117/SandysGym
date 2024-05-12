<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$mar_desc	= request_var( 'mar_descripcion', '' );
	$exito		= array();
	
	if( $enviar )
	{
		if( $mar_desc )
		{
			$query		= "INSERT INTO san_marcas( mar_id_consorcio, mar_descripcion ) VALUES( $id_consorcio, '$mar_desc' )";
			$resultado	= mysqli_query( $conexion, $query );
			
			if( $resultado )
			{
				$exito['num'] = 1;
				$exito['msj'] = "Marca guardado.";
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
			$exito['msj'] = "No se escribio una Marca.";
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