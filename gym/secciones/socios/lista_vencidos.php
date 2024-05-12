<?php
	$pag_busqueda	= request_var( 'pag_busqueda', '' );
	
	$fecha_hoy	= strtotime( date( 'd-m-Y' ) );
	$fecha_hoy	= strtotime( '-20 day', $fecha_hoy );
	
	$fecha_ini	= request_var( 'pag_fechai', date( 'd-m-Y', $fecha_hoy ) );
	$fecha_fin	= request_var( 'pag_fechaf', date( 'd-m-Y' ) );
	
	$exito		= lista_socios_fechas( $fecha_ini, $fecha_fin, $pag_busqueda );
	$paginas	= paginado( $exito['num'], $seccion, $item );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-user"></span> Lista de Socios con pagos vencidos</h4>
	</div>
</div>

<hr/>

<form method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-2">Búsqueda</label>
		<div class="col-md-4"><input type="text" name="pag_busqueda" class="form-control" value="<?= $pag_busqueda ?>" autofocus="on" /></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Fecha anterior</label>
		<div class="col-md-4">
			<input type="text" class="form-control" value="<?= $fecha_ini ?>" name="pag_fechai" id="rango_ini" />
		</div>
	</div>	
	
	
	<div class="row">
		<label class="col-md-2">Fecha actual</label>
		<div class="col-md-4">
			<input type="text" class="form-control" value="<?= $fecha_fin ?>" name="pag_fechaf" id="rango_fin" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-2 col-md-4">
			<input type="submit" name="enviar" class="btn btn-primary" value="Buscar" />
		</div>
	</div>
</form>

<?= $exito['msj'] ?>

<?= $paginas ?>