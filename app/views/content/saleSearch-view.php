<div class="container is-fluid mb-6">
    <h1 class="title">Ventas</h1>
    <h2 class="subtitle"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; Buscar ventas por código</h2>
</div>

<div class="container pb-6 pt-6">
    <?php

    use app\controllers\saleController;

    $insVenta = new saleController();

    if (!isset($_SESSION[$url[0]]) && empty($_SESSION[$url[0]])) {
    ?>
        <div class="columns">
            <div class="column">
                <form class=FormularioAjaxNew action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off">
                    <input type="hidden" name="modulo_buscador" value="buscar">
                    <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                    <label>Búsqueda</label>
                    <div class="field is-grouped">

                        <p class="control is-expanded">
                            <input class="input is-rounded" type="text" name="txt_buscador" placeholder="Código de venta" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\- ]{1,30}" maxlength="30" required>
                        </p>
                        <p class="control">
                            <button class="button is-info" type="submit">Buscar</button>
                        </p>
                    </div>
                </form>
            </div>
            <div class="column">
                <form class=FormularioAjaxNew action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off">
                    <input type="hidden" name="modulo_buscador" value="filtrar">
                    <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                    <div class="select">
                        <label>Tipo Entrega</label>
                        <select id="filtro-tipo-entrega" name="filtro-tipo-entrega">
                            <option value="">Todos</option>
                            <option value="recoleccion">Recolección</option>
                            <option value="envio">Envio</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    <?php


        echo $insVenta->listarVentaControlador($url[1], 15, $url[0], "");

        include "./app/views/inc/print_invoice_script.php";
    } else { ?>
        <div class="columns">
            <div class="column">
                <form class=FormularioAjaxNew action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off">
                    <input type="hidden" name="modulo_buscador" value="buscar">
                    <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                    <div class="field is-grouped">
                        <p class="control is-expanded">
                            <input class="input is-rounded" type="text" name="txt_buscador" placeholder="Código de venta" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\- ]{1,30}" maxlength="30" required>
                        </p>
                        <p class="control">
                            <button class="button is-info" type="submit">Buscar</button>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <form class="has-text-centered mt-6 mb-6 FormularioAjaxNew" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off">
                    <input type="hidden" name="modulo_buscador" value="eliminar">
                    <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                    <p><i class="fas fa-search fa-fw"></i> &nbsp; Estas buscando por código <strong>“<?php echo $_SESSION[$url[0]]; ?>”</strong></p>
                    <br>
                    <button type="submit" class="button is-danger is-rounded"><i class="fas fa-trash-restore"></i> &nbsp; Eliminar busqueda</button>
                </form>
            </div>
        </div>
    <?php

        echo $insVenta->listarVentaControlador($url[1], 15, $url[0], $_SESSION[$url[0]]);

        include "./app/views/inc/print_invoice_script.php";
    }
    ?>
</div>
<div class="modal fade is-large" id="modal-pago-venta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head" style="background:#B99654">
            <p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cashier"></i> &nbsp; Confirmar Pago</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="columns">
                <div class="column">
                    <div class="field mt-2 mb-2">
                        <label class="label">Elegir Forma de Pago</label>
                        <div class="control has-text-centered">
                            <div class="select is-primary  is-rounded is-large">
                                <select onchange="eleccionFormaPago()" id="forma_pago_venta">
                                    <option value="1">Efectivo</option>
                                    <option value="2">Transferencia Electrónica</option>
                                    <option value="3">Tarjeta de Crédito</option>
                                    <option value="4">Tarjeta de Débito</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="columns  mb-6">
                <div class="column">
                    <div class="field">
                        <label class="label">Total a Pagar:</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input is-large" type="text" placeholder="0.00" style="font-size:50px;font-weight:bold" id="total_pagar_venta" disabled />
                            <span class="icon is-medium is-left " style="margin-top:20px">
                                <i class="fas fa-dollar-sign fa-3x"></i>
                            </span>

                        </div>
                    </div>
                </div>

            </div>
            <div class="columns  mb-6" style="display:none" id="div-payment-efectivo">
                <div class="column">
                    <div class="field">
                        <label class="label">Su Pago</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input is-large" type="text" placeholder="0.00" style="font-size:40px;font-weight:bold" id="total_pagado_venta" onkeyup="calcularCambio()" value="0.00" />
                            <span class="icon is-medium is-left " style="margin-top:20px">
                                <i class="fas fa-dollar-sign fa-3x"></i>
                            </span>

                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Cambio</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input is-large" type="text" placeholder="0.00" style="font-size:40px;font-weight:bold" id="total_cambio_venta" readonly />
                            <span class="icon is-medium is-left " style="margin-top:20px">
                                <i class="fas fa-dollar-sign fa-3x"></i>
                            </span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="columns  mb-6" style="display:none" id="div-payment-transferencia">
                <div class="column">
                    <div class="field">
                        <label class="label">Referencia Pago</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input is-large" type="text" placeholder="Capturar referencia de pago" style="font-size:20px;font-weight:bold" id="referencia_venta" />
                            <span class="icon is-medium is-left">

                                <i class="fas fa-receipt"></i>
                            </span>

                        </div>
                    </div>
                </div>

            </div>
            <div class="columns">
                <div class="column">
                    <p class="has-text-centered">
                        <button type="button" class="button is-success button-lg" onclick="confirmacionPago()"><i class="fas fa-money-check-alt"></i> &nbsp; Confirmar Pago</button>
                    </p>
                </div>
            </div>



        </section>
    </div>
</div>