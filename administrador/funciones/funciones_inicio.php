<?php
	function obtener_empresas_activas()
	{
		global $conexion, $id_empresa, $id_consorcio;
		
		$datos		= "";
		$default	= "";
		$class		= "";
		$colspan	= 5;
		
		$query		= "	SELECT		emp_id_empresa AS id_empresa,
									emp_descripcion AS descripcion,
									emp_abreviatura AS abr,
									emp_status AS status,
									CASE emp_status
										WHEN 'A' THEN 'Activo'
										WHEN 'I' THEN 'Inactivo'
									END AS status_desc
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						WHERE		coem_id_consorcio = $id_consorcio
						AND			con_status = 'A'
						AND			emp_status IN ( 'I', 'A' )";
		
		$resultdado	= mysqli_query( $conexion, $query );
		
		if( $resultdado )
		{
			while( $fila = mysqli_fetch_assoc( $resultdado ) )
			{
				if( $id_empresa == $fila['id_empresa'] )
				{
					$default	= "checked";
					$class		= "info";
				}
				else
				{
					$default	= "";
					$class		= "";
				}
				
				$datos	.= "<tr class='$class'>
								<td><input type='radio' name='cmb_id_empresa' value='$fila[id_empresa]' $default /></td>
								<td>$fila[id_empresa]</td>
								<td>$fila[abr]</td>
								<td>$fila[descripcion]</td>
								<td>$fila[status_desc]</td>
							</tr>";
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al obtener información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
	function realizar_cambio()
	{
		global $conexion;
		
		$exito		= array();
		$emp_id_emp	= request_var( 'cmb_id_empresa', '' );
		$destino	= request_var( 'destino', 'Auditor' );
		
		$query		= "	SELECT		emp_id_empresa AS id_empresa,
									emp_id_giro AS id_giro,
									emp_descripcion AS descripcion,
									emp_abreviatura AS abr
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
						WHERE		emp_id_empresa = $emp_id_emp
						AND			con_status = 'A'
						AND			emp_status IN ( 'I', 'A' )";
		
		$resultdado	= mysqli_query( $conexion, $query );
		
		if( $resultdado )
		{
			if( $fila = mysqli_fetch_assoc( $resultdado ) )
			{
				$_SESSION['sans_id_empresa']	= $fila['id_empresa'];
				$_SESSION['sans_id_giro']		= $fila['id_giro'];
				$_SESSION['sans_empresa_desc']	= $fila['descripcion'];
				$_SESSION['sans_empresa_abr']	= $fila['abr'];
				
				cambiar( $_SESSION['sans_id_giro'], $destino );
			}
			else
			{
				$exito['msj']	= 3;
				$exito['num']	= "No hay datos.";
			}
		}
		else
		{
			$exito['msj']	= 2;
			$exito['num']	= "Ocurrió un problema al obtener información. ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	/*solo se redirecciona cuando hay una sesion valida, es igual a redireccionar que esta en sessiones de acceso*/
	function cambiar( $tipo_empresa, $destino )
	{
		if( $destino == 'Auditor' )
		{
			$empresas[1]	= "administrador/gym";
			$empresas[2]	= "administrador/lav";
			$empresas[3]	= "administrador/tpv";
		}
		elseif( $destino == 'Operativo' )
		{
			$empresas[1]	= "gym";
			$empresas[2]	= "lav";
			$empresas[3]	= "tpv";
		}
		
		header( "Location: ../$empresas[$tipo_empresa]" );
		exit;
	}
	
?>