<?php
	/*CUANDO SE MODIFIQUE EL QUERY DE ARTICULOS TAMBIEN MODIFICAR EL DE DESCARGAS*/
	
	$pag_IDE			= request_var( 'pag_IDE', 0 );//id_empresa
	$pag_status			= request_var( 'pag_status', 'A' );
	$pag_proveedores	= request_var( 'pag_proveedores', 0 );
	$pag_categorias		= request_var( 'pag_categorias', 0 );
	$pag_marcas			= request_var( 'pag_marcas', 0 );
	$pag_opciones		= request_var( 'pag_opciones', 0 );
	$pag_busqueda		= request_var( 'pag_busqueda', '' );
	$var_regresar		= "";
	
	/*se envia al item EDITAR, EXCEL para cuando se de regresar no se pierda la busqueda, tambien esta en el ITEM de EDITAR*/
	$var_array_regresar	= array( 'blq', 'pag', 'pag_IDE', 'pag_status', 'pag_proveedores', 'pag_categorias', 'pag_marcas', 'pag_opciones', 'pag_busqueda' );
	
	foreach( $var_array_regresar as $ind )
	{
		$var	= request_var( "$ind", '' );
		
		if( $var )
			$var_regresar .= "&$ind=$var";
	}
	
	$var_sucursales	= combo_sucursales( $pag_IDE );
	$var_status		= opciones_status( $pag_status );
	$var_opciones	= opciones_busqueda( $pag_opciones );
	
	$var_proveedors	= combo_proveedores( $pag_proveedores );
	$var_categorias	= combo_categorias( $pag_categorias );
	$var_marcas		= combo_marcas( $pag_marcas );
	
	$var_exito		= lista_articulos( $var_regresar );
	$var_paginas	= paginado( $var_exito['num'], 'articulos' );
?>

<div class="row">
	<div class="col-md-9">
		<h4 class="text-info"><span class="glyphicon glyphicon-shopping-cart"></span> Articulos en Inventario</h4>
	</div>
	
	<div class="col-md-3 text-right">
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
			Acciones <span class="caret"></span>
			</button>
			<ul class="dropdown-menu pointer" role="menu">
				<li><a href=".?s=articulos&i=nuevo"><span class="glyphicon glyphicon-plus-sign"></span> Agregar articulo</a></li>
				<li><a onclick="nueva_transferencia( 'articulos' )"><span class="glyphicon glyphicon-plus-sign"></span> Nueva transferencia</a></li>
				<li><a target="_blank" href=".?s=articulos&d=excel<?= $var_regresar ?>"><span class="glyphicon glyphicon-download"></span> Descargar lista actual</a></li>
			</ul>
		</div>
	</div>
</div>

<hr/>

<form action=".?s=articulos" method="post">
	<div class="row">
		<label class="col-md-2">Sucursal</label>
		<div class="col-md-4">
			<select name="pag_IDE" class="form-control">
				<option value="">Todos...</option>
				<?= $var_sucursales ?>
			</select>
		</div>
		
		<label class="col-md-2">Proveedores</label>
		<div class="col-md-4">
			<select name="pag_proveedores" class="form-control">
				<option value="">Todos...</option>
				<?= $var_proveedors ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Status</label>
		<div class="col-md-4">
			<select name="pag_status" class="form-control">
				<option value="">Todos...</option>
				<?= $var_status ?>
			</select>
		</div>
		
		<label class="col-md-2">Categorias</label>
		<div class="col-md-4">
			<select name="pag_categorias" class="form-control">
				<option value="">Todos...</option>
				<?= $var_categorias ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Opciones</label>
		<div class="col-md-4">
			<select name="pag_opciones" class="form-control">
				<option value="">Todos...</option>
				<?= $var_opciones ?>
			</select>
		</div>
		
		<label class="col-md-2">Marcas</label>
		<div class="col-md-4">
			<select name="pag_marcas" class="form-control">
				<option value="">Todos...</option>
				<?= $var_marcas ?>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Búsqueda</label>
		<div class="col-md-4"><input type="text" name="pag_busqueda" class="form-control" value="<?= $pag_busqueda ?>" autofocus="on" /></div>
	</div>
	
	<div class="row">
		<div class="col-md-offset-2 col-md-4">
			<input type="submit" class="btn btn-primary" value="Buscar" name="enviar" />
			<input type="button" class="btn btn-default" value="Todos" onclick="location.href='.?s=<?= $seccion ?>'" />
		</div>
	</div>
</form>

<hr/>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover h6">
			<thead>
				<tr class="active">
					<th>#</th>
					<th></th>
					<th>Código</th>
					<th>Descripción <span class='label label-warning'>Apartado</span> <span class='label label-info'>Tránsito</span> <span class='label label-primary'>Transferencia</span></th>
					<th class="text-right">Stock</th>
					<th class="text-right">Costo</th>
					<th class="text-right">Precio</th>
					<th class="text-right">Monedero</th>
				</tr>
			</thead>
			
			<tbody>
				<?= $var_exito['msj'] ?>
			</tbody>
		</table>
	</div>
</div>

<?= $var_paginas ?>