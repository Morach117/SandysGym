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



function lista_promociones()
{
    global $conexion, $gbl_paginado;

    $pag_busqueda = request_var('pag_busqueda', '');
    $pag_opciones = request_var('pag_opciones', 0);

    $datos = "";
    $pagina = (request_var('pag', 1) - 1) * $gbl_paginado;
    $fecha_actual = date('Y-m-d');
    $colspan = 6; // Incrementado para agregar el botón
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
            $query_codigos = "SELECT codigo_generado, status FROM san_codigos WHERE id_promocion = $id_promocion";
            $resultado_codigos = mysqli_query($conexion, $query_codigos);
            
            // Construir la lista de códigos
            $codigos = "";
            if ($resultado_codigos) {
                while ($row = mysqli_fetch_assoc($resultado_codigos)) {
                    $codigo = $row['codigo_generado'];
                    $status = $row['status'];
                    if ($status == 0) {
                        // El código ha sido usado
                        $codigos .= "<li>$codigo - El código ha sido usado</li>";
                    } else {
                        // El código está activo
                        $codigos .= "<li>$codigo</li>";
                    }
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





?>

