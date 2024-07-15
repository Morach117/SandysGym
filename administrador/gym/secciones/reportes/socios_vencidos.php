<?php
    $pag_busqueda    = request_var('pag_busqueda', '');
    
    $fecha_hoy       = strtotime(date('d-m-Y'));
    $fecha_hoy       = strtotime('-20 day', $fecha_hoy);
    
    $fecha_ini       = request_var('pag_fechai', date('d-m-Y', $fecha_hoy));
    $fecha_fin       = request_var('pag_fechaf', date('d-m-Y'));
    
    $exito           = lista_socios_fechas($fecha_ini, $fecha_fin, $pag_busqueda);
    $paginas         = paginado($exito['num'], $seccion, $item);
?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-gift"></span> Lista de Socios con pagos vencidos
        </h4>
    </div>
</div>

<hr/>

<!-- Contenedor del formulario de búsqueda y la tabla pequeña -->
<div class="row">
    <div class="col-md-6">
<form method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>">
            <div class="row">
                <label class="col-md-4">Búsqueda</label>
                <div class="col-md-8">
                    <input type="text" name="pag_busqueda" class="form-control" value="<?= $pag_busqueda ?>" autofocus="on" />
                </div>
            </div>
            <div class="row">
                <label class="col-md-4">Fecha anterior</label>
                <div class="col-md-8">
			<input type="text" class="form-control" value="<?= $fecha_ini ?>" name="pag_fechai" id="rango_ini" />

                </div>
            </div>
            <div class="row">
                <label class="col-md-4">Fecha actual</label>
                <div class="col-md-8">
			<input type="text" class="form-control" value="<?= $fecha_fin ?>" name="pag_fechaf" id="rango_fin" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-offset-4 col-md-8">
                    <input type="submit" name="enviar" class="btn btn-primary" value="Buscar" />
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla pequeña con scroll para promociones -->
    <div class="col-md-6" style="height: 200px; overflow-y: auto;">
        <h5 class="text-info"><span class="glyphicon glyphicon-tag"></span> Promociones Disponibles</h5>
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Activo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fecha_hoy_mysql = date('Y-m-d');

                $query_promociones = "
                    SELECT san_codigos.id_codigo, san_codigos.codigo_generado, san_codigos.is_active
                    FROM san_codigos
                    INNER JOIN san_promociones ON san_codigos.id_promocion = san_promociones.id_promocion
                    WHERE san_codigos.is_active = 0
                    AND san_promociones.vigencia_inicial <= '$fecha_hoy_mysql'
                    AND san_promociones.vigencia_final >= '$fecha_hoy_mysql'
                ";
                $resultado_promociones = mysqli_query($conexion, $query_promociones);
                $contador = 1;
                if ($resultado_promociones) {
                    while ($row = mysqli_fetch_assoc($resultado_promociones)) {
                        $checked = $row['is_active'] ? 'checked' : '';
                        echo "<tr>
                                <td>{$contador}</td>
                                <td>{$row['codigo_generado']}</td>
                                <td><input type='checkbox' class='promocion-checkbox' data-id='{$row['id_codigo']}' $checked></td>
                              </tr>";
                        $contador++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?= $exito['msj'] // Aquí se imprime el contenido de la tabla principal ?>

<?= $paginas ?>

<script>
$(document).ready(function() {
    // Manejar el evento de cambio en los checkboxes de las promociones
    $('.promocion-checkbox').change(function() {
        var id_codigo = $(this).data('id');
        var is_active = $(this).is(':checked') ? 1 : 0;

        // Enviar solicitud AJAX para actualizar el estado de la promoción
        $.ajax({
            url: window.location.href, // URL actual de la página
            type: 'POST',
            data: {
                ajax: 'actualizar_codigo',
                id_codigo: id_codigo,
                is_active: is_active
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    alert('Estado de la promoción actualizado con éxito.');
                } else {
                    alert('Error al actualizar el estado de la promoción.');
                }
            },
            error: function() {
                alert('Ocurrió un error en la solicitud.');
            }
        });
    });
});
</script>

<?php
// Procesar la actualización del estado de la promoción vía AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == 'actualizar_codigo') {
    $id_codigo = intval($_POST['id_codigo']);
    $is_active = intval($_POST['is_active']);
    $query = "UPDATE san_codigos SET is_active = $is_active WHERE id_codigo = $id_codigo";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}
?>
