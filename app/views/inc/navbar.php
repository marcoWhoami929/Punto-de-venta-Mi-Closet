<div class="full-width navBar">
    <div class="full-width navBar-options">
        <i class="fas fa-exchange-alt fa-fw" id="btn-menu"></i>
        <nav class="navBar-options-list">
            <ul class="list-unstyle">

                <li class="text-condensedLight noLink">
                    <?php

                    if (isset($_SESSION["sesion_caja"])) {
                        echo '<button type="button" class="button is-danger is-rounded is-medium js-modal-trigger"  data-target="modal-caja"><i class="fas fa-cash-register"></i></button>';
                    }
                    ?>

                </li>
                <li class="text-condensedLight noLink">

                </li>
                <li class="text-condensedLight noLink">
                    <a class="btn-exit" href="<?php echo APP_URL . "logOut/"; ?>">

                        <button type="button" class="button is-success is-rounded is-medium"><i class="fas fa-power-off"></i></button>
                    </a>
                </li>
                <li class="text-condensedLight noLink">
                    <small><?php echo $_SESSION['usuario']; ?></small>
                </li>
                <li class="noLink">
                    <?php
                    if (is_file("./app/views/fotos/" . $_SESSION['foto'])) {
                        echo '<img class="is-rounded img-responsive" src="' . APP_URL . 'app/views/fotos/' . $_SESSION['foto'] . '">';
                    } else {
                        echo '<img class="is-rounded img-responsive" src="' . APP_URL . 'app/views/fotos/default.png">';
                    }
                    ?>
                </li>
            </ul>
        </nav>
    </div>
</div>
<div class="modal fade" id="modal-caja" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-background"></div>
    <div class="modal-card" style="width:80%">
        <header class="modal-card-head" style="background:#B99654">
            <p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cash-register"></i> &nbsp; Acciones Caja</p>
            <button class="delete " aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="columns">
                <div class="column">
                    <div class="field mt-2 mb-2">
                        <label class="label">Elegir Acción</label>
                        <div class="control has-text-centered">
                            <div class="select is-primary  is-rounded is-large">
                                <select onchange="eleccionAccionCaja('<?= $_SESSION['sesion_caja'] ?>')" id="accion_caja">
                                    <option value="entrada">Entrada Efectivo</option>
                                    <option value="cierre">Cierre de Caja</option>
                                    <option value="salida">Salida Efectivo</option>


                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="columns  mb-6" id="div-caja-cierre" style="display:none">
                <div class="column">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">Cierre de Caja Registradora:</label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field" id="field-ordenes">
                            </div>
                        </div>

                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">Métodos de Pago:</label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Esperado:</label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Contado:</label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label">Diferencia:</label>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" style="color:#b4afaf">Saldo Inicial:</label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#b4afaf">Efectivo:</label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#b4afaf">Transferencia:</label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#b4afaf">Tarjeta Crédito:</label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#b4afaf">Tarjeta Débito:</label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#b4afaf">Salidas Efectivo:</label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#b4afaf">Entradas Efectivo:</label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654;font-size:25px">Total Caja:</label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label" style="color:#B99654"><span id="field-saldo-inicial" style="font-weight:bold;color:#000000"></span></label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654"><span id="field-efectivo" style="font-weight:bold;color:#000000"></span></label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654"><span id="field-transferencia" style="font-weight:bold;color:#000000"></span></label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654"><span id="field-td" style="font-weight:bold;color:#000000"></span></label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654"><span id="field-tc" style="font-weight:bold;color:#000000"></span></label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654"><span id="field-salida-efectivo" style="font-weight:bold;color:#000000"></span></label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654"><span id="field-entrada-efectivo" style="font-weight:bold;color:#000000"></span></label>
                            </div>
                            <div class="field">
                                <label class="label" style="color:#B99654;font-size:25px">$ <span id="field-total-caja" style="font-weight:bold;color:#B99654;font-size:25px"></span></label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">

                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-small" type="number" placeholder="0.00" style="font-size:25px;font-weight:bold" onkeyup="calcularDiferenciaCaja(this)" value="0.00" id="saldo_final_corte" />
                                    <span class="icon is-large is-left" style="margin-top:10px">
                                        <i class="fas fa-dollar-sign fa-3x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Nota de Cierre:</label>
                                <textarea class="textarea is-large" type="text" placeholder="Ingresar alguna observación si hay alguna diferencia en el corte" style="font-size:12px;font-weight:bold" id="observaciones_corte"></textarea>
                            </div>

                            <div class="field  pt-4">
                                <p class="">
                                    <button type="button" class="button is-success button-lg" onclick="cerrarCaja('<?= $_SESSION['sesion_caja'] ?>')"><i class="fas fas fa-cash-register"></i> &nbsp; Cerrar Caja</button>

                                </p>
                            </div>
                            <div class="field  pt-4">
                                <p class="">

                                    <button type="button" class="button is-info button-lg js-modal-trigger" data-target="modal-denominaciones-caja"><i class="fas fa-money-bill-wave"></i> &nbsp; Capturar Denominaciones</button>
                                </p>
                            </div>

                        </div>
                        <div class="column">
                            <div class="field">

                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-small" type="number" placeholder="0.00" style="font-size:25px;font-weight:bold" id="field-diferencia-caja" value="0.00" disabled />
                                    <span class="icon is-large is-left" style="margin-top:10px">
                                        <i class="fas fa-dollar-sign fa-3x"></i>
                                    </span>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="columns  mb-6" id="div-caja-salida" style="display:none">
                <div class="column">
                    <div class="field">
                        <label class="label">Capturar Efectivo:</label>
                        <div class="control has-icons-left">
                            <input class="input is-large" type="text" placeholder="0.00" style="font-size:50px;font-weight:bold" id="salida_efectivo" value="0.00" />
                            <span class="icon is-medium is-left " style="margin-top:20px">
                                <i class="fas fa-dollar-sign fa-3x"></i>
                            </span>

                        </div>
                        <label class="label">Motivo:</label>
                        <textarea class="textarea is-large" type="text" placeholder="Motivo" style="font-size:12px;font-weight:bold" id="salida_motivo"></textarea>

                        <p class="has-text-centered mt-4">
                            <button type="button" class="button is-success button-lg" onclick="salidaEfectivo('<?= $_SESSION['sesion_caja'] ?>')"><i class="fas fa-money-check-alt"></i> &nbsp; Confirmar Salida</button>
                        </p>


                    </div>
                </div>

            </div>


            <div class="columns  mb-6" id="div-caja-entrada">
                <div class="column">
                    <div class="field">
                        <label class="label">Capturar Efectivo:</label>
                        <div class="control has-icons-left">
                            <input class="input is-large" type="text" placeholder="0.00" style="font-size:50px;font-weight:bold" id="entrada_efectivo" value="0.00" />
                            <span class="icon is-medium is-left " style="margin-top:20px">
                                <i class="fas fa-dollar-sign fa-3x"></i>
                            </span>

                        </div>
                        <label class="label">Motivo:</label>
                        <textarea class="textarea is-large" type="text" placeholder="Motivo" style="font-size:12px;font-weight:bold" id="entrada_motivo"></textarea>

                        <p class="has-text-centered mt-4">
                            <button type="button" class="button is-success button-lg" onclick="entradaEfectivo('<?= $_SESSION['sesion_caja'] ?>')"><i class="fas fa-check-double"></i> &nbsp; Confirmar Entrada</button>
                        </p>


                    </div>
                </div>

            </div>

        </section>
    </div>
</div>
<div class="modal fade is-large" id="modal-entrada-inventario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-background"></div>
    <div class="modal-card ">
        <header class="modal-card-head" style="background:#B99654">
            <p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cash-register"></i> &nbsp; Abastecer Inventario</p>
            <button class="delete" aria-label="close" id="btn-close-pago"></button>
        </header>
        <section class="modal-card-body">

            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Unidades a Ingresar:</label>
                        <div class="control has-icons-left">
                            <input type="hidden" id="id_producto_entrada_inventario">
                            <input class="input is-large" type="text" style="font-size:50px;font-weight:bold" id="unidades_entrada_inventario" />
                            <span class="icon is-medium is-left" style="margin-top:20px">
                                <i class="fas fa-hashtag fa-3x"></i>
                            </span>

                        </div>
                    </div>
                </div>

            </div>

            <div class="columns  mb-6">
                <div class="column">
                    <div class="field">
                        <label class="label">Observaciones</label>
                        <div class="control">

                            <textarea class="textarea is-large" type="text" placeholder="Capturar descripción del movimiento" style="font-size:12px;font-weight:bold" id="descripcion_entrada_inventario"></textarea>


                        </div>
                    </div>
                </div>

            </div>
            <div class="columns">
                <div class="column">
                    <p class="has-text-centered">
                        <button type="button" class="button is-success button-lg" onclick="entradaInventario(0)"><i class="fas fa-check-double"></i> &nbsp; Confirmar Entrada</button>
                    </p>
                </div>
            </div>



        </section>
    </div>
</div>
<div class="modal fade is-large" id="modal-salida-inventario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-background"></div>
    <div class="modal-card ">
        <header class="modal-card-head" style="background:#B99654">
            <p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cash-register"></i> &nbsp; Salida de Inventario</p>
            <button class="delete" aria-label="close" id="btn-close-pago"></button>
        </header>
        <section class="modal-card-body">

            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Unidades a Sacar:</label>
                        <div class="control has-icons-left">
                            <input type="hidden" id="id_producto_salida_inventario">
                            <input class="input is-large" type="text" style="font-size:50px;font-weight:bold" id="unidades_salida_inventario" />
                            <span class="icon is-medium is-left" style="margin-top:20px">
                                <i class="fas fa-hashtag fa-3x"></i>
                            </span>

                        </div>
                    </div>
                </div>

            </div>

            <div class="columns  mb-6">
                <div class="column">
                    <div class="field">
                        <label class="label">Observaciones</label>
                        <div class="control">

                            <textarea class="textarea is-large" type="text" placeholder="Capturar descripción del movimiento" style="font-size:12px;font-weight:bold" id="descripcion_salida_inventario"></textarea>


                        </div>
                    </div>
                </div>

            </div>
            <div class="columns">
                <div class="column">
                    <p class="has-text-centered">
                        <button type="button" class="button is-success button-lg" onclick="salidaInventario(0)"><i class="fas fa-check-double"></i> &nbsp; Confirmar Salida</button>
                    </p>
                </div>
            </div>



        </section>
    </div>
</div>

<div class="modal fade" id="modal-denominaciones-caja" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head" style="background:#B99654">
            <p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cash-register"></i> &nbsp; Denominaciones</p>
            <div class="column" style="background:#B99654">
                <div class="field">

                    <div class="control has-icons-left">
                        <input class="input is-large" type="number" id="total_denominaciones_caja" placeholder="0.00" style="font-size:22px;font-weight:bold;background:#B99654;color:#ffffff;border:none" value="0.00" disabled />
                        <span class="icon is-large is-left">
                            <i class="fas fa-dollar-sign fa-1x" style="color:#ffffff"></i>
                        </span>

                    </div>

                </div>
            </div>
            <button class="delete " aria-label="close"></button>
        </header>
        <section class="modal-card-body">


            <div class="columns  mb-6">
                <div class="column">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">*Capturar denominaciones de efectivo en caja.</label>
                            </div>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column" style="background:#B99654">
                            <div class="field">
                                <label class="label" style="color:#ffffff">Denominacíon</label>
                            </div>
                        </div>
                        <div class="column" style="background:#B99654">
                            <div class="field">
                                <label class="label" style="color:#ffffff">Cantidad</label>
                            </div>
                        </div>
                        <div class="column" style="background:#B99654">
                            <div class="field">
                                <label class="label" style="color:#ffffff">Total:</label>
                            </div>
                        </div>

                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="1000" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="500" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="200" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="100" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="50" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="20" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="10" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="5" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="2" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="1" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="0.50" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="0.20" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" value="0.10" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>

                            <div class="field">
                                <label class="label" style="color:#B99654;font-size:25px">Total Caja:</label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn1" onchange="calcularDenominacion('1','1000')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn2" onchange="calcularDenominacion('2','500')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn3" onchange="calcularDenominacion('3','200')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn4" onchange="calcularDenominacion('4','100')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn5" onchange="calcularDenominacion('5','50')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn6" onchange="calcularDenominacion('6','20')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn7" onchange="calcularDenominacion('7','10')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn8" onchange="calcularDenominacion('8','5')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn9" onchange="calcularDenominacion('9','2')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn10" onchange="calcularDenominacion('10','1')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn11" onchange="calcularDenominacion('11','0.50')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn12" onchange="calcularDenominacion('12','0.20')" value="0">
                            </div>
                            <div class="field">
                                <input type="text" class="input is-medium has-text-centered" id="dn13" onchange="calcularDenominacion('13','0.10')" value="0">
                            </div>

                        </div>

                        <div class="column">

                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_1" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_2" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_3" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_4" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_5" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_6" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_7" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_8" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_9" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_10" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_11" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_12" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-large input-denominacion" type="number" placeholder="0.00" style="font-size:20px;font-weight:bold" id="dif_dn_13" value="0.00" disabled />
                                    <span class="icon is-large is-left">
                                        <i class="fas fa-dollar-sign fa-1x"></i>
                                    </span>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>


        </section>
    </div>
</div>