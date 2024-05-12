<?php
	require_once( "../funciones_globales/funciones_conexion.php" );
	require_once( "../funciones_globales/funciones_comunes.php" );
	require_once( "../funciones_globales/funciones_phpBB.php" );
	require_once( "funciones/sesiones.php" );
	
	$t_detalle		= "";
	$d_sucursal		= "";
	$d_grales		= array();
	$js_tipo		= request_var( 'tipo', '' ); //LAVANDERIA, PLANCHADURIA
	$js_id_venta	= request_var( 'id', 0 );
	$js_folio		= request_var( 'folio', 0 );			//folio
	$js_anio		= request_var( 'anio', 0 );
	$js_reimprimir	= request_var( 'reimpresion', 'N' );	//S cuando es reimpresion
	$txt_reimp		= "";
	$txt_cliente	= "";
	$total			= 0;
	
	if( $js_tipo == 'LAVANDERIA' )
		$tag = "KGS.";
	else
		$tag = "PZS.";
	
	//detalle
	$query		= "	SELECT		ser_descripcion AS descripcion,
								vense_kilogramo AS kilo,
								vense_precio AS precio,
								vense_importe AS importe
					FROM		san_venta
					INNER JOIN	san_venta_servicio ON vense_id_venta = ven_id_venta
					INNER JOIN	san_servicios ON ser_id_servicio = vense_id_servicio
					WHERE		ven_id_venta = $js_id_venta
					AND			ven_folio = $js_folio
					AND			ven_id_empresa = $id_empresa
					AND			ser_tipo = '$js_tipo'
					AND			ser_id_consorcio = $id_consorcio";
					
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $resultado )
	{
		while( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$t_detalle .= "	<tr style='vertical-align:top'>
								<td style='text-align:right'>".number_format( $fila['kilo'], 2 )."</td>
								<td>".$fila['descripcion']."</td>
								<td style='text-align:right'>$".number_format( $fila['precio'], 2 )."</td>
								<td style='text-align:right'>$".number_format( $fila['importe'], 2 )."</td>
							</tr>";
			
			$total += $fila['kilo'];
		}
	}
	
	/*detalle de la sucursal y venta*/
	$query		= "	SELECT		CASE foc_tkt_ver_sucursal
									WHEN 'S' THEN emp_descripcion
									WHEN 'Y' THEN con_razon_social
									WHEN 'N' THEN ''
								END AS sucursal,
								IF( foc_tkt_ver_direccion = 'S', emp_direccion, '' ) AS direccion,
								IF( foc_tkt_ver_colonia = 'S', emp_colonia, '' ) AS colonia,
								IF( foc_tkt_ver_ciudad = 'S', emp_ciudad, '' ) AS ciudad,
								IF( foc_tkt_ver_telefono = 'S', emp_telefono, '' ) AS telefono,
								if( foc_tkt_ver_rfc = 'S', con_rfc, '' ) AS rfc,
								UPPER( CONCAT( usua_ape_pat, ' ', usua_ape_mat, ' ', usua_nombres ) ) AS cajero,
								DATE_FORMAT( ven_fecha, '%d-%m-%Y %r' ) AS f_recepcion,
								DATE_FORMAT( ven_entrega, '%d-%m-%Y %r' ) AS f_entrega,
                                CONCAT( IF( foc_tkt_letra IS NOT NULL, foc_tkt_letra, '' ), LPAD( ven_folio, 7, 0 ) ) AS folio,
								ven_total_efectivo AS anticipo,
								ven_total_credito AS debe,
								ven_total AS total,
								foc_tkt_font_size AS font_size,
								foc_tkt_encabezado AS encabezado,
								foc_tkt_pie_pagina AS pie_pagina,
								CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS cliente
					FROM		san_empresas
					INNER JOIN	san_venta ON ven_id_empresa = emp_id_empresa
					INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
					INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
					INNER JOIN	san_usuarios ON usua_id_usuario = ven_id_usuario
					INNER JOIN	san_folios_conf ON foc_id_empresa = emp_id_empresa
					LEFT JOIN	san_socios ON soc_id_socio = ven_id_socio
					WHERE		emp_id_empresa = $id_empresa
					AND			con_id_consorcio = $id_consorcio
					AND			ven_id_venta = $js_id_venta
					AND			ven_folio = $js_folio";
	
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $fila = mysqli_fetch_assoc( $resultado ) )
		$d_grales = $fila;
	
	//armando el encabezado del ticket*/
	if( $d_grales['sucursal'] )
		$d_sucursal .= "<strong>".$d_grales['sucursal']."</strong></br>";
	
	if( $d_grales['direccion'] )
		$d_sucursal .= $d_grales['direccion']."</br>";
	
	if( $d_grales['colonia'] )
		$d_sucursal .= $d_grales['colonia']."</br>";
	
	if( $d_grales['ciudad'] )
		$d_sucursal .= $d_grales['ciudad']."</br>";
	
	if( $d_grales['telefono'] )
		$d_sucursal .= "TEL. ".$d_grales['telefono']."</br>";
	
	if( $d_grales['rfc'] )
		$d_sucursal .= $d_grales['rfc']."</br>";
	
	if( $js_reimprimir == 'S' )
		$txt_reimp = "==REIMPRESIÓN ".date( 'd-m-Y' )."==";
	
	if( $d_grales['cliente'] )
		$txt_cliente = $d_grales['cliente'];
?>

<html>
	<head>
		<title>Ticket</title>
		
		<style type="text/css"> 
			body { 
				font-size:<?= $d_grales['font_size'] ?>px;
				font-family:helvetica
			}
			
			div {
				text-align:center;
			}
			
			table {
				font-size:<?= $d_grales['font_size'] ?>px;
				font-family:helvetica;
			}
			
			.tabla_tr {
				text-align:left;
				vertical-align:bottom;
			}
			
			.tabla_tr_td {
				font-size:<?= $d_grales['font_size'] * 1.5 ?>px;
			}
			
			.table_d_v {
				font-weight:bold;
				text-align:right;
			}
		</style> 
	</head>
	
	<body>
		<!--primera copia-->
		
		<div id="imprimir">
			<p><?= $d_grales['encabezado'] ?></p>
			
			<?= $d_sucursal ?>
			
			<p>NOTA DE VENTA</p>
			
			<br/>
		</div>

		<table id="imprimir">
			<tr class="tabla_tr">
				<td>FOLIO:</td>
				<td class='tabla_tr_td'><?= $d_grales['folio']." $js_tipo" ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>RECEPCIÓN:</td>
				<td><?= $d_grales['f_recepcion'] ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>ENTREGA:</td>
				<td><?= $d_grales['f_entrega'] ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>CAJERO:</td>
				<td><?= $d_grales['cajero'] ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>CLIENTE:</td>
				<td><?= $txt_cliente ?></td>
			</tr>
		</table>

		<table id="imprimir">
			<tr style="text-align:left">
				<th><?= $tag ?></th>
				<th>DESCRIPCION</th>
				<th>PRECIO</th>
				<th>IMPORTE</th>
			</tr>
			
			<?php 
				echo $t_detalle;
			?>
			
			<tr class="table_d_v">
				<td colspan="3"><?= $tag ?></td>
				<td><?= number_format( $total, 2 ) ?></td>
			</tr>
			
			<tr class="table_d_v">
				<td colspan="3">TOTAL A PAGAR</td>
				<td>$<?= number_format( $d_grales['total'], 2 ) ?></td>
			</tr>
			
			<tr class="table_d_v">
				<td colspan="3">ANTICIPO</td>
				<td>$<?= number_format( $d_grales['anticipo'], 2 ) ?></td>
			</tr>
			
			<tr class="table_d_v">
				<td colspan="3">POR PAGAR</td>
				<td>$<?= number_format( $d_grales['debe'], 2 ) ?></td>
			</tr>
		</table>

		<div id="imprimir">
			<p><?= $txt_reimp ?></p>
			<p><?= $d_grales['pie_pagina'] ?></p>
			<p>==COPIA TIENDA==</p>
		</div>
		
		<br/>
		<hr/>
		<br/>
		
		<!--segunda copia-->
		
		<div id="imprimir">
			<p><?= $d_grales['encabezado'] ?></p>
			
			<?= $d_sucursal ?>
			
			<p>NOTA DE VENTA</p>
			
			<br/>
		</div>

		<table id="imprimir">
			<tr class="tabla_tr">
				<td>FOLIO:</td>
				<td class='tabla_tr_td'><?= $d_grales['folio']." $js_tipo" ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>RECEPCIÓN:</td>
				<td><?= $d_grales['f_recepcion'] ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>ENTREGA:</td>
				<td><?= $d_grales['f_entrega'] ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>CAJERO:</td>
				<td><?= $d_grales['cajero'] ?></td>
			</tr>
			
			<tr class="tabla_tr">
				<td>CLIENTE:</td>
				<td><?= $txt_cliente ?></td>
			</tr>
		</table>

		<table id="imprimir">
			<tr style="text-align:left">
				<th><?= $tag ?></th>
				<th>DESCRIPCION</th>
				<th>PRECIO</th>
				<th>IMPORTE</th>
			</tr>
			
			<?php 
				echo $t_detalle;
			?>
			
			<tr class="table_d_v">
				<td colspan="3"><?= $tag ?></td>
				<td><?= number_format( $total, 2 ) ?></td>
			</tr>
			
			<tr class="table_d_v">
				<td colspan="3">TOTAL A PAGAR</td>
				<td>$<?= number_format( $d_grales['total'], 2 ) ?></td>
			</tr>
			
			<tr class="table_d_v">
				<td colspan="3">ANTICIPO</td>
				<td>$<?= number_format( $d_grales['anticipo'], 2 ) ?></td>
			</tr>
			
			<tr class="table_d_v">
				<td colspan="3">POR PAGAR</td>
				<td>$<?= number_format( $d_grales['debe'], 2 ) ?></td>
			</tr>
		</table>

		<div id="imprimir">
			<p><?= $txt_reimp ?></p>
			<p><?= $d_grales['pie_pagina'] ?></p>
			<p>==COPIA CLIENTE==</p>
		</div>
	</body>
</html>