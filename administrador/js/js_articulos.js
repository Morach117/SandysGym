function calculos_costo()
{
	var costo	= document.getElementById( 'art_costo' ).value;
	
	document.getElementById( 'art_mayoreo_2' ).value = ( costo * 1.20 ).toFixed( 2 );
	
	utilidad( document.getElementById( 'art_precio' ).value, 'util_pp_pesos', 'util_pp_porce' );
	utilidad( document.getElementById( 'art_mayoreo_1' ).value, 'util_m1_pesos', 'util_m1_porce' );
	utilidad( document.getElementById( 'art_mayoreo_2' ).value, 'util_m2_pesos', 'util_m2_porce' );
}

function calculos_monedero()
{
	var art_monedero	= document.getElementById( 'art_monedero' ).value;
	var art_costo		= document.getElementById( 'art_costo' ).value;
	var art_precio		= document.getElementById( 'art_precio' ).value;
	var mon_monto		= ( art_monedero * art_precio ) / 100;
	var mon_porcentaje	= 0;
	var utilidad		= art_precio - art_costo;
	
	if( utilidad > 0 )
		mon_porcentaje = ( mon_monto * 100 ) / utilidad;
	
	document.getElementById( 'mon_monto' ).innerHTML = '$' + mon_monto.toFixed( 2 );
	document.getElementById( 'mon_porce_util_monto' ).innerHTML = mon_porcentaje.toFixed( 2 ) + '%';
}

function utilidad( valor, tag_utilidad_pesos, tag_utilidad_porce )
{
	var art_costo	= document.getElementById( 'art_costo' ).value;
	
	if( valor > 0 )
	{
		var u_porce		= ( ( valor * 100 ) / art_costo ) - 100;
		var u_pesos		= valor - art_costo;
	}
	else
	{
		var u_porce		= 0;
		var u_pesos		= 0;
	}
	
	document.getElementById( tag_utilidad_porce ).innerHTML = u_porce.toFixed( 2 ) + '%';
	document.getElementById( tag_utilidad_pesos ).innerHTML = '$' + u_pesos.toFixed( 2 );
}

function mostrar_modal_categorias()
{
	$.post( "peticiones/pet_articulos_categorias.php", { envio : true },
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: false });
		$( '#modal_principal' ).modal('show');
	});
}

function mostrar_modal_marcas()
{
	$.post( "peticiones/pet_articulos_marcas.php", { envio : true },
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: false });
		$( '#modal_principal' ).modal('show');
	});
}

function guardar_categoria()
{
	var descripcion	= document.getElementById( 'cat_descripcion' ).value;
	
	if( descripcion )
	{
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
		document.getElementById( 'img_procesar' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
		document.getElementById( 'msj_procesar' ).innerHTML	= "Un momento, procesando...";
		
		$.post( "peticiones/pet_articulos_categorias_guardar.php", { cat_descripcion:descripcion, envio : true },
		function( datos )
		{
			var exito = JSON.parse( datos );
			
			document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
			document.getElementById( 'img_procesar' ).innerHTML	= "";
			document.getElementById( 'msj_procesar' ).innerHTML	= exito.msj;
			
			if( exito.num == 1 )
			{
				document.getElementById( 'tabla_categorias' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
				
				$.post( "peticiones/pet_articulos_categorias_recargar.php", { envio : true },
				function( recargar )
				{
					document.getElementById( 'tabla_categorias' ).innerHTML = recargar;
				});
			}
		});
	}
}

function guardar_marca()
{
	var descripcion	= document.getElementById( 'mar_descripcion' ).value;
	
	if( descripcion )
	{
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
		document.getElementById( 'img_procesar' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
		document.getElementById( 'msj_procesar' ).innerHTML	= "Un momento, procesando...";
		
		$.post( "peticiones/pet_articulos_marcas_guardar.php", { mar_descripcion:descripcion, envio : true },
		function( datos )
		{
			var exito = JSON.parse( datos );
			
			document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
			document.getElementById( 'img_procesar' ).innerHTML	= "";
			document.getElementById( 'msj_procesar' ).innerHTML	= exito.msj;
			
			if( exito.num == 1 )
			{
				document.getElementById( 'tabla_marcas' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
				
				$.post( "peticiones/pet_articulos_marcas_recargar.php", { envio : true },
				function( recargar )
				{
					document.getElementById( 'tabla_marcas' ).innerHTML = recargar;
				});
			}
		});
	}
}