<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tower"></span> Configuración de Tickets para la Terminal Punto de Venta
		</h4>
	</div>
</div>

<hr/>

<?php
	if( $enviar )
	{
		$exito	= actualizar_conf_folios();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$tabla	= lista_configuracion_tickets();
?>

<div class="row">
	<div class="col-md-12">
		<p>Esta información solo debería ser Actualizada en una sola ocasión, el momento adecuado para realizar los cambios es antes de realizar movimientos en la parte Operativa.</p>
	</div>
</div>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr class="active">
						<th>#</th>
						<th>Sucursal</th>
						<th>Encabezado y pie de página</th>
						<th>Opciones del encabezado</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<h5 class="text-primary text-bold">Notas</h5>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<p>Si no se desea mostrar un texto para el <strong>encabezado y/o pie de página</strong>, no se debe capturar ningún texto.</p>
			
			<p>Si se desean mostrar las <strong>opciones del encabezado</strong>, se debe tomar en cuenta que ya tienen que estar capturados para que aparezcan en el Ticket.</p>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
		</div>
	</div>
</form>