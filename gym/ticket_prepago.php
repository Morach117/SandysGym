<?php
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_comunes.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	require_once( "funciones/sesiones.php" );
	
	$t_detalle		= "";
	$t_encab		= "";
	$t_id_prepago	= request_var( 'IDP', 0 );
	$t_id_prepago_d	= request_var( 'IDD', 0 );
	$t_id_socio		= request_var( 'IDS', 0 );
	$t_token		= request_var( 'token', '' );
	$t_token_chk	= hash_hmac( 'md5', $t_id_prepago, $gbl_key );
	
	if( $t_token == $t_token_chk )
	{
		//detalle de la venta
		$query		= "	SELECT		ROUND( prep_saldo, 2 ) AS saldo,
									ROUND( pred_importe, 2 ) AS importe,
									pred_descripcion AS descripcion,
									DATE_FORMAT( pred_fecha, '%d-%m-%Y %r' ) AS fecha_pago,
									UPPER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) AS cliente,
									UPPER( CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) ) AS cajero
						FROM		san_prepago
						INNER JOIN	san_prepago_detalle ON pred_id_prepago = prep_id_prepago
						INNER JOIN	san_socios ON soc_id_socio = prep_id_socio
						INNER JOIN	san_usuarios ON usua_id_usuario = pred_id_usuario
						WHERE		prep_id_prepago = $t_id_prepago
						AND			prep_id_socio = $t_id_socio
						AND			prep_id_empresa = $id_empresa
						AND			pred_id_pdetalle = $t_id_prepago_d
						AND			pred_movimiento = 'S'";
						
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
									<td>ABONO:</td>
									<td>$$fila[importe]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>SALDO:</td>
									<td>$$fila[saldo]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>DESCRIPCION:</td>
									<td>$fila[descripcion]</td>
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