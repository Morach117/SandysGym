<?php
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_comunes.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	require_once( "funciones/sesiones.php" );
	
	$d_grales	= array();
	$e_prepago	= "";	//encabezado prepago
	$p_prepago	= "";	//pie pagina prepago
	$t_detalle	= "";
	$t_encab	= "";
	$cambio		= 0;
	$t_folio	= request_var( 'folio', 0 );
	$t_id_venta	= request_var( 'IDV', 0 );
	$t_efectivo	= request_var( 'efectivo', 0 );
	$t_prepago	= request_var( 'prepago_imp', 0 );
	
	//si se paga con prepago
	if( $t_prepago )
	{
		$query		= "	SELECT		UPPER( CONCAT( soc_apepat, ' ', soc_apemat,  ' ', soc_nombres ) ) AS cliente,
									ROUND( prep_saldo, 2 ) AS saldo
						FROM		san_venta
						INNER JOIN	san_prepago ON prep_id_prepago = ven_id_prepago
						INNER JOIN	san_socios ON soc_id_socio = ven_id_socio
						WHERE		ven_id_venta = $t_id_venta
						AND			ven_folio = $t_folio
						AND			ven_id_empresa = $id_empresa
						AND			ven_status = 'V'";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$e_prepago	= "	<tr style='text-align:left; vertical-align:top'>
									<td>CLIENTE:</td>
									<td>$fila[cliente]</td>
								</tr>
								
								<tr style='text-align:left'>
									<td>SALDO:</td>
									<td>$$fila[saldo]</td>
								</tr>";
				
				$p_prepago	= "	<tr style='font-weight:bold'>
									<td style='text-align:right' colspan='3'>PREPAGO</td>
									<td style='text-align:right'>$".number_format( $t_prepago, 2 )."</td>
								</tr>";
			}
		}
	}
	
	//detalle de la venta
	$query		= "	SELECT		ROUND( vende_cantidad, 2 ) AS cantidad,
								art_descripcion AS descripcion,
								ROUND( vende_precio, 2 ) AS precio,
								ROUND( vende_importe, 2 ) AS importe
					FROM		san_venta
					INNER JOIN	san_venta_detalle ON vende_id_venta = ven_id_venta
					INNER JOIN	san_articulos ON art_codigo = vende_codigo
					WHERE		ven_id_venta = $t_id_venta
					AND			ven_folio = $t_folio
					AND			ven_id_empresa = $id_empresa";
					
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $resultado )
	{
		while( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$t_detalle .= "	<tr style='vertical-align:top'>
								<td style='text-align:right'>".$fila['cantidad']."</td>
								<td>".$fila['descripcion']."</td>
								<td style='text-align:right'>$".$fila['precio']."</td>
								<td style='text-align:right'>$".$fila['importe']."</td>
							</tr>";
		}
	}
	
	/*encabezado*/
	$query		= "	SELECT		emp_descripcion AS sucursal,
								emp_direccion AS direccion,
								emp_colonia AS colonia,
								emp_telefono AS telefono,
								emp_ciudad AS ciudad,
								UPPER( CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) ) AS cajero,
								DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) AS fecha,
								ven_folio AS folio,
								ROUND( ven_total, 2 ) AS total
					FROM		san_empresas
					INNER JOIN	san_venta ON ven_id_empresa = emp_id_empresa
					INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
					AND			ven_id_venta = $t_id_venta
					INNER JOIN	san_usuarios ON usua_id_usuario = ven_id_usuario
					WHERE		emp_id_empresa = $id_empresa
					AND			coem_id_consorcio = $id_consorcio";
	
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $fila = mysqli_fetch_assoc( $resultado ) )
		$d_grales = $fila;
	
	//armando el encabezado del ticket*/
	if( $d_grales['sucursal'] )
		$t_encab .= $d_grales['sucursal']."</br>";
	
	if( $d_grales['direccion'] )
		$t_encab .= $d_grales['direccion']."</br>";
	
	if( $d_grales['colonia'] )
		$t_encab .= $d_grales['colonia']."</br>";
	
	if( $d_grales['telefono'] )
		$t_encab .= "TEL. ".$d_grales['telefono']."</br>";
	
	if( $d_grales['ciudad'] )
		$t_encab .= $d_grales['ciudad']."</br>";
	
	//se calcula el cambio
	$cambio = $t_efectivo - ( $d_grales['total'] - $t_prepago );
?>
<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<p>BIENVENIDO!!!</p>
</div>

<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<?= $t_encab ?>
	<br/>
</div>

<table id="imprimir" style="font-size:8px; font-family:helvetica">
	<tr style="text-align:left">
		<td>FOLIO:</td>
		<td><?= $d_grales['folio'] ?></td>
	</tr>
	
	<tr style="text-align:left">
		<td>FECHA:</td>
		<td><?= $d_grales['fecha'] ?></td>
	</tr>
	
	<tr style="text-align:left; vertical-align:top">
		<td>CAJERO:</td>
		<td><?= $d_grales['cajero'] ?></td>
	</tr>
	
	<?= $e_prepago ?>
</table>

<table id="imprimir" style="font-size:8px; font-family:helvetica">
	<tr style="text-align:left">
		<th>CANT.</th>
		<th>DESCRIPCION</th>
		<th>PRECIO</th>
		<th>IMPORTE</th>
	</tr>
	
	<?= $t_detalle ?>
	
	<tr style="font-weight:bold">
		<td style="text-align:right" colspan="3">TOTAL A PAGAR</td>
		<td style="text-align:right">$<?= $d_grales['total'] ?></td>
	</tr>
	
	<tr style="font-weight:bold">
		<td style="text-align:right" colspan="3">EFECTIVO</td>
		<td style="text-align:right">$<?= number_format( $t_efectivo, 2 ) ?></td>
	</tr>
	
	<?= $p_prepago ?>
	
	<tr style="font-weight:bold">
		<td style="text-align:right" colspan="3">CAMBIO</td>
		<td style="text-align:right">$<?= number_format( $cambio, 2 ) ?></td>
	</tr>
</table>

<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<p>GRACIAS POR SU COMPRA!!!</p>
</div>