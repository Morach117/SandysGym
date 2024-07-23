<?php
function obtener_promociones_usadas($mes = null)
{
    global $conexion, $id_empresa;

    // Si no se pasa un mes, se utiliza el mes actual
    if (is_null($mes)) {
        $mes = date('m');
    }

    // Consulta para obtener las promociones usadas junto con el nombre del socio y la fecha de uso
    $query = "SELECT 
                s.soc_id_socio AS id_socio,
                CONCAT(s.soc_apepat, ' ', s.soc_apemat, ' ', s.soc_nombres) AS nombres,
                cu.codigo_generado AS codigo_usado,
                DATE_FORMAT(cu.fecha_usado, '%d-%m-%Y') AS fecha_usado
            FROM 
                san_codigos_usados cu
            INNER JOIN 
                san_socios s ON cu.id_socio = s.soc_id_socio
            WHERE 
                cu.id_empresa = $id_empresa
            AND 
                MONTH(cu.fecha_usado) = $mes
            ORDER BY 
                cu.fecha_usado DESC";
    
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $datos = "";
        $i = 1;
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos .= "<tr>
                        <td>$i</td>
                        <td>$fila[nombres]</td>
                        <td>$fila[codigo_usado]</td>
                        <td>$fila[fecha_usado]</td>
                      </tr>";
            $i++;
        }
        // Liberar el resultado
        mysqli_free_result($resultado);

        // Si no hay datos
        if ($i == 1) {
            $datos = "<tr><td colspan='4'>No hay promociones usadas para el mes seleccionado.</td></tr>";
        }

        return $datos;
    } else {
        // Si hay un error en la consulta
        return "<tr><td colspan='4'>Ocurrió un problema al obtener los datos: " . mysqli_error($conexion) . "</td></tr>";
    }
}

// Obtener el mes seleccionado del formulario, por defecto es el mes actual
$mes_seleccionado = isset($_POST['mes']) ? $_POST['mes'] : date('m');

// Incluye este código en la parte donde deseas mostrar la lista de promociones usadas
$var_exito_promociones = obtener_promociones_usadas($mes_seleccionado);
?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-tags"></span> Lista de Promociones Usadas
        </h4>
    </div>
</div>

<hr/>

<!-- Formulario para seleccionar el mes -->
<form method="post" action="">
    <div class="row">
        <div class="col-md-4">
            <label for="mes">Seleccionar Mes:</label>
            <select name="mes" id="mes" class="form-control" onchange="this.form.submit()">
                <?php
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                foreach ($meses as $num => $nombre) {
                    $selected = ($num == $mes_seleccionado) ? 'selected' : '';
                    echo "<option value='$num' $selected>$nombre</option>";
                }
                ?>
            </select>
        </div>
    </div>
</form>

<hr/>

<div class="row">
    <div class="col-md-12">
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Código Usado</th>
                    <th>Fecha</th>
                </tr>
            </thead>

            <tbody id="lista_promociones_usadas">
            <?php echo $var_exito_promociones; ?>
            </tbody>
        </table>
    </div>
</div>
