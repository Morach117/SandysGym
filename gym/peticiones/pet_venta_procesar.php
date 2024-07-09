<?php
require_once("../../funciones_globales/funciones_conexion.php");
require_once("../../funciones_globales/funciones_comunes.php");
require_once("../../funciones_globales/funciones_phpBB.php");
require_once("../funciones/sesiones.php");
require_once("../funciones/funciones_venta.php");

require '../../funciones_globales/phpmailer/src/PHPMailer.php';
require '../../funciones_globales/phpmailer/src/SMTP.php';
require '../../funciones_globales/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


/* desde javascript */
$enviar = isset($_POST['envio']) ? true : false;
$ids_articulo = request_var('id_articulo', '');
$cantidad_id = request_var('cantidad', '');
$js_tot_efectivo = request_var('efectivo', 0.0); /* dinero dado en efectivo */
$js_tot_prepago = request_var('prepago_imp', 0.0); /* dinero seleccionado para descontar del prepago */
$js_tot_a_pagar = request_var('total_a_pagar', 0.0); /* CANTIDAD A PAGAR */
$saldo = request_var('saldo', 0.0); /* saldo a favor en prepago del socio */
$chk_prepago = request_var('prepago', false);
$id_socio = request_var('id_prepago', 0); /* ID del socio para descontar de su saldo */
$commit = request_var('commit', 'N'); /* indica si se termina o no la venta */
$js_metodo_pago = request_var('tipo_pago', ''); /* E-T-P */

/* propias del código del modal */
$v_tot_tarjeta = 0;
$pet_saldo = 0;
$v_efectivo_prep = 0;
$v_tot_cambio = 0;
$mensaje = "";
$tabla = "";
$etiqueta = "text-success";
$continuar = false;
$articulos = array();
$exito = array();
$v_sub_total = 0;
$total_articulo = 0;

$array_ids = explode(',', $ids_articulo);
$array_cant_id = explode(',', $cantidad_id);

$count_ids = count($array_ids);
$count_cants = count($array_cant_id);

if ($enviar) {
    if ($js_metodo_pago == 'T') {
        $v_tot_tarjeta = $js_tot_a_pagar;
    } elseif ($js_metodo_pago == 'P') {
        $pet_saldo = obtener_saldo_socio($id_socio);
        if ($pet_saldo < $js_tot_a_pagar) {
            $js_tot_efectivo = $js_tot_a_pagar - $pet_saldo;
            $js_tot_prepago = $pet_saldo;
        } else {
            $js_tot_prepago = $js_tot_a_pagar;
            $js_tot_efectivo = 0;
        }
    }

    if ($chk_prepago) {
        $pet_saldo = obtener_saldo_socio($id_socio);

        if ($pet_saldo == $saldo && $pet_saldo >= $js_tot_prepago) {
            $continuar = true;
        } else {
            $mensaje = "<li>No hay saldo suficiente para procesar la venta.</li>";
        }
    } else {
        $continuar = true;
    }

    $v_efectivo_prep = $js_tot_efectivo + $js_tot_prepago;

    if ($continuar) {
        if ($js_metodo_pago == 'E' || $js_metodo_pago == 'T' || $js_metodo_pago == 'P') {
            if ($count_ids == $count_cants && is_numeric($js_tot_efectivo) && is_numeric($js_tot_prepago)) {
                $query = "SELECT art_id_articulo AS id_articulo, art_codigo AS codigo, art_descripcion AS descripcion, stk_existencia AS stock, art_precio AS precio
                          FROM san_articulos
                          INNER JOIN san_stock ON stk_id_articulo = art_id_articulo
                          WHERE art_id_articulo IN ($ids_articulo) AND stk_id_empresa = $id_empresa AND art_id_consorcio = $id_consorcio";

                $resultado = mysqli_query($conexion, $query);

                while ($fila = mysqli_fetch_assoc($resultado)) {
                    array_push($articulos, $fila);
                }

                foreach ($array_cant_id as $cant_id) {
                    list($cantidad, $id_articulo) = explode('-', $cant_id);

                    foreach ($articulos as $articulo) {
                        if ($articulo['id_articulo'] == $id_articulo) {
                            $importe = $articulo['precio'] * $cantidad;
                            $v_sub_total += $importe;
                            $total_articulo += $cantidad;

                            $tabla .= " <tr>
                                            <td>$cantidad</td>
                                            <td>$articulo[descripcion]</td>
                                            <td class='text-right'>$" . number_format($articulo['precio'], 2) . "</td>
                                            <td class='text-right'>$" . (number_format($importe, 2)) . "</td>
                                        </tr>";

                            break;
                        }
                    }
                }

                if ($js_metodo_pago == 'T') {
                    $v_tot_tarjeta = round($v_sub_total, 2);
                    $js_tot_efectivo = 0;
                    $v_efectivo_prep = 0;
                }

                if ($continuar && (($js_tot_a_pagar == $v_sub_total && $js_metodo_pago == 'E') || ($js_metodo_pago == 'T' && $js_tot_a_pagar == $v_tot_tarjeta) || ($js_metodo_pago == 'P' && $js_tot_a_pagar == ($js_tot_prepago + $js_tot_efectivo)))) {
                    if ($js_metodo_pago == 'E') {
                        $v_tot_cambio = $v_efectivo_prep - $v_sub_total;
                    }

                    if (!$mensaje) {
                        if (($v_efectivo_prep >= $v_sub_total && $js_metodo_pago == 'E') || $js_metodo_pago == 'T' || $js_metodo_pago == 'P') {
                            if ($commit == 'S') {
                                $exito = preocesar_venta($array_cant_id, $chk_prepago, $id_socio, $js_tot_prepago, $js_tot_efectivo - $v_tot_cambio, $v_sub_total, $js_metodo_pago, $v_tot_tarjeta);

                                if ($exito['num'] != 1) {
                                    $etiqueta = "text-danger";
                                }

                                $exito['efectivo'] = $js_tot_efectivo;
                                $exito['prepago_imp'] = $js_tot_prepago;

                                echo json_encode($exito);
                            } else {
                                $exito['num'] = 1;
                                $exito['msj'] = "Se puede terminar la venta.";
                            }
                        } else {
                            $mensaje .= "<li>Operación con verificación faltante.</li>";
                        }
                    }
                } else {
                    $mensaje .= "<li>El total de la venta no coincide con la validación. ($js_metodo_pago == T && $js_tot_a_pagar == $v_tot_tarjeta)</li>";
                }
            } else {
                $mensaje .= "<li>No se escribieron cantidades correctas para los Artículos o Efectivo.</li>";
            }
        } else {
            $mensaje .= "<li>Tipo de pago no identificado.</li>";
        }
    }
} else {
    $mensaje .= "<li>No se validó el envío del formulario.</li>";
}

if ($mensaje) {
?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-warning">Venta detenida.</h4>
            </div>
            
            <div class="modal-body">
                <ul>
                    <?= $mensaje ?>
                </ul>
            </div>
            
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Finalizar</button>
            </div>
        </div>
    </div>
<?php
} elseif ($exito['num'] == 1 && $commit == 'N') { // para confirmar el COMMIT
?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-primary">Artículos vendidos.</h4>
            </div>
            
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                        <tr class="active">
                            <th>Cantidad</th>
                            <th>Descripción</th>
                            <th class="text-right">Precio</th>
                            <th class="text-right">Importe</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?= $tabla ?>
                    </tbody>
                </table>

                <p class="text-right">
                    <br/><label><?= $total_articulo ?> artículos por un total de $<?= number_format($v_sub_total, 2) ?></label>
                    <br/>Tarjeta: <label>$<?= number_format($v_tot_tarjeta, 2) ?></label>
                    <br/>Efectivo: <label>$<?= number_format($js_tot_efectivo, 2) ?></label>
                    <br/>Monedero: <label>$<?= number_format($js_tot_prepago, 2) ?></label>
                    <br/><h4 class="text-right text-success">Cambio: $<?= number_format($v_tot_cambio, 2) ?></h4>
                </p>
                
                <h4 class="text-right <?= $etiqueta ?>">
                    <?= $exito['num'] .". ". $exito['msj'] ?>
                </h4>
            </div>
            
            <div class="modal-footer">
                <label id="msj_procesar">&nbsp;</label>
                <label id="img_procesar">&nbsp;</label>
                
                <label id="btn_procesar">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
                    <button type="button" onclick="checar_articulos('S')" class="btn btn-primary">Realizar la Venta</button>
                </label>
            </div>
        </div>
    </div>
<?php
}

mysqli_close($conexion);
?>
