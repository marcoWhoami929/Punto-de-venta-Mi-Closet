<?php

use app\models\mainModel;

$mainModel = new mainModel();



?>
<div class="container is-fluid mb-6">
    <h1 class="title">Notas</h1>
    <h2 class="subtitle"> <i class="fas fa-clipboard-list"></i> &nbsp; Nueva Nota</h2>
</div>

<div class="container pb-6 pt-6">
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
        <input type="hidden" name="route_qr" id="route_qr" value="<?php echo APP_URL . "notas/" . $folioNota ?>">
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

                        <input class="input" type="number" name="porc_descuento_nota" id="porc_descuento_nota" onchange="datosNota()" value="<?= (isset($_SESSION['porc_descuento_nota']) ? $_SESSION['porc_descuento_nota'] : '0')  ?>" maxlength="25">
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

        <!--
        <p class="has-text-centered">
            <button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
            <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
        </p>
        <p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
-->
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
    ?>
    <form class="pt-6 pb-6" id="sale-barcode-form" autocomplete="off">
        <div class="columns">
            <div class="column is-one-quarter">
                <button type="button" class="button is-link is-light js-modal-trigger" data-target="modal-js-product"><i class="fas fa-search"></i> &nbsp; Buscar producto</button>
            </div>
            <div class="column">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input" type="text" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70" autofocus="autofocus" placeholder="Código de barras" id="sale-barcode-input">
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
    
    <div class="table-container">
        <?php
        if(isset($_SESSION['datos_producto_nota'])){

       
        $total = count($_SESSION['datos_producto_nota']);
        $registros = 5;

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $numeroPaginas = ceil($total / $registros);

        $url = $url[0];

        if ($total >= 1 && $pagina <= $numeroPaginas) {
            $tabla = "";
            $contador = $inicio + 1;
            $pag_inicio = $inicio + 1;
            foreach ($_SESSION['datos_producto_nota'] as $productos) {
                $tabla .= '
		            <article class="media pb-3 pt-3">
		                <figure class="media-left">
		                    <p class="image is-64x64">';
                if (is_file("./app/views/productos/" . $productos['foto'])) {
                    $tabla .= '<img src="' . APP_URL . 'app/views/productos/' . $productos['foto'] . '">';
                } else {
                    $tabla .= '<img src="' . APP_URL . 'app/views/productos/default.png">';
                }
                $tabla .= '</p>
		                </figure>
		                <div class="media-content">
		                    <div class="content">
		                        <p>
		                            <strong>' . $contador . ' - ' . $productos['descripcion'] . '</strong><br>
		                            <strong>CODIGO:</strong> ' . $productos['codigo'] . ', 
		                            <strong>PRECIO:</strong> $' . $productos['precio_venta'] . ', 
		                            <strong>LIMITE:</strong> ' . $productos['limite_nota'] . ', 
		                    
		                        </p>
                                 <div class="columns">
                                    <div class="column">
                                    <strong>TALLAS:</strong>
                                    <input class="input" type="tags" id="tallas" name="tallas" value="' . $productos['tallas'] . '" placeholder="...">
                                    </div>
                                     <div class="column">
                                    <strong>COLORES:</strong>
                                    <input class="input"  type="tags" id="colores" name="colores" value="' . $productos['colores'] . '" placeholder="...">
                                    </div>
                                    
                                </div>
		                    </div>
                           
		                    <div class="has-text-right">
		                      
		                        <form class="FormularioAjax" action="'. APP_URL.'app/ajax/notasAjax.php" method="POST" autocomplete="off">

                                                <input type="hidden" name="codigo" value="'.$productos['codigo'].'">
                                                <input type="hidden" name="modulo_notas" value="remover_producto">

                                                <button type="submit" class="button is-danger is-rounded " title="Remover producto">
                                                    <i class="fas fa-trash fa-fw"></i>
                                                </button>
                                            </form>
		                    </div>
		                </div>
		            </article>


		            <hr>
		            ';
                $contador++;
            }
            $pag_final = $contador - 1;
        } else {
            $tabla = '
            <section class="hero-body">
                 <div class="hero-body">
                     <p class="has-text-centered  pb-3">
                        
                            <i class="fas fa-gifts  fa-8x"></i>
                     </p>
                     <p class="title has-text-centered">Upps</p>
                     <p class="subtitle has-text-centered">No hay productos agregados a la nota</p>
                 </div>
             </section>
         ';
        }
        ### Paginacion ###
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

            $tabla .= $mainModel->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }
     } else{
            $tabla = '
               <section class="hero-body">
                    <div class="hero-body">
                        <p class="has-text-centered  pb-3">
       
                            <i class="fas fa-gifts  fa-8x"></i>
                        </p>
                        <p class="title has-text-centered">Upps</p>
                        <p class="subtitle has-text-centered">No hay productos agregados a la nota</p>
                    </div>
                </section>
            ';
        }

        echo $tabla;

        ?>
    </div>
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
