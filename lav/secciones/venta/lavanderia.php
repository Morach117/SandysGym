<div class="row">
	<div class="col-md-12">
		<h4 class="bg-info text-info">
			<span class="glyphicon glyphicon-usd"></span> PUNTO DE VENTA DE SERVICIOS DE LAVANDERIA
		</h4>
	</div>
</div>

<hr/>

<?php
	$datos		= obtener_servicios( 'LAVANDERIA' );
	$entrega	= fecha_entrega( 'LAVANDERIA' );
	$v_iva		= obtener_por_iva();
	$v_comision	= obtener_p_comision_tarjeta();
?>

<div class="row">
	<div class="col-md-6 venta">
		<div class="bs-touch">
			<ul class="bs-touch-list pointer">
				<?= $datos ?>
			</ul>
		</div>
	</div>
	
	<div class="col-md-6">
		<form action=".?s=venta" method="post" onsubmit="return checar_servicios( 'N' )">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="text-bold" id="nombre_socio"><em>Cliente por seleccionar</em></h3>
				</div>
				
				<div class="panel-body">
					<table class="table table-hover h6">
						<thead>
							<tr>
								<th></th>
								<th>Kilos</th>
								<th>Descripción</th>
								<th class="text-right">Precio</th>
								<th class="text-right">Importe</th>
							</tr>
						</thead>
						
						<tbody id="servicio_venta">
							
						</tbody>
					</table>
					
					<hr/>
					
					<div class="row text-info text-bold h4">
						<label class="col-md-9 text-right">Subtotal</label>
						<label class="col-md-3" id="subtotal">$0.00</label>
					</div>
					
					<div class="row text-info text-bold h4">
						<label class="col-md-9 text-right">Descuento %</label>
						<label class="col-md-3" id="por_descuento">0.00%</label>
					</div>
					
					<div class="row text-info text-bold h4">
						<label class="col-md-9 text-right">Descuento $</label>
						<label class="col-md-3" id="mon_descuento">$0.00</label>
					</div>
					
					<div class="row text-primary text-bold h4">
						<label class="col-md-9 text-right">Incluir IVA</label>
						<label class="col-md-3">
							<input type="checkbox" id="chk_iva" onclick="ver_iva()" />
						</label>
					</div>
					
					<div id="ver_iva" class="hidden">
						<div class="row text-primary text-bold h4">
							<label class="col-md-9 text-right">Porcentaje %</label>
							<label class="col-md-3"><?= number_format( $v_iva, 2 ) ?>%</label>
						</div>
						
						<div class="row text-primary text-bold h4">
							<label class="col-md-9 text-right">Monto $</label>
							<label class="col-md-3" id="ver_iva_monto">$0.00</label>
						</div>
					</div>
					
					<div class="row text-success text-bold h4">
						<label class="col-md-9 text-right">Total</label>
						<label class="col-md-3" id="total">$0.00</label>
					</div>
					
					<div class="row-min text-right">
						<label class="col-md-9">Fecha de Entrega</label>
						<div class="col-md-3"><input type="text" class="form-control" id="f_entrega" value="<?= $entrega['fecha'] ?>" onchange="hora_entrega()" required="required" /></div>
					</div>
					
					<div class="row-min text-right">
						<label class="col-md-9">Hora de Entrega</label>
						<div class="col-md-3"><input type="text" class="form-control" id="h_entrega" value="<?= $entrega['hora'] ?>" readonly="on" /></div>
					</div>
					
					<div class="row text-primary">
						<label class="col-md-9 text-bold h4 text-right">Método de pago</label>
						
					</div>
					
					<div class="row-min text-right">
						<label class="col-md-9"><input type="radio" name="m_pago" id="m_pago_e" value="E" required onclick="calcular_total()" checked /> Efectivo/Anticipo $</label>
						<div class="col-md-3"><input type="text" class="form-control" id="efectivo" value="0" onclick="seleccionar(this)" required="required" /></div>
					</div>
					
					<div class="row-min">
						<label class="col-md-9 text-right"><input type="radio" name="m_pago" id="m_pago_t" value="T" required onclick="calcular_total()" /> Tarjeta (comisión: <?= $v_comision ?>%)</label>
						<label class="col-md-3" id="tag_tarjeta">$0.00</label>
					</div>
					
					<div class="row">
						<label class="col-md-4">Observaciones</label>
						<div class="col-md-8">
							<textarea id="observaciones" maxlength="100" class="form-control" rows="2"></textarea>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12 text-right">
							<input type="hidden" id="tipo" value="LAVANDERIA" />
							<input type="hidden" id="input_total" value="0" />
							<input type="hidden" id="pag_tarjeta" value="0" />
							<input type="hidden" id="pag_comision" value="0" />
							<input type="hidden" id="id_socio" value="0" />
							<input type="hidden" id="comision" value="<?= $v_comision ?>" />
							<input type="hidden" id="iva_por" value="<?= $v_iva ?>" />
							<input type="hidden" id="iva_monto" value="0" />
							<input type="hidden" id="descuento" value="0" />
							<input type="button" class="btn btn-default" value="Nuevo" onclick="location.href='.?s=<?= $seccion ?>&i=<?= $item ?>'" />
							<input type="button" class="btn btn-primary" value="Seleccionar Cliente" onclick="mostrar_socios()" />
							<input type="submit" name="enviar" class="btn btn-primary" value="Continuar" />
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>