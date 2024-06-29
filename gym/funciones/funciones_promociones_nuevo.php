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
    $servicios_permitidos = isset($_POST['servicios_permitidos']) ? $_POST['servicios_permitidos'] : array();

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

        // Insertar datos en la tabla san_descuentos_promociones
        if (is_array($servicios_permitidos)) {
            foreach ($servicios_permitidos as $id_servicio) {
                $query_descuentos = "INSERT INTO san_descuentos_promociones (id_promocion, id_servicio, permitir_descuento) VALUES ($id_promocion, $id_servicio, 1)";
                $resultado_descuentos = mysqli_query($conexion, $query_descuentos);
                if (!$resultado_descuentos) {
                    $mensaje['num'] = 3;
                    $mensaje['msj'] = "Error al guardar los datos de los descuentos de promoción.";
                    return $mensaje;
                }
            }
        }

        // Generar los códigos según el tipo de promoción
        if ($tipo_promocion == 'Individual') {
            // Generar la cantidad de códigos especificada
            for ($i = 0; $i < $cantidad_codigos; $i++) {
                $codigo_generado = generar_codigo_promocion();
                $query_codigo = "INSERT INTO san_codigos (codigo_generado, id_promocion, status) VALUES ('$codigo_generado', $id_promocion, 1)";
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
            $query_codigo = "INSERT INTO san_codigos (codigo_generado, id_promocion, status) VALUES ('$codigo_generado', $id_promocion, 1)";
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
    $numeros = '0123456789';
    $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $codigo = '';

    // Generar 2 números
    for ($i = 0; $i < 2; $i++) {
        $codigo .= $numeros[rand(0, strlen($numeros) - 1)];
    }

    // Generar 1 letra
    $codigo .= $letras[rand(0, strlen($letras) - 1)];

    // Generar 2 números
    for ($i = 0; $i < 2; $i++) {
        $codigo .= $numeros[rand(0, strlen($numeros) - 1)];
    }

    // Generar 1 letra
    $codigo .= $letras[rand(0, strlen($letras) - 1)];

    // Generar 2 números
    for ($i = 0; $i < 2; $i++) {
        $codigo .= $numeros[rand(0, strlen($numeros) - 1)];
    }

    return $codigo;
}

// Resto del código PHP permanece igual
?>
