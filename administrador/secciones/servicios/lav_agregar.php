<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-bullhorn"></span> Nuevo Servicio
		</h4>
	</div>
</div>

<hr/>

<?php
	$tipo	= request_var( 'tipo', '' );
	
	if( $enviar )
	{
		$exito	= guardar_servicio();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=servicios&i=lav&tipo=$tipo" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$detalle	= sucursales_del_giro();
?>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<div class="row">
		<label class="col-md-2">Descripción</label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="s_descripcion" maxlength="50" required="required" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Orden</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="s_orden" maxlength="2" required="required" placeholder="Orden de lista" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Tipo</label>
		<label class="col-md-2"><?= $tipo ?></label>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr>
						<th>Sucursal</th>
						<th class="text-right">Cuota</th>
						<th class="text-right info">Promo Cuota</th>
						<th class="text-right info">Promo día</th>
						<th class="text-right">Mínimo</th>
						<th>Mostrar</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $detalle ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" name="tipo" value="<?= $tipo ?>" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
			<input type="button" class="btn btn-default" value="Regresar" onclick="location.href='.?s=servicios&i=lav'" />
		</div>
	</div>
</form>