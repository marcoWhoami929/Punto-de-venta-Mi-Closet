<div class="container is-fluid mb-6">
    <h1 class="title">Sesiones</h1>
    <h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de sesiones de caja</h2>
</div>
<div class="container is-fluid pb-2 pt-2">

    <div class="columns">
        <div class="column is-half">
            <label></label><br>
            <div class="field is-grouped">
                <input type="hidden" id="pagina" value="<?= $url[1] ?>">
                <input type="hidden" id="url" value="<?= $url[0] ?>">
                <p class="control is-expanded">
                    <input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué sesión estás buscando?" onkeyup="listarSesiones()">
                </p>
                <p class="control">
                    <button class="button is-info" type="button" onclick="listarSesiones()">Actualizar</button>
                </p>
            </div>
        </div>
        <div class="column">
            <label>Mostrar</label><br>
            <div class="select">
                <select id="per_page" onchange="listarSesiones()">
                    <option value="15">15</option>
                    <option value="50">50</option>
                    <option value="100">100</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Ordenar por</label><br>
            <div class="select">
                <select id="campoOrden" onchange="listarSesiones()">
                    <option value="estado">Estatus</option>
                    <option value="codigo_sesion">Codigo</option>
                    <option value="fecha_apertura">Fecha</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Orden</label><br>
            <div class="select">
                <select id="orden" onchange="listarSesiones()">
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>

                </select>
            </div>
        </div>
    </div>
</div>
<div class="container is-fluid pb-2 pt-2 ">
    <div class="columns">
        <div class="column div-sesiones">

        </div>
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


            <div class="columns  mb-6">
                <div class="column">
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">Detalle Corte Caja:</label>
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
                                    <button type="button" class="button is-success button-lg" onclick="cerrarCaja('<?= $_SESSION['sesion_caja'] ?>')"><i class="fas fa-money-check-alt"></i> &nbsp; Cerrar Caja</button>
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


        </section>
    </div>
</div>