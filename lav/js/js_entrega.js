function ver_detalle( id_venta, folio )
{
	$.post( "peticiones/pet_entrega.php", { id_venta:id_venta, folio:folio, envio : true },
	
	function( datos )
	{
		$( '#modal_principal' ).html( datos );
		
		$( '#modal_principal' ).modal();
		$( '#modal_principal' ).modal({ keyboard: true });
		$( '#modal_principal' ).modal('show');
	});
}

function cambiar_status( id_venta, folio, status )
{
	//del no modal
	var b_cliente	= document.getElementById( 'b_cliente' ).value;
	var b_orden		= document.getElementById( 'b_orden' ).value;
	var c_dia		= document.getElementById( 'c_dia' ).value;
	var c_mes		= document.getElementById( 'c_mes' ).value;
	var c_anio		= document.getElementById( 'c_anio' ).value;
	var b_status	= "";
	var b_lista		= "";
	
	if( c_dia < 10 )
		c_dia = '0' + c_dia;
	
	var b_corte		= c_anio + '-' + c_mes + '-' + c_dia;
	
	if( document.getElementById( 'b_status_1' ).checked )
		b_status = document.getElementById( 'b_status_1' ).value;
	else if( document.getElementById( 'b_status_2' ).checked )
		b_status = document.getElementById( 'b_status_2' ).value;
	
	if( document.getElementById( 'b_pago_1' ).checked )
		b_lista = document.getElementById( 'b_pago_1' ).value;
	else if( document.getElementById( 'b_pago_2' ).checked )
		b_lista = document.getElementById( 'b_pago_2' ).value;
	
	//del modal
	var	obs			= document.getElementById( 'observaciones' ).value;
	var	pagando		= parseFloat( document.getElementById( 'pagar_adeudo' ).value );
	var	por_pagar	= parseFloat( document.getElementById( 'por_pagar' ).value );
	
	if( ( status == 'N' && !obs ) || ( status == 'N' && pagando > 0 ) )
		return false;
	
	document.getElementById( 'btn_procesar' ).innerHTML		= "<button type='button' disabled='disabled' class='btn btn-primary'>Espera</button>";
	document.getElementById( 'msj_procesar' ).innerHTML		= "Un momento, procesando...";
	document.getElementById( 'img_procesar' ).innerHTML		= "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	document.getElementById( 'lista_entrega' ).innerHTML = "<img src='../imagenes/spinner.gif' alt='Cargando...' />";
	
	$.post( "peticiones/pet_entrega_status.php", { b_corte:b_corte, b_status:b_status, b_cliente:b_cliente, b_orden:b_orden, b_lista:b_lista, por_pagar:por_pagar, pagando:pagando, obs:obs, status:status, id_venta:id_venta, folio:folio, envio : true },
	
	function( datos )
	{
		document.getElementById( 'lista_entrega' ).innerHTML = datos;
	});
	
	//si se guarda comentario, vuelvo a cargar el modal para ver el comentario que se guardo
	
	$( '#modal_principal' ).modal('hide');
	
	if( status == 'N' )
		ver_detalle( id_venta, folio );
}