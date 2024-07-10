<?php

    $var_exito_promociones = lista_promociones();
    $paginas_promociones = paginado($var_exito_promociones['num'], 'promociones');
    $pag_busqueda	= request_var( 'pag_busqueda', '' );

?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-gift"></span> Lista de Promociones
        </h4>
    </div>
</div>

<hr/>

<form method="post" action=".?s=<?= $seccion ?>">
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

<div class="row">
    <div class="col-md-12">
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Vigencia Inicial</th>
                    <th>Vigencia Final</th>
                    <th>Porcentaje Descuento</th>
                    <th>Ver</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody id="lista_promociones">
                <?= $var_exito_promociones['msj'] ?>
            </tbody>
        </table>
    </div>
</div>

<?= $paginas_promociones ?>
