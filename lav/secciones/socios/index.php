<div class="row">
	<div class="col-md-12">
		<h4 class="text-info">
			<span class="glyphicon glyphicon-user"></span> Socios
		</h4>
	</div>
</div>

<hr/>
<?php
	$pag_blq		= request_var( 'blq', 0 );
	$pag_pag		= request_var( 'pag', 0 );
	$pag_busqueda	= request_var( 'pag_busqueda', '' );
	$letra			= request_var( 'letra', '' );
	
	/*se envia al item EDITAR para cuando se de regresar no se pierda la busqueda, tambien esta en el ITEM de EDITAR*/
	$var_array_regresar	= array( 'blq', 'pag', 'letra', 'pag_busqueda' );
	$var_regresar		= '';
	
	foreach( $var_array_regresar as $ind )
	{
		$var	= request_var( "$ind", '' );
		
		if( $var )
			$var_regresar .= "&$ind=$var";
	}
	
	$var_exito	= lista_socios( $pag_busqueda, $letra, $var_regresar );
	$paginas	= paginado( $var_exito['num'], 'socios', '', $letra );
?>
<div class="row text-center h6" id="paginado">
	<div class="col-md-12">
		<ul class="pagination">
			<li <?= ( $letra == '' ) ? "class='active'":'' ?>><a href=".?s=socios">Todos</a></li>
			<li <?= ( $letra == 'A' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=A">A</a></li>
			<li <?= ( $letra == 'B' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=B">B</a></li>
			<li <?= ( $letra == 'C' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=C">C</a></li>
			<li <?= ( $letra == 'D' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=D">D</a></li>
			<li <?= ( $letra == 'E' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=E">E</a></li>
			<li <?= ( $letra == 'F' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=F">F</a></li>
			<li <?= ( $letra == 'G' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=G">G</a></li>
			<li <?= ( $letra == 'H' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=H">H</a></li>
			<li <?= ( $letra == 'I' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=I">I</a></li>
			<li <?= ( $letra == 'J' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=J">J</a></li>
			<li <?= ( $letra == 'K' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=K">K</a></li>
			<li <?= ( $letra == 'L' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=L">L</a></li>
			<li <?= ( $letra == 'M' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=M">M</a></li>
		</ul>
		<ul class="pagination">
			<li <?= ( $letra == 'N' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=N">N</a></li>
			<li <?= ( $letra == 'Ñ' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=Ñ">Ñ</a></li>
			<li <?= ( $letra == 'O' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=O">O</a></li>
			<li <?= ( $letra == 'P' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=P">P</a></li>
			<li <?= ( $letra == 'Q' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=Q">Q</a></li>
			<li <?= ( $letra == 'R' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=R">R</a></li>
			<li <?= ( $letra == 'S' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=S">S</a></li>
			<li <?= ( $letra == 'T' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=T">T</a></li>
			<li <?= ( $letra == 'U' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=U">U</a></li>
			<li <?= ( $letra == 'V' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=V">V</a></li>
			<li <?= ( $letra == 'W' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=W">W</a></li>
			<li <?= ( $letra == 'X' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=X">X</a></li>
			<li <?= ( $letra == 'Y' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=Y">Y</a></li>
			<li <?= ( $letra == 'Z' ) ? "class='active'":'' ?>><a href=".?s=socios&letra=Z">Z</a></li>
		</ul>
	</div>
</div>

<form method="post" action=".?s=socios">
	<div class="row">
		<div class="col-md-4"><input type="text" name="pag_busqueda" class="form-control" value="<?= $pag_busqueda ?>" /></div>
		<div class="col-md-2">
			<input type="hidden" name="letra" value="<?= $letra ?>" />
			<input type="submit" name="enviar" class="btn btn-primary btn-sm" value="Buscar" />
		</div>
	</div>
</form>
	
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-condensed pointer">
			<thead>
				<tr>
					<th>#</th>
					<th>ID</th>
					<th>Apellidos</th>
					<th>Nombre</th>
					<th>Correo</th>
					<th>Celular</th>
				</tr>
			</thead>
			
			<tbody id="lista_socios">
				<?= $var_exito['msj'] ?>
			</tbody>
		</table>
	</div>
</div>

<?= $paginas ?>