
function mostrar_modal_pago( id_pago, token )
{
	if( id_pago )
	{
		$.post( "peticiones/pet_socios_pagos_ticket.php", { id_pago:id_pago, token:token, envio : true },
		function( datos )
		{
			$( '#modal_principal' ).html( datos );
			
			$( '#modal_principal' ).modal();
			$( '#modal_principal' ).modal({ keyboard: true });
			$( '#modal_principal' ).modal('show');
		});
	}
}

function imprimir_ticket_pago( id_pago, token )
{
	if( id_pago )
	{
		var parametros = "?IDP=" + id_pago + "&token=" + token;
		
		cerrar_modal();
		
		document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket_pagos.php" + parametros + "' frameborder=0 width=0 height=0></iframe>";
		ticket.focus();
		ticket.print();
		
		setInterval( false, 1000 );
		location.href='.?s=socios&pag_opciones=2';
	}
	else
	{
		document.getElementById( 'msj_procesar' ).innerHTML	= "Ticket inválido.";
		document.getElementById( 'img_procesar' ).innerHTML	= "";
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
	}
}

