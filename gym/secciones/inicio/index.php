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
							<li>
								<span class='glyphicon glyphicon-refresh'></span>
								<h5><strong>Cambio</strong></h5>
								<span class='touch-class'>Cambio de sucursal</span>
							</li>
						</a>";
	}
?>

<div class="bs-touch">
    <ul class="bs-touch-list">
        <a href=".?s=socios">
			<li>
				<span class="glyphicon glyphicon-user"></span>
				<h5><strong>Socios</strong></h5>
				<span class="touch-class">Lista de Socios registrados</span>
			</li>
		</a>
		
		<a href=".?s=horas">
			<li>
				<span class="glyphicon glyphicon-time"></span>
				<h5><strong>Horas</strong></h5>
				<span class="touch-class">Lista de Horas del día</span>
			</li>
		</a>
		
		<a href=".?s=visitas">
			<li>
				<span class="glyphicon glyphicon-time"></span>
				<h5><strong>Visitas</strong></h5>
				<span class="touch-class">Lista de Visitas del día</span>
			</li>
		</a>
		
		<a href=".?s=venta">
			<li>
				<span class="glyphicon glyphicon-shopping-cart"></span>
				<h5><strong>Venta</strong></h5>
				<span class="touch-class">Formulario para procesar las Ventas</span>
			</li>
		</a>
		
		<a href=".?s=prepagos">
			<li>
				<span class="glyphicon glyphicon-usd"></span>
				<h5><strong>PrePagos</strong></h5>
				<span class="touch-class">Lista de socios con PrePago</span>
			</li>
		</a>
		
		<a href=".?s=caja&i=registro">
			<li>
				<span class="glyphicon glyphicon-usd"></span>
				<h5><strong>Registro de caja</strong></h5>
				<span class="touch-class">Monto con que se inicia o cierra caja</span>
			</li>
		</a>
		
		<a href=".?s=caja">
			<li>
				<span class="glyphicon glyphicon-usd"></span>
				<h5><strong>Retiro de efectivo</strong></h5>
				<span class="touch-class">Retiro de efectivo de la venta</span>
			</li>
		</a>


		
		<?= $m_cambio ?>
	</ul>
</div>