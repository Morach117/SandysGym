<?php
	function obtener_conexion()
	{
		// $conexion	= mysqli_connect( 'db586975701.db.1and1.com', 'dbo586975701', 'sergym2015', 'db586975701' );
		$conexion	= mysqli_connect( 'localhost', 'root', '', 'dbs1756575' );
		//$conexion	= mysqli_connect( 'db5002171142.hosting-data.io', 'dbu577361', 'Sandys_empresas_2', 'dbs1756575' );
		
		if( !mysqli_connect_errno() )
		{
			mysqli_set_charset( $conexion, 'utf8' );
			return $conexion;
		}
		else
			return false;
	}
?>