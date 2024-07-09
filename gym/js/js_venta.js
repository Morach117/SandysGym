$(document).ready(function() {
    var importeTotal = 0; // Asignar el total de la venta

    function agregar_articulo_venta(id_articulo) {
        if (!document.getElementById('art_' + id_articulo)) {
            $.post("peticiones/pet_venta.php", { id_articulo: id_articulo, envio: true },
            function(datos) {
                $("#articulo_venta").append(datos);
                calcular_total();
                var saldo = parseFloat(document.getElementById('prep_saldo').value);
                actualizarCamposPago(saldo);
            });
        }
    }

    function calcular_importe(id_articulo) {
        var precio = document.getElementById('pre_' + id_articulo).value;
        var cantidad = document.getElementById('can_' + id_articulo).value;

        var importe = cantidad * precio;

        document.getElementById('imp_' + id_articulo).innerHTML = '$' + importe.toFixed(2);

        calcular_total();
        var saldo = parseFloat(document.getElementById('prep_saldo').value);
        actualizarCamposPago(saldo);
    }

    function calcular_total() {
        var total = 0;
        var sub_total = 0;
        var id_articulo = '';
        var tag_text = document.getElementsByTagName('input');

        var precio = 0;
        var cantidad = 0;

        for (var i = 0; i < tag_text.length; i++) {
            if (tag_text[i].type == 'hidden' && 'art' == tag_text[i].name.substring(0, 3)) {
                id_articulo = tag_text[i].value;
                precio = parseFloat(document.getElementById('pre_' + id_articulo).value);
                cantidad = parseFloat(document.getElementById('can_' + id_articulo).value);

                var importe = cantidad * precio;

                sub_total += importe;
            }
        }

        total = sub_total;

        document.getElementById('input_total').value = total.toFixed(2);

        document.getElementById('tag_sub_total').innerHTML = '$' + sub_total.toFixed(2);
        document.getElementById('tag_total_pago').innerHTML = '$' + total.toFixed(2);
        importeTotal = total; // Asignar el importe total de la venta

        var metodoPago = $('#m_pago').val();
        if (metodoPago === 'P') {
            var saldo = parseFloat($('#prep_saldo').val());
            actualizarCamposPago(saldo);
        } else {
            $('#prepago').val('0.00');
            $('#efectivo').val(total.toFixed(2));
        }
    }

    function actualizarCamposPago(saldo) {
        var total_a_pagar = parseFloat(document.getElementById('input_total').value);
        var prepago = saldo >= total_a_pagar ? total_a_pagar : saldo;
        var efectivo = total_a_pagar - prepago;

        document.getElementById('prepago').value = prepago.toFixed(2);
        document.getElementById('efectivo').value = efectivo.toFixed(2);
    }

    function quitar_de_lista(id_articulo) {
        document.getElementById('art_' + id_articulo).remove();

        calcular_total();
        var saldo = parseFloat(document.getElementById('prep_saldo').value);
        actualizarCamposPago(saldo);
    }

    function checar_articulos(commit) {
        var regex_decimal = /^[\d]+$/;
        var tag_text = document.getElementsByTagName('input');
        var efectivo = parseFloat(document.getElementById('efectivo').value);
        var prepago_imp = parseFloat(document.getElementById('prepago').value);
        var saldo = parseFloat(document.getElementById('prep_saldo').value);
        var id_prepago = parseInt(document.getElementById('prep_id_prepago').value);
        var prepago = document.getElementById('mostrar_socio').checked;
        var ban_prepago = 0; // 0=no, 1=si para php
        var total_a_pagar = parseFloat(document.getElementById('input_total').value);
        var total_pago = 0;
        var cantidad = "";
        var id_articulo = "";
        var continuar = false;
        var tipo_pago = $('#m_pago').val();
    
        if (tipo_pago === 'P' && id_prepago <= 1) {
            alert('No hay socio seleccionado.');
            return false;
        }
    
        if (commit == 'S') {
            document.getElementById('btn_procesar').innerHTML = "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
            document.getElementById('msj_procesar').innerHTML = "Un momento, procesando...";
            document.getElementById('img_procesar').innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
        }
    
        if (isNaN(prepago_imp)) { prepago_imp = 0; }
        if (isNaN(efectivo)) { efectivo = 0; }
    
        if (prepago) {
            if (prepago_imp > saldo) {
                alert('La cantidad de PrePago no puede ser mayor al Saldo que tiene el Socio.');
                return false;
            }
    
            if (prepago_imp > total_a_pagar) {
                alert('La cantidad del PrePago no puede ser mayor al total a pagar.');
                return false;
            }
    
            ban_prepago = 1;
        } else
            prepago_imp = 0;
    
        total_pago = efectivo + prepago_imp;
    
        if (total_a_pagar > total_pago && tipo_pago == 'E') {
            alert('Pago incompleto.');
            return false;
        }
    
        for (var i = 0; i < tag_text.length; i++) {
            if (tag_text[i].type == 'text' && 'can' == tag_text[i].name.substring(0, 3)) {
                if (regex_decimal.test(tag_text[i].value)) {
                    cantidad += tag_text[i].value + '-';
                    continuar = true;
                } else {
                    continuar = false;
                    break;
                }
            }
    
            if (tag_text[i].type == 'hidden' && 'art' == tag_text[i].name.substring(0, 3)) {
                cantidad += tag_text[i].value + ',';
                id_articulo += tag_text[i].value + ",";
            }
        }
    
        cantidad = cantidad.substring(0, cantidad.length - 1);
        id_articulo = id_articulo.substring(0, id_articulo.length - 1);
    
        if (continuar) {
            if (cantidad && id_articulo) {
                $.post("peticiones/pet_venta_procesar.php", { commit: commit, tipo_pago: tipo_pago, prepago: ban_prepago, saldo: saldo, id_prepago: id_prepago, total_a_pagar: total_a_pagar, efectivo: efectivo, prepago_imp: prepago_imp, cantidad: cantidad, id_articulo: id_articulo, envio: true },
                function(datos) {
                    if (commit == 'S') {
                        var exito = JSON.parse(datos);
    
                        if (exito.num == 1) {
                            var t_ticket = "?folio=" + exito.folio + "&IDV=" + exito.IDV + "&efectivo=" + exito.efectivo + "&prepago_imp=" + exito.prepago_imp;
    
                            cerrar_modal();
    
                            if (exito.ticket == 'S') {
                                document.getElementById('ticket_cliente').innerHTML = "<iframe name='ticket' src='ticket.php" + t_ticket + "' frameborder=0 width=0 height=0></iframe>";
                                ticket.focus();
                                ticket.print();
                            }
    
                            setInterval(false, 1000);
                            location.href = '.?s=venta';
                        } else {
                            document.getElementById('btn_procesar').innerHTML = "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
                            document.getElementById('msj_procesar').innerHTML = exito.num + '. ' + exito.msj;
                            document.getElementById('img_procesar').innerHTML = "";
                        }
                    } else {
                        $('#modal_principal').html(datos);
    
                        $('#modal_principal').modal();
                        $('#modal_principal').modal({ keyboard: false });
                        $('#modal_principal').modal('show');
                    }
                });
            } else
                alert('Operación inválida.');
        } else
            alert('Cantidad de un Articulo inválido o no hay articulos seleccionados.');
    
        return false;
    }
    

    function buscar_articulo() {
        document.getElementById('lista_articulos').innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";

        var criterio = document.getElementById('criterio_busqueda').value;

        $.post("peticiones/pet_venta_buscar.php", { criterio: criterio, envio: true },
        function(datos) {
            document.getElementById('lista_articulos').innerHTML = datos;
        });
    }

    // Cambiar comportamiento al seleccionar Monedero
    $('#m_pago').change(function() {
        var metodoPago = $(this).val();
        if (metodoPago === 'P') {
            var idSocio = $('#prep_id_prepago').val();
            if (idSocio === '') {
                alert('Debes seleccionar un cliente válido para usar el monedero.');
                $('#m_pago').val('E'); // Cambiar el valor seleccionado de vuelta a Efectivo
                return;
            }
            $.ajax({
                url: './funciones/saldo_monedero.php',
                type: 'GET',
                data: { id_socio: idSocio },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var saldoMonedero = parseFloat(response.saldo_monedero);
                        
                        $('#prepago').val(Math.min(saldoMonedero, importeTotal).toFixed(2));
                        
                        if (saldoMonedero < importeTotal) {
                            $('#div_prepago').show(); // Mostrar la sección de pago en monedero
                            var cantidadFaltante = importeTotal - saldoMonedero;
                            $('#efectivo').val(cantidadFaltante.toFixed(2)); // Mostrar la cantidad faltante en el campo de efectivo
                        } else {
                            $('#div_prepago').show(); // Mostrar la sección del monedero
                            $('#efectivo').val('0.00'); // No se requiere efectivo
                        }
                    } else {
                        console.error('Error al obtener el saldo del monedero:', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener el saldo del monedero:', error);
                    alert('Error al obtener el saldo del monedero: No se proporcionó ningún ID de socio en la URL.');
                }
            });
        } else {
            $('#div_prepago').hide(); // Ocultar la sección de pago en monedero si no se selecciona monedero
            $('#prepago').val('0.00'); // Restablecer el campo de monedero
            $('#efectivo').val(importeTotal.toFixed(2)); // Restablecer el campo de efectivo al importe total
        }
    });

    // Cargar la lista de socios y configurar DataTables
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

                        $('#nombre_socio').text(nombreSocio + ', saldo: $' + parseFloat($(this).closest('tr').find('td').eq(1).text().replace('$', '').replace(',', '')).toFixed(2));
                        $('#prep_id_prepago').val(idSocio);
                        $('#prep_saldo').val(parseFloat($(this).closest('tr').find('td').eq(1).text().replace('$', '').replace(',', '')).toFixed(2));

                        $('#div_prepago').hide(); // Ocultar la sección del monedero inicialmente
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
    $('#mostrar_socio').click(function() {
        $('#modalSocios').modal('show');
        cargarListaSocios();
    });

    $('#mostrar_socio').change(function() {
        if ($(this).is(':checked')) {
            $('#modalSocios').modal('show');
        } else {
            $('#nombre_socio').text('');
            $('#prep_id_prepago').val('');
            $('#prep_saldo').val('');
            $('#prepago').val('');
            $('#div_prepago').hide();
        }
    });

    window.agregar_articulo_venta = agregar_articulo_venta;
    window.calcular_importe = calcular_importe;
    window.calcular_total = calcular_total;
    window.actualizarCamposPago = actualizarCamposPago;
    window.quitar_de_lista = quitar_de_lista;
    window.checar_articulos = checar_articulos;
    window.buscar_articulo = buscar_articulo;
});
