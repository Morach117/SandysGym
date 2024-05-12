<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$envio		= isset( $_POST['envio'] ) ? true:false;
	$id_corte	= request_var( 'id_corte', 0 );
	$id_usuario	= request_var( 'id_usuario', 0 );
	$datos		= "";
	
	if( $envio )
	{
		$query		= "	SELECT		DATE_FORMAT( cor_fecha, '%d-%m-%Y %r' ) AS movimiento,
									DATE_FORMAT( cor_fecha_venta, '%d-%m-%Y' ) AS fecha_venta,
									ROUND( cor_importe, 2 ) AS importe,
									ROUND( cor_caja, 2 ) AS caja,
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
						AND			cor_id_corte = $id_corte
						AND			cor_id_usuario = $id_usuario";
		
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			if( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos = "	<label>Realizó: </label> $fila[realizo] <br/>
							<label>Cajero: </label> $fila[cajero] <br/>
							<label>Movimiento: </label> ".fecha_generica( $fila['movimiento'] )." <br/>
							<label>Fecha de Venta: </label> ".fecha_generica( $fila['fecha_venta'] )." <br/>
							<label>Corte: </label> $$fila[importe] <br/>
							<label>Observaciones: </label> $fila[obs] <br/><br/>";
				
				$datos .= "	<table class='table table-hover h6'>
								<tr class='active'>
									<th>Denominaciones</th>
									<th class='text-right'>Cantidad</th>
									<th class='text-right'>Importe</th>
								</tr>";
				
				if( $fila['b_1000'] )
				{
					$datos .= "	<tr>
									<td>Billetes de $1000</td>
									<td class='text-right'>$fila[b_1000]</td>
									<td class='text-right'>$".number_format( $fila['b_1000'] * 1000, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_500'] )
				{
					$datos .= "	<tr>
									<td>Billetes de $500</td>
									<td class='text-right'>$fila[b_500]</td>
									<td class='text-right'>$".number_format( $fila['b_500'] * 500, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_200'] )
				{
					$datos .= "	<tr>
									<td>Billetes de $200</td>
									<td class='text-right'>$fila[b_200]</td>
									<td class='text-right'>$".number_format( $fila['b_200'] * 200, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_100'] )
				{
					$datos .= "	<tr>
									<td>Billetes de $100</td>
									<td class='text-right'>$fila[b_100]</td>
									<td class='text-right'>$".number_format( $fila['b_100'] * 100, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_50'] )
				{
					$datos .= "	<tr>
									<td>Billetes de $50</td>
									<td class='text-right'>$fila[b_50]</td>
									<td class='text-right'>$".number_format( $fila['b_50'] * 50, 2 )."</td>
								</tr>";
				}
				
				if( $fila['b_20'] )
				{
					$datos .= "	<tr>
									<td>Billetes de $20</td>
									<td class='text-right'>$fila[b_20]</td>
									<td class='text-right'>$".number_format( $fila['b_20'] * 20, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_20'] )
				{
					$datos .= "	<tr>
									<td>Monedas de $20</td>
									<td class='text-right'>$fila[m_20]</td>
									<td class='text-right'>$".number_format( $fila['m_20'] * 20, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_10'] )
				{
					$datos .= "	<tr>
									<td>Monedas de $10</td>
									<td class='text-right'>$fila[m_10]</td>
									<td class='text-right'>$".number_format( $fila['m_10'] * 10, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_5'] )
				{
					$datos .= "	<tr>
									<td>Monedas de $5</td>
									<td class='text-right'>$fila[m_5]</td>
									<td class='text-right'>$".number_format( $fila['m_5'] * 5, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_2'] )
				{
					$datos .= "	<tr>
									<td>Monedas de $2</td>
									<td class='text-right'>$fila[m_2]</td>
									<td class='text-right'>$".number_format( $fila['m_2'] * 2, 2 )."</td>
								</tr>";
				}
				
				if( $fila['m_1'] )
				{
					$datos .= "	<tr>
									<td>Monedas de $1</td>
									<td class='text-right'>$fila[m_1]</td>
									<td class='text-right'>$".number_format( $fila['m_1'] * 1, 2 )."</td>
								</tr>";
				}
				
				if( $fila['c_50'] )
				{
					$datos .= "	<tr>
									<td>Monedas de $0.50</td>
									<td class='text-right'>$fila[c_50]</td>
									<td class='text-right'>$".number_format( $fila['c_50'] * 0.5, 2 )."</td>
								</tr>";
				}
				
				$datos .= "</table>";
			}
		}
	}
	
	mysqli_close( $conexion );
?>

<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title text-primary">Retiro de efectivo</h4>
		</div>
		
		<div class="modal-body">
			<?= $datos ?>
		</div>
		
		<div class="modal-footer">
			<label id="msj_procesar">&nbsp;</label>
			<label id="img_procesar">&nbsp;</label>
			
			<label id="btn_procesar">
				<button type="button" onclick="imprimir_ticket_corte_diario( <?= $id_corte.", ".$id_usuario ?> )" class="btn btn-primary">Imprimir Ticket</button>
			</label>
		</div>
	</div>
</div>