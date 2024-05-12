function mostrar_modal_visita( id_visita, token )
{
	if( id_visita )
	{
		$.post( "peticiones/pet_visita_ticket.php", { id_visita:id_visita, token:token, envio : true },
		function( datos )
		{
			$( '#modal_principal' ).html( datos );
			
			$( '#modal_principal' ).modal();
			$( '#modal_principal' ).modal({ keyboard: false });
			$( '#modal_principal' ).modal('show');
		});
	}
}

function imprimir_ticket_visita( id_visita, token )
{
	if( id_visita )
	{
		var parametros = "?IDV=" + id_visita + "&token=" + token;
		
		document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket_visita.php" + parametros + "' frameborder=0 width=0 height=0></iframe>";
		ticket.focus();
		ticket.print();
		location.href='.?s=visitas';
	}
	else
	{
		document.getElementById( 'msj_procesar' ).innerHTML	= "Ticket inválido.";
		document.getElementById( 'img_procesar' ).innerHTML	= "";
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
	}
}