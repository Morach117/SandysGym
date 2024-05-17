<?php
function obtener_socios_cumpleaños()
{
    global $conexion, $id_empresa, $gbl_paginado;
    
    // Obtener la fecha de inicio y fin del mes actual
    $fecha_inicio_mes = date('Y-m-01');
    $fecha_fin_mes = date('Y-m-t');

    // Consulta para obtener los socios que cumplen años este mes
    $query = "SELECT 
                soc_id_socio AS id_socio,
                CONCAT(soc_apepat, ' ', soc_apemat, ' ', soc_nombres) AS nombres,
                soc_fecha_nacimiento
            FROM 
                san_socios
            WHERE 
                soc_id_empresa = $id_empresa AND
                MONTH(soc_fecha_nacimiento) = MONTH(CURRENT_DATE()) AND
                DAY(soc_fecha_nacimiento) >= DAY('$fecha_inicio_mes') AND
                DAY(soc_fecha_nacimiento) <= DAY('$fecha_fin_mes')";
    
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $datos = "";
        $i = 1;
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos .= "<tr>
                        <td>$i</td>
                        <td>$fila[nombres]</td>
                        <td>$fila[soc_fecha_nacimiento]</td>
                      </tr>";
            $i++;
        }
        // Liberar el resultado
        mysqli_free_result($resultado);

        // Si no hay datos
        if ($i == 1) {
            $datos = "<tr><td colspan='3'>No hay socios que cumplan años este mes.</td></tr>";
        }

        return $datos;
    } else {
        // Si hay un error en la consulta
        return "<tr><td colspan='3'>Ocurrió un problema al obtener los datos de los socios: " . mysqli_error($conexion) . "</td></tr>";
    }
}

// Incluye este código en la parte donde deseas mostrar la lista de cumpleaños
$gbl_paginado = 10; // Establece aquí la cantidad de registros por página
$var_exito_cumpleaños = obtener_socios_cumpleaños();
?>



<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-gift"></span> Lista de Cumpleaños
        </h4>
    </div>
</div>

<hr/>


<div class="row">
    <div class="col-md-12">
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Fecha de Nacimiento</th>

                </tr>
            </thead>

            <tbody id="lista_cumpleaños">
            <?php echo $var_exito_cumpleaños; ?>

            </tbody>
        </table>
    </div>
</div>

