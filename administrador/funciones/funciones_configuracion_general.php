<?php
	function actualizar_conf_general()
	{
		global $conexion, $id_consorcio;
		
		$exito			= array();
		$g_iva			= request_var('g_iva', 0.0);
		$g_comision_t	= request_var('g_comision_t', 0.0);
		$g_venta		= request_var('g_venta', 0.0);
		$g_visita		= request_var('g_visita', 0.0);
		$g_abono		= request_var('g_abono', 0.0);
		$g_mensualidad	= request_var('g_mensualidad', 0.0);
		
		$query	= "	UPDATE	san_consorcios
					SET		con_iva = $g_iva,
							con_comision_tarjeta = $g_comision_t,
							con_venta = $g_venta,
							con_visita = $g_visita,
							con_abono = $g_abono,
							con_mensualidad = $g_mensualidad
					WHERE	con_id_consorcio = $id_consorcio";
		
		if ($resultado = mysqli_query($conexion, $query))
		{
			$exito['num'] = 1;
			$exito['msj'] = "Se actualizó la información. Se vuelven a cargar los datos.";
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se puede actualizar la información: " . mysqli_error($conexion);
		}
		
		return $exito;
	}
	
	function conf_general()
	{
		global $conexion, $id_consorcio;
		
		$datos = "";
		
		$query = "	SELECT	con_iva,
							con_comision_tarjeta,
							con_venta,
							con_visita,
							con_abono,
							con_mensualidad
					FROM	san_consorcios
					WHERE	con_id_consorcio = $id_consorcio";
		
		if ($resultado = mysqli_query($conexion, $query))
		{
			if ($fila = mysqli_fetch_assoc($resultado))
			{
				$datos .= "<div class='row'>
								<div class='col-md-2'>IVA</div>
								<div class='col-md-4'><input type='text' name='g_iva' class='form-control' value='{$fila['con_iva']}' /></div>
							</div>
							
							<div class='row'>
								<div class='col-md-2'>Comisión tarjeta</div>
								<div class='col-md-4'><input type='text' name='g_comision_t' class='form-control' value='{$fila['con_comision_tarjeta']}' /></div>
							</div>
														
							<div class='row'>
								<div class='col-md-12'>
									<h4 class='text-info'>
										<span class='glyphicon glyphicon-tower'></span> Monedero
									</h4>
								</div>
							</div>
							
							<div class='row'>
								<div class='col-md-2'>Venta</div>
								<div class='col-md-4'><input type='text' name='g_venta' class='form-control' value='{$fila['con_venta']}' /></div>
							</div>
							
							<div class='row'>
								<div class='col-md-2'>Visita</div>
								<div class='col-md-4'><input type='text' name='g_visita' class='form-control' value='{$fila['con_visita']}' /></div>
							</div>
							
							<div class='row'>
								<div class='col-md-2'>Monedero</div>
								<div class='col-md-4'><input type='text' name='g_abono' class='form-control' value='{$fila['con_abono']}' /></div>
							</div>
							
							<div class='row'>
								<div class='col-md-2'>Mensualidad</div>
								<div class='col-md-4'><input type='text' name='g_mensualidad' class='form-control' value='{$fila['con_mensualidad']}' /></div>
							</div>";
			}
			else
			{
				$datos .= "<div class='row'>
								<div class='col-md-12'>No hay datos.</div>
							</div>";
			}
		}
		else
		{
			$datos .= "<div class='row'>
							<div class='col-md-12'>Error: " . mysqli_error($conexion) . "</div>
						</div>";
		}
		
		return $datos;
	}
?>
