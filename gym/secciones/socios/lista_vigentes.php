<?php
	$pag_busqueda	= request_var( 'pag_busqueda', '' );
	
	$exito		= lista_socios_fechas( $pag_busqueda );
	$paginas	= paginado( $exito['num'], $seccion, $item );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-user"></span> Lista de Socios con pagos vigentes</h4>
	</div>
</div>

<hr/>

<form method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
	<div class="row">
		<label class="col-md-2">Búsqueda</label>
		<div class="col-md-4"><input type="text" name="pag_busqueda" class="form-control" value="<?= $pag_busqueda ?>" autofocus="on" /></div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-2 col-md-4">
			<input type="submit" name="enviar" class="btn btn-primary" value="Buscar" />
		</div>
	</div>
</form>

<?= $exito['msj'] ?>

<?= $paginas ?>