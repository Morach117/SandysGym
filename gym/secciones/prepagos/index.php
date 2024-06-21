<?php
$pag_busqueda = request_var('pag_busqueda', '');
$pag_opciones = request_var('pag_opciones', 0);

$var_exito = lista_socios();
$paginas = paginado($var_exito['num'], 'prepagos');
?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-user"></span> Lista de Socios
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
                    <th>Nombre Completo</th>

                    <th>Monedero</th> <!-- Nuevo encabezado para el monedero -->
                </tr>
            </thead>
            
            <tbody id="lista_socios">
                <?= $var_exito['msj'] ?>
            </tbody>
        </table>
    </div>
</div>

<?= $paginas ?>

