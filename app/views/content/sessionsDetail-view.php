<div class="container is-fluid mb-6">
    <h1 class="title">Sesion <?= $url[1] ?></h1>
    <h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Detalle Movimientos Sesion</h2>
</div>
<div class="container is-fluid pb-2 pt-2">
    <?php

    include "./app/views/inc/btn_back.php";

    ?>
    <div class="columns">
        <div class="column is-two-fifths">
            <label></label><br>
            <div class="field is-grouped">
                <input type="hidden" id="pagina" value="<?= $url[2] ?>">
                <input type="hidden" id="url" value="<?= "kardexDetail/" . $url[1] ?>">
                <p class="control is-expanded">
                    <input class="input is-rounded" placeholder="¿Qué estas buscando?" type="text" id="busqueda" onkeyup="listarDetalleSession('<?= $url[1] ?>')">
                </p>
                <p class="control">
                    <button class="button is-info" type="button" onclick="listarDetalleSession('<?= $url[1] ?>')">Buscar</button>
                </p>
            </div>
        </div>
        <div class="column">
            <label>Mostrar</label><br>
            <div class="select">
                <select id="per_page" onchange="listarDetalleSession('<?= $url[1] ?>')">

                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Ordenar por</label><br>
            <div class="select">
                <select id="campoOrden" onchange="listarDetalleSession('<?= $url[1] ?>')">
                    <option value="id_movimiento">Id</option>
                    <option value="tipo_movimiento">Movimiento</option>
                    <option value="fecha">Fecha</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Orden</label><br>
            <div class="select">
                <select id="orden" onchange="listarDetalleSession('<?= $url[1] ?>')">
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Filtro Tipo Movimiento</label><br>
            <div class="select">
                <select id="tipo_movimiento" onchange="listarDetalleSession('<?= $url[1] ?>')">

                    <option value="">Todos</option>
                    <option value="ingreso">Ingreso</option>
                    <option value="egreso">Egreso</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Filtro Forma Pago</label><br>
            <div class="select">
                <select id="metodo_pago" onchange="listarDetalleSession('<?= $url[1] ?>')">

                    <option value="">Todos</option>
                    <option value="1">Efectivo</option>
                    <option value="2">Transferencia Electrónica</option>
                    <option value="3">Tarjeta de Crédito</option>
                    <option value="4">Tarjeta de Débito</option>

                </select>
            </div>
        </div>
    </div>
</div>
<div class="container is-fluid pb-2 pt-2">

    <div class="columns is-multiline">
        <div class="column ">
            <div class="column div-movimientos-session">

            </div>
        </div>
    </div>
</div>