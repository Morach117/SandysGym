<?php
	function guardar_gasto()
	{
		global $conexion, $id_usuario;
		
		$exito			= array();
		$g_id_proveedor	= request_var( 'g_proveedor', 0 );
		$g_id_empresa	= request_var( 'g_sucursal', 0 );
		$g_fecha		= request_var( 'g_fecha', '' );
		$g_total		= request_var( 'g_total', 0.0 );
		
		$datos		= array
		(
			'gas_id_usuario'	=> $id_usuario,
			'gas_id_empresa'	=> $g_id_empresa,
			'gas_id_proveedor'	=> $g_id_proveedor,
			'gas_fnota'			=> request_var( 'g_fnota', '' ),
			'gas_fecha_fnota'	=> fecha_formato_mysql( $g_fecha ),
			'gas_importe'		=> request_var( 'g_importe', 0.0 ),
			'gas_iva'			=> request_var( 'g_iva', 0.0 ),
			'gas_descuento'		=> request_var( 'g_descuento', 0.0 ),
			'gas_total'			=> $g_total,
			'gas_observaciones'	=> request_var( 'g_observaciones', '' )
		);
		
		$query		= construir_insert( 'san_gastos', $datos );
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $g_total )
		{
			if( $g_id_empresa && $g_id_proveedor )
			{
				if( validar_fecha( $g_fecha ) )
				{
					if( $resultado )
					{
						if( mysqli_affected_rows( $conexion ) )
						{
							$exito['num'] = 1;
							$exito['msj'] = "Información guardada con exito.";
						}
						else
						{
							$exito['num'] = 5;
							$exito['msj'] = "No se ha guardado nada.";
						}
					}
					else
					{
						$exito['num'] = 5;
						$exito['msj'] = "Ocurrió un problema la tratar de guardar la captura de Gastos. ".mysqli_error( $conexion );
					}
				}
				else
				{
					$exito['num'] = 4;
					$exito['msj'] = "No se seleccionó una Fecha válida para la Factura o Nota.";
				}
			}
			else
			{
				$exito['num'] = 3;
				$exito['msj'] = "No se seleccionó una Sucursal o Proveedor.";
			}
		}
		else
		{
			$exito['num'] = 2;
			$exito['msj'] = "No se escribio el Total del Gasto.";
		}
		
		return $exito;
	}
	
?>