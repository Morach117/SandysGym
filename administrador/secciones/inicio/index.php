<div class="row">
	<div class="col-md-12"><h4 class="text-info"><span class="glyphicon glyphicon-home"></span> Página principal del Administrador del Sistema</h4></div>
</div>

<hr/>

<?php
	$datos		= obtener_empresas_activas();
	$destino	= request_var( 'destino', '' );
	
	if( $destino == 'Auditor' || $destino == 'Operativo' )
	{
		$exito	= realizar_cambio();
		
		if( $exito )
		{
			mostrar_mensaje_div( $exito['num']." ".$exito['msj'] );
		}
	}
?>

<div class="row">
	<div class="col-md-12"><p>El módulo de <label>Auditor</label> está diseñado principalmente para realizar movimientos específicos de acuerdo al Giro de la Sucursal, además de reportes relacionados con las Ventas. El módulo <label>Operativo</label> es únicamente para el proceso de las Venta y está dirigido para el personal operativo de mostrador.</p></div>
</div>

<form action=".?s=inicio" method="post">
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover">
				<thead>
					<tr>
						<th></th>
						<th>IDE</th>
						<th>Abreviatura</th>
						<th>Sacursal</th>
						<th>Status</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $datos ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="destino" value="Auditor" class="btn btn-primary" />
			<input type="submit" name="destino" value="Operativo" class="btn btn-primary" />
		</div>
	</div>
</form>