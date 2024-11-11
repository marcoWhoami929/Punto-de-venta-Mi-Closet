<?php

use app\models\mainModel;

$mainModel = new mainModel();



?>
<div class="container is-fluid mb-6">
    <h1 class="title">Notas</h1>
    <h2 class="subtitle"> <i class="fas fa-clipboard-list"></i> &nbsp; Nueva Nota</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
    if (isset($_SESSION["QrNota"])) {
        $folioNota = $_SESSION["QrNota"];
    } else {
        $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 22;
        $prefijo = 'NOT';
        $folioNota =  strtoupper($prefijo . "-" . substr(str_shuffle($caracteres_permitidos), 0, $longitud));
    }

    if (isset($_SESSION["porc_descuento"])) {
    } else {
        $_SESSION["porc_descuento"] = "0.00";
    }

    ?>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/notasAjax.php" method="POST" autocomplete="off">

        <input type="hidden" name="modulo_notas" value="registrar">
        <input type="hidden" name="route" id="route" value="<?php echo APP_URL; ?>">
        <input type="hidden" name="route_qr" id="route_qr" value="<?php echo APP_URL_CLIENT; ?>detalleNota/<?php echo $folioNota ?>">
        <div class="columns">
            <div class="column is-narrow">
                <div class="box" style="width: 300px">
                    <div class="control">

                        <div class="showQRCode"></div>

                    </div>
                </div>
            </div>
            <div class="column">
                <div class="column">
                    <div class="control">
                        <label>Folio Nota <?php echo CAMPO_OBLIGATORIO; ?></label>
                        <input class="input" type="text" name="folio_nota" id="folio_nota" required value="<?= $folioNota ?>" readonly>
                    </div>
                </div>
                <div class="column">
                    <div class="control">
                        <label>Título Nota <?php echo CAMPO_OBLIGATORIO; ?></label>
                        <input class="input" type="text" name="titulo_nota" id="titulo_nota" required onchange="datosNota()" value="<?= (isset($_SESSION['titulo_nota']) ? $_SESSION['titulo_nota'] : '')  ?>">
                    </div>
                </div>
                <div class="column">
                    <div class="control">
                        <label>%Descuento <?php echo CAMPO_OBLIGATORIO; ?></label>

                        <input class="input" type="number" name="porc_descuento_nota" required id="porc_descuento_nota" onchange="datosNota()" value="<?= (isset($_SESSION['porc_descuento_nota']) ? $_SESSION['porc_descuento_nota'] : '0')  ?>" maxlength="25">
                    </div>
                </div>
                <div class="columns" style="padding-left:10px;width:100%">
                    <div class="column">
                        <div class="control">
                            <label>Fecha Publicación <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input class="input" type="datetime-local" name="fecha_publicacion" id="fecha_publicacion" onchange="datosNota()" required value="<?= (isset($_SESSION['fecha_publicacion_nota']) ? $_SESSION['fecha_publicacion_nota'] : '')  ?>">
                        </div>
                    </div>

                    <div class="column">
                        <div class="control">
                            <label>Fecha Expiracion <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input class="input" type="datetime-local" name="fecha_expiracion" id="fecha_expiracion" onchange="datosNota()" required value="<?= (isset($_SESSION['fecha_expiracion_nota']) ? $_SESSION['fecha_expiracion_nota'] : '')  ?>">
                        </div>
                    </div>

                </div>



            </div>
        </div>


        <p class="has-text-centered">

            <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
        </p>
        <p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>

    </form>
    <div class="column">
        <div id="reader" width="600px"></div>
        <div class="mt-2 w-full">
            <div class="select">
                <select class="form-select" id="listaCamaras" onchange="camaraSeleccionada(this)">

                </select>
            </div>

            <button class="button is-link " onclick="detenerCamara()">Detener camara</button>
        </div>
    </div>
    <?php

    echo '<div class="alerta_producto"></div>';
    ?>
    <form class="pt-6 pb-6" id="sale-barcode-form" autocomplete="off">
        <div class="columns">
            <div class="column is-one-quarter">
                <button type="button" class="button is-link js-modal-trigger" data-target="modal-js-product"><i class="fas fa-search"></i> &nbsp; Buscar producto</button>
            </div>
            <div class="column">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input" type="text" maxlength="70" autofocus="autofocus" placeholder="Código de barras" id="sale-barcode-input">
                        <input type="hidden" id="tipo_busqueda" value="nota">
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

    <div class="table-container container-carrito-notas">
    </div>

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