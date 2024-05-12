function mostrar_captura_prepago( par_id_socio )
{
	document.getElementById( 'nuevo_prepago' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_prepago_nuevo.php", { id_socio : par_id_socio, envio : true },
			
	function( datos )
	{
		document.getElementById( 'nuevo_prepago' ).innerHTML = datos;
	});
}

function mostrar_modal_prepago( id_prepago, id_prepago_d, id_socio, token )
{
	if( id_prepago )
	{
		$.post( "peticiones/pet_prepago_ticket.php", { id_prepago:id_prepago, id_prepago_d:id_prepago_d, id_socio:id_socio, token:token, envio : true },
		function( datos )
		{
			$( '#modal_principal' ).html( datos );
			
			$( '#modal_principal' ).modal();
			$( '#modal_principal' ).modal({ keyboard: true });
			$( '#modal_principal' ).modal('show');
		});
	}
}

function imprimir_ticket_prepago( id_prepago, id_prepago_d, id_socio, token )
{
	if( id_prepago )
	{
		var parametros = "?IDP=" + id_prepago + "&IDD=" + id_prepago_d + "&IDS=" + id_socio + "&token=" + token;
		
		cerrar_modal();
		document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket_prepago.php" + parametros + "' frameborder=0 width=0 height=0></iframe>";
		ticket.focus();
		ticket.print();
		location.href='.?s=prepagos';
	}
	else
	{
		document.getElementById( 'msj_procesar' ).innerHTML	= "Ticket inválido.";
		document.getElementById( 'img_procesar' ).innerHTML	= "";
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
	}
}