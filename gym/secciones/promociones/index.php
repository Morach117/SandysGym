<?php
    //$pag_busqueda_promociones = request_var('pag_busqueda_promociones', '');
    //$pag_opciones_promociones = request_var('pag_opciones_promociones', 0);

   // $opciones_promociones = opciones_busqueda_promociones($pag_opciones_promociones);
    $var_exito_promociones = lista_promociones();
    $paginas_promociones = paginado($var_exito_promociones['num'], 'promociones');
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
        <label class="col-md-2">Opciones</label>
        <div class="col-md-4">
            <select name="pag_opciones_promociones" class="form-control">
                <option value="">Todos...</option>
                </select>
        </div>
    </div>

    <div class="row">
        <label class="col-md-2">Búsqueda</label>
        <div class="col-md-4"><input type="text" name="pag_busqueda_promociones" class="form-control" autofocus="on" /></div>
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
                </tr>
            </thead>

            <tbody id="lista_promociones">
                <?= $var_exito_promociones['msj'] ?>
            </tbody>
        </table>
    </div>
</div>

<?= $paginas_promociones ?>
