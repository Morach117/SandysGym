<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-remove-sign"></span> Quitar articulo de la transferencia</h4>
	</div>
</div>

<hr/>

<?php
	$id_transfer	= request_var( 'folio', 0 );
	$id_articulo	= request_var( 'id_articulo', 0 );
	
	if( $enviar )
	{
		$exito	= quitar_articulo( $id_transfer, $id_articulo );
		
		if( $exito['num'] == 1 )
		{
			header( "location: .?s=transferencias&i=detalle&folio=$id_transfer" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], "danger" );
	}
	
	$detalle	= transferencia_detalle( $id_transfer );
	$adetelle	= transferencia_articulo_detalle( $id_transfer, $id_articulo );
	
	echo $detalle;
	
	if( !$detalle || !$adetelle )
	{
		header( "location: ?s=transferencias&i=detalle&folio=$id_transfer" );
		exit;
	}
	
	if( $adetelle['status'] == 'A' )
	{
		mostrar_mensaje_div( "Si se quita <b>$adetelle[articulo]</b> de la transferencia, se recuperan <b>$adetelle[transito] artículos</b> para la surcursal de origen.", 'info' );
?>
		<form action=".?s=<?= $seccion ?>&i=<?= $item ?>" method="post">
			<div class="row">
				<div class="col-md-12">
					<input type="submit" name="enviar" class="btn btn-primary" value="Quitar" />
					<input type="button" name="cancel" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=transferencias&i=detalle&folio=<?= $id_transfer ?>'" />
					<input type="hidden" name="folio" value="<?= $id_transfer ?>" />
					<input type="hidden" name="id_articulo" value="<?= $id_articulo ?>" />
				</div>
			</div>
		</form>
<?php
	}
	else
	{
		mostrar_mensaje_div( 'Solo las transferencias con status de ABIERTO pueden ser modificados.', 'danger' );
	}
?>