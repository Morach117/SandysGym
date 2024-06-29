<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-time"></span> Registrar nueva Visita
        </h4>
    </div>
</div>

<hr/>

<?php
$cuota       = obtener_servicio('VISITA');
$hor_nombre  = request_var('hor_nombre', '');
$metodo_pago = request_var('metodo_pago', ''); // Nuevo campo para método de pago

if ($enviar) {
    $validar = validar_registro_dia();
    
    if ($validar['num'] == 1) {
        $exito = guardar_nuevo_dia();
        
        if ($exito['num'] == 1) {
            header("Location: .?s=visitas&IDV=$exito[IDV]&token=$exito[tkn]");
            exit;
        } else {
            mostrar_mensaje_div($exito['msj'], 'danger');
        }
    } else {
        mostrar_mensaje_div($validar['msj'], 'warning');
    }
}
?>

<style>
    .input-group .input-group-btn {
        margin-left: 10px; /* Adds space between the input and the button */
    }
    .input-group-btn button {
        height: 34px; /* Ensures the button is the same height as the input */
        line-height: 1.42857143; /* Vertically centers the text inside the button */
    }
</style>


<div class="row">
<form action=".?s=visitas&i=nuevo" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="hor_nombre" class="col-md-2 control-label">Nombre</label>
        <div class="col-md-5">
            <div class="input-group">
                <input type="text" name="hor_nombre" id="hor_nombre" class="form-control" required="required" value="<?= $hor_nombre ?>" />
                <input type="hidden" name="id_socio" id="id_socio" />
                <div class="input-group-btn">
                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalSocios">
                        <span class="glyphicon glyphicon-user"></span> Socios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-2 control-label">Cuota</label>
        <div class="col-md-5">
            <p class="form-control-static text-info">$<?= number_format($cuota['cuota'], 2) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label for="metodo_pago" class="col-md-2 control-label">Método de Pago</label>
        <div class="col-md-5">
            <select name="metodo_pago" id="metodo_pago" class="form-control">
                <option value="E" <?= ($metodo_pago == 'Efectivo') ? 'selected' : '' ?>>Efectivo</option>
                <option value="T" <?= ($metodo_pago == 'Tarjeta') ? 'selected' : '' ?>>Tarjeta</option>
                <option value="M" <?= ($metodo_pago == 'Monedero') ? 'selected' : '' ?>>Monedero</option>
            </select>
        </div>
    </div>

    <div id="monedero-section" class="form-group" style="display: none;">
        <label class="col-md-2 control-label">Saldo Monedero</label>
        <div class="col-md-5">
            <input type="text" id="saldo_monedero" class="form-control" readonly />
        </div>
    </div>

    <div id="efectivo-section" class="form-group" style="display: none;">
        <label class="col-md-2 control-label">Cantidad en Efectivo</label>
        <div class="col-md-5">
            <input type="text" class="form-control" id="cantidad_efectivo" name="cantidad_efectivo" value="0" />
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-5">
            <div class="btn-group" role="group">
                <input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=visitas'" />
                <input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
            </div>
        </div>
    </div>
</form>


<style>
    .input-group .form-control {
    width: calc(130% - 80px); /* Ajusta el ancho del input */
}

.input-group .input-group-btn {
    margin-left: 10px; /* Añade espacio entre el input y el botón */
    width: 80px; /* Ancho del botón */
}

.input-group-btn button {
    height: 29px; /* Asegura que el botón tenga la misma altura que el input */
}

</style>

    </div>

<!-- Modal para mostrar socios -->
<div class="modal fade" id="modalSocios" tabindex="-1" role="dialog" aria-labelledby="modalSociosLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalSociosLabel">Lista de Socios</h4>
            </div>
            <div class="modal-body">
                <table id="tablaSocios" class="table table-striped table-bordered" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Saldo Monedero</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargarán dinámicamente los socios -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>



<script>
$(document).ready(function() {
    var importeServicio = <?= number_format($cuota['cuota'], 2) ?>;

    $('#metodo_pago').change(function () {
        var metodoPago = $(this).val();
        if (metodoPago === 'M') {
            var idSocio = $('#id_socio').val();
            if (idSocio === '') {
                alert('Debes seleccionar un cliente válido para usar el monedero.');
                $(this).val('E'); // Cambiar el valor seleccionado de vuelta a Efectivo
                return;
            }
            $.ajax({
                url: './funciones/saldo_monedero.php',
                type: 'GET',
                data: { id_socio: idSocio },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var saldoMonedero = parseFloat(response.saldo_monedero);
                        
                        if (saldoMonedero < importeServicio) {
                            $('#efectivo-section').show(); // Mostrar la sección de pago en efectivo
                            $('#monedero-section').show(); // Mostrar la sección del monedero
                            $('#saldo_monedero').val(saldoMonedero.toFixed(2)); // Mostrar el saldo del monedero en el campo de entrada
                            
                            var cantidadFaltante = importeServicio - saldoMonedero;
                            $('#cantidad_efectivo').val(cantidadFaltante.toFixed(2)); // Mostrar la cantidad faltante en el campo de efectivo
                        } else {
                            $('#efectivo-section').hide(); // Ocultar la sección de pago en efectivo si no es necesario
                            $('#monedero-section').show(); // Mostrar la sección del monedero
                            $('#saldo_monedero').val(importeServicio.toFixed(2)); // Mostrar el importe del servicio en el campo de entrada del monedero
                        }
                    } else {
                        console.error('Error al obtener el saldo del monedero:', response.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al obtener el saldo del monedero:', error);
                }
            });
        } else {
            $('#efectivo-section').hide(); // Ocultar la sección de pago en efectivo si no se selecciona monedero
            $('#monedero-section').hide(); // Ocultar la sección del monedero si no se selecciona monedero
        }
    });

    // Función para cargar la lista de socios y configurar DataTables
    function cargarListaSocios() {
        $.ajax({
            url: './funciones/cargar_socios.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Limpiar la tabla antes de cargar los datos
                    $('#tablaSocios').DataTable().clear().destroy();
                    
                    // Llenar la tabla con los datos de los socios
                    $('#tablaSocios').DataTable({
                        data: response.socios,
                        columns: [
                            { data: 'nombre_completo', title: 'Nombre' },
                            { data: 'soc_mon_saldo', title: 'Saldo Monedero', render: $.fn.dataTable.render.number(',', '.', 2, '$') },
                            {
                                data: 'soc_id_socio',
                                title: 'Acción',
                                render: function(data, type, row) {
                                    return '<button type="button" class="btn btn-primary btn-xs seleccionar-socio" data-id="' + data + '" data-nombre="' + row.nombre_completo + '">Seleccionar</button>';
                                }
                            }
                        ],
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.11.4/i18n/Spanish.json' // Idioma español para DataTables
                        }
                    });

                    // Al hacer clic en el botón de seleccionar socio
                    $('#tablaSocios').off('click', '.seleccionar-socio').on('click', '.seleccionar-socio', function() {
                        var idSocio = $(this).data('id');
                        var nombreSocio = $(this).data('nombre');

                        $('#hor_nombre').val(nombreSocio);
                        $('#id_socio').val(idSocio);

                        // Cerrar el modal después de seleccionar
                        $('#modalSocios').modal('hide');
                    });
                } else {
                    // Manejo de errores si fuera necesario
                    alert('Error al cargar la lista de socios: ' + response.error);
                }
            },
            error: function() {
                alert('Error al cargar la lista de socios. Por favor, inténtelo de nuevo más tarde.');
            }
        });
    }

    // Cargar la lista de socios al abrir el modal
    $('#modalSocios').on('shown.bs.modal', function () {
        // Limpiar campos y cargar lista de socios al abrir el modal
        $('#hor_nombre').val('');
        $('#id_socio').val('');
        cargarListaSocios();
    });

});
</script>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-time"></span> Listado de Visitas
        </h4>
    </div>
</div>

<hr/>

<?php
    $id_visita    = request_var( 'IDV', 0 );
    $token        = request_var( 'token', '' );
    $eliminar    = request_var( 'eliminar', false );
    
    if( $id_visita && $token )
    {
        $validar_token    = hash_hmac( 'md5', $id_visita, $gbl_key );
        
        if( $validar_token == $token )
            echo "<script>mostrar_modal_visita( $id_visita, '$token' )</script>";
    }
    
    if( $eliminar )
    {
        $exito    = eliminar_horas();
        
        if( $exito['num'] == 1 )
        {
            header( "Location: .?s=visitas" );
            exit;
        }
        else
            mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
    }
    
    $horas_d    = lista_horas_visitas();    
?>

<div class="row">
    <div class="col-md-12">
        <h5 class="text-info"><strong>Clientes por visitas</strong></h5>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>Nombres</th>
                    <th>Hora de entrada</th>
                </tr>
            </thead>
            
            <tbody>
                <?= $horas_d ?>
            </tbody>
        </table>
    </div>
</div>
