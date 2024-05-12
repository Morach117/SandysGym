<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-edit"></span> Editar Artículo</h4>
	</div>
</div>
		
<hr/>

<?php
	$id_articulo	= request_var( 'id_articulo', '' );
	$IDC			= request_var( 'IDC', '' );//id_consorcio
	$eliminar		= isset( $_POST['eliminar'] ) ? true:false;
	
	if( $eliminar )
	{
		$exito = eliminar_articulo();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=articulos" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	if( $enviar )
	{
		$exito = actualizar_articulo();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$articulo	= obtener_detalle_articulo( $id_articulo, $IDC );
	
	if( !$articulo )
	{
		header( "Location: .?s=articulos" );
		exit;
	}
	
	$lista_unidades		= combo_tipos_unidades( $articulo['unidad'] );
	$lista_proveedores	= combo_proveedores( $articulo['id_proveedor'] );
	$lista_categorias	= combo_categorias( $articulo['id_categoria'] );
	$lista_marcas		= combo_marcas( $articulo['id_marca'] );
	$stock				= obtener_stock( $id_articulo );
	
	///*se envia al item EDITAR para cuando se de regresar no se pierda la busqueda*/
	$pag_blq			= request_var( 'blq', 0 );
	$pag_pag			= request_var( 'pag', 0 );
	$pag_IDE			= request_var( 'pag_IDE', 0 );//id_empresa
	$pag_status			= request_var( 'pag_status', '' );
	$pag_proveedores	= request_var( 'pag_proveedores', 0 );
	$pag_categorias		= request_var( 'pag_categorias', 0 );
	$pag_marcas			= request_var( 'pag_marcas', 0 );
	$pag_opciones		= request_var( 'pag_opciones', 0 );
	$pag_busqueda		= request_var( 'pag_busqueda', '' );
	$var_regresar		= "";
	
	/**/
	
	$art_util_pp_pesos	= $articulo['precio'] - $articulo['costo'];	
	$art_util_m1_pesos	= $articulo['mayoreo_1'] - $articulo['costo'];
	$art_util_m2_pesos	= $articulo['mayoreo_2'] - $articulo['costo'];
	
	if( $articulo['costo'] > 0 )
	{
		$art_util_pp_porce	= ( ( $articulo['precio'] * 100 ) / $articulo['costo'] ) - 100;
		$art_util_m1_porce	= ( ( $articulo['mayoreo_1'] * 100 ) / $articulo['costo'] ) - 100;
		$art_util_m2_porce	= ( ( $articulo['mayoreo_2'] * 100 ) / $articulo['costo'] ) - 100;
	}
	else
	{
		$art_util_pp_porce	= 100;
		$art_util_m1_porce	= 100;
		$art_util_m2_porce	= 100;
	}
	
	$mon_monto			= ( $articulo['monedero'] * $articulo['precio'] ) / 100;
	$mon_porce_util		= ( $mon_monto * 100 ) / $art_util_pp_pesos;
	
	$var_array_regresar	= array( 'blq', 'pag', 'pag_IDE', 'pag_status', 'pag_proveedores', 'pag_categorias', 'pag_marcas', 'pag_opciones', 'pag_busqueda' );
	
	foreach( $var_array_regresar as $ind )
	{
		$var	= request_var( "$ind", '' );
		
		if( $var )
			$var_regresar .= "&$ind=$var";
	}
?>

<form role="form" method="post" action=".?s=articulos&i=editar" >
	<div class="row">
		<label class="col-md-2">Código</label>
		<div class="col-md-4"><?= $articulo['codigo'] ?></div>
	
		<label class="col-md-2">Status</label>
		<div class="col-md-4">
			<select name="art_status" class="form-control">
				<option <?= ( $articulo['status'] == 'A' ) ? 'selected':'' ?> value="A">Activo</option>
				<option <?= ( $articulo['status'] == 'D' ) ? 'selected':'' ?> value="D">Descontinuado</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Descripción</label>
		<div class="col-md-10"><input type="text" class="form-control" name="art_desc" maxlength="95" required="required" autocomplete="off" value="<?= $articulo['descripcion'] ?>" /></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Detalle</label>
		<div class="col-md-10"><textarea class="form-control" name="art_detalle" maxlength="198" rows="3"><?= $articulo['detalle'] ?></textarea></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Costo</label>
		<div class="col-md-2"><input type="text" class="form-control" name="art_costo" id="art_costo" maxlength="7" required="required" autocomplete="off" value="<?= $articulo['costo'] ?>" onkeyup="calculos_costo()" /></div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Tipo / Unidad</label>
		<div class="col-md-2">
			<select name="art_unidad" class="form-control">
				<option value="">Ninguno...</option>
				<?= $lista_unidades ?>
			</select>
		</div>
		
		<div class="col-md-8"><small><b>Nota: </b>Cuando se selecciona <b>Servicio</b>, no se agregan existencias, es decir, el stock siempre permanece en 0 pero si puede ser vendido.</small></div>
	</div>
	
	<hr/>
	
	<div class="row">
		<label class="col-md-2">Precio</label>
		<div class="col-md-2"><input type="text" class="form-control" id="art_precio" name="art_precio" maxlength="7" required="required" autocomplete="off" value="<?= $articulo['precio'] ?>" onkeyup="utilidad( this.value, 'util_pp_pesos', 'util_pp_porce' )" /></div>
		
		<label class="col-md-2">Utilidad $</label>
		<div class="col-md-2 text-right" id="util_pp_pesos">$<?= number_format( $art_util_pp_pesos, 2 ) ?></div>
		
		<label class="col-md-2">Utilidad %</label>
		<div class="col-md-2 text-right" id="util_pp_porce"><?= number_format( $art_util_pp_porce, 2 ) ?>%</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">% Monedero</label>
		
		<div class="col-md-2">
			<input type="text" class="form-control" name="art_monedero" id="art_monedero" maxlength="5" autocomplete="off" value="<?= $articulo['monedero'] ?>" onkeyup="calculos_monedero()" />
		</div>
		
		<label class="col-md-2">Monto</label>
		<div class="col-md-2 text-right" id="mon_monto">$<?= number_format( $mon_monto, 2 )?></div>
	</div>
	
	<div class="row">
		<div class="col-md-6">Porcentaje del <b>monto</b> del monedero respecto a la <b>utilidad</b></div>
		<div class="col-md-2 text-right" id="mon_porce_util_monto"><?= number_format( $mon_porce_util, 2 ) ?>%</div>
	</div>
	
	<hr/>
	
	<div class="row">
		<label class="col-md-2">Mayoreo 1</label>
		<div class="col-md-2"><input type="text" class="form-control" id="art_mayoreo_1" name="art_mayoreo_1" maxlength="7" autocomplete="off" value="<?= $articulo['mayoreo_1'] ?>" onkeyup="utilidad( this.value, 'util_m1_pesos', 'util_m1_porce' )" /></div>
		
		<label class="col-md-2">Utilidad $</label>
		<div class="col-md-2 text-right" id="util_m1_pesos">$<?= number_format( $art_util_m1_pesos, 2 ) ?></div>
		
		<label class="col-md-2">Utilidad %</label>
		<div class="col-md-2 text-right" id="util_m1_porce"><?= number_format( $art_util_m1_porce, 2 ) ?>%</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Mayoreo 2</label>
		<div class="col-md-2"><input type="text" class="form-control" id="art_mayoreo_2" name="art_mayoreo_2" maxlength="7" autocomplete="off" value="<?= $articulo['mayoreo_2'] ?>" onkeyup="utilidad( this.value, 'util_m2_pesos', 'util_m2_porce' )" /></div>
		
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
		<div class="col-md-12"><h5 class="text-info text-bold">Existencia por Sucursal</h5></div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr class="active">
						<th>#</th>
						<th>Surcusal</th>
						<th class="text-right">Existencia</th>
						<th>Agregar/quitar (+/-)</th>
						<th>Ubicación <abbr class="text-info text-bold" title="10 caracteres como máximo">?</abbr></th>
					</tr>
				</thead>
				
				<tbody>
					<?= $stock ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<!--solo se utiliza para el botan de regresar, para que no se pierda la busqueda en el index-->
			<input type="hidden" name="blq" value="<?= $pag_blq ?>" />
			<input type="hidden" name="pag" value="<?= $pag_pag ?>" />
			<input type="hidden" name="pag_IDE" value="<?= $pag_IDE ?>" />
			<input type="hidden" name="pag_status" value="<?= $pag_status ?>" />
			<input type="hidden" name="pag_proveedores" value="<?= $pag_proveedores ?>" />
			<input type="hidden" name="pag_categorias" value="<?= $pag_categorias ?>" />
			<input type="hidden" name="pag_marcas" value="<?= $pag_marcas ?>" />
			<input type="hidden" name="pag_opciones" value="<?= $pag_opciones ?>" />
			<input type="hidden" name="pag_busqueda" value="<?= $pag_busqueda ?>" />
			
			<input type="hidden" name="id_articulo" value="<?= $id_articulo ?>" />
			<input type="hidden" name="IDC" value="<?= $IDC ?>" />
			
			<input type="button" name="cancelar" value="Regresar" class="btn btn-default" onclick="location.href='.?s=articulos<?= $var_regresar ?>'" />
			<input type="submit" name="enviar" value="Guardar" class="btn btn-primary" />
			<input type="submit" name="eliminar" value="Eliminar" class="btn btn-danger" />
		</div>
	</div>
</form>