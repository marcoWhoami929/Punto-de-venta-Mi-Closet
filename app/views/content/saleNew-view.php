<div class="container is-fluid mb-6">
    <h1 class="title">Ventas</h1>
    <h2 class="subtitle"><i class="fas fa-cart-plus fa-fw"></i> &nbsp; Nueva venta</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
    $check_empresa = $insLogin->seleccionarDatos("Normal", "empresa LIMIT 1", "*", 0);

    if ($check_empresa->rowCount() == 1) {
        $check_empresa = $check_empresa->fetch();
    ?>
        <div class="columns">

            <div class="column pb-6">

                <p class="has-text-centered pt-6 pb-6">
                    <small>Para agregar productos debe de digitar el código de barras en el campo "Código de producto" y luego presionar &nbsp; <strong class="is-uppercase"><i class="far fa-check-circle"></i> &nbsp; Agregar producto</strong>. También puede agregar el producto mediante la opción &nbsp; <strong class="is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar producto</strong>. Ademas puede escribir el código de barras y presionar la tecla <strong class="is-uppercase">enter</strong></small>
                </p>
                <form class="pt-6 pb-6" id="sale-barcode-form" autocomplete="off">
                    <div class="columns">
                        <div class="column is-one-quarter">
                            <button type="button" class="button is-link is-light js-modal-trigger" data-target="modal-js-product"><i class="fas fa-search"></i> &nbsp; Buscar producto</button>
                        </div>
                        <div class="column">
                            <div class="field is-grouped">
                                <p class="control is-expanded">
                                    <input class="input" type="text" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70" autofocus="autofocus" placeholder="Código de barras" id="sale-barcode-input">
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

                if (isset($_SESSION['alerta_producto_agregado']) && $_SESSION['alerta_producto_agregado'] != "") {
                    echo '
                    <div class="notification is-success is-light">
                      ' . $_SESSION['alerta_producto_agregado'] . '
                    </div>
                    ';
                    unset($_SESSION['alerta_producto_agregado']);
                }
                if (isset($_SESSION['alerta_carrito_actualizado']) && $_SESSION['alerta_carrito_actualizado'] != "") {
                    echo '
                    <div class="notification is-success is-light">
                      ' . $_SESSION['alerta_carrito_actualizado'] . '
                    </div>
                    ';
                    unset($_SESSION['alerta_carrito_actualizado']);
                }

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
                                        Imprimir factura de venta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    unset($_SESSION['codigo_factura']);
                }
                ?>
                <div class="table-container">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <thead>
                            <tr>
                                <th class="has-text-centered">#</th>
                                <th class="has-text-centered">Código de barras</th>
                                <th class="has-text-centered">Producto</th>
                                <th class="has-text-centered">Cant.</th>
                                <th class="has-text-centered">Precio</th>
                                <th class="has-text-centered">% Desc</th>
                                <th class="has-text-centered">Desc</th>
                                <th class="has-text-centered">Subtotal</th>
                                <th class="has-text-centered">Total</th>
                                <th class="has-text-centered">Actualizar</th>
                                <th class="has-text-centered">Remover</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_SESSION['datos_producto_venta']) && count($_SESSION['datos_producto_venta']) >= 1) {

                                $_SESSION['total'] = 0;
                                $_SESSION['subtotal'] = 0;
                                $_SESSION['descuento'] = 0;
                                $cc = 1;

                                foreach ($_SESSION['datos_producto_venta'] as $productos) {
                            ?>
                                    <tr class="has-text-centered">
                                        <td><?php echo $cc; ?></td>
                                        <td><?php echo $productos['codigo']; ?></td>
                                        <td><?php echo $productos['descripcion']; ?></td>
                                        <td>
                                            <div class="control">
                                                <input class="input sale_input-cant has-text-centered" value="<?php echo $productos['cantidad']; ?>" id="sale_input_<?php echo str_replace(" ", "_", $productos['codigo']); ?>" onchange="actualizar_cantidad('#sale_input_<?php echo str_replace(" ", "_", $productos['codigo']); ?>','<?php echo $productos['codigo']; ?>')" type="number" style="max-width: 80px;">
                                            </div>
                                        </td>
                                        <td><?php echo MONEDA_SIMBOLO . number_format($productos['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
                                        <td>
                                            <div class="control">
                                                <input class="input sale_input-cant has-text-centered" value="<?php echo $productos['porc_descuento']; ?>" id="porc_descuento<?php echo str_replace(" ", "_", $productos['codigo']); ?>" type="number" style="max-width: 80px;" readonly>
                                            </div>
                                        </td>
                                        <td><?php echo MONEDA_SIMBOLO . number_format($productos['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
                                        <td><?php echo MONEDA_SIMBOLO . number_format($productos['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
                                        <td><?php echo MONEDA_SIMBOLO . number_format($productos['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>

                                        <td>
                                            <button type="button" class="button is-success is-rounded is-small" onclick="actualizar_cantidad('#sale_input_<?php echo str_replace(" ", "_", $productos['codigo']); ?>','<?php echo $productos['codigo']; ?>')">
                                                <i class="fas fa-redo-alt fa-fw"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off">

                                                <input type="hidden" name="codigo" value="<?php echo $productos['codigo']; ?>">
                                                <input type="hidden" name="modulo_venta" value="remover_producto">

                                                <button type="submit" class="button is-danger is-rounded is-small" title="Remover producto">
                                                    <i class="fas fa-trash-restore fa-fw"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                    $cc++;
                                    /*
                                    $_SESSION['subtotal'] += $productos['subtotal'];
                                    $_SESSION['descuento'] += $productos['descuento'];
                                    $_SESSION['total'] += $productos['total'];
                                    */
                                    $_SESSION['subtotal'] += $productos['subtotal'];
                                    $_SESSION['descuento'] += $productos['descuento'];
                                    $_SESSION['total'] += $productos['total'];
                                }
                                ?>
                                <tr class="has-text-centered">
                                    <td colspan="4"></td>
                                    <td class="has-text-weight-bold">
                                        SUBTOTAL
                                    </td>
                                    <td class="has-text-weight-bold">
                                        <?php echo MONEDA_SIMBOLO . number_format($_SESSION['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
                                    </td>
                                    <td colspan="5"></td>
                                </tr>
                                <tr class="has-text-centered">
                                    <td colspan="4"></td>
                                    <td class="has-text-weight-bold">
                                        DESCUENTO
                                    </td>
                                    <td class="has-text-weight-bold">
                                        <?php echo MONEDA_SIMBOLO . number_format($_SESSION['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
                                    </td>
                                    <td colspan="5"></td>
                                </tr>
                                <tr class="has-text-centered">
                                    <td colspan="4"></td>
                                    <td class="has-text-weight-bold">
                                        TOTAL
                                    </td>
                                    <td class="has-text-weight-bold">
                                        <?php echo MONEDA_SIMBOLO . number_format($_SESSION['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
                                    </td>
                                    <td colspan="5"></td>
                                </tr>
                            <?php
                            } else {
                                $_SESSION['total'] = 0;
                            ?>
                                <tr class="has-text-centered">
                                    <td colspan="13">
                                        No hay productos agregados
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="column is-one-quarter">
                <h2 class="title has-text-centered">Datos de la venta</h2>
                <?php

                if (isset($_SESSION["porc_descuento"])) {
                } else {
                    $_SESSION["porc_descuento"] = "0.00";
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

                        <h4 class="subtitle is-5 has-text-centered has-text-weight-bold mb-5"><small>TOTAL A PAGAR: <?php echo MONEDA_SIMBOLO . number_format($_SESSION['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></small></h4>

                        <?php if ($_SESSION['total'] > 0) { ?>
                            <p class="has-text-centered">
                                <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar venta</button>
                            </p>
                        <?php } ?>
                        <p class="has-text-centered pt-6">
                            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
                        </p>
                        <input type="hidden" value="<?php echo number_format($_SESSION['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, ""); ?>" id="total_hidden">
                        <input type="hidden" value="<?php echo number_format($_SESSION['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, ""); ?>" id="subtotal_hidden">
                        <input type="hidden" value="<?php echo number_format($_SESSION['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, ""); ?>" id="descuento_hidden">
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
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar producto</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field mt-6 mb-6">
                <label class="label">Nombre, marca, modelo, código</label>
                <div class="control">
                    <input class="input" type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" placeholder="Ingresa el nombre del producto,marca o código del producto" name="input_codigo" autofocus="autofocus" onkeyup="buscar_codigo()" id="input_codigo" maxlength="30">
                </div>
            </div>
            <div class="container" id="tabla_productos"></div>
            <p class="has-text-centered">
                <button type="button" class="button is-link is-light" onclick="buscar_codigo()"><i class="fas fa-search"></i> &nbsp; Buscar</button>
            </p>
        </section>
    </div>
</div>

<!-- Modal buscar cliente -->
<div class="modal" id="modal-js-client">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar y agregar cliente</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field mt-6 mb-6">
                <label class="label">Nombre, Apellido, Celular</label>
                <div class="control">
                    <input class="input" type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" name="input_cliente" placeholder="Ingresa el nombre, apellidos o celular del cliente" id="input_cliente" autofocus="autofocus" onkeyup="buscar_cliente()" maxlength="30">
                </div>
            </div>
            <div class="container" id="tabla_clientes"></div>
            <p class="has-text-centered">
                <button type="button" class="button is-link is-light" onclick="buscar_cliente()"><i class="fas fa-search"></i> &nbsp; Buscar</button>
            </p>
        </section>
    </div>
</div>

<?php
include "./app/views/inc/print_invoice_script.php";
?>