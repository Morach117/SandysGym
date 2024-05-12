<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar			= isset( $_POST['envio'] ) ? true:false;
	$id_servicio	= request_var( 'id_servicio', 0 );
	$js_tipo_venta	= request_var( 'tipo_venta', 'X' );
	$codigo			= 0;
	$descripcion	= '';
	$precio			= '';
	$tabla			= '';
	$condicion		= "";
	
	if( $enviar )
	{
		if( $js_tipo_venta == 'PEDREDONES' && $rol == 'S' )
			$condicion = " OR 1 = 1 ";
		
		$query		= "	SELECT	id_servicio,
								descripcion,
								minimo,
								ROUND( precio, 2 ) As precio,
								ROUND( IF( minimo > 1, minimo * precio, precio ), 2 ) AS cuota
						FROM
						(
							SELECT		ser_id_servicio AS id_servicio,
										ser_descripcion AS descripcion,
										IF( src_kg_minimo > 0, src_kg_minimo, 1 ) AS minimo,
										IF( DAYOFWEEK( CURDATE() ) = src_promo_dia $condicion, src_promo_cuota, src_cuota  ) AS precio
							FROM		san_servicios
							INNER JOIN	san_servicios_cuotas ON src_id_servicio = ser_id_servicio
							AND			src_id_empresa = $id_empresa
							WHERE		ser_id_servicio = $id_servicio
							AND			ser_id_giro = 2
							AND			ser_id_consorcio = $id_consorcio
							AND			ser_status = 'A'
						) a";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$descripcion	= $fila['descripcion'];
				$precio_float	= $fila['precio'];
				
				$tabla	= "	<tr id='ser_$id_servicio'>
								<td onclick='quitar_de_lista( $id_servicio )' class='text-danger pointer'>
									<span class='glyphicon glyphicon-remove-sign'></span>
								</td>
								<td style='width:20px'>
									<input type='text' id='kg_$id_servicio' name='kg_$id_servicio' class='form-control' value='1' required='required' maxlength='5' onKeyUp='calcular_importe( $id_servicio )' onclick='seleccionar(this)' />
									<input type='hidden' name='ser_$id_servicio' value='$id_servicio' />
									<input type='hidden' id='pre_$id_servicio' value='$precio_float' />
									<input type='hidden' id='min_$id_servicio' value='$fila[minimo]' />
								</td>
								<td>$descripcion</td>
								<td class='text-right'>$$fila[precio]</td>
								<td class='text-right' id='imp_$id_servicio'>$$fila[cuota]</td>
							</tr>";
			}
		}
	}
	
	echo $tabla;
	
	mysqli_close( $conexion );
?>