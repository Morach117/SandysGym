<?php
	function actualizar_conf_general()
	{
		global $conexion, $id_consorcio;
		
		$exito			= array();
		$g_iva			= request_var( 'g_iva', 0.0 );
		$g_comision_t	= request_var( 'g_comision_t', 0.0 );
		
		$query		= "	UPDATE	san_consorcios
						SET		con_iva = $g_iva,
								con_comision_tarjeta = $g_comision_t
						WHERE	con_id_consorcio = $id_consorcio";
		
		if( $resultado	= mysqli_query( $conexion, $query ) )
		{
			$exito['num'] = 1;
			$exito['msj'] = "Se actualizó la información. Se vuelven a cargar los datos.";
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se puede actualizar la información: ".mysqli_error( $conexion );
		}
		
		return $exito;
	}
	
	function conf_general()
	{
		global $conexion, $id_consorcio;
		
		$datos		= "";
		
		$query		= "	SELECT	con_iva,
								con_comision_tarjeta
						FROM	san_consorcios
						WHERE	con_id_consorcio = $id_consorcio";
		
		if( $resultado	= mysqli_query( $conexion, $query ) )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<div class='row'>
								<div class='col-md-2'>IVA</div>
								<div class='col-md-4'><input type='text' name='g_iva' class='form-control' value='$fila[con_iva]' /></div>
							</div>
							
							<div class='row'>
								<div class='col-md-2'>Comisión tarjeta</div>
								<div class='col-md-4'><input type='text' name='g_comision_t' class='form-control' value='$fila[con_comision_tarjeta]' /></div>
							</div>
							
							";
			}
			else
			{
				$datos	.= "<div class='row'>
								<div class='col-md-12'>No hay datos.</div>
							</div>";
			}
		}
		else
		{
			$datos	.= "<div class='row'>
							<div class='col-md-12'>Error: ".mysqli_error( $conexion )."</div>
						</div>";
		}
		
		return $datos;
	}
	
	
?>