<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-list-alt"></span> Lista de artículos de la transferencia seleccionada</h4>
	</div>
</div>

<hr/>

<?php
	$id_transfer	= request_var( 'folio', 0 );
	$movimiento		= request_var( 'm', '' ); //eliminar, cancelar
	$eliminar		= isset( $_POST['eliminar'] ) ? true:false;
	$cancelar		= isset( $_POST['cancelar'] ) ? true:false;
	$chk_transfer	= chk_transferencia( $id_transfer );
	
	if( $eliminar )
	{
		$exito = eliminar_transferencia( $id_transfer );
		
		if( $exito['num'] == 1 )
		{
			header( "location: .?s=$seccion" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], "danger" );
	}
	
	if( $cancelar )
	{
		$exito = cancelar_transferencia( $id_transfer );
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], "success" );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], "danger" );
	}
	
	if( ( $movimiento == 'eliminar' && $chk_transfer['status'] != 'R' && $chk_transfer['articulos'] == 0 ) || ( $movimiento == 'cancelar' && $chk_transfer['status'] == 'A' )  )
	{
		if( $movimiento == 'eliminar' )
		{
			mostrar_mensaje_div( "¿Estás seguro de eliminar esta transferencia? Solo las transferencias que no tengan artículos en la lista y que no hayan sido RECIBIDAS pueden ser eliminadas.", 'info' );
			
			echo "	<form action='.?s=$seccion&i=$item' method='post'>
						<div class='row'>
							<div class='col-md-12'>
								<input type='submit' name='eliminar' value='Eliminar transferencia' class='btn btn-danger' />
								<input type='hidden' name='folio' value='$id_transfer' />
								<input type='hidden' name='m' value='eliminar' />
							</div>
						</div>
					</form>";
		}
		
		if( $movimiento == 'cancelar' )
		{
			mostrar_mensaje_div( "¿Estás seguro de cancelar esta transferencia? Solo las transferencias ABIERTAS pueden ser cancelados. Se recuperan los artículos en tránsito a la sucursal de origen.", 'info' );
			
			echo "	<form action='.?s=$seccion&i=$item' method='post'>
						<div class='row'>
							<div class='col-md-12'>
								<input type='submit' name='cancelar' value='Cancelar transferencia' class='btn btn-danger' />
								<input type='hidden' name='folio' value='$id_transfer' />
								<input type='hidden' name='m' value='cancelar' />
							</div>
						</div>
					</form>";
		}
	}
	elseif( $movimiento )
	{
		if( $movimiento == 'eliminar' )
			mostrar_mensaje_div( "Solo se pueden aliminar transferencias no RECIBIDAS y/o que no tengan artículos en la lista.", "danger" );
		else
			mostrar_mensaje_div( "Solo se pueden cancelar transferencias con status de ABIERTO.", "danger" );
	}
	
	$datos			= lista_articulos_en_transferencia( $id_transfer );
	$detalle		= transferencia_detalle( $id_transfer );
	$historico		= lista_historico( $id_transfer );
	
	echo $detalle;
?>

<div class="row">
	<div class="col-md-12"><h5 class="text-info text-bold">Lista de artículos</h5></div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th></th>
					<th>Código</th>
					<th>Descripción</th>
					<th class="text-right">Stock al recibir</th>
					<th class="text-right">Tránsito</th>
					<th class="text-right">Costo</th>
					<th class="text-right">Costos</th>
					<th class="text-right">Precio</th>
					<th class="text-right">Importe</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $datos ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12"><h5 class="text-info text-bold">Movimientos en la transferencia</h5></div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th>Usuario</th>
					<th>Status</th>
					<th>Fecha</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $historico ?>
			</tbody>
		</table>
	</div>
</div>