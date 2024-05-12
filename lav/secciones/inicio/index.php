<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-home"></span> Página principal</h4>
	</div>
</div>

<hr/>

<?php
	$m_cambio	= "";
	
	if( $id_secundario )
	{
		$m_cambio	= "	<a href='.?s=inicio&i=cambio'>
							<li class='colord'>
								<span class='glyphicon glyphicon-refresh'></span>
								<h5><strong>Cambio</strong></h5>
								<span class='touch-class'>Cambio de sucursal</span>
							</li>
						</a>";
	}
?>

<div class="bs-touch-md">
    <ul class="bs-touch-list">
        <a href=".?s=socios">
			<li class="colord">
				<span class="glyphicon glyphicon-user"></span>
				<h4><strong>Clientes</strong></h4>
				<span class="touch-class">Registro de clientes</span>
			</li>
		</a>
		
		<a href=".?s=recepcion">
			<li class="color1">
				<span class="glyphicon glyphicon-circle-arrow-down"></span>
				<h4><strong>Recepción</strong></h4>
				<span class="touch-class">Recepción de ropa en Lavanderia y Planchaduria</span>
			</li>
		</a>
		
		<a href=".?s=lavado">
			<li class="color2">
				<span class="glyphicon glyphicon-ok-sign"></span>
				<h4><strong>Lavado</strong></h4>
				<span class="touch-class">Lavado de ropa en Lavanderia</span>
			</li>
		</a>
		
		<a href=".?s=entrega">
			<li class="color3">
				<span class="glyphicon glyphicon-circle-arrow-right"></span>
				<h4><strong>Entrega</strong></h4>
				<span class="touch-class">Entrega de ropa en Lavanderia y Planchaduria</span>
			</li>
		</a>
		
		<a href=".?s=caja&i=registro">
			<li class="colord">
				<span class="glyphicon glyphicon-usd"></span>
				<h4><strong>Registro de caja</strong></h4>
				<span class="touch-class">Monto con que se inicia o cierra caja</span>
			</li>
		</a>
		
		<a href=".?s=caja">
			<li class="colord">
				<span class="glyphicon glyphicon-usd"></span>
				<h4><strong>Retiro de efectivo</strong></h4>
				<span class="touch-class">Retiro de efectivo de la venta</span>
			</li>
		</a>
		
		<?= $m_cambio ?>
	</ul>
</div>