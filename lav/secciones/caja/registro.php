<div class="row">
	<div class="col-md-12">
		<h4 class="text-primary">
			<span class="glyphicon glyphicon-usd"></span> REGISTRO DE CAJA
		</h4>
	</div>
</div>

<hr/>

<?php
	$v_monto	= request_var( 'v_monto', '' );
	$v_obs		= request_var( 'v_obs', '' );
	
	if( $enviar )
	{
		$exito = registrar_caja();
		
		if( $exito['num'] == 1 )
		{
			header( "location: .?s=$seccion" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['msj'], 'danger' );
	}
?>

<div class="row">
	<div class="col-md-12">
		<p>Monto con que se apertura o cierra caja</p>
	</div>
</div>

<form method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-2">Monto</label>
		<div class="col-md-4"><input type="text" name="v_monto" class="form-control" maxlength="7" value="<?= $v_monto ?>" /></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Movimiento</label>
		<div class="col-md-4">
			<input type="radio" name="v_tipo" value="3" />Apertura de caja <br/>
			<input type="radio" name="v_tipo" value="4" />Cierre de caja
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Observaciones</label>
		<div class="col-md-10">
			<textarea name="v_obs" class="form-control" rows="2" maxlength="100"><?= $v_obs ?></textarea>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12"><input type="submit" name="enviar" value="Registrar" class="btn btn-primary" /></div>
	</div>
</form>