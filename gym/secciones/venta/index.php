<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-usd"></span> Punto de venta
		</h4>
	</div>
</div>

<hr/>

<?php
	$articulos	= lista_articulos();
	$v_comision	= obtener_p_comision_tarjeta();
?>

<div class="row">
	<label class="col-md-3">Escribe el Nombre</label>
	<div class="col-md-2"><input type="text" id="criterio_busqueda" class="form-control" placeholder="Escribe algo para buscar" onKeyUp="buscar_articulo()" /></div>
</div>

<div class="row">
	<div class="col-md-5">
		<table class="table table-hover pointer h6">
			<thead>
				<tr class="active">
					<th>Descripción</th>
					<th class="text-right">Stock</th>
					<th class="text-right">Precio</th>
				</tr>
			</thead>
			
			<tbody id="lista_articulos">
				<?= $articulos ?>
			</tbody>
		</table>
	</div>
	
	<div class="col-md-7">
		<form action=".?s=articulos&i=venta" method="post" onsubmit="return checar_articulos( 'N' )">
			<table class="table table-hover h6">
				<thead>
					<tr class="active">
						<th></th>
						<th>Cant.</th>
						<th>Descripción</th>
						<th class="text-right">Precio</th>
						<th class="text-right">Importe</th>
					</tr>
				</thead>
				
				<tbody id="articulo_venta">
					
				</tbody>
			</table>
			
			<hr/>
			
			<div class="row">
				<div class="col-md-12">
					<h5 class="text-info"><strong>Método de pago</strong></h5>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12 text-bold">
					<input type="radio" name="m_pago" id="m_pago_e" value="E" required onclick="calcular_total()" checked /> Efectivo </br>
					<input type="radio" name="m_pago" id="m_pago_t" value="T" required onclick="calcular_total()" /> Tarjeta (comisión: <?= $v_comision ?>%)
				</div>
			</div>
			
			<hr/>
			
			<div class="row">
				<div class="col-md-2"><label>Monedero</label></div>
				<div class="col-md-1"><input type="checkbox" id="mostrar_socio" onclick="mostrar_socios( event )" /></div>
				<div class="col-md-9"><label id="nombre_socio"></label></div>
			</div>
			
			<div class="row" style="display:none" id="div_prepago">
				<div class="col-md-2"><label>Monedero</label></div>
				<div class="col-md-4"><input type="text" class="form-control" id="prepago" /></div>
			</div>
			
			<div class="row">
				<div class="col-md-2"><label>Efectivo</label></div>
				<div class="col-md-4"><input type="text" class="form-control" id="efectivo" /></div>
			</div>
			
			<div class="row">
				<div class="col-md-3"><label>Subtotal</label></div>
				<div class="col-md-4">
					<label id="tag_sub_total">$00.00</label>
				</div>
			</div>
			
			<div class="row text-danger">
				<div class="col-md-3 text-bold">Total a pagar</div>
				<div class="col-md-4 text-bold" id="tag_total_pago">$00.00</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<input type="hidden" name="comision" id="comision" value="<?= $v_comision ?>" />
					<input type="hidden" id="input_total" value="0" />
					<input type="hidden" name="prep_id_prepago" id="prep_id_prepago" value="0" />
					<input type="hidden" name="prep_saldo" id="prep_saldo" value="0" />
					<input type="submit" name="enviar" class="btn btn-primary" value="Procesar" />
					<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=venta'" />
				</div>
			</div>
		</form>
	</div>
</div>