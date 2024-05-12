<?php
	$g_sucursal			= request_var( 'g_sucursal', 0 );
	$g_proveedor		= request_var( 'g_proveedor', 0 );
	$g_fnota			= request_var( 'g_fnota', '' );
	$g_importe			= request_var( 'g_importe', 0.0 );
	$g_iva				= request_var( 'g_iva', 0.0 );
	$g_descuento		= request_var( 'g_descuento', 0.0 );
	$g_total			= request_var( 'g_total', 0.0 );
	$g_fecha			= request_var( 'g_fecha', '' );
	$g_observaciones	= request_var( 'g_observaciones', '' );
	
	if( $enviar )
	{
		$exito	= guardar_gasto();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=catalogos&i=gastos" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$provedores	= combo_proveedores( $g_proveedor );
	$sucursales	= combo_sucursales( $g_sucursal );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Formulario para captura de gastos
		</h4>
	</div>
</div>

<hr/>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<div class="row">
		<label class="col-md-2">Sucursal</label>
		<div class="col-md-4">
			<select name="g_sucursal" class="form-control">
				<option value="">Selecciona...</option>
				<?= $sucursales ?>
			</select>
		</div>
		<div class="col-md-6"><small>Opción TODOS no válido en esta sección</small></div>
	</div>
		
	<div class="row">
		<label class="col-md-2">Proveedor</label>
		<div class="col-md-4">
			<select name="g_proveedor" class="form-control">
				<option value="">Selecciona...</option>
				<?= $provedores ?>
			</select>
		</div>
		
		<label class="col-md-3">No. de Factura o Nota</label>
		<div class="col-md-3">
			<input type="text" class="form-control" name="g_fnota" value="<?= $g_fnota ?>" maxlength="10" required="required" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Importe</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="g_importe" value="<?= $g_importe ?>" id="g_importe" maxlength="7" required="required" onkeyup="calcular_total_gastos()" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Monto del IVA</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="g_iva" id="g_iva" value="<?= $g_iva ?>" maxlength="7" onkeyup="calcular_total_gastos()" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Monto de Descuento</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="g_descuento" id="g_descuento" value="<?= $g_descuento ?>" maxlength="7" onkeyup="calcular_total_gastos()" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Total</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="g_total" id="g_total" value="<?= $g_total ?>" maxlength="7" required="required" />
		</div>
		
		<label class="col-md-offset-2 col-md-2">Fecha</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="g_fecha" id="f_actual" value="<?= $g_fecha?>" maxlength="10" required="required" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Observaciones</label>
		<div class="col-md-10">
			<textarea rows="2" class="form-control" name="g_observaciones" maxlength="100"><?= $g_observaciones ?></textarea>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
			<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=catalogos&i=gastos'" />
		</div>
	</div>
</form>