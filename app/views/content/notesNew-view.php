<div class="container is-fluid mb-6">
    <h1 class="title">Notas</h1>
    <h2 class="subtitle"> <i class="fas fa-clipboard-list"></i> &nbsp; Nueva Nota</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
    $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $longitud = 22;
    $prefijo = 'NOT';
    $folioNota =  strtoupper($prefijo . "-" . substr(str_shuffle($caracteres_permitidos), 0, $longitud));
    ?>
    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/cajaAjax.php" method="POST" autocomplete="off">

        <input type="hidden" name="modulo_caja" value="registrar">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Folio Nota <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="folio_nota" required value="<?= $folioNota ?>">
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Título Nota <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="titulo_nota" required>
                </div>
            </div>

        </div>
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Fecha Publicación <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="datetime-local" name="fecha_publicacion" required>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Fecha Expiracion <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="datetime-local" name="fecha_expiracion" required>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Codigo Nota </label>
                    <div class="showQRCode"></div>

                </div>
            </div>

        </div>
        <p class="has-text-centered">
            <button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
            <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
        </p>
        <p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
    </form>
</div>