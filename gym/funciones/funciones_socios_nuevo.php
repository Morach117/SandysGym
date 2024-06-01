<?php
    function guardar_nuevo_socio()
    {
        global $conexion, $id_usuario, $id_empresa, $id_consorcio;

        $mensaje = array();
        $correo = request_var('soc_correo', '');

        $datos_sql = array(
            'soc_nombres'           => strtoupper(request_var('soc_nombres', '')),
            'soc_apepat'            => strtoupper(request_var('soc_apepat', '')),
            'soc_apemat'            => strtoupper(request_var('soc_apemat', '')),
            'soc_genero'            => request_var('soc_genero', ''),
            'soc_turno'             => request_var('soc_turno', ''),
            'soc_direccion'         => strtoupper(request_var('soc_direccion', '')),
            'soc_tel_fijo'          => request_var('soc_tel_fijo', ''),
            'soc_tel_cel'           => request_var('soc_tel_cel', ''),
            'soc_correo'            => $correo,
            'soc_emer_tel'          => request_var('soc_emer_tel', ''),
            'soc_observaciones'     => strtoupper(request_var('soc_observaciones', '')),
            'soc_descuento'         => request_var('soc_descuento', ''), // Nuevo campo de descuento
            'soc_fecha_nacimiento'  => request_var('soc_fecha_nacimiento', ''), // Nuevo campo de fecha de nacimiento
            'soc_id_usuario'        => $id_usuario,
            'soc_id_empresa'        => $id_empresa,
            'soc_id_consorcio'      => $id_consorcio
        );

        if (!empty($correo)) {
            // Verificar si el correo ya existe en la base de datos
            $correo_escaped = mysqli_real_escape_string($conexion, $correo);
            $correo_query = "SELECT COUNT(*) as total FROM san_socios WHERE soc_correo = '$correo_escaped'";
            $correo_resultado = mysqli_query($conexion, $correo_query);

            if ($correo_resultado) {
                $correo_fila = mysqli_fetch_assoc($correo_resultado);

                if ($correo_fila['total'] > 0) {
                    $mensaje['num'] = 2;
                    $mensaje['msj'] = "El correo ingresado ya ha sido capturado para otro socio, es necesario cambiarlo.";
                    return $mensaje;
                }
            } else {
                $mensaje['num'] = 3;
                $mensaje['msj'] = "Error en la consulta del correo. " . mysqli_error($conexion);
                return $mensaje;
            }
        }

        $query = construir_insert('san_socios', $datos_sql);
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            $mensaje['num'] = 1;
            $mensaje['msj'] = "Registro guardado correctamente.";
        } else {
            $mensaje['num'] = 3;
            $mensaje['msj'] = "No se ha podido guardar la información de este socio. " . mysqli_error($conexion);
        }

        return $mensaje;
    }
?>
