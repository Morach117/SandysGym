<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tower"></span> Configuración de Folios
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
	
	$tabla	= lista_folios_consecutivos();
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
						<th>IDE</th>
						<th>ABR</th>
						<th>Sucursal</th>
						<th>Letra</th>
						<th>Opciones</th>
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
			<p>La <strong>Letra</strong> se utiliza para identificar el Folio de una Sucursal, cuando se coloca, el Folio se muestra así <strong>Z0000999</strong>, si no se coloca, se muestra de esta manera: <strong>0000999</strong>.</p>
			
			<p>Para el <strong>Número de Folio que se reinicia cada año</strong> significa que cada inicio de año el folio comenzara en 1 con incremento en 1.</p>
			
			<p>Para el <strong>Número de Folio infinito</strong> significa que el Folio se inicia en 1 con incremento en 1 hasta llegar al máximo permitido que es de 2,147,483,647.</p>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
		</div>
	</div>
</form>