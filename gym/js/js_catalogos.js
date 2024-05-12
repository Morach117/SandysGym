function buscar_gastos()
{
	document.getElementById( 'tabla_gastos' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	var rango_ini	= document.getElementById( 'rango_ini' ).value;
	var rango_fin	= document.getElementById( 'rango_fin' ).value;
	
	if( rango_ini.length == 10 && rango_fin.length == 10 )
	{
		$.post( "peticiones/pet_catalogos_gastos.php", { rango_ini:rango_ini, rango_fin:rango_fin, envio : true },
				
		function( datos )
		{
			document.getElementById( 'tabla_gastos' ).innerHTML = datos;
		});
	}
	
	document.getElementById( 'tabla_gastos' ).innerHTML = "<tr><td colspan='7'>Pendiente de seleccionar fechas</td></tr>";
}

function calcular_total_gastos()
{
	var g_importe	= parseFloat( document.getElementById( 'g_importe' ).value );
	var g_iva		= parseFloat( document.getElementById( 'g_iva' ).value );
	var g_descuento	= parseFloat( document.getElementById( 'g_descuento' ).value );
	var resultado	= 0;
	
	if( g_importe > 0 && g_iva >= 0 && g_descuento >= 0 )
	{
		resultado = ( g_importe + g_iva ) - g_descuento;
	}
		
	document.getElementById( 'g_total' ).value = resultado.toFixed( 2 );
}