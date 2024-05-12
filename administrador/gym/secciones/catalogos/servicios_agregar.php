<?php
	if( $enviar )
	{
		$exito	= guardar_servicio();
		
		if( $exito['num'] == 1 )
		{
			header( "Location: .?s=catalogos&i=servicios" );
			exit;
		}
		else
		{
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
		}
	}
?>

<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-bullhorn"></span> Formulario para agregar un nuevo Servicio
		</h4>
	</div>
</div>

<hr/>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=servicios_agregar" >
	<div class="row">
		<label class="col-md-2">CLAVE</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="s_clave" maxlength="15" required="required" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Descripción</label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="s_descripcion" maxlength="50" required="required" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Tipo</label>
		<div class="col-md-4">
			<select name="s_tipo" class="form-control">
				<option value="PERIODO">Período</option>
				<option value="PARCIAL">Parcial</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Cuotas</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="s_cuota" maxlength="7" required="required" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Días</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="s_dias" maxlength="2" />
		</div>
	</div>
	
	<div class="row">
		<label class="col-md-2">Meses</label>
		<div class="col-md-2">
			<input type="text" class="form-control" name="s_meses" maxlength="2" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12 text-center">
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
			<input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=catalogos&i=servicios'" />
		</div>
	</div>
</form>