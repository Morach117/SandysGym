document.ready = function()
{
	mostrar_socios();
}

function ver_iva()
{
	var check = document.getElementById( "chk_iva" );
	
	$( "#ver_iva" ).removeClass( "hidden" );
	$( "#ver_iva" ).removeClass( "show" );
	
	if( check.checked == true )
		$( "#ver_iva" ).addClass( "show" );
	else
		$( "#ver_iva" ).addClass( "hidden" );
	
	calcular_total();
}

function agregar_cliente()
{
	var soc_nombres	= document.getElementById( 'soc_nombres' ).value;
	var soc_apepat	= document.getElementById( 'soc_apepat' ).value;
	var soc_apemat	= document.getElementById( 'soc_apemat' ).value;
	var soc_correo	= document.getElementById( 'soc_correo' ).value;
	
	document.getElementById( 'modal_venta_mensajes' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	if( soc_nombres && soc_apepat && soc_apemat && soc_correo )
	{
		$.post( "peticiones/pet_venta_socios_agregar.php", { soc_nombres:soc_nombres, soc_apepat:soc_apepat, soc_apemat:soc_apemat, soc_correo:soc_correo, envio : true },
		
		function( datos )
		{
			if( datos )
				document.getElementById( 'modal_venta_mensajes' ).innerHTML = datos;
			else
			{
				cerrar_modal();
				mostrar_socios();
			}
		});
	}
	else
		document.getElementById( 'modal_venta_mensajes' ).innerHTML = "Faltan datos para agregar un nuevo Cliente.";
}

function buscar_clientes()
{
	var criterio	= document.getElementById( 'nombre_cliente' ).value;
	
	document.getElementById( 'lista_clientes' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_venta_socios_busqueda.php", { criterio:criterio, envio : true },
	
	function( datos )
	{
		document.getElementById( 'lista_clientes' ).innerHTML = datos;
	});
}

function hora_entrega()
{
	var f_entrega	= document.getElementById( 'f_entrega' ).value;
	var f_tipo		= document.getElementById( 'tipo' ).value;
	
	$.post( "peticiones/pet_venta_hentrega.php", { f_tipo:f_tipo, f_entrega:f_entrega, envio : true },
	
	function( datos )
	{
		document.getElementById( 'h_entrega' ).value = datos;
	});
}

function seleccionar_socio( par_id_socio, p_descuento, par_nombre )
{
	var id_socio	= parseInt( par_id_socio );
	
	if( id_socio )
	{
		document.getElementById( 'nombre_socio' ).innerHTML = par_nombre;
		document.getElementById( 'id_socio' ).value = par_id_socio;
		document.getElementById( 'por_descuento' ).innerHTML = p_descuento.toFixed(2) + '%';
		document.getElementById( 'descuento' ).value = p_descuento;
		
		cerrar_modal();
		calcular_total();
	}
}

function mostrar_socios()
{
	$.post( "peticiones/pet_venta_socios.php", { envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: true });
		$( '#modal_principal' ).modal('show');
	});
}

function agregar_servicio_venta( id_servicio )
{
	var tipo_venta = document.getElementById( 'tipo' ).value;
	
	if( !document.getElementById( 'ser_' + id_servicio ) )
	{
		$.post( "peticiones/pet_venta.php", { id_servicio : id_servicio, tipo_venta:tipo_venta, envio : true },
		function( datos )
		{
			$( "#servicio_venta" ).append( datos );
			
			calcular_total();
		});
	}
}

function calcular_importe( id_servicio )
{
	var precio		= parseFloat( document.getElementById( 'pre_' + id_servicio ).value );
	var cantidad	= parseFloat( document.getElementById( 'kg_' + id_servicio ).value );
	var minimo		= parseFloat( document.getElementById( 'min_' + id_servicio ).value );
	var importe		= 0;
	
	if( minimo > cantidad )
	{
		importe		= minimo * precio;
	}
	else
	{
		importe		= cantidad * precio;
	}
	
	document.getElementById( 'imp_' + id_servicio ).innerHTML = '$' + importe.toFixed(2);
	
	calcular_total();
}

function calcular_total()
{
	var total		= 0;
	var subtotal	= 0;
	var importe		= 0;
	var id_servicio	= 0;
	var tag_text	= document.getElementsByTagName( 'input' );
	var descuento	= document.getElementById( 'descuento' ).value;
	var chk_iva 	= document.getElementById( "chk_iva" ).checked;
	var iva_por		= document.getElementById( "iva_por" ).value;
	var comision	= document.getElementById( 'comision' ).value;
	var iva_monto	= 0;
	
	var aux_desc	= 0;
	var precio		= 0;
	var cantidad	= 0;
	
	var pag_tarjeta		= 0;
	var pag_comision	= 0;
	
	for( var i = 0; i < tag_text.length; i++ )
	{
		if( tag_text[i].type == 'hidden' && 'ser' == tag_text[i].name.substring( 0, 3 ) )
		{
			id_servicio = tag_text[i].value;
			precio		= parseFloat( document.getElementById( 'pre_' + id_servicio ).value );
			cantidad	= parseFloat( document.getElementById( 'kg_' + id_servicio ).value );
			var minimo	= parseFloat( document.getElementById( 'min_' + id_servicio ).value );
			var importe	= 0;
			
			if( minimo > cantidad )
				importe		= minimo * precio;
			else
				importe		= cantidad * precio;
			
			subtotal += importe;
			
			if( descuento )
			{
				aux_desc	= subtotal * ( descuento / 100 );
				total		= subtotal - aux_desc;
			}
			else
				total = subtotal;
		}
	}
	
	if( chk_iva == true && iva_por > 0 )
	{
		iva_monto	= total * ( iva_por / 100 );
		total		= total + iva_monto;
		document.getElementById( 'ver_iva_monto' ).innerHTML = '$' + iva_monto.toFixed(2);
		document.getElementById( 'iva_monto' ).value = iva_monto;
	}
	else
	{
		iva_monto = 0;
		document.getElementById( 'ver_iva_monto' ).innerHTML = '$' + iva_monto.toFixed(2);
		document.getElementById( 'iva_monto' ).value = iva_monto;
	}
	
	// metodo de pago
	
	if( document.getElementById( 'm_pago_e' ).checked )
	{
		$( "#efectivo" ).attr( 'disabled', false );
	}
	else if( document.getElementById( 'm_pago_t' ).checked )
	{
		$( "#efectivo" ).attr( 'disabled', true );
		
		if( comision > 0 )
		{
			pag_comision	= parseFloat( total * ( comision / 100 ) );
			pag_tarjeta		= total;
		}
		else
		{
			pag_comision	= 0;
			pag_tarjeta		= total;
		}
	}
	
	// fin
	
	document.getElementById( 'subtotal' ).innerHTML = '$' + subtotal.toFixed(2);
	document.getElementById( 'mon_descuento' ).innerHTML = '$' + aux_desc.toFixed(2);
	document.getElementById( 'total' ).innerHTML = '$' + total.toFixed(2);
	document.getElementById( 'tag_tarjeta' ).innerHTML = '$' + parseFloat( pag_tarjeta + pag_comision ).toFixed(2);
	
	document.getElementById( 'input_total' ).value = total;
	document.getElementById( 'pag_tarjeta' ).value = pag_tarjeta;
	document.getElementById( 'pag_comision' ).value = pag_comision;
}

function quitar_de_lista( id_servicio )
{
	document.getElementById( 'ser_' + id_servicio ).innerHTML = '';
	
	calcular_total();
}

/*se ejecuta cuando se le da procesar*/
function checar_servicios( commit )
{
	var regex_entero 	= /^[\d]+$/;
	var regex_decimal 	= /^[0-9]*[.][0-9]+$/;
	var tag_text 		= document.getElementsByTagName( 'input' );
	var efectivo		= parseFloat( document.getElementById( 'efectivo' ).value );
	var total_a_pagar	= parseFloat( document.getElementById( 'input_total' ).value );
	var id_socio		= parseFloat( document.getElementById( 'id_socio' ).value );
	var pag_tarjeta		= parseFloat( document.getElementById( 'pag_tarjeta' ).value );
	var pag_comision	= parseFloat( document.getElementById( 'pag_comision' ).value );
	var obs				= document.getElementById( 'observaciones' ).value;
	var f_entrega		= document.getElementById( 'f_entrega' ).value;
	var tipo			= document.getElementById( 'tipo' ).value;
	var descuento		= document.getElementById( 'descuento' ).value;
	var total_pago		= 0;
	var kilos			= "";
	var id_servicio		= "";
	var continuar		= false;
	var chk_iva 		= document.getElementById( "chk_iva" ).checked;
	var iva_monto 		= document.getElementById( "iva_monto" ).value;
	var iva_cobro		= "N";
	
	var chk_tipo_pago	= '';
	
	if( document.getElementById( 'm_pago_e' ).checked )
		chk_tipo_pago = 'E';
	else if( document.getElementById( 'm_pago_t' ).checked )
		chk_tipo_pago = 'T';
	
	if( chk_iva )
		iva_cobro = "S";
	
	if( commit == 'S' )
	{
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
		document.getElementById( 'msj_procesar' ).innerHTML	= "Un momento, procesando...";
		document.getElementById( 'img_procesar' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	}
	
	if( f_entrega.length != 10 )
	{
		alert( 'Fecha de entrega inválida.' );
		return false;
	}
	
	if( isNaN( efectivo ) ) 
	{
		alert( 'Número inválido en Efectivo/Anticipo.' );
		return false;
	}
	
	if( id_socio <= 0 )
	{
		mostrar_socios();
		return false;
	}
	
	for( var i = 0; i < tag_text.length; i++ )
	{
		if( tag_text[i].type == 'text' && 'kg' == tag_text[i].name.substring( 0, 2 ) )
		{
			if( regex_entero.test( tag_text[i].value ) || regex_decimal.test( tag_text[i].value ) )
			{
				kilos += tag_text[i].value + '-';
				continuar = true;
			}
			else
			{
				continuar = false;
				break;
			}
		}
		
		if( tag_text[i].type == 'hidden' && 'ser' == tag_text[i].name.substring( 0, 3 ) )
		{
			kilos += tag_text[i].value + ',';
			id_servicio += tag_text[i].value + ',';
		}
	}
	
	kilos	= kilos.substring( 0, kilos.length - 1 );
	id_servicio	= id_servicio.substring( 0, id_servicio.length - 1 );
	
	if( continuar )
	{
		if( kilos )
		{
			$.post( "peticiones/pet_venta_procesar.php", { commit:commit, chk_tipo_pago:chk_tipo_pago, pag_tarjeta:pag_tarjeta, pag_comision:pag_comision, iva_cobro:iva_cobro, iva_monto:iva_monto, f_entrega:f_entrega, obs:obs, id_socio:id_socio, total_a_pagar:total_a_pagar, efectivo:efectivo, descuento:descuento, kilos:kilos, id_servicios:id_servicio, tipo:tipo, envio:true },
			
			function( datos )
			{
				if( commit == 'S' )
				{
					var datos	= JSON.parse( datos );
					
					if( datos.num == 1 )
					{
						cerrar_modal();
						
						if( datos.tkt == 'S' )
						{
							if( datos.imp == 'T' )
							{
								var t_ticket = "?folio=" + datos.msj + "&id=" + datos.idv + "&efectivo=" + datos.efectivo + "&tipo=" + tipo;
								
								document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket.php" + t_ticket + "' frameborder=0 width=0 height=0></iframe>";
								
								ticket.print();
								ticket.focus();
								
								setInterval( false, 1000 );
								location.href = ".?s=venta&i=" + tipo.toLowerCase();
							}
							else
								location.href = ".?s=venta&i=ticket&folio=" + datos.msj + "&id_socio=" + datos.soc + "&id=" + datos.idv + "&anio=" + datos.anio + "&tipo=" + tipo;
						}
						else
							location.href = ".?s=venta&i=" + tipo.toLowerCase();
					}
					else
					{
						document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
						document.getElementById( 'msj_procesar' ).innerHTML	= datos.num + '. ' + datos.msj;
						document.getElementById( 'img_procesar' ).innerHTML	= "";
					}
				}
				else
				{
					$( '#modal_principal' ).html( datos );
					
					$( '#modal_principal' ).modal();
					$( '#modal_principal' ).modal({ keyboard: true });
					$( '#modal_principal' ).modal('show');
				}
			});
		}
		else
		{
			alert( 'Operación inválida.' );
		}
	}
	else
	{
		alert( 'Cantidad de un Servicio inválido.' );
	}
	
	return false;
}
