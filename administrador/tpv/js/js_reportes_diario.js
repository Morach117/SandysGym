$(document).ready(function() {
    $( "#tbl_datos" ).DataTable( {
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            api.columns('.sum', { page: 'current'}).every( function () {
              var sum = this
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
              
              this.footer().innerHTML = sum.toFixed( 2 );
            } );
        },
		language: {
					"decimal":        "",
					"emptyTable":     "No hay datos",
					"info":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"infoEmpty":      "Mostrando 0 a 0 de 0 registros",
					"infoFiltered":   "(filtrado de _MAX_ registros totales)",
					"infoPostFix":    "",
					"thousands":      ",",
					"lengthMenu":     "Mostrar _MENU_ registros por página",
					"loadingRecords": "Cargando...",
					"processing":     "Procesando...",
					"search":         "Busqueda rápida:",
					"zeroRecords":    "No se encontró ninguna coincidencia",
					"paginate": {
						"first":      "Primero",
						"last":       "Último",
						"next":       "Siguiente",
						"previous":   "Anterior"
					},
					"aria": {
						"sortAscending":  ": activate to sort column ascending",
						"sortDescending": ": activate to sort column descending"
					}
				},
		pageLength: 25
    } );
} );

function tipo_corte( tipo_corte )
{
	if( tipo_corte == 't1' )
	{
		document.getElementById( 'mos_tipo_2' ).style.display = "block";
		document.getElementById( 'mos_tipo_1' ).style.display = "none";
	}
	else
	{
		document.getElementById( 'mos_tipo_2' ).style.display = "none";
		document.getElementById( 'mos_tipo_1' ).style.display = "block";
	}
}

function calcular_importe()
{
	var total		= 0;
	var cor_b_1000	= parseInt( document.getElementById( 'cor_b_1000' ).value ) * 1000;
	var cor_b_500	= parseInt( document.getElementById( 'cor_b_500' ).value ) * 500;
	var cor_b_200	= parseInt( document.getElementById( 'cor_b_200' ).value ) * 200;
	var cor_b_100	= parseInt( document.getElementById( 'cor_b_100' ).value ) * 100;
	var cor_b_50	= parseInt( document.getElementById( 'cor_b_50' ).value ) * 50;
	var cor_b_20	= parseInt( document.getElementById( 'cor_b_20' ).value ) * 20;
	
	var cor_m_20	= parseInt( document.getElementById( 'cor_m_20' ).value ) * 20;
	var cor_m_10	= parseInt( document.getElementById( 'cor_m_10' ).value ) * 10;
	var cor_m_5		= parseInt( document.getElementById( 'cor_m_5' ).value ) * 5;
	var cor_m_2		= parseInt( document.getElementById( 'cor_m_2' ).value ) * 2;
	var cor_m_1		= parseInt( document.getElementById( 'cor_m_1' ).value ) * 1;
	var cor_c_50	= parseInt( document.getElementById( 'cor_c_50' ).value ) * 0.5;
	
	total = cor_b_1000 + cor_b_500 + cor_b_200 + cor_b_100 + cor_b_50 + cor_b_20 + cor_m_20 + cor_m_10 + cor_m_5 + cor_m_2 + cor_m_1 + cor_c_50;
	
	document.getElementById( 'cal_importe' ).innerHTML = "Importe: $" + total.toFixed( 2 );
	document.getElementById( 'cor_importe' ).value = total;
}

function mostrar_modal_corte( id_corte, id_usuario, tipo_corte )
{
	if( id_corte )
	{
		$.post( "peticiones/pet_corte_diario_ticket.php", { id_corte:id_corte, id_usuario:id_usuario, tipo_corte:tipo_corte, envio : true },
		function( datos )
		{
			$( '#modal_principal' ).html( datos );
			
			$( '#modal_principal' ).modal();
			$( '#modal_principal' ).modal({ keyboard: true });
			$( '#modal_principal' ).modal('show');
		});
	}
}

function imprimir_ticket_corte_diario( id_corte, id_usuario, tipo_corte )
{
	if( id_corte )
	{
		var parametros = "?IDC=" + id_corte + "&IDU=" + id_usuario + "&CTC=" + tipo_corte;
		
		cerrar_modal();
		document.getElementById( 'ticket' ).innerHTML = "<iframe name='ticket' src='ticket_corte.php" + parametros + "' frameborder=0 width=0 height=0></iframe>";
		ticket.focus();
		ticket.print();
		location.href='.?s=reportes&i=diario';
	}
	else
	{
		document.getElementById( 'msj_procesar' ).innerHTML	= "Ticket inválido.";
		document.getElementById( 'img_procesar' ).innerHTML	= "";
		document.getElementById( 'btn_procesar' ).innerHTML	= "<button type='button' data-dismiss='modal' class='btn btn-danger'>Cerrar</button>";
	}
}