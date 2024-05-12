<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tag"></span> Formulario para agregar a nuevo Proveedor
		</h4>
	</div>
</div>

<hr/>

<?php
	if( $enviar )
	{
		$exito	= guardar_proveedora();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=catalogos&i=proveedores" );
			exit;
		}
		else
		{
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
		}
	}
	else
	{
?>
		<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
			<div class="row">
				<label class="col-md-3">Nombre o Razón Social</label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="p_nombres" maxlength="50" required="required" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Dirección</label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="p_direccion" maxlength="100" />
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<h5 class="text-primary"><strong>Información acerca del primer contacto.</strong></h5>
				</div>
			</div>
				
			<div class="row">
				<label class="col-md-3">Nombre</label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="pc_nombres_1" maxlength="50" required="required" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Teléfono</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pc_telefono_1" maxlength="15" />
				</div>
				
				<label class="col-md-3">Extensión</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pc_ext_1" maxlength="10" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Celular</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pc_telcel_1" maxlength="15" />
				</div>
				
				<label class="col-md-2">Correo</label>
				<div class="col-md-4">
					<input type="email" class="form-control" name="pc_correo_1" maxlength="50" />
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<h5 class="text-primary"><strong>Información acerca del segundo contacto.</strong></h5>
				</div>
			</div>
				
			<div class="row">
				<label class="col-md-3">Nombre</label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="pc_nombres_2" maxlength="50" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Teléfono</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pc_telefono_2" maxlength="15" />
				</div>
				
				<label class="col-md-3">Extensión</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pc_ext_2" maxlength="10" />
				</div>
			</div>
			
			<div class="row">
				<label class="col-md-3">Celular</label>
				<div class="col-md-3">
					<input type="text" class="form-control" name="pc_telcel_2" maxlength="15" />
				</div>
				
				<label class="col-md-2">Correo</label>
				<div class="col-md-4">
					<input type="email" class="form-control" name="pc_correo_2" maxlength="50" />
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12 text-center">
					<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
					<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=catalogos&i=proveedores'" />
				</div>
			</div>
		</form>
<?php
	}
?>