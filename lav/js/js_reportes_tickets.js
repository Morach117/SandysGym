function mostrar_detalle_ticket( folio )
{
	$.post( "peticiones/pet_reportes_tickets.php", { folio:folio, envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: true });
		$( '#modal_principal' ).modal('show');
	});
}

function eliminar_ticket( folio )
{
	document.getElementById( 'btn_eliminar_ticket' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-danger'>Espera</button>";
	
	document.getElementById( 'msj_eliminar_ticket' ).innerHTML	= "Un momento, procesando...";
	document.getElementById( 'img_eliminar_ticket' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	if( folio )
	{
		$.post( "peticiones/pet_reportes_tickets_eliminar.php", { folio : folio, envio : true },
		
		function( datos )
		{
			if( datos )
			{
				document.getElementById( 'msj_eliminar_ticket' ).innerHTML	= datos;
				document.getElementById( 'img_eliminar_ticket' ).innerHTML	= "";
			}
			else
			{
				recargar_lista();
				cerrar_modal();
			}
		});
	}
	else
	{
		document.getElementById( 'msj_eliminar_ticket' ).innerHTML	= "Folio inválido, intenta nuevamente.";
		document.getElementById( 'img_eliminar_ticket' ).innerHTML	= "";
	}
}

function recargar_lista()
{
	document.getElementById( 'lista_tickets' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_reportes_tickets_recargar.php", { envio : true },
	
	function( datos )
	{
		document.getElementById( 'lista_tickets' ).innerHTML = datos;
	});
}

function buscar_tickets( criterio )
{
	document.getElementById( 'lista_tickets' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_reportes_tickets_buscar.php", { criterio:criterio, envio : true },
	
	function( datos )
	{
		document.getElementById( 'lista_tickets' ).innerHTML = datos;
	});
}