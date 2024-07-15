<?php
function lista_socios_fechas($rango_ini, $rango_fin, $pag_busqueda)
{
    global $conexion, $id_empresa, $gbl_paginado;

    $datos = "";
    $condicion = "";
    $contador = 1;
    $exito = array();
    $pagina = (request_var('pag', 1) - 1) * $gbl_paginado;
    $bloque = request_var('blq', 0);
    $pag = request_var('pag', 0);

    $parametros = "&pag_fechai=$rango_ini&pag_fechaf=$rango_fin&item=lista_vencidos";

    if ($pag_busqueda)
        $parametros .= "&pag_busqueda=$pag_busqueda";

    if ($bloque)
        $parametros .= "&blq=$bloque";

    if ($pag)
        $parametros .= "&pag=$pag";

    $rango_ini = fecha_formato_mysql($rango_ini);
    $rango_fin = fecha_formato_mysql($rango_fin);

    if ($pag_busqueda) {
        $condicion = "AND (
                            LOWER( CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) ) LIKE LOWER( '%$pag_busqueda%' )
                        )";
    }

    $query = "  SELECT      soc_id_socio AS id_socio
                FROM        san_socios
                INNER JOIN  san_pagos ON pag_id_socio = soc_id_socio
                            AND pag_fecha_fin < '$rango_fin'
                            AND pag_status = 'A'
                            AND pag_fecha_fin = ( SELECT    pag_fecha_fin
                                                  FROM      san_pagos
                                                  WHERE     pag_id_socio = soc_id_socio
                                                            AND pag_status = 'A'
                                                  ORDER BY  pag_fecha_fin DESC 
                                                  LIMIT     0, 1 )
                WHERE       soc_id_empresa = $id_empresa
                            AND (is_active IS NULL OR is_active = '0000-00-00')
                            $condicion
                GROUP BY    soc_id_socio";

    $resultado = mysqli_query($conexion, $query);

    if ($resultado)
        $exito['num'] = mysqli_num_rows($resultado);

    $query = "  SELECT      soc_id_socio AS id_socio,
                        pag_id_pago AS id_pago,
                        soc_nombres AS nombres,
                        CONCAT(soc_apepat, ' ', soc_apemat) AS apellidos,
                        'Pago Vencido' AS status_pago,
                        IF(soc_imagen IS NULL OR soc_imagen = '', 'Sin nombre de archivo', soc_imagen) AS img,
                        soc_correo AS correo,
                        soc_tel_cel AS telefono,
                        is_active
                FROM        san_socios
                INNER JOIN  san_pagos ON pag_id_socio = soc_id_socio
                            AND pag_fecha_fin < '$rango_fin'
                            AND pag_status = 'A'
                            AND pag_fecha_fin = ( SELECT    pag_fecha_fin
                                                  FROM      san_pagos
                                                  WHERE     pag_id_socio = soc_id_socio
                                                            AND pag_status = 'A'
                                                  ORDER BY  pag_fecha_fin DESC 
                                                  LIMIT     0, 1 )
                WHERE       soc_id_empresa = $id_empresa
                            $condicion
                GROUP BY    soc_id_socio
                ORDER BY    IF(soc_tel_cel IS NOT NULL AND soc_tel_cel <> '', 0, 1), pag_fecha_fin DESC
                LIMIT       $pagina, $gbl_paginado";

    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $datos .= "<table class='table table-striped'>";
        $datos .= "<thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Vigencia</th>
                            <th>Foto</th>
                            <th>Activo</th>
                        </tr>
                   </thead>
                   <tbody>";

        $current_month = date('Y-m'); // Obtener el mes actual

        while ($fila = mysqli_fetch_assoc($resultado)) {
            if (file_exists("../../imagenes/avatar/$fila[id_socio].jpg"))
                $fotografia = "<img src='../../imagenes/avatar/$fila[id_socio].jpg' class='img-responsive' alt='$fila[id_socio]' width='50px' />";
            else
                $fotografia = "<img src='../../imagenes/avatar/noavatar.jpg' class='img-responsive' width='50px' alt='noavatar' />";

            // Verificar si el socio está activo en el mes actual
            $is_active_date = $fila['is_active'];
            $is_active = ($is_active_date !== '0000-00-00' && $is_active_date !== null && strpos($is_active_date, $current_month) === 0) ? 'checked' : '';

            $datos .= "<tr>
                            <td>{$contador}</td>
                            <td>{$fila['id_socio']}</td>
                            <td>{$fila['apellidos']} {$fila['nombres']}</td>
                            <td>{$fila['correo']}</td>
                            <td>{$fila['telefono']}</td>
                            <td>{$fila['status_pago']}</td>
                            <td>
                                <a href='.?s=reportes&i=fotografia&id_socio=$fila[id_socio]'>$fotografia</a>
                            </td>
                            <td>
                                <input type='checkbox' class='socio-checkbox' data-id='{$fila['id_socio']}' $is_active>
                            </td>
                          </tr>";

            $contador++;
        }

        $datos .= "</tbody></table>";

        if (isset($exito['num']) && $exito['num'] == 0) {
            $datos = "<div class='alert alert-info'>No hay datos</div>";
        }
    } else {
        $datos .= "<div class='alert alert-danger'>Error: " . mysqli_error($conexion) . "</div>";
    }

    $exito['msj'] = $datos;

    return $exito;
}
?>

<script>
$(document).ready(function() {
    // Manejar el evento de cambio en los checkboxes de los socios
    $('.socio-checkbox').change(function() {
        var id_socio = $(this).data('id');
        var is_active = $(this).is(':checked') ? 1 : 0;

        // Enviar solicitud AJAX para actualizar el estado del socio
        $.ajax({
            url: window.location.href, // URL actual de la página
            type: 'POST',
            data: {
                ajax: 'actualizar_socio',
                id_socio: id_socio,
                is_active: is_active
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    alert('Estado del socio actualizado con éxito.');
                } else {
                    alert('Error al actualizar el estado del socio.');
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
// Procesar la actualización del estado del socio vía AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == 'actualizar_socio') {
    $id_socio = intval($_POST['id_socio']);
    $is_active = $_POST['is_active'] ? date('Y-m-d') : '0000-00-00';

    // Actualizar el estado del socio con la fecha actual o restablecer la fecha
    $query = "UPDATE san_socios SET is_active = '$is_active' WHERE soc_id_socio = $id_socio";

    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}
?>
