<div class="container is-fluid mb-6">
    <h1 class="title">Notas</h1>
    <h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Información de nota</h2>
</div>

<div class="container is-fluid pb-6">
    <?php

    include "./app/views/inc/btn_back.php";

    use app\models\mainModel;

    $mainModel = new mainModel();




    $code = $insLogin->limpiarCadena($url[1]);

    //$datos = $insLogin->seleccionarDatos("Normal", "venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario INNER JOIN caja ON venta.id_caja=caja.id_caja WHERE (codigo='" . $code . "')", "*", 0);
    $datos = $insLogin->seleccionarDatos("Normal", "notas WHERE (codigo='$code')", "*", 0);

    if ($datos->rowCount() == 1) {
        $datos_nota = $datos->fetch();

        $fecha = $datos_nota["fecha"];
        $fecha = explode(' ', $fecha);

        if ($datos_nota["estatus"] == 1) {
            $estatus = "Activa";
        } else {
            $estatus = "Cancelada";
        }

    ?>

        <div class="columns ">
            <p class="has-text-centered full-width">
                <img src="<?php echo ROUTE_QR . $datos_nota['codigo'] . ".png" ?>">
            </p>
        </div>
        <h2 class="title has-text-centered">Datos de la nota <?php echo " (" . $code . ")"; ?></h2>

        <form>
            <input type="hidden" name="modulo_notas" value="actualizar_nota">
            <input type="hidden" name="id_nota" value="<?php echo $datos_nota['id_nota']; ?>">
            <div class="columns pb-6">
                <div class="column">

                    <div class="full-width sale-details text-condensedLight">
                        <div class="has-text-weight-bold">Fecha</div>
                        <span class="has-text-link"><?php echo date("d-m-Y", strtotime($fecha[0])) . " " . $fecha[1]; ?></span>
                    </div>

                    <div class="full-width sale-details text-condensedLight">
                        <div class="has-text-weight-bold">Nro. de Nota</div>
                        <span class="has-text-link"><?php echo $datos_nota['id_nota']; ?></span>
                    </div>

                    <div class="full-width sale-details text-condensedLight">
                        <div class="has-text-weight-bold">Código de Nota</div>
                        <span class="has-text-link"><?php echo $datos_nota['codigo']; ?></span>
                    </div>

                </div>

                <div class="column">


                    <div class="full-width sale-details text-condensedLight">
                        <div class="has-text-weight-bold">Titulo</div>

                        <input class="input input-note" type="text" name="titulo_nota" id="titulo_nota" required value="<?= $datos_nota['titulo_nota']  ?>">
                    </div>

                    <div class="full-width sale-details text-condensedLight">
                        <div class="has-text-weight-bold">Fecha Publicación</div>

                        <input class="input input-note" type="datetime-local" name="fecha_publicacion" id="fecha_publicacion" required value="<?= $datos_nota['fecha_publicacion'] ?>">
                    </div>

                    <div class="full-width sale-details text-condensedLight">
                        <div class="has-text-weight-bold">Fecha Expiración</div>

                        <input class="input input-note" type="datetime-local" name="fecha_expiracion" id="fecha_expiracion" required value="<?= $datos_nota['fecha_expiracion'] ?>">
                    </div>

                </div>

                <div class="column">
                    <div class="full-width sale-details text-condensedLight">
                        <div class="has-text-weight-bold">% Descuento</div>

                        <input class="input input-note" type="number" name="porc_descuento_nota" id="porc_descuento_nota" required value="<?= $datos_nota['porc_descuento']  ?>">
                    </div>
                    <div class="full-width sale-details text-condensedLight">

                        <div class="has-text-weight-bold">Estatus</div>
                        <span class="has-text-link"><button class="button is-success is-rounded"><?php echo $estatus ?></button></span>
                    </div>

                </div>

            </div>
            <div class="columns ">
                <p class="has-text-centered full-width">

                    <button type="button" class="button is-info is-rounded" onclick="actualizarNota('<?= $datos_nota['id_nota']; ?>','<?= $datos_nota['fecha_publicacion']; ?>')"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
                </p>
            </div>
        </form>

        <div class="columns pb-6">
            <div class="column">
                <div class="table-container">
                    <?php

                    $detalle_nota = $insLogin->seleccionarDatos("Normal", "productos_notas as prod_not INNER JOIN producto as prod ON prod_not.id_producto = prod.id_producto WHERE codigo_nota ='" . $datos_nota['codigo'] . "'", "prod_not.*,prod.foto", 0);


                    $total = $detalle_nota->rowCount();
                    $registros = 5;

                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $numeroPaginas = ceil($total / $registros);

                    $url = $url[0];

                    if ($total >= 1 && $pagina <= $numeroPaginas) {
                        if ($detalle_nota->rowCount() >= 1) {


                            $detalle_nota = $detalle_nota->fetchAll();
                            $tabla = "";
                            $contador = $inicio + 1;
                            $pag_inicio = $inicio + 1;

                            foreach ($detalle_nota as $detalle) {
                                $tabla .= '
                            <article class="media pb-3 pt-3">
                                <figure class="media-left">
                                    <p class="image is-64x64">';
                                if (is_file("./app/views/productos/" . $detalle['foto'])) {
                                    $tabla .= '<img src="' . APP_URL . 'app/views/productos/' . $detalle['foto'] . '">';
                                } else {
                                    $tabla .= '<img src="' . APP_URL . 'app/views/productos/default.png">';
                                }
                                $tabla .= '</p>
                                </figure>
                                <div class="media-content">
                                    <div class="content">
                                        <p>
                                            <strong>' . $contador . ' - ' . $detalle['descripcion'] . '</strong><br>
                                            <strong>CODIGO:</strong> ' . $detalle['codigo'] . ', 
                                            <strong>PRECIO:</strong> $' . $detalle['precio_venta'] . ', 
                                            <strong>LIMITE:</strong> ' . $detalle['limite_nota'] . ', 
                                    
                                        </p>
                                         <div class="columns">
                                            <div class="column">
                                            <strong>TALLAS:</strong>
                                            <input class="input" type="tags" id="tallas" name="tallas" value="' . $detalle['tallas'] . '" placeholder="..." disabled>
                                            </div>
                                             <div class="column">
                                            <strong>COLORES:</strong>
                                            <input class="input"  type="tags" id="colores" name="colores" value="' . $detalle['colores'] . '" placeholder="...">
                                            </div>
                                            
                                        </div>
                                    </div>
                                   
                                    <div class="has-text-right">
                                     
                                    </div>
                                </div>
                            </article>
                            
                            
                            <hr>
                            ';
                                $contador++;
                    ?>

                            <?php

                            }
                            $pag_final = $contador - 1;
                            ?>

                        <?php
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
                        ?>

                    <?php }
                        ### Paginacion ###
                        if ($total > 0 && $pagina <= $numeroPaginas) {
                            $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

                            $tabla .= $mainModel->paginadorTablas($pagina, $numeroPaginas, $url, 7);
                        }
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
                    echo $tabla;

                    ?>
                </div>
            </div>
        </div>


    <?php
        include "./app/views/inc/print_invoice_script.php";
    } else {
        include "./app/views/inc/error_alert.php";
    }
    ?>
</div>
<script>
    bulmaTagsinput.attach();
</script>