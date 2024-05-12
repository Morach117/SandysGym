function mostrar_modal_corte( id_corte, id_usuario )
{
	if( id_corte )
	{
		$.post( "peticiones/pet_caja_ticket.php", { id_corte:id_corte, id_usuario:id_usuario, envio : true },
		function( datos )
		{
			$( '#modal_principal' ).html( datos );
			
			$( '#modal_principal' ).modal();
			$( '#modal_principal' ).modal({ keyboard: true });
			$( '#modal_principal' ).modal('show');
		});
	}
}

function imprimir_ticket_corte_diario( id_corte, id_usuario )
{
	if( id_corte )
	{
		var parametros = "?IDC=" + id_corte + "&IDU=" + id_usuario;
		
		cerrar_modal();
		
		document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket_caja.php" + parametros + "' frameborder=0 width=0 height=0></iframe>";
		ticket.focus();
		ticket.print();
		
		setInterval( "location.href='.?s=caja'", 1000 );
	}
	else
	{
		document.getElementById( 'msj_procesar' ).innerHTML	= "Ticket inválido.";
		document.getElementById( 'img_procesar' ).innerHTML	= "";
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
	}
}