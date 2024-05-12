function eliminar_detalle_gasto_recargar()
{
	var anio		= document.getElementById( 'año_calcular' ).value;
	var mes			= document.getElementById( 'mes_calcular' ).value;
	var sucursal	= document.getElementById( 'sucursal' ).value;
	
	$.post( "peticiones/pet_catalogos_gastos_recargar.php", { anio:anio, mes:mes, sucursal:sucursal, envio : true },
	function( datos )
	{
		document.getElementById( 'tabla_gastos' ).innerHTML = datos;
	});
}

function eliminar_detalle_gasto_commit( id_gasto )
{
	document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
	document.getElementById( 'msj_procesar' ).innerHTML	= "Un momento, procesando...";
	document.getElementById( 'img_procesar' ).innerHTML	= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_catalogos_gastos_eliminar_commit.php", { id_gasto:id_gasto, envio : true },
	function( datos )
	{
		var exito = JSON.parse( datos );
		
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
		document.getElementById( 'msj_procesar' ).innerHTML	= exito.num + '. ' + exito.msj;
		document.getElementById( 'img_procesar' ).innerHTML	= "";
		
		if( exito.num == 1 )
			eliminar_detalle_gasto_recargar();
	});
}

function eliminar_detalle_gasto( id_gasto )
{
	$.post( "peticiones/pet_catalogos_gastos_eliminar.php", { id_gasto:id_gasto, envio : true },
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: false });
		$( '#modal_principal' ).modal('show');
	});
}

function mostrar_detalle_gasto( id_gasto )
{
	$.post( "peticiones/pet_catalogos_gastos.php", { id_gasto:id_gasto, envio : true },
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: false });
		$( '#modal_principal' ).modal('show');
	});
}

function calcular_total_gastos()
{
	var g_importe	= parseFloat( document.getElementById( 'g_importe' ).value );
	var g_iva		= parseFloat( document.getElementById( 'g_iva' ).value );
	var g_descuento	= parseFloat( document.getElementById( 'g_descuento' ).value );
	var resultado	= 0;
	
	if( g_importe > 0 && g_iva >= 0 && g_descuento >= 0 )
		resultado = ( g_importe + g_iva ) - g_descuento;
	else if( g_importe > 0 && g_iva > 0 )
		resultado = g_importe + g_iva;
	else
		resultado = g_importe;
		
	document.getElementById( 'g_total' ).value = resultado.toFixed( 2 );
}