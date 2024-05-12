<?php
	$busqueda	= request_var( 'op_prepago', 1 );
	$id_prepago	= request_var( 'IDP', 0 );
	$id_pre_det	= request_var( 'IDD', 0 );
	$id_socio	= request_var( 'IDS', 0 );
	$token		= request_var( 'token', '' );
	
	// if( $id_prepago && $id_pre_det )
	// {
		// $token_chk	= hash_hmac( 'md5', $id_prepago, $gbl_key );
		
		// if( $token == $token_chk )
			// echo "<script>mostrar_modal_prepago( $id_prepago, $id_pre_det, $id_socio, '$token' )</script>";
	// }
	
	if( $busqueda == 1 )
		$condicion = " prep_saldo > 0 ";
	else
		$condicion = " prep_saldo <= 0 ";
		
	$subquery	= "	SELECT		COUNT(*) AS total,
								'$gbl_paginado' AS mostrar
					FROM		san_prepago
					INNER JOIN	san_socios ON soc_id_socio = prep_id_socio
					WHERE		$condicion
					AND			prep_id_empresa = $id_empresa";
	
	$paginas_con_saldo	= paginado( $subquery, "$seccion" );
	
	$lista_socios	= lista_socios( $busqueda );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Control de PrePagos
		</h4>
	</div>
</div>

<hr/>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-2">Opciones</label>
		<div class="col-md-4">
			<select name="op_prepago" class="form-control">
				<option <?= ( $busqueda == '1' ) ? 'selected':'' ?> value="1">Socios con Saldo</option>
				<option <?= ( $busqueda == '2' ) ? 'selected':'' ?> value="2">Socios sin Saldo</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-2 col-md-4"><input type="submit" class="btn btn-primary btn-sm" value="Buscar" name="enviar" /></div>
	</div>
</form>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-condensed pointer">
			<thead>
				<tr>
					<th>#</th>
					<th>Nombre completo</th>
					<th class="text-right">Saldo</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_socios ?>
			</tbody>
		</table>
		
		<?= $paginas_con_saldo ?>
	</div>
</div>