var fecha			= new Date();
var fecha_actual	= fecha.getDate() + "-" + ( fecha.getMonth() + 1 ) + "-" + fecha.getFullYear();
var rango			= ( fecha.getFullYear() - 5 ) + ":" + ( fecha.getFullYear() + 5 );

window.onload = function()
{
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

//>>>>>aqui transferencias porque se usa en transferencias y articulos

function nueva_transferencia( seccion )
{
	$.post( "peticiones/pet_transferencias_nueva.php", { seccion:seccion, envio : true },
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: false });
		$( '#modal_principal' ).modal('show');
	});
}

function guardar_transferencia( seccion )
{
	var t_origen	= document.getElementById( 't_origen' ).value;
	var t_destino	= document.getElementById( 't_destino' ).value;
	var t_entrega	= document.getElementById( 't_entrega' ).value;
	
	if( t_origen && t_destino )
	{
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
		document.getElementById( 'img_procesar' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
		document.getElementById( 'msj_procesar' ).innerHTML	= "Un momento, procesando...";
		
		$.post( "peticiones/pet_transferencias_nueva_guardar.php", { t_origen:t_origen, t_destino:t_destino, t_entrega:t_entrega, envio : true },
		function( datos )
		{
			var exito = JSON.parse( datos );
			
			if( exito.num == 1 )
			{
				document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-default'>Cerrar</button>";
				document.getElementById( 'img_procesar' ).innerHTML	= "";
				document.getElementById( 'msj_procesar' ).innerHTML	= exito.msj;
				
				if( seccion == 'transferencias' )
				{
					setInterval( false, 1000 );
					location.href = ".?s=transferencias";
				}
			}
			else
			{
				document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' onclick='guardar_transferencia()' class='btn btn-primary'>Reintentar</button>";
				document.getElementById( 'img_procesar' ).innerHTML	= "";
				document.getElementById( 'msj_procesar' ).innerHTML	= exito.msj;
			}
		});
	}
}

function agregar_a_transferencia( id_articulo, folio, seccion )
{
	$.post( "peticiones/pet_transferencias_articulo.php", { id_articulo:id_articulo, folio:folio, seccion:seccion, envio : true },
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: false });
		$( '#modal_principal' ).modal('show');
	});
}

function guardar_transferencia_articulo( id_articulo, folio, seccion )
{
	var regex_numero 	= /^-?\d+(\.\d+)?$/;
	var tag_text 		= document.getElementsByTagName( 'input' );
	var cadena			= "";
	var bandera			= false;
	
	for( var i = 0; i < tag_text.length; i++ )
	{
		if( tag_text[i].type == 'text' && 'tcan' == tag_text[i].id.substring( 0, 4 ) )
		{
			if( regex_numero.test( tag_text[i].value ) )
			{
				cadena	+= tag_text[i].value + ':';
				bandera	= true;
			}
			else
				cadena += 0 + ':';
		}
		
		if( tag_text[i].type == 'hidden' && 'tfol' == tag_text[i].id.substring( 0, 4 ) )
			cadena += tag_text[i].value + ':';
		
		if( tag_text[i].type == 'hidden' && 'tmov' == tag_text[i].id.substring( 0, 4 ) )
			cadena += tag_text[i].value + ',';
	}
	
	if( bandera )
	{
		cadena	= cadena.substring( 0, cadena.length - 1 );
		
		// alert( cadena ); //11:2:U,22:1:I    cantidad:folio:movimiento
		
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
		document.getElementById( 'msj_procesar' ).innerHTML	= "Un momento, procesando...";
		document.getElementById( 'img_procesar' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
		
		$.post( "peticiones/pet_transferencias_articulo_guardar.php", { id_articulo:id_articulo, cadena:cadena, envio : true },
		function( datos )
		{
			var exito = JSON.parse( datos );
			
			if( exito.num == 1 )
			{
				document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-default'>Cerrar</button>";
				document.getElementById( 'msj_procesar' ).innerHTML	= exito.msj;
				document.getElementById( 'img_procesar' ).innerHTML	= "";
				
				if( seccion == 'transferencias' )
				{
					setInterval( false, 1000 );
					location.href = ".?s=transferencias&i=detalle&folio=" + folio;
				}
			}
			else
			{
				document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' onclick='guardar_transferencia_articulo( " + id_articulo + ", " + folio + ", \"" + seccion + "\" )' class='btn btn-primary'>Reintentar</button>";
				document.getElementById( 'msj_procesar' ).innerHTML	= exito.num + '. ' + exito.msj;
				document.getElementById( 'img_procesar' ).innerHTML	= "";
			}
		});
	}
	else
	{
		document.getElementById( 'msj_procesar' ).innerHTML = "No se escribieron cantidades para agregar";
	}
}

//<<<<<aqui termina transferencias