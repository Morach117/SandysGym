<div class="row">
	<div class="col-md-12">
		<h4 class="text-info"><span class="glyphicon glyphicon-plus-sign"></span> Nuevo usuario</h4>
	</div>
</div>

<hr/>

<?php
	$tabla		= '';
	
	if( $rol == 'S' && $id_consorcio == 1 )
	{
		$tag_supervisor	= "	<div class='row'>
								<label class='col-md-2'>Nombres</label>
								<div class='col-md-4'>
									<input type='checkbox' name='u_rol' value= 'R' /> Supervisor
								</div>
							</div>";
	}
	else
		$tag_supervisor = "";
	
	if( $enviar )
	{
		$exito	= agregar_usuario();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=catalogos&i=usuarios" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$u_apis			= isset( $_POST['api'] ) ? $_POST['api'] : false;
	$u_sucursal		= request_var( 'u_sucursal', 0 );
	$u_empresa_sec	= request_var( 'u_empresa_sec', 0 );
	$u_nombres		= request_var( 'u_nombres', '' );
	$u_ape_pat		= request_var( 'u_ape_pat', '' );
	$u_ape_mat		= request_var( 'u_ape_mat', '' );
	$u_correo		= request_var( 'u_correo', '' );
	$u_pass			= request_var( 'u_pass', '' );
	$u_rol			= request_var( 'u_rol', 'O' );
	
	$apis		= obtener_aplicaciones();
	$sucursales	= combo_sucursales( $u_sucursal );
	$secundario	= combo_sucursales( $u_empresa_sec );
	
	foreach( $apis as $api )
	{
		if( $u_apis )
		{
			foreach( $u_apis as $api_seleccionada )
			{
				if( $api_seleccionada == $api['api'] )
				{
					$seleccion = "<input type='checkbox' name='api[]' value='$api[api]' checked  />";
					break;
				}
				else
					$seleccion = "<input type='checkbox' name='api[]' value='$api[api]' />";
			}
		}
		else
			$seleccion = "<input type='checkbox' name='api[]' value='$api[api]' />";
		
		$tabla	.= "<tr>
						<td>$seleccion</td>
						<td>$api[api]</td>
						<td>$api[descripcion]</td>
						<td>$api[seccion]</td>
						<td>$api[item]</td>
						<td>$api[item_excepto]</td>
					</tr>";
	}
	
	if( $id_consorcio = 1 )
	{
		$empresa_sec	= "	<div class='row'>
								<label class='col-md-2'>Secundario</label>
								<div class='col-md-4'>
									<select name='u_empresa_sec' class='form-control'>
										<option value=''>Ninguna sucursal secundario...</option>
										$secundario
									</select>
								</div>
							</div>";
	}
?>

<div class="row">
	<div class="col-md-12">
		<p>Selecciona los accesos que tendrá el Personal Operativo</p>
	</div>
</div>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover h6">
				<thead>
					<tr class="active">
						<th></th>
						<th>API</th>
						<th>Giro</th>
						<th>Seccion (menu)</th>
						<th>Item (submenu)</th>
						<th>Item excepción (submenu)</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Sucursal</label>
		<div class="col-md-4">
			<select name="u_sucursal" class="form-control">
				<option value="">Selecciona...</option>
				<?= $sucursales ?>
			</select>
		</div>
	</div>
	
	<?= $empresa_sec ?>
	
	<?= $tag_supervisor ?>
	
	<div class="row">
		<label class="col-md-2">Nombres</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_nombres" maxlength="20" required="required" value="<?= $u_nombres ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">A Paterno</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_ape_pat" maxlength="20" required="required" value="<?= $u_ape_pat ?>" />
		</div>
		
		<label class="col-md-2">A Materno</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_ape_mat" maxlength="20" required="required" value="<?= $u_ape_mat ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Correo</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_correo" maxlength="50" required="required" value="<?= $u_correo ?>" />
		</div>
		
		<label class="col-md-2">Contraseña</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_pass" maxlength="15" required="required" value="<?= $u_pass ?>" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12 text-center">
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
			<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=catalogos&i=usuarios'" />
		</div>
	</div>
</form>