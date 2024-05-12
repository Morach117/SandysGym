<div class="row">
	<div class="col-md-12">
		<h4 class="text-primary">
			<span class="glyphicon glyphicon-usd"></span> RETIRO DE EFECTIVO
		</h4>
	</div>
</div>

<hr/>

<?php
	$fecha_mov		= date( 'd-m-Y' );
	$cor_importe	= request_var( 'cor_importe', '' );
	$cor_obs		= request_var( 'cor_observaciones', '' );
	
	if( $enviar )
	{
		$exito = realizar_corte();
		
		if( $exito['num'] == 1 )
		{
			header( "location: .?s=$seccion&idc=$exito[IDC]&idu=$exito[IDU]" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$idc	= request_var( 'idc', 0 );
	$idu	= request_var( 'idu', 0 );
	
	if( $idc && $idu )
		echo "<script>mostrar_modal_corte( $idc, $idu )</script>";
	
	$lista_cortes	= lista_cortes_del_dia( $fecha_mov );
?>

<div class="row">
	<label class="col-md-2">Día</label>
	<label class="col-md-4"><?= fecha_generica( date( 'd-m-Y' ) ) ?></label>
</div>

<div class="row">
	<label class="col-md-2">Cajero</label>
	<label class="col-md-4"><?= $nombres." ". $apellidos ?></label>
</div>

<div class="row">	
	<form action=".?s=<?= $seccion ?>" method="post">
		<div class="col-md-12">
			<div class="row">
				<label class="col-md-2">Importe</label>
				<div class="col-md-4"><input type="text" name="cor_importe" class="form-control" value="<?= $cor_importe ?>" /></div>
			</div>
			
			<div class="row">
				<label class="col-md-2">Notas</label>
				<div class="col-md-10">
					<textarea name="cor_observaciones" class="form-control" maxlength="50" rows="2"><?= $cor_obs ?></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-10">
					<input type="submit" name="enviar" class="btn btn-primary" value="Retirar efectivo">
				</div>
			</div>
		</div>
	</form>
</div>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info text-bold">Lista movimientos del día de hoy</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Movimiento</th>
					<th>Procesó</th>
					<th>Cajero</th>
					<th class="text-right">Caja</th>
					<th>Tipo</th>
					<th>Observaciones</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $lista_cortes ?>
				</tr>
			</tbody>
		</table>
	</div>
</div>