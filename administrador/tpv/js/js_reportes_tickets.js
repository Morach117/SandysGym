function mostrar_detalle_cancelacion( id_venta, folio )
{
	if( id_venta && folio )
	{
		$.post( "peticiones/pet_tickets.php", { id_venta:id_venta, folio:folio, envio : true },
		
		function( datos )
		{
			$('#modal_principal').html( datos );
			
			$('#modal_principal').modal();
			$('#modal_principal').modal({ keyboard: true });
			$('#modal_principal').modal('show');
		});
	}
}