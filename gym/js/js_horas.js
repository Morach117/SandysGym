function calcular_total_pago()
{
	var tiempo		= document.getElementById( 'tiempo' ).value;
	var comision	= document.getElementById( 'comision' ).value;
	var cuota		= document.getElementById( 'hor_cuota' ).value;
	var total		= 0;
	
	cuota = cuota * tiempo;
	
	if( document.getElementById( 'm_pago_e' ).checked )
	{
		total = cuota;
	}
	else if( document.getElementById( 'm_pago_t' ).checked )
	{
		if( comision > 0 )
			total = parseFloat( cuota ) + ( cuota * ( comision / 100 ) );
		else
			total = cuota;
	}
	
	document.getElementById( 'tag_total_pago' ).innerHTML = '$' + parseFloat( total ).toFixed(2);
}

function mostrar_modal_hora( id_hora, token )
{
	if( id_hora )
	{
		$.post( "peticiones/pet_hora_ticket.php", { id_hora:id_hora, token:token, envio : true },
		function( datos )
		{
			$( '#modal_principal' ).html( datos );
			
			$( '#modal_principal' ).modal();
			$( '#modal_principal' ).modal({ keyboard: false });
			$( '#modal_principal' ).modal('show');
		});
	}
}

function imprimir_ticket_hora( id_hora, token )
{
	if( id_hora )
	{
		var parametros = "?IDH=" + id_hora + "&token=" + token;
		
		document.getElementById( 'ticket_cliente' ).innerHTML = "<iframe name='ticket' src='ticket_hora.php" + parametros + "' frameborder=0 width=0 height=0></iframe>";
		ticket.focus();
		ticket.print();
		setInterval( false, 1000 );
		location.href='.?s=horas';
	}
}

//aqui se separa
function actualizar_couta( cuota, multiplicador )
{
	document.getElementById( 'tiempo' ).value = multiplicador;
	document.getElementById( 'cuota' ).innerHTML = '$' + ( cuota * multiplicador ).toFixed(2);
	
	calcular_total_pago();
}

function actualiza_horas()
{
	var fecha_actual = new Date();
	
	//1. de entrada
	
	var horas		= fecha_actual.getHours();
	var minutos		= fecha_actual.getMinutes();
	var segundos	= fecha_actual.getSeconds();
	var am_pm		= '';
	var tiempo		= hora_seleccionada();
	
	if( horas >= 12 )
	{
		if( horas > 12 )
			horas = horas - 12;
		am_pm = 'pm';
	}
	else
	{
		am_pm = 'am';
	}
	
	if( horas < 10 ) { horas		= '0' + horas; };
	if( minutos < 10 ) { minutos	= '0' + minutos; };
	if( segundos < 10 ) { segundos	= '0' + segundos; };
	
	document.getElementById( 'h_entrada' ).innerHTML	= horas + ':' + minutos + ':' + segundos + ' ' + am_pm;
	
	//2. para salida
	
	horas		= fecha_actual.getHours();
	minutos		= fecha_actual.getMinutes();
	
	horas	= parseInt( horas ) +  parseInt( tiempo[0] );
	minutos	= parseInt( minutos ) +  parseInt( tiempo[1] );
	
	if( minutos >= 60 )
	{
		minutos -= 60;
		horas	+= 1;
	}
	
	if( horas >= 12 )
	{
		if( horas >= 12 && horas <= 23 )
			am_pm = 'pm';
		else
			am_pm = 'am';
		
		if( horas >= 24 )
			horas = horas - 24;
		else if( horas > 12 )
			horas = horas - 12;
	}
	else
		am_pm = 'am';
	
	if( horas < 10 ) { horas		= '0' + horas; };
	if( minutos < 10 ) { minutos	= '0' + minutos; };
	
	document.getElementById( 'h_salida' ).innerHTML		= horas + ':' + minutos + ':' + segundos + ' ' + am_pm;
}

window.onload = function()
{
	calcular_total_pago();
	setInterval( actualiza_horas, 1000 );
}

function hora_seleccionada()
{
	var tiempo_seleccionado	= document.getElementsByName( 'hor_horas' );
	var hora				= '00:00:00';
	var cadena				= '';
	
	for( i = 0; i < tiempo_seleccionado.length; i++ )
	{
		if( tiempo_seleccionado[i].checked )
		{
			hora = tiempo_seleccionado[i].value;
			break;
		}
	}
	
	cadena = hora.split( ':' );
	
	return cadena;
}