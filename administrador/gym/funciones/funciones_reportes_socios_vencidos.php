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
                        soc_tel_cel AS telefono
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
                        </tr>
                   </thead>
                   <tbody>";

        while ($fila = mysqli_fetch_assoc($resultado)) {
            if (file_exists("../../imagenes/avatar/$fila[id_socio].jpg"))
                $fotografia = "<img src='../../imagenes/avatar/$fila[id_socio].jpg' class='img-responsive' alt='$fila[id_socio]' width='50px' />";
            else
                $fotografia = "<img src='../../imagenes/avatar/noavatar.jpg' class='img-responsive' width='50px' alt='noavatar' />";

            $datos .= "<tr>
                            <td>{$contador}</td>
                            <td>{$fila['id_socio']}</td>
                            <td>{$fila['apellidos']} {$fila['nombres']}</td>
                            <td>{$fila['correo']}</td>
                            <td>{$fila['telefono']}</td>
                            <td>{$fila['status_pago']}</td>
                            <td>{$fotografia}</td>
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
