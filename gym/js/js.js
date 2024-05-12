function cerrar_modal()
{
	$( '#modal_principal' ).empty();
	$( '#modal_principal' ).modal('hide');
}

var fecha			= new Date();
var fecha_actual	= fecha.getDate() + "-" + ( fecha.getMonth() + 1 ) + "-" + fecha.getFullYear();
var rango			= ( fecha.getFullYear() - 5 ) + ":" + ( fecha.getFullYear() + 5 );

window.onload = function()
{
	if( document.form_pago || document.form_fechas )
	{
		$( "#pag_fecha_pago" ).datepicker
		({ 
			monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
			dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
			yearRange: rango,
			changeYear: true,
			defaultDate: fecha_actual,
			dateFormat: "dd-mm-yy" 
		});
		
		$( "#pag_fecha_ini" ).datepicker
		({ 
			monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
			dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
			yearRange: rango,
			changeYear: true,
			defaultDate: fecha_actual,
			dateFormat: "dd-mm-yy" 
		});
		
		$( "#pag_fecha_fin" ).datepicker
		({ 
			monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
			dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
			yearRange: rango,
			changeYear: true,
			defaultDate: fecha_actual,
			dateFormat: "dd-mm-yy" 
		});
	}
	
	//fechas para la seccion de socios
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
	
	$( "#g_fecha" ).datepicker
	({ 
		monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
		yearRange: rango,
		changeYear: true,
		defaultDate: fecha_actual,
		dateFormat: "dd-mm-yy" 
	});
}