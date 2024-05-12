<?php
function obtener_servicios($mostrar_estatus = false)
{
    global $conexion, $id_giro, $id_consorcio;

    $datos    = '';
    $colspan  = 8;

    $query    = "SELECT ser_id_servicio AS id_servicio,
                        ser_clave AS clave,
                        ser_status AS status,
                        CASE ser_status
                            WHEN 'A' THEN 'Activo'
                            WHEN 'D' THEN 'Descontinuado'
                        END AS status_desc,
                        ser_descripcion AS descripcion,
                        ser_tipo AS tipo,
                        ROUND( ser_cuota, 2 ) AS cuota,
                        IF( ser_dias, ser_dias, 'N/A' ) AS dias,
                        IF( ser_meses, ser_meses, 'N/A' ) AS meses
                FROM san_servicios
                WHERE ser_id_giro = $id_giro
                AND ser_id_consorcio = $id_consorcio
                ORDER BY status,
                        tipo,
                        descripcion";

    $resultado    = mysqli_query($conexion, $query);

    if ($resultado) {
        $i = 1;
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $class = ($fila['status'] == 'D') ? 'danger' : '';
            
            // Verificar si se debe mostrar el estatus
            $estatus = '';
            if ($mostrar_estatus) {
                $estatus = "<td>".$fila['status_desc']."</td>";
            }

            // Concatenar datos
            $datos    .= "<tr class='$class' onclick='location.href=\".?s=catalogos&i=servicios_editar&id_servicio=$fila[id_servicio]\"'>
                            <td>$i</td>
                            <td>".$fila['clave']."</td>
                            <td>".$fila['descripcion']."</td>
                            <td>".$fila['tipo']."</td>
                            $estatus
                            <td class='text-right'>$".$fila['cuota']."</td>
                            <td class='text-right'>".$fila['dias']."</td>
                            <td class='text-right'>".$fila['meses']."</td>
                        </tr>";
            $i++;
        }
    } else {
        $datos    = "<tr><td colspan='$colspan'>No se pudo obtener el reporte. ".mysqli_error($conexion)."</td></tr>";
    }

    if (!$datos) {
        $datos    = "<tr><td colspan='$colspan'>No hay datos.</td></tr>";
    }

    return $datos;
}
?>
