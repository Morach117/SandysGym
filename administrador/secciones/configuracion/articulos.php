<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-file"></span> Configuración de Artículos
		</h4>
	</div>
</div>

<hr/>

<?php
	if( $enviar )
	{
		$exito	= actualizar_conf_articulos();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$tabla	= lista_conf_articulos();
?>
<div class="row">
	<div class="col-md-12">
		<h5 class="text-primary text-bold">Descuento de Artículos desde la Terminal Punto de Venta</h5>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p>Si se habilita, los Cajeros tendrán la posibilidad de utilizar las opciones desde la Terminal Punto de Venta.</p>
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
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
		</div>
	</div>
</form>