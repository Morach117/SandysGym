<?php
function guardar_nueva_promocion()
{
    global $conexion;

    $mensaje = array();

    // Obtener los datos del formulario
    $titulo_promocion = strtoupper(request_var('titulo_promocion', ''));
    $vigencia_inicial = request_var('vigencia_inicial', '');
    $vigencia_final = request_var('vigencia_final', '');
    $porcentaje_descuento = request_var('porcentaje_descuento', '');
    $utilizado = request_var('utilizado', '');
    $tipo_promocion = request_var('tipo_promocion', '');
    $cantidad_codigos = request_var('cantidad_codigos', '');

    // Insertar la nueva promoción en la base de datos
    $datos_sql = array(
        'titulo' => $titulo_promocion,
        'fecha_generada' => date('Y-m-d'),
        'vigencia_inicial' => $vigencia_inicial,
        'vigencia_final' => $vigencia_final,
        'porcentaje_descuento' => $porcentaje_descuento,
        'utilizado' => $utilizado,
        'tipo_promocion' => $tipo_promocion
    );

    $query = construir_insert('san_promociones', $datos_sql);
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $id_promocion = mysqli_insert_id($conexion); // Obtener el ID de la promoción recién insertada

        // Generar los códigos según el tipo de promoción
        if ($tipo_promocion == 'Individual') {
            // Generar la cantidad de códigos especificada
            for ($i = 0; $i < $cantidad_codigos; $i++) {
                $codigo_generado = generar_codigo_promocion();
                $query_codigo = "INSERT INTO san_codigos (codigo_generado, id_promocion) VALUES ('$codigo_generado', $id_promocion)";
                $resultado_codigo = mysqli_query($conexion, $query_codigo);
                if (!$resultado_codigo) {
                    $mensaje['num'] = 3;
                    $mensaje['msj'] = "Error al generar los códigos individuales de promoción.";
                    return $mensaje;
                }
            }
        } else if ($tipo_promocion == 'Masivo') {
            // Generar un único código para promoción masiva
            $codigo_generado = generar_codigo_promocion();
            $query_codigo = "INSERT INTO san_codigos (codigo_generado, id_promocion) VALUES ('$codigo_generado', $id_promocion)";
            $resultado_codigo = mysqli_query($conexion, $query_codigo);
            if (!$resultado_codigo) {
                $mensaje['num'] = 3;
                $mensaje['msj'] = "Error al generar el código masivo de promoción.";
                return $mensaje;
            }
        }

        $mensaje['num'] = 1;
        $mensaje['msj'] = "Promoción registrada correctamente.";
    } else {
        $mensaje['num'] = 3;
        $mensaje['msj'] = "No se ha podido guardar la información de la promoción. " . mysqli_error($conexion);
    }

    return $mensaje;
}

// Función para generar un código de promoción
function generar_codigo_promocion()
{
    // Se genera un código aleatorio de 10 caracteres
    $codigo = '';
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $longitud = strlen($caracteres);
    for ($i = 0; $i < 10; $i++) {
        $codigo .= $caracteres[rand(0, $longitud - 1)];
    }
    return $codigo;
}
?>
