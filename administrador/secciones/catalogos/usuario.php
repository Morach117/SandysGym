<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-user"></span> Usuarios
		</h4>
	</div>
</div>

<hr/>

<?php
	$usuarioe		= request_var( 'usuarioe', 0 );
	$eliminar		= request_var( 'eliminar', 0 );
	$tabla			= "";
	$empresa_sec	= "";
	
	if( $eliminar )
	{
		$exito	= eliminar_usuario( $eliminar );
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=catalogos&i=usuarios" );
			exit;
		}
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	if( $enviar )
	{
		$exito	= actualizar_datos();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$info		= obtener_datos_usuario( $usuarioe );
	$apis		= obtener_aplicaciones( $info['id_giro'] );
	$empresas	= combo_sucursales( $info['id_empresa'] );
	$secundario	= combo_sucursales( $info['id_secundario'] );
	$apis_user	= explode( ',', $info['apis'] );
	
	if( !$info )
	{
		header( "Location: .?s=catalogos&i=usuarios" );
		exit;
	}
	
	if( $rol == 'S' && $id_consorcio == 1 )
	{
		if( $info['rol'] == 'R' )
			$chk = "checked";
		else
			$chk = "";
		
		$tag_supervisor	= "	<div class='row'>
								<label class='col-md-2'>Nombres</label>
								<div class='col-md-4'>
									<input type='checkbox' name='u_rol' value= 'R' $chk /> Supervisor
								</div>
							</div>";
	}
	else
		$tag_supervisor = "";
	
	foreach( $apis as $api )
	{
		if( in_array( $api['api'], $apis_user ) )
			$seleccion = "<input type='checkbox' name='api[]' value='$api[api]' checked />";
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
		<p>Selecciona los accesos que tendrá el Personal Operativo.</p>
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
			<select name="u_empresa" class="form-control">
				<?= $empresas ?>
			</select>
		</div>
	</div>
	
	<?= $empresa_sec ?>
	
	<?= $tag_supervisor ?>
	
	<div class="row">
		<label class="col-md-2">Nombres</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_nombres" maxlength="20" required="required" value="<?= $info['nombres'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">A Paterno</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_ape_pat" maxlength="20" required="required" value="<?= $info['ape_pat'] ?>" />
		</div>
		
		<label class="col-md-2">A Materno</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_ape_mat" maxlength="20" required="required" value="<?= $info['ape_mat'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Status</label>
		<div class="col-md-4">
			<select name="u_status" class="form-control">
				<option <?= ( $info['status'] == 'A' ) ? 'selected':'' ?> value="A">Activo</option>
				<option <?= ( $info['status'] == 'B' ) ? 'selected':'' ?> value="B">Baja</option>
			</select>
		</div>
	</div>
		
	<div class="row">
		<label class="col-md-2">Correo</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_correo" maxlength="50" required="required" value="<?= $info['correo'] ?>" />
		</div>
		
		<label class="col-md-2">Contraseña</label>
		<div class="col-md-4">
			<input type="text" class="form-control" name="u_pass" maxlength="15" required="required" value="<?= $info['pass'] ?>" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12 text-center">
			<input type="hidden" name="id_usuario" value="<?= $info['id_usuario'] ?>" />
			<input type="hidden" name="usuarioe" value="<?= $usuarioe ?>" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
			<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=catalogos&i=usuarios'" />
			<input type="button" class="btn btn-danger" value="Eliminar" onclick="location.href='.?s=catalogos&i=usuario&usuarioe=<?= $usuarioe ?>&eliminar=<?= $usuarioe ?>'" />
		</div>
	</div>
</form>