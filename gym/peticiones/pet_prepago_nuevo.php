<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$id_socio	= request_var( 'id_socio', 0 );
	$enviar		= isset( $_POST['envio'] ) ? true:false;
	$dsocio		= ":/";
	
	$query		= "	SELECT 
						CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS nombres
					FROM 
						san_socios 
					WHERE 
						soc_id_socio = $id_socio";
	
	$resultado	= mysqli_query( $conexion, $query );
	
	if( $resultado )
	{
		if( $fila = mysqli_fetch_assoc( $resultado ) )
			$dsocio = $fila['nombres'];
	}
	
	if( !$enviar )
	{
		echo "No se ha válidado el envio del formaluario. Intenta nuevamente.";
		exit;
	}
	
	if( !$id_socio )
	{
		echo "No se pudo identificar al socio. Intenta nuevamente.";
		exit;
	}
	
	mysqli_close( $conexion );
?>

<form action=".?s=prepagos&i=nuevo" method="post">
	<div class="row">
		<div class="col-md-12">
			<h5><strong><?= $dsocio ?></strong></h5>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-3">Importe</div>
		<div class="col-md-9">
			<input type="text" name="prep_importe" class="form-control" maxlength="6" required="required" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12 text-right">
			<input type="hidden" name="id_socio" value="<?= $id_socio ?>" />
			<input type="submit" name="enviar" class="btn btn-primary" value="Registrar" />
		</div>
	</div>
</form>