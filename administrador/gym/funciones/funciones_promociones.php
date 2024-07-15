<?php
function validar_registro_promociones()
{
    $validar = array(
        'titulo_promocion' => array('tipo' => 'T', 'max' => 100, 'req' => 'S', 'for' => '', 'txt' => 'Título de la promoción'),
        'vigencia_inicial' => array('tipo' => 'D', 'max' => 10, 'req' => 'S', 'for' => '', 'txt' => 'Vigencia inicial'),
        'vigencia_final' => array('tipo' => 'D', 'max' => 10, 'req' => 'S', 'for' => '', 'txt' => 'Vigencia final'),
        'porcentaje_descuento' => array('tipo' => 'N', 'max' => 3, 'req' => 'S', 'for' => '', 'txt' => 'Porcentaje de descuento'),
        'utilizado' => array('tipo' => 'T', 'max' => 1, 'req' => 'S', 'for' => '', 'txt' => 'Utilizado'),
        'tipo_promocion' => array('tipo' => 'T', 'max' => 10, 'req' => 'S', 'for' => '', 'txt' => 'Tipo de promoción')
    );

    $exito = validar_php($validar);

    return $exito;
}

function opciones_busqueda($default = 0)
{
    $busqueda = array(
        1 => 'Promociones agregadas hoy',
        2 => 'Promociones vigentes hoy',
    );
    $opc_busqueda = "";

    foreach ($busqueda as $ind => $opcion) {
        if ($default == $ind)
            $opc_busqueda .= "<option selected value='$ind'>$opcion</option>";
        else
            $opc_busqueda .= "<option value='$ind'>$opcion</option>";
    }

    return $opc_busqueda;
}

function eliminar_promocion($id_promocion)
{
    global $conexion;

    $query = "DELETE FROM san_promociones WHERE id_promocion = $id_promocion";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        return true;
    } else {
        return false;
    }
}

function actualizar_codigo($id_codigo, $is_active)
{
    global $conexion;

    $query = "UPDATE san_codigos SET is_active = $is_active WHERE id_codigo = $id_codigo";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        return true;
    } else {
        return false;
    }
}

function lista_promociones()
{
    global $conexion, $gbl_paginado;

    $pag_busqueda = request_var('pag_busqueda', '');
    $pag_opciones = request_var('pag_opciones', 0);

    $datos = "";
    $pagina = (request_var('pag', 1) - 1) * $gbl_paginado;
    $fecha_actual = date('Y-m-d');
    $colspan = 8; // Incrementado para agregar el checkbox y el botón de eliminar
    $var_total = 0;
    $var_exito = array();

    // Para el paginado
    $pag_bloque = request_var('blq', 0);
    $pag_pag = request_var('pag', 0);

    $parametros = "";

    if ($pag_opciones)
        $parametros .= "&pag_opciones=$pag_opciones";

    if ($pag_busqueda)
        $parametros .= "&pag_busqueda=$pag_busqueda";

    if ($pag_bloque)
        $parametros .= "&blq=$pag_bloque";

    if ($pag_pag)
        $parametros .= "&pag=$pag_pag";

    // Querys
    if ($pag_busqueda) {
        $limite = 'LIMIT 0, 50';
        $condicion = "AND LOWER(titulo) LIKE LOWER('%$pag_busqueda%')";
    } else {
        $limite = "LIMIT $pagina, $gbl_paginado";
        $condicion = "";
    }

    // Query para obtener el total de promociones
    $query_total = "SELECT COUNT(*) AS total FROM san_promociones WHERE 1=1 $condicion";
    $resultado_total = mysqli_query($conexion, $query_total);

    if ($resultado_total)
        $var_total = mysqli_fetch_assoc($resultado_total)['total'];

    mysqli_free_result($resultado_total);

    // Query para obtener los datos de las promociones
    $query_promociones = "SELECT * FROM san_promociones WHERE 1=1 $condicion ORDER BY fecha_generada DESC $limite";
    $resultado_promociones = mysqli_query($conexion, $query_promociones);

    if ($resultado_promociones) {
        $i = 1;
        while ($fila = mysqli_fetch_assoc($resultado_promociones)) {
            // Obtener los códigos relacionados con la promoción actual
            $id_promocion = $fila['id_promocion'];
            $query_codigos = "SELECT id_codigo, codigo_generado, status, is_active FROM san_codigos WHERE id_promocion = $id_promocion";
            $resultado_codigos = mysqli_query($conexion, $query_codigos);
            
            // Construir la lista de códigos
            $codigos = "";
            if ($resultado_codigos) {
                while ($row = mysqli_fetch_assoc($resultado_codigos)) {
                    $id_codigo = $row['id_codigo'];
                    $codigo = $row['codigo_generado'];
                    $status = $row['status'];
                    $is_active = $row['is_active'];
                    $checked = $is_active ? 'checked' : '';
            
                    $codigos .= "<li style='font-size: 14px;'>
                                    <span style='font-size: 16px;'>$codigo</span> - " . ($status == 0 ? 'El código ha sido usado' : 'El código está activo') . "
                                    <input type='checkbox' class='codigo-checkbox' data-id='$id_codigo' $checked>
                                 </li>";
                }
                mysqli_free_result($resultado_codigos);
            }
            
            

            // Si no hay códigos, mostrar un mensaje
            if (empty($codigos)) {
                $codigos = "<li>No hay códigos disponibles para esta promoción.</li>";
            }

            // Agregar botón y modal para mostrar códigos relacionados
            $datos .= "<tr>
                        <td>" . ($pagina + $i) . "</td>
                        <td>$fila[titulo]</td>
                        <td>$fila[vigencia_inicial]</td>
                        <td>$fila[vigencia_final]</td>
                        <td>$fila[porcentaje_descuento]%</td>
                        <td>
                            <button type='button' class='btn btn-info' data-toggle='modal' data-target='#modalCodigos_$i'>Ver Códigos</button>
                            <!-- Modal -->
                            <div class='modal fade' id='modalCodigos_$i' tabindex='-1' role='dialog' aria-labelledby='modalCodigosLabel_$i' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title' id='modalCodigosLabel_$i'>Códigos Relacionados</h5>
                                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                <span aria-hidden='true'>&times;</span>
                                            </button>
                                        </div>
                                        <div class='modal-body'>
                                            <ul>
                                                $codigos
                                            </ul>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form method='POST' action=''>
                                <input type='hidden' name='id_promocion' value='$fila[id_promocion]'>
                                <button type='submit' name='eliminar_promocion' class='btn btn-danger'>Eliminar</button>
                            </form>
                        </td>
                      </tr>";
            $i++;
        }
    } else {
        $datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener los datos. " . mysqli_error($conexion) . "</td></tr>";
    }

    if (!$datos)
        $datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";

    $var_exito['num'] = $var_total;
    $var_exito['msj'] = $datos;

    return $var_exito;
}

// Procesar la eliminación de la promoción si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_promocion'])) {
    $id_promocion = intval($_POST['id_promocion']);
    if (eliminar_promocion($id_promocion)) {
        echo "<script>
                alert('Promoción eliminada con éxito.');
              </script>";
              header("Location: ?s=promociones");
              
    } else {
        echo "<script>alert('Error al eliminar la promoción.');</script>";
    }
}

// Procesar la actualización del estado del código vía AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == 'actualizar_codigo') {
    $id_codigo = intval($_POST['id_codigo']);
    $is_active = intval($_POST['is_active']);
    if (actualizar_codigo($id_codigo, $is_active)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}
?>


<script>
$(document).ready(function() {
    // Manejar el evento de clic en el botón de eliminar
    $('.eliminar-promocion').click(function() {
        var id_promocion = $(this).data('id');

        // Confirmación antes de eliminar
        if (confirm('¿Estás seguro de que deseas eliminar esta promoción?')) {
            // Enviar solicitud AJAX para eliminar la promoción
            $.ajax({
                url: window.location.href, // URL actual de la página
                type: 'POST',
                data: {
                    ajax: 'eliminar_promocion',
                    id_promocion: id_promocion
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        alert('Promoción eliminada con éxito.');
                        // Recargar la tabla de promociones
                        location.reload();
                    } else {
                        alert('Error al eliminar la promoción.');
                    }
                },
                error: function() {
                    alert('Ocurrió un error en la solicitud.');
                }
            });
        }
    });

    // Manejar el evento de cambio en los checkboxes de los códigos
    $('.codigo-checkbox').change(function() {
        var id_codigo = $(this).data('id');
        var is_active = $(this).is(':checked') ? 1 : 0;

        // Enviar solicitud AJAX para actualizar el estado del código
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
                    alert('Estado del código actualizado con éxito.');
                } else {
                    alert('Error al actualizar el estado del código.');
                }
            },
            error: function() {
                alert('Ocurrió un error en la solicitud.');
            }
        });
    });
});
</script>

