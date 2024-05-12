function buscar_horase()
{
	document.getElementById( 'tabla_horase' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	var rango_ini	= document.getElementById( 'rango_ini' ).value;
	var rango_fin	= document.getElementById( 'rango_fin' ).value;
	
	if( rango_ini.length == 10 && rango_fin.length == 10 )
	{
		$.post( "peticiones/pet_reportes_horas_eliminadas.php", { rango_ini:rango_ini, rango_fin:rango_fin, envio : true },
				
		function( datos )
		{
			document.getElementById( 'tabla_horase' ).innerHTML = datos;
		});
	}
	
	document.getElementById( 'tabla_horase' ).innerHTML = "<tr><td colspan='6'>Pendiente de seleccionar fechas</td></tr>";
}

function buscar_pagose()
{
	document.getElementById( 'tabla_pagose' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	var rango_ini	= document.getElementById( 'rango_ini' ).value;
	var rango_fin	= document.getElementById( 'rango_fin' ).value;
	
	if( rango_ini.length == 10 && rango_fin.length == 10 )
	{
		$.post( "peticiones/pet_reportes_mensualidades_eliminadas.php", { rango_ini:rango_ini, rango_fin:rango_fin, envio : true },
				
		function( datos )
		{
			document.getElementById( 'tabla_pagose' ).innerHTML = datos;
		});
	}
	
	document.getElementById( 'tabla_pagose' ).innerHTML = "<tr><td colspan='6'>Pendiente de seleccionar fechas</td></tr>";
}

function buscar_mensualidades()
{
	document.getElementById( 'tabla_mensualidades' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	var rango_ini	= document.getElementById( 'rango_ini' ).value;
	var rango_fin	= document.getElementById( 'rango_fin' ).value;
	
	if( rango_ini.length == 10 && rango_fin.length == 10 )
	{
		$.post( "peticiones/pet_reportes_mensualidades_pagadas.php", { rango_ini:rango_ini, rango_fin:rango_fin, envio : true },
				
		function( datos )
		{
			document.getElementById( 'tabla_mensualidades' ).innerHTML = datos;
		});
	}
	
	document.getElementById( 'tabla_mensualidades' ).innerHTML = "<tr><td colspan='5'>Pendiente de seleccionar fechas</td></tr>";
}

function buscar_fechasa()
{
	document.getElementById( 'tabla_fechasa' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	var rango_ini	= document.getElementById( 'rango_ini' ).value;
	var rango_fin	= document.getElementById( 'rango_fin' ).value;
	
	if( rango_ini.length == 10 && rango_fin.length == 10 )
	{
		$.post( "peticiones/pet_reportes_fechas_actualizadas.php", { rango_ini:rango_ini, rango_fin:rango_fin, envio : true },
				
		function( datos )
		{
			document.getElementById( 'tabla_fechasa' ).innerHTML = datos;
		});
	}
	
	document.getElementById( 'tabla_fechasa' ).innerHTML = "<tr><td colspan='6'>Pendiente de seleccionar fechas</td></tr>";
}