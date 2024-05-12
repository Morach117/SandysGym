function cambiar_status_commit( folio, id_venta )
{
	document.getElementById( 'btn_ticket' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-danger'>Espera</button>";
	document.getElementById( 'msj_ticket' ).innerHTML	= "Un momento, procesando...";
	document.getElementById( 'img_ticket' ).innerHTML	= "<img src='../../imagenes/spinner.gif' alt='Cargando...' />";
	
	if( folio )
	{
		$.post( "peticiones/pet_reportes_tickets_status_commit.php", { folio : folio, id_venta:id_venta, envio : true },
		
		function( datos )
		{
			document.getElementById( 'btn_ticket' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-default'>Salir</button>";
			document.getElementById( 'msj_ticket' ).innerHTML	= datos;
			document.getElementById( 'img_ticket' ).innerHTML	= "";
			recargar_lista();
		});
	}
	else
	{
		document.getElementById( 'msj_ticket' ).innerHTML	= "Folio inválido, intenta nuevamente.";
		document.getElementById( 'img_ticket' ).innerHTML	= "";
	}
}

function cambiar_status( folio, id_venta )
{
	$.post( "peticiones/pet_reportes_tickets_status.php", { folio:folio, id_venta:id_venta, envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: true });
		$( '#modal_principal' ).modal('show');
	});
}

function mostrar_detalle_ticket( folio, id_venta )
{
	$.post( "peticiones/pet_reportes_tickets.php", { folio:folio, id_venta:id_venta, envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: true });
		$( '#modal_principal' ).modal('show');
	});
}

function eliminar_ticket( folio, id_venta )
{
	document.getElementById( 'btn_ticket' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-danger'>Espera</button>";
	
	document.getElementById( 'msj_ticket' ).innerHTML	= "Un momento, procesando...";
	document.getElementById( 'img_ticket' ).innerHTML	= "<img src='../../imagenes/spinner.gif' alt='Cargando...' />";
	
	if( folio )
	{
		$.post( "peticiones/pet_reportes_tickets_eliminar.php", { folio : folio, id_venta:id_venta, envio : true },
		
		function( datos )
		{
			if( datos )
			{
				document.getElementById( 'msj_ticket' ).innerHTML	= datos;
				document.getElementById( 'img_ticket' ).innerHTML	= "";
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
		document.getElementById( 'msj_ticket' ).innerHTML	= "Folio inválido, intenta nuevamente.";
		document.getElementById( 'img_ticket' ).innerHTML	= "";
	}
}

function desactivar_ticket( folio, id_venta )
{
	document.getElementById( 'btn_ticket' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-danger'>Espera</button>";
	
	document.getElementById( 'msj_ticket' ).innerHTML	= "Un momento, procesando...";
	document.getElementById( 'img_ticket' ).innerHTML	= "<img src='../../imagenes/spinner.gif' alt='Cargando...' />";
	
	if( folio )
	{
		$.post( "peticiones/pet_reportes_tickets_desactivar.php", { folio : folio, id_venta:id_venta, envio : true },
		
		function( datos )
		{
			if( datos )
			{
				document.getElementById( 'msj_ticket' ).innerHTML	= datos;
				document.getElementById( 'img_ticket' ).innerHTML	= "";
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
		document.getElementById( 'msj_ticket' ).innerHTML	= "Folio inválido, intenta nuevamente.";
		document.getElementById( 'img_ticket' ).innerHTML	= "";
	}
}

function recargar_lista()
{
	document.getElementById( 'lista_tickets' ).innerHTML = "<img src='../../imagenes/spinner.gif' alt='Cargando...' />";
	var busqueda	= document.getElementById( 'busqueda' ).value;
	var anio		= document.getElementById( 'año_calcular' ).value;
	var mes			= document.getElementById( 'mes_calcular' ).value;
	var mes_evaluar	= anio + '-' + mes;
	
	$.post( "peticiones/pet_reportes_tickets_recargar.php", { anio:anio, busqueda:busqueda, mes_evaluar:mes_evaluar, envio : true },
	
	function( datos )
	{
		document.getElementById( 'lista_tickets' ).innerHTML = datos;
	});
}