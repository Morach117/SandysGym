function seleccionar_usuario( id_usuario )
{
	var actual	= document.getElementById( 'id_usuario' ).value;
	
	document.getElementById( 'li_' + id_usuario ).style.backgroundColor	= "#CA5844";
	document.getElementById( 'id_usuario' ).value = id_usuario;
	
	if( actual > 0 )
		document.getElementById( 'li_' + actual ).style.backgroundColor		= "#B7BAD6";
}

function cambiar_status_todos_commit()
{
	document.getElementById( 'btn_procesar' ).innerHTML		= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
	document.getElementById( 'msj_procesar' ).innerHTML		= "Un momento, procesando...";
	document.getElementById( 'img_procesar' ).innerHTML		= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_lavado_cambiar_status_todos_commit.php", { envio : true },
	function( datos )
	{
		var exito = JSON.parse( datos );
		
		document.getElementById( 'btn_procesar' ).innerHTML		= "<button type='button' onclick='location.href=\".?s=lavado\"' class='btn btn-default' data-dismiss='modal'>Salir</button>";
		document.getElementById( 'msj_procesar' ).innerHTML		= exito.msj;
		document.getElementById( 'img_procesar' ).innerHTML		= "";
	});
}

function cambiar_status_todos()
{
	$.post( "peticiones/pet_lavado_cambiar_status_todos.php", { envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal('show');
	});
}

function ver_detalle( id_venta, folio )
{
	$.post( "peticiones/pet_lavado.php", { id_venta:id_venta, folio:folio, envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal('show');
	});
}
						//folio, cambiar_status[S|N](S)
function cambiar_status( id_venta, folio, status )
{
	var	obs			= document.getElementById( 'observaciones' ).value;
	var lavador		= document.getElementById( 'id_usuario' ).value;
	var	pagando		= parseFloat( document.getElementById( 'pagar_adeudo' ).value );
	var	por_pagar	= parseFloat( document.getElementById( 'por_pagar' ).value );
	
	if( status == 'N' && !obs && pagando <= 0 )
		return false;
	
	document.getElementById( 'btn_procesar' ).innerHTML		= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
	document.getElementById( 'guardar_anticipo' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
	document.getElementById( 'msj_procesar' ).innerHTML		= "Un momento, procesando...";
	document.getElementById( 'img_procesar' ).innerHTML		= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	document.getElementById( 'lista_lavados' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_lavado_status.php", { por_pagar:por_pagar, pagando:pagando, lavador:lavador, obs:obs, status:status, id_venta:id_venta, folio:folio, envio : true },
	
	function( datos )
	{
		document.getElementById( 'lista_lavados' ).innerHTML = datos;
	});
	
	$( '#modal_principal' ).modal('hide');
	
	if( status == 'N' )
		ver_detalle( id_venta, folio );
}