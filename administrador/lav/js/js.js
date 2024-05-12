function cerrar_modal()
{
	$( '#modal_principal' ).empty();
	$( '#modal_principal' ).modal('hide');
}

function ver_detalle( id_venta, folio )
{
	$.post( "peticiones/pet_reportes_revision.php", { id_venta:id_venta, folio:folio, envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: true });
		$( '#modal_principal' ).modal('show');
	});
}

function cambiar_status( id_venta, folio )
{
	//del no modal
	var b_cliente	= document.getElementById( 'b_cliente' ).value;
	var b_orden		= document.getElementById( 'b_orden' ).value;
	var b_status	= "";
	
	if( document.getElementById( 'b_status_1' ).checked )
		b_status = document.getElementById( 'b_status_1' ).value;
	else if( document.getElementById( 'b_status_2' ).checked )
		b_status = document.getElementById( 'b_status_2' ).value;
	
	//del modal
	var	obs			= document.getElementById( 'observaciones' ).value;
	
	document.getElementById( 'btn_procesar' ).innerHTML		= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
	document.getElementById( 'msj_procesar' ).innerHTML		= "Un momento, procesando...";
	document.getElementById( 'img_procesar' ).innerHTML		= "<img src='../../imagenes/spinner.gif' alt='Cargando...' />";
	
	document.getElementById( 'lista_entrega' ).innerHTML = "<img src='../../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_reportes_revision_commit.php", { b_status:b_status, b_cliente:b_cliente, b_orden:b_orden, obs:obs, id_venta:id_venta, folio:folio, envio : true },
	
	function( datos )
	{
		document.getElementById( 'lista_entrega' ).innerHTML = datos;
	});
	
	$( '#modal_principal' ).modal('hide');
}

var fecha			= new Date();
var fecha_actual	= fecha.getDate() + "-" + ( fecha.getMonth() + 1 ) + "-" + fecha.getFullYear();
var rango			= ( fecha.getFullYear() - 5 ) + ":" + ( fecha.getFullYear() + 5 );

window.onload = function()
{
	//catalogos
	$( "#f_actual, #f1, #f2" ).datepicker
	({ 
		monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
		yearRange: rango,
		changeYear: true,
		defaultDate: fecha_actual,
		dateFormat: "dd-mm-yy" 
	});
}