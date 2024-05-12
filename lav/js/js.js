//se utiliza para seleccionar el texto que contiene un input cuando se le da click
function seleccionar( objeto )
{
	objeto.select();
}

function cerrar_modal()
{
	$( '#modal_principal' ).empty();
	$( '#modal_principal' ).modal('hide');
}

function impresion_termica( p_folio, p_idv, p_tipo )
{
	var t_ticket = "?folio=" + p_folio + "&id=" + p_idv + "&efectivo=0&tipo=" + p_tipo;
	
	document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket.php" + t_ticket + "' frameborder=0 width=0 height=0></iframe>";
	
	ticket.print();
	ticket.focus();
}

var fecha			= new Date();
var fecha_actual	= fecha.getDate() + "-" + ( fecha.getMonth() + 1 ) + "-" + fecha.getFullYear();
var rango			= ( fecha.getFullYear() - 5 ) + ":" + ( fecha.getFullYear() + 5 );

window.onload = function()
{
	//fechas para la seccion de venta
	$( "#f_entrega" ).datepicker
	({ 
		monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
		yearRange: rango,
		changeYear: true,
		defaultDate: fecha_actual,
		dateFormat: "dd-mm-yy" 
	});
	
	//catalogos
	$( "#f_actual" ).datepicker
	({ 
		monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
		yearRange: rango,
		changeYear: true,
		defaultDate: fecha_actual,
		dateFormat: "dd-mm-yy" 
	});
	
	$( "#rango_ini" ).datepicker
	({ 
		monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
		yearRange: rango,
		changeYear: true,
		defaultDate: fecha_actual,
		dateFormat: "dd-mm-yy" 
	});
	
	$( "#rango_fin" ).datepicker
	({ 
		monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
		yearRange: rango,
		changeYear: true,
		defaultDate: fecha_actual,
		dateFormat: "dd-mm-yy" 
	});
}