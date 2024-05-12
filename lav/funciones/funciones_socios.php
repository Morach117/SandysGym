<?php
	function lista_socios( $par_buscar = "", $letra = "", $par_regresar = "" )
	{
		Global $conexion, $id_empresa, $gbl_paginado;
		$datos		= "";
		$condicion	= "";
		$colspan	= 6;
		$pagina		= ( request_var( 'pag', 1 ) - 1 ) * $gbl_paginado;
		$var_total	= 0;
		$var_exito	= array();
		
		if( $par_buscar )
		{
			$condicion	.= "AND	(
									LOWER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) LIKE LOWER( '%$par_buscar%' )
								)";
		}
		
		if( $letra )
			$condicion	.= "AND UPPER( SUBSTR( soc_apepat, 1, 1 ) ) = '$letra'";
		
		$query		= "	SELECT 		COUNT(*) AS total
						FROM 		san_socios
						WHERE		soc_id_empresa = $id_empresa
									$condicion
						GROUP BY	soc_id_socio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
			$var_total = mysqli_num_rows( $resultado );
		
		mysqli_free_result( $resultado );
		
		$query		= "	SELECT 		soc_id_socio AS id_socio,
									soc_nombres AS nombres,
									CONCAT( soc_apepat, ' ', soc_apemat ) AS apellidos,
									soc_correo AS correo,
									soc_tel_cel AS telcel
						FROM 		san_socios
						WHERE		soc_id_empresa = $id_empresa
									$condicion
						ORDER BY	apellidos,
									nombres
						LIMIT		$pagina, $gbl_paginado";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr onclick=\"location.href='.?s=socios&i=datosg&soc_id_socio=$fila[id_socio]$par_regresar'\">
								<td>".( $pagina + $i )."</td>
								<td>$fila[id_socio]</td>
								<td>$fila[apellidos]</td>
								<td>$fila[nombres]</td>
								<td>$fila[correo]</td>
								<td>$fila[telcel]</td>
							</tr>";
				$i++;
			}
		}
		else
		{
			$error = mysqli_error( $conexion );
			$datos	= "	<tr>
							<td colspan='$colspan'>Ocurrió un problema al obtener los datos. $error</td>
						</tr>";
		}
		
		if( !$datos )
		{
			$datos	= "	<tr>
							<td colspan='$colspan'>No hay datos.</td>
						</tr>";
		}
		
		$var_exito['num'] = $var_total;
		$var_exito['msj'] = $datos;
		
		return $var_exito;;
	}
	
	function obtener_nombre_socio()
	{
		Global $conexion, $id_socio, $id_empresa;
		
		$query		= "SELECT CONCAT( soc_nombres, ' ', soc_apepat, ' ', soc_apemat ) AS nombre_socio FROM san_socios WHERE soc_id_socio = $id_socio AND soc_id_empresa = $id_empresa";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
				return $fila['nombre_socio'];
		}
		elseif( $error = mysqli_error() )
			echo $error;
		
		return false;
	}
	
	function validar_registro_socios()
	{
		$validar	= array
		(
			'soc_nombres'			=> array( 'tipo' => 'T',	'max' => 50,	'req' => 'S', 'for' => '',	'txt' => 'Nombre'),
			'soc_apepat'			=> array( 'tipo' => 'T',	'max' => 50,	'req' => 'N', 'for' => '',	'txt' => 'Apellido paterno'),
			'soc_apemat'			=> array( 'tipo' => 'T',	'max' => 50,	'req' => 'N', 'for' => '',	'txt' => 'Apellido materno'),
			'soc_direccion'			=> array( 'tipo' => 'T',	'max' => 100,	'req' => 'N', 'for' => '',	'txt' => 'Dirección'),
			'soc_colonia'			=> array( 'tipo' => 'T',	'max' => 100,	'req' => 'N', 'for' => '',	'txt' => 'Colonia'),
			'soc_tel_cel'			=> array( 'tipo' => 'T',	'max' => 15,	'req' => 'N', 'for' => '',	'txt' => 'Teléfono celular'),
			'soc_correo'			=> array( 'tipo' => 'C',	'max' => 50,	'req' => 'N', 'for' => '',	'txt' => 'Correo electronico'),
			'soc_observaciones'		=> array( 'tipo' => 'T',	'max' => 200,	'req' => 'N', 'for' => '',	'txt' => 'Observaciones')
		);
		
		$exito		= validar_php( $validar );
		
		return $exito;
	}
	
	function subir_fotografia()
	{
		global $id_socio;
		$dir_ponencias		= "../imagenes/avatar/";
		$extenciones		= "/^\.(jpg){1}$/i";
		$tamaño_maximo		= 2 * 1024 * 1024;
		$exito				= array( 'num' => 4, 'msj' => 'No se selecciono ninguna fotografía.' );
		
		if( isset( $_FILES['avatar'] ) && $_FILES['avatar']['name'] )
		{
			$extencion_archivo	= tipo_archivo( $_FILES['avatar']['type'] );
			$nombre_archivo		= $id_socio.$extencion_archivo;
			$valido				= is_uploaded_file($_FILES['avatar']['tmp_name']); 
			
			if( $valido )
			{
				$safe_filename = preg_replace( array( "/\s+/", "/[^-\.\w]+/" ), array( "_", "" ), trim( $_FILES['avatar']['name'] ) );
				
				if( $extencion_archivo && $_FILES['avatar']['size'] <= $tamaño_maximo && preg_match( $extenciones, strrchr( $safe_filename, '.' ) ) )
				{
					if( move_uploaded_file ( $_FILES['avatar']['tmp_name'], $dir_ponencias.$nombre_archivo ) )
					{
						$exito['num'] = 1;
						$exito['msj'] = 'Fotografía guardada.';
					}
					else
					{
						$exito['num'] = 2;
						$exito['msj'] = 'La fotografía no se ha guardado.<br/>';
					}
				}
				else
				{
					$exito['num'] = 3;
					$exito['msj'] = 'La fotografía no es del tipo solicitado o excede el tamaño permitido.<br/>';
				}
			}
		}
		
		return $exito;
	}
	
	function eliminar_fotografia()
	{
		global $id_socio;
		
		if( file_exists( "../imagenes/avatar/$id_socio.jpg" ) )
			if( unlink( "../imagenes/avatar/$id_socio.jpg" ) )
				return true;
		
		return false;
	}
	
?>