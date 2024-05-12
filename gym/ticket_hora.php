<?php
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_comunes.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	require_once( "funciones/sesiones.php" );
	
	$t_encab	= "";
	$t_detalle	= "";
	$t_id_hora	= request_var( 'IDH', 0 );
	$t_token	= request_var( 'token', '' );
	$chk_token	= hash_hmac( 'md5', $t_id_hora, $gbl_key );
	
	if( $t_token == $chk_token )
	{
		//detalle de la venta
		$query		= "	SELECT		UPPER( hor_nombre ) AS cliente,
									DATE_FORMAT( hor_fecha, '%d-%m-%Y %r' ) AS fecha,
									hor_horas AS horas,
									hor_hora_inicial AS inicio,
									hor_hora_final AS fin,
									ser_descripcion AS modalidad,
									ROUND( hor_importe, 2 ) AS importe,
									UPPER( CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) ) AS cajero
						FROM 		san_horas
						INNER JOIN	san_usuarios ON usua_id_usuario = hor_id_usuario
						INNER JOIN	san_servicios ON ser_id_servicio = hor_id_servicio
						WHERE		hor_id_horas = $t_id_hora
						AND			hor_id_empresa = $id_empresa
						AND			hor_status = 'A'";
						
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
									<td>".fecha_generica( $fila['fecha'], true )."</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>CLIENTE:</td>
									<td>$fila[cliente]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>HORAS:</td>
									<td>$fila[horas]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>ENTRADA:</td>
									<td>$fila[inicio]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>SALIDA:</td>
									<td>$fila[fin]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>IMPORTE:</td>
									<td>$$fila[importe]</td>
								</tr>
								
								<tr style='text-align:left; vertical-align:top'>
									<td>MODALIDAD:</td>
									<td>$fila[modalidad]</td>
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