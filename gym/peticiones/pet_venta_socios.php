<?php
    require_once("../../funciones_globales/funciones_conexion.php");
    require_once("../../funciones_globales/funciones_comunes.php");
    require_once("../../funciones_globales/funciones_phpBB.php");
    require_once("../funciones/sesiones.php");
    
    $enviar = isset($_POST['envio']) ? true : false;
    $tabla = "";
    $mensaje = "";
    
    if ($enviar) {
        $query = "  SELECT    soc_id_socio AS id_socio,
                                soc_apepat AS apepat,
                                soc_apemat AS apemat,
                                soc_nombres AS nombres,
                                soc_mon_saldo AS saldo
                    FROM      san_socios
                    WHERE     soc_id_empresa = $id_empresa
                    ORDER BY  saldo DESC,
                              apepat,
                              apemat,
                              nombres";
        
        $resultado = mysqli_query($conexion, $query);
        
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $saldo = number_format($fila['saldo'], 2);
            
            if ($fila['saldo'])
                $tabla .= " <tr onclick='seleccionar_socio($fila[id_socio], \"$fila[apepat] $fila[apemat] $fila[nombres]\", $fila[saldo])'>";
            else
                $tabla .= " <tr>";
            
            $tabla .= "     <td>$fila[apepat]</td>
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

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title text-primary">Socios con Monedero.</h4>
        </div>
        
        <div class="modal-body">
            <ul><?= $mensaje ?></ul>

            <input type="text" id="busqueda" onkeyup="filtrarSocios()" class="form-control" placeholder="Buscar socio...">
            
            <table class="table table-hover pointer" id="tabla_socios">
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
    function filtrarSocios() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("busqueda");
    filter = input.value.toUpperCase();
    table = document.getElementById("tabla_socios");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) { // Empezar en 1 para saltar el encabezado
        tr[i].style.display = "none"; // Ocultar todas las filas por defecto
        
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break; // Si encuentra una coincidencia, mostrar la fila y salir del bucle interno
                }
            }
        }
    }
}

</script>