<div class="row">
	<div class="col-md-12">
		<h4 class="text-primary"><span class="glyphicon glyphicon-refresh"></span> Cambio de sucursal</h4>
	</div>
</div>

<hr/>

<?php
	if( $id_secundario )
	{
		$query		= "	SELECT		emp_id_giro AS id_giro,
									emp_descripcion AS descripcion,
									emp_abreviatura AS abr
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						WHERE		emp_id_empresa = $id_secundario
						AND			coem_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$_SESSION['sans_id_empresa']	= $id_secundario;		//cambiar en cambio de empresa
				$_SESSION['sans_id_secundario']	= $id_empresa;			//cambiar en cambio de empresa
				$_SESSION['sans_id_giro']		= $fila['id_giro'];		//cambiar en cambio de empresa
				$_SESSION['sans_empresa_desc']	= $fila['descripcion'];	//cambiar en cambio de empresa
				$_SESSION['sans_empresa_abr']	= $fila['abr'];			//cambiar en cambio de empresa
				
				$empresas[1]	= "gym";
				$empresas[2]	= "lav";
				
				$id_giro		= $fila['id_giro'];
				
				header( "Location: ../$empresas[$id_giro]" );
				exit;
			}
		}
	}
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">No se puede hacer el cambio de sucursal</h4>
	</div>
</div>