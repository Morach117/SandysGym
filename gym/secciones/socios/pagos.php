<?php
	$eliminar		= request_var( 'eliminar', false );
	$servicio		= request_var( 'servicio', '' );
	$pag_fecha_pago	= request_var( 'pag_fecha_pago', date( 'd-m-Y' ) );
	$pag_fecha_ini	= request_var( 'pag_fecha_ini', '' );
	$pag_fecha_fin	= request_var( 'pag_fecha_fin', '' );
	$id_socio		= request_var( 'id_socio', 0 );
	$id_pago		= request_var( 'IDP', 0 );
	$pag_efectivo	= request_var( 'pag_efectivo', '' );
	$pag_tarjeta	= request_var( 'pag_tarjeta', '' );
	$token			= request_var( 'token', '' );
	$pag_importe	= '';
	$id_servicio	= 0;
	$servicio_cve	= '';
	$class_oculto	= 'hide';
	$op_fecha_pago	= "";
	$volver			= ".?s=socios";

	$codigo_promocion = isset($_POST['codigo_promocion']) ? $_POST['codigo_promocion'] : '';

	
	//para el paginado
	$pag_opciones	= request_var( 'pag_opciones', 0 );
	$pag_busqueda	= request_var( 'pag_busqueda', '' );
	$pag_fechai		= request_var( 'pag_fechai', '' );
	$pag_fechaf		= request_var( 'pag_fechaf', '' );
	$pag_item		= request_var( 'item', '' );
	$pag_blq		= request_var( 'blq', 0 );
	$pag_pag		= request_var( 'pag', 0 );
	
	if( $pag_item )
		$volver .= "&i=$pag_item";
	
	if( $pag_opciones )
		$volver .= "&pag_opciones=$pag_opciones";
	
	if( $pag_busqueda )
		$volver .= "&pag_busqueda=$pag_busqueda";
	
	if( $pag_fechai )
		$volver .= "&pag_fechai=$pag_fechai";
	
	if( $pag_fechaf )
		$volver .= "&pag_fechaf=$pag_fechaf";
	
	if( $pag_blq )
		$volver .= "&bql=$pag_blq";
	
	if( $pag_pag )
		$volver .= "&pag=$pag_pag";
	
	if( !$id_socio)
	{
		header( "Location: .?s=socios" );
		exit;
	}
	
	if( $id_pago && $token )
	{
		$impresion	= checar_impresion_pagos();
		$chk_token	= hash_hmac( 'md5', $id_pago, $gbl_key );
		
		if( $chk_token == $token && $impresion == 'S' )
			echo "<script>mostrar_modal_pago( $id_pago, '$token' )</script>";
	}
	
	$servicios		= obtener_servicios( $servicio );
	
	/*MEN PARCIAL solo se utiliza en socios es decir en s=socio y todos los item(i) que lo puedan contener. index,  js, funciones
	configuracion, configuracion -> mensualidades*/
	if( $servicio )
	{
		list( $id_servicio, $meses ) = explode( '-', $servicio );
		
		$servicio_cve	= obtener_servicio( $id_servicio );

		
		$servicio_cve	= $servicio_cve['clave'];
		
		if( $servicio_cve == 'MEN PARCIAL' )
			$class_oculto = '';
	}
	
	if( file_exists( "../imagenes/avatar/$id_socio.jpg" ) )
		$fotografia	= "	<img src='../imagenes/avatar/$id_socio.jpg' class='img-thumbnail' style='width:100%' />";
	else
		$fotografia	= "	<img src='../imagenes/avatar/noavatar.jpg' class='img-thumbnail' style='width:100%' />";
	
	if( $eliminar )
	{
		$mensaje	= eliminar_pago_socio();
		
		if( $mensaje['num'] == 1 )
			mostrar_mensaje_div( $mensaje['msj'], 'success' );
		else
			mostrar_mensaje_div( $mensaje['num'].". ".$mensaje['msj'], 'danger' );
	}
	
	//solo superadministrador
	if( $rol == 'S' )
	{
		$op_fecha_pago	= "	<div class='row'>
								<label class='col-md-5'>Fecha pago</label>
								<div class='col-md-7'>
									<input type='text' class='form-control' name='pag_fecha_pago' id='pag_fecha_pago' maxlength='10' value='$pag_fecha_pago' />
								</div>
							</div>";
	}
	
	if( $enviar )
	{
		$pag_importe	= request_var( 'pag_importe', 0.0 );
		$validar 		= validar_pago_socio();
		
		if( $validar['num'] == 1 )
		{
			$exito = guardar_pago_socio();
			
			if( $exito['num'] == 1 )
			{
				header( "Location: .?s=socios&i=pagos&id_socio=$exito[IDS]&IDP=$exito[IDP]&token=$exito[tkn]" );
				// header( "Location: .?s=socios&pag_opciones=2" );
				exit;
			}
			else
				mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
		}
		else
			mostrar_mensaje_div( $validar['msj'], 'warning' );
	}
	
	$nombre			= obtener_datos_socio();
	$tabla			= lista_pagos_socio();
	$archivo_img	= nombre_archivo_imagen( $id_socio );
	?>

<div class="row">
    <div class="col-md-12">
        <h4 class="text-info"><span class="glyphicon glyphicon-usd"></span> Captura de Pagos</h4>
    </div>
</div>

<hr />

<form role="form" method="post" action=".?s=socios&i=pagos" name="form_pago" enctype="multipart/form-data">
    <div class="row">
        <label class="col-md-3">Socio</label>
        <label class="col-md-9">
            <?= $nombre['soc_apepat']." ".$nombre['soc_apemat']." ".$nombre['soc_nombres'] ?>
        </label>
    </div>

    <div class="row">
        <label class="col-md-3">Descuento del Cliente (%)</label>
        <label class="col-md-9">
            <?= $nombre['soc_descuento']?>%
        </label>
    </div>

    <div class="row">
        <label class="col-md-3">Archivo de Img</label>
        <label class="col-md-9">
            <?= $archivo_img ?>
        </label>
        <input type="hidden" id="id_socio" value="<?= $id_socio ?>" />

    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <label class="col-md-5">Fecha de pago</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" value="<?= fecha_generica( date( 'd-m-Y' ) ); ?>"
                        readonly="on" />
                </div>
            </div>

            <?= $op_fecha_pago ?>

            <div class="row">
                <label class="col-md-5">Servicio</label>
                <div class="col-md-7">
                    <select class="form-control" name="servicio" id="servicio" onchange="calcular_servicio()" required>
                        <?= $servicios ?>
                    </select>
                </div>
            </div>

			<div class="row">
    <label class="col-md-5">Método de pago</label>
    <div class="col-md-7">
        <select class="form-control" name="m_pago" id="m_pago" required>
            <option value="E" selected>Efectivo</option>
            <option value="T">Tarjeta</option>
            <option value="M">Monedero</option>
        </select>
    </div>
</div>


            <div class="row <?= $class_oculto ?>" id="importe">
                <label class="col-md-offset-5 col-md-4"><em>Importe a pagar</em></label>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="pag_importe" maxlength="5"
                        value="<?= $pag_importe ?>" />
                </div>
            </div>

            <div class="row">
                <label class="col-md-5">Fecha inicial</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="pag_fecha_ini" id="pag_fecha_ini"
                        onchange="calcular_servicio()" required="required" maxlength="10" value="<?= $pag_fecha_ini ?>"
                        autocomplete="off" />
                </div>
            </div>

            <div class="row">
                <label class="col-md-5">Fecha vencimiento</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="pag_fecha_fin" id="pag_fecha_fin"
                        value="<?= $pag_fecha_fin ?>" autocomplete="off" />
                </div>
            </div>
            <div class="row">
                <label class="col-md-5">Código de Promoción</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="codigo_promocion" id="codigo_promocion"
                        value="<?= $codigo_promocion ?>" autocomplete="off" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h4 class="text-info" style="font-size: 1.5em;"><strong>Detalle del Pago</strong></h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" style="font-size: 18px;">
                    <p style="font-size: 18px; font-weight: bold;">Subtotal: <span id="subtotal"></span></p>
                    <p style="font-size: 18px; font-weight: bold;">Descuento: <span id="descuento"></span></p>
                    <p style="font-size: 18px; font-weight: bold;">Total: <span id="total"></span></p>
                </div>
            </div>
        </div>

        <div class="col-md-5" align="center">
            <div class="row">
                <div class="col-md-12">
                    <?= $fotografia ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <input type="file" name="avatar" />
                </div>
            </div>
        </div>
    </div>


	<div class="row" id="monedero-section" style="display: none;">
    <label class="col-md-5">Saldo del monedero</label>
    <div class="col-md-7">
        <input type="text" class="form-control" id="saldo_monedero" name="saldo_monedero" value="" readonly />
    </div>
</div>

<div class="row" id="efectivo-section" style="display: none;">
        <label class="col-md-5">Cantidad a pagar en efectivo</label>
        <div class="col-md-7">
            <input type="text" class="form-control" id="cantidad_efectivo" name="cantidad_efectivo" value="0" />
        </div>
    </div>




    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="pag_opciones" value="<?= $pag_opciones ?>" />
            <input type="hidden" name="pag_busqueda" value="<?= $pag_busqueda ?>" />
            <input type="hidden" name="pag_fechai" value="<?= $pag_fechai ?>" />
            <input type="hidden" name="pag_fechaf" value="<?= $pag_fechaf ?>" />
            <input type="hidden" name="pag_item" value="<?= $pag_item ?>" />
            <input type="hidden" name="blq" value="<?= $pag_blq ?>" />
            <input type="hidden" name="pag" value="<?= $pag_pag ?>" />

            <input type="hidden" name="id_socio" value="<?= $id_socio ?>" />
            <input type="submit" name="enviar" value="Cobrar y guardar" class="btn btn-primary" />
            <input type="button" name="Regresar" value="Regresar" class="btn btn-default"
                onclick="location.href='<?= $volver ?>'" />
        </div>
    </div>
</form>

<div class="row">
    <div class="col-md-12">
        <h5 class="text-info"><strong>Historico de pagos</strong></h5>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-hover h6">
            <thead>
                <th></th>
                <th>Servicio pagado</th>
                <th>Fecha de pago</th>
                <th>Fecha inicial</th>
                <th>Vencimiento</th>
                <th class="text-right">Importe</th>
            </thead>

            <tbody>
                <?= $tabla ?>
            </tbody>
        </table>
    </div>
</div>


<script>
$('#m_pago').change(function () {
    var metodoPago = $(this).val();
    if (metodoPago === 'M') {
        var idSocio = $('#id_socio').val();
        $.ajax({
            url: './funciones/saldo_monedero.php',
            type: 'GET',
            data: { id_socio: idSocio },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var saldoMonedero = parseFloat(response.saldo_monedero);
                    var importeServicio = parseFloat(document.getElementById("subtotal").textContent);
                    
                    if (saldoMonedero < importeServicio) {
                        $('#efectivo-section').show(); // Mostrar la sección de pago en efectivo
                        $('#monedero-section').show(); // Mostrar la sección del monedero
                        $('#saldo_monedero').val(saldoMonedero.toFixed(2)); // Mostrar el saldo del monedero en el campo de entrada
                        
                        var cantidadFaltante = importeServicio - saldoMonedero;
                        $('#cantidad_efectivo').val(cantidadFaltante.toFixed(2)); // Mostrar la cantidad faltante en el campo de efectivo
                    } else {
                        $('#efectivo-section').hide(); // Ocultar la sección de pago en efectivo si no es necesario
                        $('#monedero-section').show(); // Mostrar la sección del monedero
                        $('#saldo_monedero').val(saldoMonedero.toFixed(2)); // Mostrar el importe del servicio en el campo de entrada del monedero
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




    // Función para obtener parámetros de la URL
    function obtenerParametroURL(nombre) {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(nombre);
    }

    // Bandera para verificar si ya se ha aplicado el descuento de cumpleaños
    var descuentoCumpleanosAplicado = false;

    // Función para obtener la cuota del servicio mediante una solicitud AJAX
    function obtenerCuotaServicio() {
        var servicioSeleccionado = document.getElementById("servicio").value;
        var id_servicio = servicioSeleccionado.split('-')[0];

        if (!id_servicio) {
            console.error("Error: El id_servicio es inválido.");
            return;
        }

        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var respuesta = JSON.parse(xhr.responseText);

                    if (respuesta.success) {
                        var cuota = parseFloat(respuesta.cuota);

                        // Mostrar la cuota sin aplicar ningún descuento
                        document.getElementById("subtotal").textContent = cuota.toFixed(2);

                        // Verificar si es el mes de cumpleaños del cliente
                        verificarCumpleanos();

                        // Si no es el mes de cumpleaños, verificar y aplicar otros descuentos
                        if (!descuentoCumpleanosAplicado) {
                            // Verificar si el cliente tiene un descuento almacenado
                            var descuentoCliente = parseFloat(<?= json_encode($nombre['soc_descuento']); ?>);
                            if (!isNaN(descuentoCliente)) {
                                aplicarDescuentoCliente(descuentoCliente);
                            } else {
                                document.getElementById("descuento").textContent = '0.00';
                                document.getElementById("total").textContent = cuota.toFixed(2);
                            }
                        }
                    } else {
                        console.error("Error al obtener la cuota del servicio:", respuesta.error);
                    }
                } else {
                    console.error('Error al realizar la solicitud:', xhr.status);
                }
            }
        };

        xhr.open("GET", "././funciones/obtener_cuota_servicio.php?id_servicio=" + id_servicio, true);
        xhr.send();
    }

    // Función para verificar si el servicio seleccionado tiene descuentos promocionales permitidos
    function verificarDescuentosPromocionales(id_servicio) {
        if (descuentoCumpleanosAplicado) return;

        var xhrDescuentos = new XMLHttpRequest();
        xhrDescuentos.onreadystatechange = function () {
            if (xhrDescuentos.readyState === XMLHttpRequest.DONE) {
                if (xhrDescuentos.status === 200) {
                    var respuestaDescuentos = JSON.parse(xhrDescuentos.responseText);
                    if (!respuestaDescuentos.success) {
                        // Mostrar una alerta si el servicio no tiene descuentos promocionales permitidos
                        alert("El servicio seleccionado no tiene descuentos promocionales permitidos.");

                        // Recargar la página después de 2 segundos
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                } else {
                    console.error('Error al realizar la solicitud para verificar los descuentos promocionales:', xhrDescuentos.status);
                }
            }
        };

        xhrDescuentos.open("GET", "././funciones/verificar_descuentos_promocionales.php?id_servicio=" + id_servicio, true);
        xhrDescuentos.send();
    }

    // Función para aplicar el descuento del cliente
    function aplicarDescuentoCliente(descuentoCliente) {
        if (descuentoCumpleanosAplicado) return;

        var cuota = parseFloat(document.getElementById("subtotal").textContent);
        var montoDescontadoCliente = cuota * (descuentoCliente / 100);
        var totalConDescuentoCliente = cuota - montoDescontadoCliente;

        // Mostrar el descuento del cliente y el total a pagar
        document.getElementById("descuento").textContent = montoDescontadoCliente.toFixed(2);
        document.getElementById("total").textContent = totalConDescuentoCliente.toFixed(2);
    }

    // Función para aplicar el descuento del código promocional
    function aplicarDescuentoPromocional(codigo_promocion) {
        if (descuentoCumpleanosAplicado) return;

        var servicioSeleccionado = document.getElementById("servicio").value;
        var id_servicio = servicioSeleccionado.split('-')[0];

        verificarDescuentosPromocionales(id_servicio); // Verificar descuentos promocionales para el nuevo servicio seleccionado

        var xhrPromocion = new XMLHttpRequest();
        xhrPromocion.onreadystatechange = function () {
            if (xhrPromocion.readyState === XMLHttpRequest.DONE) {
                if (xhrPromocion.status === 200) {
                    var respuestaPromocion = JSON.parse(xhrPromocion.responseText);
                    if (respuestaPromocion.success) {
                        var descuentoPromocion = parseFloat(respuestaPromocion.porcentaje_descuento);
                        var cuota = parseFloat(document.getElementById("subtotal").textContent);

                        // Calcular el descuento total sumando el descuento del cliente y el descuento del código promocional
                        var descuentoTotal = 0;

                        // Verificar si el cliente tiene un descuento almacenado
                        var descuentoCliente = parseFloat(<?= json_encode($nombre['soc_descuento']); ?>);
                        if (!isNaN(descuentoCliente)) {
                            descuentoTotal += descuentoCliente;
                        }

                        descuentoTotal += descuentoPromocion;

                        var montoDescontadoTotal = cuota * (descuentoTotal / 100);
                        var totalConDescuentoTotal = cuota - montoDescontadoTotal;

                        // Mostrar el descuento total y el total a pagar
                        document.getElementById("descuento").textContent = montoDescontadoTotal.toFixed(2);
                        document.getElementById("total").textContent = totalConDescuentoTotal.toFixed(2);
                    } else {
                        alert("Error: " + respuestaPromocion.error);
                    }
                } else {
                    console.error('Error al realizar la solicitud para verificar el código promocional:', xhrPromocion.status);
                }
            }
        };

        xhrPromocion.open("GET", "././funciones/verificar_codigo_promocional.php?codigo_promocion=" + codigo_promocion, true);
        xhrPromocion.send();
    }

    // Función para verificar si el cliente cumple años este mes
    function verificarCumpleanos() {
        console.log("Verificando cumpleaños del cliente...");

        var fechaNacimientoString = "<?= $nombre['soc_fecha_nacimiento']; ?>";
        console.log("Fecha de nacimiento (string):", fechaNacimientoString);

        var fechaNacimiento = new Date(fechaNacimientoString + "T00:00:00");
        console.log("Fecha de nacimiento (Date):", fechaNacimiento);

        var fechaActual = new Date();
        console.log("Fecha actual:", fechaActual);

        if (fechaNacimiento.getMonth() === fechaActual.getMonth()) {
            alert("¡Feliz cumpleaños! Tienes un descuento especial.");
            document.getElementById("codigo_promocion").value = "70x05U99";
            aplicarDescuentoPromocional("70x05U99");
            descuentoCumpleanosAplicado = true; // Marcar que se ha aplicado el descuento de cumpleaños
        } else {
            console.log("No es el mes de cumpleaños del cliente.");
        }
    }

    // Llamar a la función inicialmente para que se muestre el total correcto al cargar la página
    obtenerCuotaServicio();

    // Agregar un evento onchange al select de servicio para llamar a la función obtenerCuotaServicio() cuando cambie
    document.getElementById("servicio").onchange = obtenerCuotaServicio;

    // Agregar un evento onchange al campo de código promocional
    document.getElementById("codigo_promocion").onchange = function () {
        var codigo_promocion = this.value;
        if (codigo_promocion) {
            aplicarDescuentoPromocional(codigo_promocion);
        }
    };
</script>