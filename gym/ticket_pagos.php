<?php
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_comunes.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	require_once( "funciones/sesiones.php" );
	
	$t_detalle	= "";
	$t_encab	= "";
	$t_id_pago	= request_var( 'IDP', 0 );
	$t_token	= request_var( 'token', '' );
	$token_chk	= hash_hmac( 'md5', $t_id_pago, $gbl_key );
	
	if( $t_token == $token_chk )
	{
		//detalle de la venta
		$query		= "	SELECT		DATE_FORMAT( pag_fecha_ini, '%d-%m-%Y' ) AS fecha_ini,
									DATE_FORMAT( pag_fecha_fin, '%d-%m-%Y' ) AS fecha_fin,
									DATE_FORMAT( pag_fecha_pago, '%d-%m-%Y %r' ) AS fecha_pago,
									UPPER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) AS cliente,
									UPPER( CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) ) AS cajero,
									ser_descripcion AS descripcion,
									ROUND( pag_importe, 2 ) AS importe
						FROM		san_pagos
						INNER JOIN	san_socios ON soc_id_socio = pag_id_socio
						INNER JOIN	san_servicios ON pag_id_servicio = ser_id_servicio
						INNER JOIN	san_usuarios ON usua_id_usuario = pag_id_usuario
						WHERE		pag_id_pago = $t_id_pago
						AND			pag_id_empresa = $id_empresa
						AND			pag_status = 'A'";
						
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$t_detalle	= "	<tr style='text-align:left; vertical-align:top'>
									<td>CAJERO:</td>
									<td>$fila[cajero]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>FECHA:</td>
									<td>".fecha_generica( $fila['fecha_pago'], true )."</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>CLIENTE:</td>
									<td>$fila[cliente]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>IMPORTE:</td>
									<td>$$fila[importe]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>MODALIDAD:</td>
									<td>$fila[descripcion]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>INICIO:</td>
									<td>".fecha_generica( $fila['fecha_ini'], true )."</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>TERMINO:</td>
									<td>".fecha_generica( $fila['fecha_fin'], true )."</td>
								</tr>";
			}
		}
		
		/*encabezado*/
		$query		= "	SELECT		emp_descripcion AS sucursal,
									emp_direccion AS direccion,
									emp_colonia AS colonia,
									emp_telefono AS telefono,
									emp_ciudad AS ciudad
						FROM		san_empresas
						INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
						WHERE		emp_id_empresa = $id_empresa
						AND			coem_id_consorcio = $id_consorcio";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$t_encab .= $fila['sucursal']."</br>";
			$t_encab .= $fila['direccion']."</br>";
			$t_encab .= $fila['colonia']."</br>";
			$t_encab .= "TEL. ".$fila['telefono']."</br>";
			$t_encab .= $fila['ciudad']."</br>";
		}
	}
	else
	{
		$t_encab	= "Token no válido.";
		$t_detalle	= "Token no válido.";
	}
?>
<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<p>BIENVENIDO!!!</p>
</div>

<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<?= $t_encab ?>
	<br/>
</div>

<table id="imprimir" style="font-size:8px; font-family:helvetica">
	<?= $t_detalle ?>
</table>

<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<p>GRACIAS POR SU COMPRA!!!</p>
</div>