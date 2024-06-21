<?php


function lista_socios()
{
    global $conexion, $id_empresa, $gbl_paginado;
    
    $pag_busqueda = request_var('pag_busqueda', '');
    $pag_opciones = request_var('pag_opciones', 0);
    
    $datos      = "";
    $pagina     = (request_var('pag', 1) - 1) * $gbl_paginado;
    $fecha_mov  = date('Y-m-d');
    $colspan    = 4; // Colspan updated to match the new table structure
    $var_total  = 0;
    $var_exito  = array();
    
    // Parameters for pagination
    $pag_bloque = request_var('blq', 0);
    $pag_pag    = request_var('pag', 0);
    
    $parametros = "";
    
    if ($pag_opciones)
        $parametros .= "&pag_opciones=$pag_opciones";
    
    if ($pag_busqueda)
        $parametros .= "&pag_busqueda=$pag_busqueda";
    
    if ($pag_bloque)
        $parametros .= "&blq=$pag_bloque";
    
    if ($pag_pag)
        $parametros .= "&pag=$pag_pag";
    
    // Queries
    if ($pag_busqueda) {
        $limite     = 'LIMIT 0, 50';
        $condicion  = "AND (LOWER(CONCAT(soc_apepat, ' ', soc_apemat, ' ', soc_nombres)) LIKE LOWER('%$pag_busqueda%'))";
    } else {
        $limite     = "LIMIT $pagina, $gbl_paginado";
        $condicion  = "";
    }
    
    if ($pag_opciones == 1)
        $condicion .= " AND DATE_FORMAT(soc_fecha_captura, '%Y-%m-%d') = '$fecha_mov' ";
    
    if ($pag_opciones == 2)
        $condicion .= " AND DATE_FORMAT(pag_fecha_pago, '%Y-%m-%d') = '$fecha_mov' ";
    
    if ($pag_opciones == 3)
        $condicion .= " AND DATE_FORMAT(pag_fecha_fin, '%Y-%m-%d') = '$fecha_mov' ";
    
    // Query for pagination count
    $query      = "SELECT COUNT(*) AS total
                   FROM san_socios
                   LEFT JOIN san_pagos ON pag_id_socio = soc_id_socio
                   AND pag_fecha_fin = (SELECT pag_fecha_fin
                                        FROM san_pagos
                                        WHERE pag_id_socio = soc_id_socio
                                        AND '$fecha_mov' <= pag_fecha_fin 
                                        AND pag_status = 'A'
                                        ORDER BY pag_fecha_fin DESC 
                                        LIMIT 0, 1)
                   AND pag_status = 'A'
                   WHERE soc_id_empresa = $id_empresa
                   $condicion
                   GROUP BY soc_id_socio";
    
    $resultado = mysqli_query($conexion, $query);
    
    if ($resultado)
        $var_total = mysqli_num_rows($resultado);
    
    mysqli_free_result($resultado);
    
    // Query for data retrieval
    $query      = "SELECT soc_id_socio AS id_socio,
                          CONCAT(soc_apepat, ' ', soc_apemat, ' ', soc_nombres) AS socio,
                          soc_correo,
                          DATE_FORMAT(soc_fecha_nacimiento, '%d-%m-%Y') AS fecha_nacimiento,
                          IF(pag_id_pago > 0, CONCAT(DATE_FORMAT(pag_fecha_ini, '%d-%m-%Y'), ' al ', DATE_FORMAT(pag_fecha_fin, '%d-%m-%Y')), 'Pago Vencido') AS status_pago,
                          ROUND(IFNULL(soc_mon_saldo, 0), 2) AS saldo
                   FROM san_socios
                   LEFT JOIN san_pagos ON pag_id_socio = soc_id_socio
                   AND pag_fecha_fin = (SELECT pag_fecha_fin
                                        FROM san_pagos
                                        WHERE pag_id_socio = soc_id_socio
                                        AND '$fecha_mov' <= pag_fecha_fin 
                                        AND pag_status = 'A'
                                        ORDER BY pag_fecha_fin DESC 
                                        LIMIT 0, 1)
                   AND pag_status = 'A'
                   WHERE soc_id_empresa = $id_empresa
                   $condicion
                   GROUP BY soc_id_socio
                   ORDER BY pag_fecha_fin DESC, socio
                   $limite";
    
    $resultado = mysqli_query($conexion, $query);
    
    if ($resultado) {
        $i = 1;
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos .= "<tr onclick='location.href=\".?s=prepagos&i=editar&id_socio=$fila[id_socio]\"'>
                           <td>" . ($pagina + $i) . "</td>
                           <td>$fila[socio]</td>
                           <td class='text-right'>$$fila[saldo]</td>
                       </tr>";
            $i++;
        }
    } else {
        $datos = "<tr><td colspan='$colspan'>Ocurrió un problema al obtener los datos. " . mysqli_error($conexion) . "</td></tr>";
    }
    
    if (!$datos) {
        $datos = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
    }
    
    $var_exito['num'] = $var_total;
    $var_exito['msj'] = $datos;
    
    return $var_exito;
}

?>
