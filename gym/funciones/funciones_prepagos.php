<?php
	function lista_socios( $busqueda = 1 )
	{
		global $conexion, $id_empresa, $gbl_paginado;
		
		$pagina		= ( request_var( 'pag', 1 ) - 1 ) * $gbl_paginado;
		$condicion2	= "LIMIT $pagina, $gbl_paginado";
		$datos		= "";
		$colspan	= 4;
		
		if( $busqueda == 1 )
			$condicion = " prep_saldo > 0 ";
		else
			$condicion = " prep_saldo <= 0 ";
		
		$query		= "	SELECT		prep_id_prepago AS id_prepago,
									CONCAT( soc_apepat, ' ', soc_apemat, ' ', soc_nombres ) AS socio,
									ROUND( prep_saldo, 2 ) AS saldo
						FROM		san_prepago
						INNER JOIN	san_socios ON soc_id_socio = prep_id_socio
						WHERE		$condicion
						AND			prep_id_empresa = $id_empresa
						ORDER BY	socio
									$condicion2";
							
		$resultado	= mysqli_query( $conexion, $query );
		
		if( $resultado )
		{
			$i = 1;
			while( $fila = mysqli_fetch_assoc( $resultado ) )
			{
				$datos	.= "<tr onclick='location.href=\".?s=prepagos&i=editar&id_prepago=$fila[id_prepago]\"'>
								<td>".( $pagina + $i )."</td>
								<td>$fila[socio]</td>
								<td class='text-right'>$$fila[saldo]</td>
							</tr>";
				$i++;
			}
		}
		else
			$datos	= "	<tr><td colspan='$colspan'>Ocurrió un problema al tratar de obtener la información. ".mysqli_error( $conexion )."</td></tr>";
		
		if( !$datos )
			$datos	= "	<tr><td colspan='$colspan'>No hay datos.</td></tr>";
		
		return $datos;
	}
	
?>