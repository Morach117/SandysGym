<?php
	$subquery	= "	SELECT		COUNT(*) AS total,
								'$gbl_paginado' AS mostrar
					FROM		san_socios
					LEFT JOIN	san_prepago ON prep_id_socio = soc_id_socio
					WHERE 		soc_id_empresa = $id_empresa
					AND			prep_id_socio IS NULL";
							
	$paginas	= paginado( $subquery, "$seccion", "$item" );
	
	if( $enviar )
	{
		$exito = guardar_nuevo_prepago();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=prepagos&IDP=$exito[IDP]&IDD=$exito[IDD]&IDS=$exito[IDS]&token=$exito[tkn]" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$socios_sin_prepago	= obtener_socios_sin_prepago();
?>

<div class="row">
	<div class="col-md-5">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-plus-sign"></span> Nuevo saldo para socio
		</h4>
	</div>
</div>

<hr/>

<div class="row">
	<div class="col-md-12">
		Lista de Socios que nunca han pagado PrePago
	</div>
</div>

<div class="row">
	<div class="col-md-5" id="nuevo_prepago">
		<p>Seleccione un socio para agregar PrePago.</p>
	</div>
	
	<div class="col-md-7">
		<table class="table table-hover pointer table-condensed">
			<thead>
				<tr>
					<th>#</th>
					<th>Socios sin prepago</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $socios_sin_prepago ?>
			</tbody>
		</table>
	</div>
</div>

<?= $paginas ?>