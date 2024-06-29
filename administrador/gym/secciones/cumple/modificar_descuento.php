<?php
// Función para actualizar el descuento de cumpleaños
function actualizar_cumpleaños()
{
    // Suponiendo que tienes una función obtener_conexion() que devuelve la conexión a la base de datos
    $conexion = obtener_conexion();

    // Verificar si se envió el formulario
    if (isset($_POST['enviar'])) {
        // Obtener el ID del cumpleaños y el porcentaje de descuento desde el formulario
        $id_cumpleaños = $_POST['id_cumpleaños'];
        $porcentaje_descuento = $_POST['porcentaje_descuento'];

        // Escapar los valores para prevenir inyección SQL
        $id_cumpleaños = mysqli_real_escape_string($conexion, $id_cumpleaños);
        $porcentaje_descuento = mysqli_real_escape_string($conexion, $porcentaje_descuento);

        // Consulta SQL para actualizar el porcentaje de descuento
        $query = "UPDATE san_promociones SET porcentaje_descuento = '$porcentaje_descuento' WHERE id_promocion = '$id_cumpleaños'";

        // Ejecutar la consulta
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            // Si la consulta fue exitosa, retornar un array con el mensaje de éxito
            return array('num' => 1, 'msj' => 'Descuento actualizado correctamente.');
        } else {
            // Si la consulta falló, retornar un array con el mensaje de error
            return array('num' => 2, 'msj' => 'Error al actualizar el descuento: ' . mysqli_error($conexion));
        }
    } else {
        // Si no se envió el formulario, retornar un array con un mensaje de advertencia
        return array('num' => 3, 'msj' => 'No se envió el formulario de actualización.');
    }
}

// Verificar si se envió el formulario de actualización
if (isset($_POST['enviar'])) {
    // Llamar a la función para actualizar el descuento de cumpleaños
    $exito = actualizar_cumpleaños();

    // Redirigir según el resultado de la actualización
    if ($exito['num'] == 1) {
        header("Location: ?s=cumple");
        exit;
    } else {
        mostrar_mensaje_div($exito['num'] . ". " . $exito['msj'], 'danger');
    }
}

// Función para obtener el porcentaje de descuento
function obtener_porcentaje_descuento($id_promocion)
{
    $conexion = obtener_conexion();
    
    if ($conexion) {
        // Escapar el ID de promoción para prevenir inyecciones SQL
        $id_promocion = mysqli_real_escape_string($conexion, $id_promocion);

        // Consulta SQL para obtener el porcentaje de descuento
        $query = "SELECT porcentaje_descuento FROM san_promociones WHERE id_promocion = '$id_promocion'";
        
        // Ejecutar la consulta
        $resultado = mysqli_query($conexion, $query);
        
        if ($resultado) {
            // Extraer el resultado
            $fila = mysqli_fetch_assoc($resultado);
            
            // Liberar el resultado
            mysqli_free_result($resultado);
            
            // Cerrar la conexión
            mysqli_close($conexion);
            
            // Retornar el porcentaje de descuento
            return $fila['porcentaje_descuento'];
        } else {
            // Si la consulta falla, mostrar error
            echo "Error al ejecutar la consulta: " . mysqli_error($conexion);
        }
    } else {
        // Si no se puede conectar a la base de datos, mostrar error
        echo "Error al conectar a la base de datos.";
    }
    
    // Si ocurre un error, retorna falso
    return false;
}

// Ejemplo de uso
$id_promocion = 23; // Supongamos que quieres obtener el porcentaje de descuento para la promoción con ID 1
$porcentaje_descuento = obtener_porcentaje_descuento($id_promocion);
?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-pencil"></span> Modificar Descuento
        </h4>
    </div>
</div>

<hr/>

<form method="post" action="?s=cumple&i=modificar_descuento">
    <div class="row">
        <label class="col-md-2">Porcentaje de Descuento</label>
        <div class="col-md-4">
            <input type="hidden" name="id_cumpleaños" value="<?= $id_promocion ?>" />
            <input type="number" name="porcentaje_descuento" class="form-control" value="<?= $porcentaje_descuento ?>" min="0" max="100" required />
        </div>
    </div>

    <div class="row">
        <div class="col-md-offset-2 col-md-4">
            <input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
        </div>
    </div>
</form>
