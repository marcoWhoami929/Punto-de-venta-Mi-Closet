<div class="container is-fluid mb-6">
    <h1 class="title">Ventas</h1>
    <h2 class="subtitle"><i class="fas fa-cart-plus fa-fw"></i> &nbsp; Nueva venta</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
    $check_empresa = $insLogin->seleccionarDatos("Normal", "empresa LIMIT 1", "*", 0);

    if ($check_empresa->rowCount() == 1) {
        $check_empresa = $check_empresa->fetch();
    ?>
        <div class="columns">

            <div class="column pb-6">

                <p class="has-text-centered">
                    <small>Para agregar productos debe de digitar el código de barras en el campo "Código de producto" y luego presionar &nbsp; <strong class="is-uppercase"><i class="far fa-check-circle"></i> &nbsp; Agregar producto</strong>. También puede agregar el producto mediante la opción &nbsp; <strong class="is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar producto</strong>. Ademas puede escribir el código de barras y presionar la tecla <strong class="is-uppercase">enter</strong></small>
                </p>
                <div class="column">
                    <div id="reader" width="600px"></div>
                    <div class="mt-2 w-full">
                        <div class="select">
                            <select class="form-select" id="listaCamaras" onchange="camaraSeleccionada(this)">

                            </select>
                        </div>

                        <button class="button is-link" onclick="detenerCamara()">Detener camara</button>
                    </div>
                </div>
                <form class="pt-6 pb-6" id="sale-barcode-form" autocomplete="off">
                    <div class="columns">
                        <div class="column is-one-quarter">
                            <button type="button" class="button is-link js-modal-trigger" data-target="modal-js-product"><i class="fas fa-search"></i> &nbsp; Buscar producto</button>
                        </div>
                        <div class="column">
                            <div class="field is-grouped">
                                <p class="control is-expanded">
                                    <input class="input" type="text" maxlength="70" autofocus="autofocus" placeholder="Código de barras" id="sale-barcode-input">
                                    <input type="hidden" id="tipo_busqueda" value="venta">
                                </p>
                                <a class="control">

                                    <button type="submit" class="button is-info">
                                        <i class="far fa-check-circle"></i> &nbsp; Agregar producto
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <?php


                echo '<div class="alerta_producto"></div>';

                if (isset($_SESSION['codigo_factura']) && $_SESSION['codigo_factura'] != "") {
                ?>
                    <div class="notification is-info is-light mb-2 mt-2">
                        <h4 class="has-text-centered has-text-weight-bold">Venta realizada</h4>
                        <p class="has-text-centered mb-2">La venta se realizó con éxito. ¿Que desea hacer a continuación? </p>
                        <br>
                        <div class="container">
                            <div class="columns">
                                <div class="column has-text-centered">
                                    <button type="button" class="button is-link is-light" onclick="print_ticket('<?php echo APP_URL . "app/pdf/ticket.php?code=" . $_SESSION['codigo_factura']; ?>')">
                                        <i class="fas fa-receipt fa-2x"></i> &nbsp;
                                        Imprimir ticket de venta
                                        </buttona>
                                </div>
                                <div class="column has-text-centered">
                                    <button type="button" class="button is-link is-light" onclick="print_invoice('<?php echo APP_URL . "app/pdf/invoice.php?code=" . $_SESSION['codigo_factura']; ?>')">
                                        <i class="fas fa-file-invoice-dollar fa-2x"></i> &nbsp;
                                        Imprimir nota de venta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    unset($_SESSION['codigo_factura']);
                }
                ?>
                <div class="table-container container-carrito">


                </div>

                <div class="container container-totales">

                </div>
            </div>

            <div class="column is-one-quarter">
                <h2 class="title has-text-centered">Datos de la venta</h2>
                <?php

                if (isset($_SESSION["porc_descuento"])) {
                } else {
                    $_SESSION["porc_descuento"] = "0.00";
                }
                if (isset($_SESSION["total"])) {
                } else {
                    $_SESSION["total"] = "0.00";
                }

                ?>

                <hr>

                <?php if ($_SESSION['total'] > 0) { ?>
                    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off" name="formsale">
                        <input type="hidden" name="modulo_venta" value="registrar_venta">
                    <?php } else { ?>
                        <form name="formsale">
                        <?php } ?>

                        <div class="control mb-5">
                            <label>Fecha</label>
                            <input class="input" type="date" value="<?php echo date("Y-m-d"); ?>" readonly>
                        </div>

                        <label>Caja de ventas <?php echo CAMPO_OBLIGATORIO; ?></label><br>
                        <div class="select mb-5">
                            <select name="venta_caja">
                                <?php
                                $datos_cajas = $insLogin->seleccionarDatos("Normal", "caja", "*", 0);

                                while ($campos_caja = $datos_cajas->fetch()) {
                                    if ($campos_caja['id_caja'] == $_SESSION['caja']) {
                                        echo '<option value="' . $campos_caja['id_caja'] . '" selected="" >Caja No.' . $campos_caja['numero'] . ' - ' . $campos_caja['nombre'] . ' (Actual)</option>';
                                    } else {
                                        echo '<option value="' . $campos_caja['id_caja'] . '">Caja No.' . $campos_caja['numero'] . ' - ' . $campos_caja['nombre'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <br>

                        <label>Cliente</label>
                        <?php

                        if (isset($_SESSION['datos_cliente_venta']) && count($_SESSION['datos_cliente_venta']) >= 1 && $_SESSION['datos_cliente_venta']['id_cliente'] != 1) {
                        ?>
                            <div class="field has-addons mb-5">
                                <div class="control">
                                    <input class="input" type="text" readonly id="venta_cliente" value="<?php echo $_SESSION['datos_cliente_venta']['nombre'] . " " . $_SESSION['datos_cliente_venta']['apellidos']; ?>">
                                </div>
                                <div class="control">
                                    <a class="button is-danger" title="Remove cliente" id="btn_remove_client" onclick="remover_cliente(<?php echo $_SESSION['datos_cliente_venta']['id_cliente']; ?>)">
                                        <i class="fas fa-user-times fa-fw"></i>
                                    </a>
                                </div>
                            </div>
                        <?php
                        } else {
                            $datos_cliente = $insLogin->seleccionarDatos("Normal", "cliente WHERE id_cliente='1'", "*", 0);
                            if ($datos_cliente->rowCount() == 1) {
                                $datos_cliente = $datos_cliente->fetch();

                                $_SESSION['datos_cliente_venta'] = [
                                    "id_cliente" => $datos_cliente['id_cliente'],
                                    "nombre" => $datos_cliente['nombre'],
                                    "apellidos" => $datos_cliente['apellidos']
                                ];
                            } else {
                                $_SESSION['datos_cliente_venta'] = [
                                    "id_cliente" => 1,
                                    "nombre" => "Publico",
                                    "apellidos" => "General"
                                ];
                            }

                        ?>
                            <div class="field has-addons mb-5">
                                <div class="control">
                                    <input class="input" type="text" readonly id="venta_cliente" value="<?php echo $_SESSION['datos_cliente_venta']['nombre'] . " " . $_SESSION['datos_cliente_venta']['apellidos']; ?>">
                                </div>
                                <div class="control">
                                    <a class="button is-info js-modal-trigger" data-target="modal-js-client" title="Agregar cliente" id="btn_add_client">
                                        <i class="fas fa-user-plus fa-fw"></i>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="control mb-5">
                            <label>% Descuento</label>
                            <input class="input" type="text" name="descuento" id="descuento" value="<?= (isset($_SESSION['porc_descuento']) ? $_SESSION['porc_descuento'] : '0.00')  ?>" maxlength="25" onchange="actualizarCarrito();">
                        </div>
                        <div class="control mb-5">
                            <label>Total pagado por cliente <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input class="input" type="text" name="venta_abono" id="venta_abono" value="0.00" pattern="[0-9.]{1,25}" maxlength="25">
                        </div>

                        <div class="control mb-5">
                            <label>Cambio devuelto a cliente</label>
                            <input class="input" type="text" id="cambio" value="0.00" readonly>
                        </div>

                        <p class="has-text-centered">
                            <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar venta</button>
                        </p>

                        <p class="has-text-centered pt-6">
                            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
                        </p>

                        </form>
            </div>

        </div>
    <?php } else { ?>
        <article class="message is-warning">
            <div class="message-header">
                <p>¡Ocurrio un error inesperado!</p>
            </div>
            <div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>No hemos podio seleccionar algunos datos sobre la empresa, por favor <a href="<?php echo APP_URL; ?>companyNew/">verifique aquí los datos de la empresa</div>
        </article>
    <?php } ?>
</div>

<!-- Modal buscar producto -->
<div class="modal" id="modal-js-product">
    <div class="modal-background"></div>
    <div class="modal-card" style="width:80%">
        <header class="modal-card-head" style="background:#B99654">
            <p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-search"></i> &nbsp; Buscar producto</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field mt-6 mb-6">
                <label class="label">Nombre, marca, modelo, código</label>
                <div class="control">
                    <input class="input" type="text" placeholder="Ingresa el nombre del producto,marca o código del producto" name="input_codigo" autofocus="autofocus" onkeyup="cargarCatalogoProductos(1)" id="input_codigo" maxlength="30">
                </div>
            </div>
            <div class="container" id="tabla_productos"></div>

        </section>
    </div>
</div>

<!-- Modal buscar cliente -->
<div class="modal" id="modal-js-client">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head" style="background:#B99654">
            <p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-search"></i> &nbsp; Buscar y agregar cliente</p>

            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field mt-6 mb-6">
                <label class="label">Nombre, Apellido, Celular</label>
                <div class="control">
                    <input class="input" type="text" name="input_cliente" placeholder="Ingresa el nombre, apellidos o celular del cliente" id="input_cliente" autofocus="autofocus" onkeyup="buscar_cliente()" maxlength="30">
                </div>
            </div>
            <div class="container" id="tabla_clientes"></div>

        </section>
    </div>
</div>

<?php
include "./app/views/inc/print_invoice_script.php";
?>