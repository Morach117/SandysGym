<?php
	require_once( "../../funciones_globales/funciones_conexion.php" );
	require_once( "../../funciones_globales/funciones_comunes.php" );
	require_once( "../../funciones_globales/funciones_phpBB.php" );
	require_once( "../funciones/sesiones.php" );
	
	$enviar = isset($_POST['envio']) ? true : false;
	$tabla = "";
	$mensaje = "";
	
	if ($enviar) {
		$query = "SELECT soc_id_socio AS id_socio,
									soc_mon_saldo AS saldo,
									soc_apepat AS apepat,
									soc_apemat AS apemat,
									soc_nombres AS nombres
						FROM san_socios
						WHERE soc_id_empresa = $id_empresa
						ORDER BY saldo DESC,
									apepat,
									apemat,
									nombres";
		
		$resultado = mysqli_query($conexion, $query);
		
		while ($fila = mysqli_fetch_assoc($resultado)) {
			$saldo = number_format($fila['saldo'], 2);
			
			if ($fila['saldo'])
				$tabla .= "<tr onclick='seleccionar_socio($fila[id_socio], \"$fila[apepat] $fila[apemat] $fila[nombres]\", $fila[saldo])'>";
			else
				$tabla .= "<tr>";
			
			$tabla .= "<td>$fila[apepat]</td>
							<td>$fila[apemat]</td>
							<td>$fila[nombres]</td>
							<td class='text-right'>$$saldo</td>
						</tr>";
		}
	} else {
		$mensaje = "<li>Operación inválida.</li>";
	}
	
	mysqli_close($conexion);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Socios con Saldo en Monedero</title>
	<style>
		/* Estilo básico para la tabla */
		.table {
			width: 100%;
			border-collapse: collapse;
		}
		.table th, .table td {
			padding: 8px;
			text-align: left;
			border: 1px solid #ddd;
		}
		.table th {
			background-color: #f2f2f2;
		}
		.pointer {
			cursor: pointer;
		}
		.search-input {
			margin-bottom: 10px;
			padding: 8px;
			width: 100%;
			border: 1px solid #ccc;
		}
	</style>
</head>
<body>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title text-primary">Socios con Saldo en Monedero.</h4>
		</div>
		
		<div class="modal-body">
			<ul><?= $mensaje ?></ul>

			<input type="text" id="searchInput" class="search-input" onkeyup="filterTable()" placeholder="Buscar por cualquier campo...">

			<table id="sociosTable" class="table table-hover pointer">
				<thead>
					<tr class="active">
						<th>A Paterno</th>
						<th>A Materno</th>
						<th>Nombres</th>
						<th>Saldo</th>
					</tr>
				</thead>
				
				<tbody>
					<?= $tabla ?>
				</tbody>
			</table>
		</div>
		
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-primary">Cerrar</button>
		</div>
	</div>
</div>

<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('sociosTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let showRow = false;

        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const cellValue = td[j].textContent || td[j].innerText;
                if (cellValue.toLowerCase().indexOf(filter) > -1) {
                    showRow = true;
                    break;
                }
            }
        }

        if (showRow) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}
</script>
</body>
</html>
