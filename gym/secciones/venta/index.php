<div class="row">
    <div class="col-md-12">
        <h4 class="text-info">
            <span class="glyphicon glyphicon-usd"></span> Punto de venta
        </h4>
    </div>
</div>

<hr />

<?php

$articulos = lista_articulos();
$v_comision = obtener_p_comision_tarjeta();
?>

<div class="row">
    <label class="col-md-3">Escribe el Nombre</label>
    <div class="col-md-2"><input type="text" id="criterio_busqueda" class="form-control"
            placeholder="Escribe algo para buscar" onKeyUp="buscar_articulo()" /></div>
</div>

<div class="row">
    <div class="col-md-5">
        <table class="table table-hover pointer h6">
            <thead>
                <tr class="active">
                    <th>Descripción</th>
                    <th class="text-right">Stock</th>
                    <th class="text-right">Precio</th>
                </tr>
            </thead>

            <tbody id="lista_articulos">
                <?= $articulos ?>
            </tbody>
        </table>
    </div>
    
    <div class="col-md-7">
        <div class="row">
            <button type="button" class="btn btn-primary" id="mostrar_socio" onclick="mostrar_socios()">
                <span class="glyphicon glyphicon-user"></span> Seleccionar Socio
            </button>
            <label id="nombre_socio" class="ml-3"></label>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-info"><strong>Método de pago</strong></h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <select class="form-control" name="m_pago" id="m_pago" required onchange="calcular_total()">
                    <option value="" disabled selected>Selecciona el método de pago</option>
                    <option value="E" id="m_pago_e">Efectivo</option>
                    <option value="T" id="m_pago_t">Tarjeta</option>
                    <option value="P" id="m_pago_p">Monedero</option>
                </select>
            </div>
        </div>

        

        <form action=".?s=articulos&i=venta" method="post" onsubmit="return checar_articulos('N')">
            <table class="table table-hover h6">
                <thead>
                    <tr class="active">
                        <th></th>
                        <th>Cant.</th>
                        <th>Descripción</th>
                        <th class="text-right">Precio</th>
                        <th class="text-right">Importe</th>
                    </tr>
                </thead>

                <tbody id="articulo_venta">

                </tbody>
            </table>

            <hr />



            <div class="row" style="display:none" id="div_prepago">
                <div class="col-md-2"><label>Monedero</label></div>
                <div class="col-md-4"><input type="text" class="form-control" id="prepago" readonly /></div>
            </div>

            <div class="row">
                <div class="col-md-2"><label>Efectivo</label></div>
                <div class="col-md-4"><input type="text" class="form-control" id="efectivo" /></div>
            </div>

            <div class="row">
                <div class="col-md-3"><label>Subtotal</label></div>
                <div class="col-md-4">
                    <label id="tag_sub_total">$00.00</label>
                </div>
            </div>

            <div class="row text-danger">
                <div class="col-md-3 text-bold">Total a pagar</div>
                <div class="col-md-4 text-bold" id="tag_total_pago">$00.00</div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="comision" id="comision" value="<?= $v_comision ?>" />
                    <input type="hidden" id="input_total" value="0" />
                    <input type="hidden" name="prep_id_prepago" id="prep_id_prepago" value="0" />
                    <input type="hidden" name="prep_saldo" id="prep_saldo" value="0" />
                    <input type="submit" name="enviar" class="btn btn-primary" value="Procesar" />
                    <input type="button" class="btn btn-default" value="Cancelar" onclick="location.href='.?s=venta'" />
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para mostrar socios -->
<div class="modal fade" id="modalSocios" tabindex="-1" role="dialog" aria-labelledby="modalSociosLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalSociosLabel">Lista de Socios</h4>
            </div>
            <div class="modal-body">
                <table id="tablaSocios" class="table table-striped table-bordered" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Saldo Monedero</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargarán dinámicamente los socios -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>