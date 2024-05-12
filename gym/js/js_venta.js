function seleccionar_socio( par_id_socio, par_id_prepago, par_saldo, nombre )
{
	var id_socio	= parseInt( par_id_socio );
	var id_prepago	= parseInt( par_id_prepago );
	var saldo		= parseFloat( par_saldo );
	
	$( '#modal_principal' ).empty();
	$( '#modal_principal' ).modal('hide');
	
	if( id_socio > 0 && id_prepago > 0 && saldo > 0 )
	{
		document.getElementById( 'nombre_socio' ).innerHTML = nombre + ', saldo: $' + saldo.toFixed(2);
		document.getElementById( 'div_prepago' ).style.display = 'block';
		
		document.getElementById( 'prep_id_prepago' ).value = id_prepago;
		document.getElementById( 'prep_saldo' ).value = saldo;
		document.getElementById( 'mostrar_socio' ).checked 	= true;
	}
}

function mostrar_socios( evento )
{
	if( evento.target.nodeName != 'INPUT' || ( evento.target.nodeName == 'INPUT' && evento.target.checked == true ) )
	{
		document.getElementById( 'mostrar_socio' ).checked 	= false;
		
		$.post( "peticiones/pet_venta_socios.php", { envio : true },
				
		function( datos )
		{
			$( '#modal_principal' ).html( datos );
			
			$( '#modal_principal' ).modal();
			$( '#modal_principal' ).modal({ keyboard: true });
			$( '#modal_principal' ).modal('show');
		});
	}
	else
	{
		document.getElementById( 'nombre_socio' ).innerHTML = '';
		document.getElementById( 'div_prepago' ).style.display = 'none';
		
		document.getElementById( 'prepago' ).value = '';
		document.getElementById( 'prep_id_prepago' ).value = 0;
		document.getElementById( 'prep_saldo' ).value = 0;
		document.getElementById( 'mostrar_socio' ).checked 	= false;
	}
}

function agregar_articulo_venta( id_articulo )
{
	if( !document.getElementById( 'art_' + id_articulo ) )
	{
		$.post( "peticiones/pet_venta.php", { id_articulo : id_articulo, envio : true },
				
		function( datos )
		{
			$( "#articulo_venta" ).append( datos );
			
			calcular_total();
		});
	}
}

function calcular_importe( id_articulo )
{
	
	var precio		= document.getElementById( 'pre_' + id_articulo ).value;
	var cantidad	= document.getElementById( 'can_' + id_articulo ).value;
	
	var importe		= cantidad * precio;
	
	document.getElementById( 'imp_' + id_articulo ).innerHTML = '$' + importe.toFixed(2);
	
	calcular_total();
}

function calcular_total()
{
	var total		= 0;
	var sub_total	= 0;
	var id_articulo	= '';
	var tag_text	= document.getElementsByTagName( 'input' );
	var comision	= document.getElementById( 'comision' ).value;
	
	var precio		= 0;
	var cantidad	= 0;
	
	for( var i = 0; i < tag_text.length; i++ )
	{
		if( tag_text[i].type == 'hidden' && 'art' == tag_text[i].name.substring( 0, 3 ) )
		{
			id_articulo	= tag_text[i].value;
			precio		= document.getElementById( 'pre_' + id_articulo ).value;
			cantidad	= document.getElementById( 'can_' + id_articulo ).value;
			
			var importe	= cantidad * precio;
			
			sub_total += importe;
		}
	}
	
	if( document.getElementById( 'm_pago_e' ).checked )
	{
		$( "#mostrar_socio" ).attr( 'disabled', false );
		$( "#prepago" ).attr( 'disabled', false );
		$( "#efectivo" ).attr( 'disabled', false );
		
		total = sub_total;
	}
	else if( document.getElementById( 'm_pago_t' ).checked )
	{
		$( "#mostrar_socio" ).attr( 'disabled', true );
		$( "#prepago" ).attr( 'disabled', true );
		$( "#efectivo" ).attr( 'disabled', true );
		
		if( comision > 0 )
			total = parseFloat( sub_total ) + ( sub_total * ( comision / 100 ) );
		else
			total = sub_total;
	}
	
	document.getElementById( 'input_total' ).value = total;
	
	document.getElementById( 'tag_sub_total' ).innerHTML = '$' + sub_total.toFixed(2);
	document.getElementById( 'tag_total_pago' ).innerHTML = '$' + parseFloat( total ).toFixed(2);
}

function quitar_de_lista( id_articulo )
{
	document.getElementById( 'art_' + id_articulo ).innerHTML = '';
	
	calcular_total();
}

/*se ejecuta cuando se le da procesar*/
function checar_articulos( commit )
{
	var regex_decimal 	= /^[\d]+$/;
	var tag_text 		= document.getElementsByTagName( 'input' );
	var comision		= parseFloat( document.getElementById( 'comision' ).value );
	var efectivo		= parseFloat( document.getElementById( 'efectivo' ).value );
	var prepago_imp		= parseFloat( document.getElementById( 'prepago' ).value );
	var saldo			= parseFloat( document.getElementById( 'prep_saldo' ).value );
	var id_prepago		= parseInt( document.getElementById( 'prep_id_prepago' ).value );
	var prepago			= document.getElementById( 'mostrar_socio' ).checked;
	var ban_prepago		= 0;//0=no, 1=si para php
	var total_a_pagar	= parseFloat( document.getElementById( 'input_total' ).value );
	var total_pago		= 0;
	var cantidad		= "";
	var id_articulo		= "";
	var continuar		= false;
	var tipo_pago		= '';
	
	if( document.getElementById( 'm_pago_e' ).checked )
		tipo_pago = 'E';
	else if( document.getElementById( 'm_pago_t' ).checked )
		tipo_pago = 'T';
	
	if( commit == 'S' )
	{
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
		document.getElementById( 'msj_procesar' ).innerHTML	= "Un momento, procesando...";
		document.getElementById( 'img_procesar' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	}
	
	if( isNaN( prepago_imp ) ) { prepago_imp = 0; }
	if( isNaN( efectivo ) ) { efectivo = 0; }
	
	if( prepago )
	{
		if( prepago_imp > saldo )
		{
			alert( 'La cantidad de PrePago no puede ser mayor al Saldo que tiene el Socio.' );
			return false;
		}
		
		if( prepago_imp > total_a_pagar )
		{
			alert( 'La cantidad del PrePago no puede ser mayor al total a pagar.' );
			return false;
		}
		
		ban_prepago = 1;
	}
	else
		prepago_imp = 0;
	
	total_pago = efectivo + prepago_imp;
	
	if( total_a_pagar > total_pago && tipo_pago == 'E' )
	{
		alert( 'Pago incompleto.' );
		return false;
	}
	
	for( var i = 0; i < tag_text.length; i++ )
	{
		if( tag_text[i].type == 'text' && 'can' == tag_text[i].name.substring( 0, 3 ) )
		{
			if( regex_decimal.test( tag_text[i].value ) )
			{
				cantidad += tag_text[i].value + '-';
				continuar = true;
			}
			else
			{
				continuar = false;
				break;
			}
		}
		
		if( tag_text[i].type == 'hidden' && 'art' == tag_text[i].name.substring( 0, 3 ) )
		{
			cantidad += tag_text[i].value + ',';
			id_articulo += tag_text[i].value + ",";
		}
	}
	
	cantidad	= cantidad.substring( 0, cantidad.length - 1 );
	id_articulo	= id_articulo.substring( 0, id_articulo.length - 1 );
	
	if( continuar )
	{
		if( cantidad && id_articulo )
		{
			$.post( "peticiones/pet_venta_procesar.php", { commit:commit, comision:comision, tipo_pago:tipo_pago, prepago:ban_prepago, saldo:saldo, id_prepago:id_prepago, total_a_pagar:total_a_pagar, efectivo:efectivo, prepago_imp:prepago_imp, cantidad : cantidad, id_articulo:id_articulo, envio : true },
			
			function( datos )
			{
				if( commit == 'S' )
				{
					var exito	= JSON.parse( datos );
					
					if( exito.num == 1 )
					{
						var t_ticket = "?folio=" + exito.folio + "&IDV=" + exito.IDV + "&efectivo=" + exito.efectivo + "&prepago_imp=" + exito.prepago_imp;
						
						cerrar_modal();
						
						if( exito.ticket == 'S' )
						{
							document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket.php" + t_ticket + "' frameborder=0 width=0 height=0></iframe>";
							ticket.focus();
							ticket.print();
						}
						
						setInterval( false, 1000 );
						location.href='.?s=venta';
					}
					else
					{
						document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
						document.getElementById( 'msj_procesar' ).innerHTML	= exito.num + '. ' + exito.msj;
						document.getElementById( 'img_procesar' ).innerHTML	= "";
					}
				}
				else
				{
					$( '#modal_principal' ).html( datos );
					
					$( '#modal_principal' ).modal();
					$( '#modal_principal' ).modal({ keyboard: false });
					$( '#modal_principal' ).modal('show');
				}
			});
		}
		else
			alert( 'Operación inválida.' );
	}
	else
		alert( 'Cantidad de un Articulo inválido o no hay articulos seleccionados.' );
	
	return false;
}

function buscar_articulo()
{
	document.getElementById( 'lista_articulos' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	var criterio	= document.getElementById( 'criterio_busqueda' ).value;
	
	$.post( "peticiones/pet_venta_buscar.php", { criterio : criterio, envio : true },
			
	function( datos )
	{
		document.getElementById( 'lista_articulos' ).innerHTML = datos;
	});
}