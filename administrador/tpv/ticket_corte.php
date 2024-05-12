<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "funciones/sesiones.php" );
	
	$t_detalle		= "";
	$t_encab		= "";
	$t_encab2		= "";
	$t_id_corte		= request_var( 'IDC', 0 );
	$t_tipo_corte	= request_var( 'CTC', 0 );//TIPO CORTE 1=SE INTRODUCE EL IMPORTE Y 2=SE CALCULA EL IMPORTE CON BILLETES Y MODENDAS
	$t_id_usuario	= request_var( 'IDU', 0 );//ID_USUARIO, EL QUE HIZO EL CORTE
	
	//detalle de la venta
	$query		= "	SELECT		DATE_FORMAT( cor_fecha, '%d-%m-%Y %r' ) AS movimiento,
								DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' ) AS fecha_venta,
								ROUND( cor_importe, 2 ) AS importe,
								cor_tipo_corte AS tipo,
								cor_b_1000 AS b_1000,
								cor_b_500 AS b_500,
								cor_b_200 AS b_200,
								cor_b_100 AS b_100,
								cor_b_50 AS b_50,
								cor_b_20 AS b_20,
								cor_m_20 AS m_20,
								cor_m_10 AS m_10,
								cor_m_5 AS m_5,
								cor_m_2 AS m_2,
								cor_m_1 AS m_1,
								cor_c_50 AS c_50,
								cor_observaciones AS obs,
								CONCAT( a.usua_ape_pat, ' ', a.usua_ape_mat, ' ', a.usua_nombres ) AS realizo,
								IF( cor_id_cajero > 0, CONCAT( b.usua_ape_pat, ' ', b.usua_ape_mat, ' ', b.usua_nombres ), 'No Seleccionado' ) AS cajero
					FROM 		san_corte 
					INNER JOIN	san_usuarios a ON a.usua_id_usuario = cor_id_usuario
					LEFT JOIN	san_usuarios b ON b.usua_id_usuario = cor_id_cajero
					WHERE 		cor_id_empresa = $id_empresa
					AND			cor_id_corte = $t_id_corte
					AND			cor_tipo_corte = $t_tipo_corte
					AND			cor_id_usuario = $t_id_usuario";
					
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $resultado )
	{
		if( $fila = mysqli_fetch_assoc( $resultado ) )
		{
			$t_encab2 = "	<label>REALIZÓ: </label> $fila[realizo] <br/>
						<label>CAJERO: </label> $fila[cajero] <br/>
						<label>MOVIMIENTO: </label> ".fecha_generica( $fila['movimiento'] )." <br/>
						<label>FECHA DE VENTA: </label> ".fecha_generica( $fila['fecha_venta'] )." <br/>
						<label>IMPORTE: </label> $$fila[importe] <br/>
						<label>OBSERVACIONES: </label> $fila[obs] <br/><br/>";
			
			if( $fila['tipo'] == 2 )
			{
				$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<th>Denominaciones</th>
									<th style='text-align:right'>Cantidad</th>
									<th style='text-align:right'>Importe</th>
								</tr>";
				
				if( $fila['b_1000'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Billetes de $1000</td>
									<td style='text-align:right'>$fila[b_1000]</td>
									<td style='text-align:right'>$".number_format( $fila['b_1000'] * 1000, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_500'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Billetes de $500</td>
									<td style='text-align:right'>$fila[b_500]</td>
									<td style='text-align:right'>$".number_format( $fila['b_500'] * 500, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_200'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Billetes de $200</td>
									<td style='text-align:right'>$fila[b_200]</td>
									<td style='text-align:right'>$".number_format( $fila['b_200'] * 200, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_100'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Billetes de $100</td>
									<td style='text-align:right'>$fila[b_100]</td>
									<td style='text-align:right'>$".number_format( $fila['b_100'] * 100, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_50'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Billetes de $50</td>
									<td style='text-align:right'>$fila[b_50]</td>
									<td style='text-align:right'>$".number_format( $fila['b_50'] * 50, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_20'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Billetes de $20</td>
									<td style='text-align:right'>$fila[b_20]</td>
									<td style='text-align:right'>$".number_format( $fila['b_20'] * 20, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_20'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Monedas de $20</td>
									<td style='text-align:right'>$fila[m_20]</td>
									<td style='text-align:right'>$".number_format( $fila['m_20'] * 20, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_10'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Monedas de $10</td>
									<td style='text-align:right'>$fila[m_10]</td>
									<td style='text-align:right'>$".number_format( $fila['m_10'] * 10, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_5'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Monedas de $5</td>
									<td style='text-align:right'>$fila[m_5]</td>
									<td style='text-align:right'>$".number_format( $fila['m_5'] * 5, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_2'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Monedas de $2</td>
									<td style='text-align:right'>$fila[m_2]</td>
									<td style='text-align:right'>$".number_format( $fila['m_2'] * 2, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_1'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Monedas de $1</td>
									<td style='text-align:right'>$fila[m_1]</td>
									<td style='text-align:right'>$".number_format( $fila['m_1'] * 1, 2 )."</td>
								</tr>";
				}
				
				if( $fila['c_50'] )
				{
					$t_detalle .= "	<tr style='text-align:left; vertical-align:top'>
									<td>Monedas de $0.50</td>
									<td style='text-align:right'>$fila[c_50]</td>
									<td style='text-align:right'>$".number_format( $fila['c_50'] * 0.5, 2 )."</td>
								</tr>";
				}
			}
		}
	}
	
	/*detalle de la sucursal*/
	$query		= "	SELECT		CASE foc_tkt_ver_sucursal
									WHEN 'S' THEN emp_descripcion
									WHEN 'Y' THEN con_razon_social
									WHEN 'N' THEN ''
								END AS sucursal,
								IF( foc_tkt_ver_direccion = 'S', emp_direccion, '' ) AS direccion,
								IF( foc_tkt_ver_colonia = 'S', emp_colonia, '' ) AS colonia,
								IF( foc_tkt_ver_ciudad = 'S', emp_ciudad, '' ) AS ciudad,
								IF( foc_tkt_ver_telefono = 'S', emp_telefono, '' ) AS telefono,
								if( foc_tkt_ver_rfc = 'S', con_rfc, '' ) AS rfc
					FROM		san_empresas
					INNER JOIN	san_corte ON cor_id_empresa = emp_id_empresa
					INNER JOIN	san_consorcio_empresa ON coem_id_empresa = emp_id_empresa
					INNER JOIN	san_consorcios ON con_id_consorcio = coem_id_consorcio
					INNER JOIN	san_usuarios ON usua_id_usuario = cor_id_usuario
					INNER JOIN	san_folios_conf ON foc_id_empresa = emp_id_empresa
					WHERE		emp_id_empresa = $id_empresa
					AND			con_id_consorcio = $id_consorcio
					AND			cor_id_corte = $t_id_corte
					AND			cor_id_usuario = $t_id_usuario";
	
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $fila = mysqli_fetch_assoc( $resultado ) )
	{
		if( $fila['sucursal'] )
			$t_encab .= "<strong>".$fila['sucursal']."</strong></br>";
		
		if( $fila['direccion'] )
			$t_encab .= $fila['direccion']."</br>";
		
		if( $fila['colonia'] )
			$t_encab .= $fila['colonia']."</br>";
		
		if( $fila['ciudad'] )
			$t_encab .= $fila['ciudad']."</br>";
		
		if( $fila['telefono'] )
			$t_encab .= "TEL. ".$fila['telefono']."</br>";
		
		if( $fila['rfc'] )
			$t_encab .= $fila['rfc']."</br>";
	}
?>
<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<p>CORTE DE CAJA</p>
</div>

<div id="imprimir" style="font-size:8px; font-family:helvetica; text-align:center">
	<?= $t_encab ?>
	<br/>
	<?= $t_encab2 ?>
	<br/>
</div>

<table id="imprimir" style="font-size:8px; font-family:helvetica">
	<?= $t_detalle ?>
</table>