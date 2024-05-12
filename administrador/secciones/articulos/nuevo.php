<?php
	$art_codigo			= request_var( 'art_codigo', '' );
	$art_unidad			= request_var( 'art_unidad', '' );
	$art_descripcion	= request_var( 'art_descripcion', '' );
	$art_detalle		= request_var( 'art_detalle', '' );
	$art_agregar		= request_var( 'art_agregar', 0.0 );
	$art_costo			= request_var( 'art_costo', 0.0 );
	$art_precio			= request_var( 'art_precio', 0.0 );
	$art_monedero		= request_var( 'art_monedero', 0.0 );
	$art_mayoreo_1		= request_var( 'art_mayoreo_1', 0.0 );
	$art_mayoreo_2		= request_var( 'art_mayoreo_2', 0.0 );
	$id_proveedor		= request_var( 'art_proveedor', 0 );
	$id_categoria		= request_var( 'art_categoria', 0 );
	$id_marca			= request_var( 'art_marca', 0 );
	
	if( $enviar )
	{
		$exito = guardar_articulo();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=articulos" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$art_util_pp_pesos	= $art_precio - $art_costo;
	$art_util_m1_pesos	= $art_mayoreo_1 - $art_costo;
	$art_util_m2_pesos	= $art_mayoreo_2 - $art_costo;
	
	if( $art_costo )
	{
		$art_util_pp_porce	= ( ( $art_precio * 100 ) / $art_costo ) - 100;
		$art_util_m1_porce	= ( ( $art_mayoreo_1 * 100 ) / $art_costo ) - 100;
		$art_util_m2_porce	= ( ( $art_mayoreo_2 * 100 ) / $art_costo ) - 100;
	}
	else
	{
		$art_util_pp_porce	= 0;
		$art_util_m1_porce	= 0;
		$art_util_m2_porce	= 0;
	}
	
	$mon_monto			= ( $art_monedero * $art_precio ) / 100;
	
	if( $art_util_pp_pesos )
		$mon_porce_util		= ( $mon_monto * 100 ) / $art_util_pp_pesos;
	else
		$mon_porce_util		= 0;
	
	$stock_empresas		= obtener_empresas_para_stock();
	$lista_unidades		= combo_tipos_unidades( $art_unidad );
	$lista_proveedores	= combo_proveedores( $id_proveedor );
	$lista_categorias	= combo_categorias( $id_categoria );
	$lista_marcas		= combo_marcas( $id_marca );
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-plus-sign"></span> Agregar articulo
		</h4>
	</div>
</div>

<hr/>

<form method="post" action=".?s=articulos&i=nuevo" >
	<div class="row">
		<label class="col-md-2">Código <span class="text-danger">*</span></label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="art_codigo" maxlength="15" required="required" autocomplete="off" value="<?= $art_codigo ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Descripción <span class="text-danger">*</span></label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="art_descripcion" maxlength="100" required="required" autocomplete="off" value="<?= $art_descripcion ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Detalle</label>
		<div class="col-md-10"><textarea class="form-control" name="art_detalle" maxlength="198" rows="3"><?= $art_detalle ?></textarea></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Costo <span class="text-danger">*</span></label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="art_costo" id="art_costo" maxlength="7" required="required" autocomplete="off" value="<?= $art_costo ?>" onkeyup="calculos_costo()" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Tipo / Unidad</label>
		<div class="col-md-2">
			<select name="art_unidad" class="form-control">
				<option value="">Ninguno...</option>
				<?= $lista_unidades ?>
			</select>
		</div>
		<div class="col-md-8"><small><strong>Nota: </strong>Cuando se selecciona <strong>Servicio</strong>, no se agregan existencias, es decir, el stock siempre permanece en 0 pero si puede ser vendido.</small></div>
	</div>
	
	<hr/>
	
	<div class="row">
		<label class="col-md-2">Precio <span class="text-danger">*</span></label>
		<div class="col-md-2"><input type="text" class="form-control"id="art_precio"  name="art_precio" maxlength="7" required="required" autocomplete="off" value="<?= $art_precio ?>" onkeyup="utilidad( this.value, 'util_pp_pesos', 'util_pp_porce' ); calculos_monedero()" /></div>
		
		<label class="col-md-2">Utilidad $</label>
		<div class="col-md-2 text-right" id="util_pp_pesos">$<?= number_format( $art_util_pp_pesos, 2 ) ?></div>
		
		<label class="col-md-2">Utilidad %</label>
		<div class="col-md-2 text-right" id="util_pp_porce"><?= number_format( $art_util_pp_porce, 2 ) ?>%</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">% Monedero</label>
		
		<div class="col-md-2">
			<input type="text" class="form-control" name="art_monedero" id="art_monedero" maxlength="5" autocomplete="off" value="<?= $art_monedero ?>" onkeyup="calculos_monedero()" />
		</div>
		
		<label class="col-md-2">Monto</label>
		<div class="col-md-2 text-right" id="mon_monto">$<?= number_format( $mon_monto, 2 ) ?></div>
	</div>
	
	<div class="row">
		<div class="col-md-6">Porcentaje del <b>monto</b> del monedero respecto a la <b>utilidad</b></div>
		<div class="col-md-2 text-right" id="mon_porce_util_monto"><?= number_format( $mon_porce_util, 2 ) ?>%</div>
	</div>
	
	<hr/>
	
	<div class="row">
		<label class="col-md-2">Mayoreo 1</label>
		<div class="col-md-2"><input type="text" class="form-control"id="art_mayoreo_1"  name="art_mayoreo_1" maxlength="7" autocomplete="off" value="<?= $art_mayoreo_1 ?>" onkeyup="utilidad( this.value, 'util_m1_pesos', 'util_m1_porce' )" /></div>
		
		<label class="col-md-2">Utilidad $</label>
		<div class="col-md-2 text-right" id="util_m1_pesos">$<?= number_format( $art_util_m1_pesos, 2 ) ?></div>
		
		<label class="col-md-2">Utilidad %</label>
		<div class="col-md-2 text-right" id="util_m1_porce"><?= number_format( $art_util_m1_porce, 2 ) ?>%</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Mayoreo 2</label>
		<div class="col-md-2"><input type="text" class="form-control" id="art_mayoreo_2"  name="art_mayoreo_2" maxlength="7" autocomplete="off" value="<?= $art_mayoreo_2 ?>" onkeyup="utilidad( this.value, 'util_m2_pesos', 'util_m2_porce' )" /></div>
		
		<label class="col-md-2">Utilidad $</label>
		<div class="col-md-2 text-right" id="util_m2_pesos">$<?= number_format( $art_util_m2_pesos, 2 ) ?></div>
		
		<label class="col-md-2">Utilidad %</label>
		<div class="col-md-2 text-right" id="util_m2_porce"><?= number_format( $art_util_m2_porce, 2 ) ?>%</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Proveedor</label>
		<div class="col-md-4">
			<select name="art_proveedor" class="form-control">
				<option value="">Ninguno...</option>
				<?= $lista_proveedores ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Categoria</label>
		<div class="col-md-4">
			<select name="art_categoria" class="form-control">
				<option value="">Ninguno...</option>
				<?= $lista_categorias ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Marca</label>
		<div class="col-md-4">
			<select name="art_marca" class="form-control">
				<option value="">Ninguno...</option>
				<?= $lista_marcas ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<p class="text-right text-danger">* <em>Campos obligatorios</em></p>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12"><h5 class="text-info text-bold">Existencia por Sucursal</h5></div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr class="active">
						<th>#</th>
						<th>Surcusal</th>
						<th>Existencia</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $stock_empresas ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row text-center">
		<div class="col-md-12">
			<input type="button" name="cancelar" value="Cancelar" class="btn btn-default" onclick="location.href='.?s=articulos'" />
			<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
		</div>
	</div>
</form>