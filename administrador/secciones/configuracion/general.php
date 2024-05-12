<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-tower"></span> Configuración general
		</h4>
	</div>
</div>

<hr/>

<?php
	if( $enviar )
	{
		$exito	= actualizar_conf_general();
		
		if( $exito['num'] == 1 )
			mostrar_mensaje_div( $exito['msj'], 'success' );
		else
			mostrar_mensaje_div( $exito['num'].". ".$exito['msj'], 'danger' );
	}
	
	$tabla	= conf_general();
?>

<form role="form" method="post" action=".?s=<?= $seccion ?>&i=<?= $item ?>" >
	<?= $tabla ?>
	
	<div class="row">
		<div class="col-md-12">
			<input type="submit" name="enviar" class="btn btn-primary" value="Guardar" />
		</div>
	</div>
</form>