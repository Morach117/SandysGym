<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-time"></span> Listado de Visitas
		</h4>
	</div>
</div>

<hr/>

<?php
	$id_visita	= request_var( 'IDV', 0 );
	$token		= request_var( 'token', '' );
	$eliminar	= request_var( 'eliminar', false );
	
	if( $id_visita && $token )
	{
		$validar_token	= hash_hmac( 'md5', $id_visita, $gbl_key );
		
		if( $validar_token == $token )
			echo "<script>mostrar_modal_visita( $id_visita, '$token' )</script>";
	}
	
	if( $eliminar )
	{
		$exito	= eliminar_horas();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=visitas" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$horas_d	= lista_horas_visitas();	
?>

<div class="row">
	<div class="col-md-12">
		<h5 class="text-info"><strong>Clientes por visitas</strong></h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th></th>
					<th>Nombres</th>
					<th>Hora de entrada</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $horas_d ?>
			</tbody>
		</table>
	</div>
</div>