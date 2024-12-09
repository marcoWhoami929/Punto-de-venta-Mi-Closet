<div class="container is-fluid mb-6">
    <h1 class="title">Historial</h1>
    <h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Detalle Movimientos Cliente</h2>
</div>
<div class="container is-fluid pb-2 pt-2">
    <?php

    include "./app/views/inc/btn_back.php";

    ?>
    <div class="columns">
        <div class="column is-half">
            <label></label><br>
            <div class="field is-grouped">
                <input type="hidden" id="pagina" value="<?= $url[2] ?>">
                <input type="hidden" id="url" value="<?= "historyDetail/" . $url[1] ?>">
                <p class="control is-expanded">
                    <input class="input is-rounded" placeholder="¿Qué estas buscando?" type="text" id="busqueda" onkeyup="listarHistorialCliente('<?= $url[1] ?>')">
                </p>
                <p class="control">
                    <button class="button is-info" type="button" onclick="listarHistorialCliente('<?= $url[1] ?>')">Actualizar</button>
                </p>
            </div>
        </div>
        <div class="column">
            <label>Mostrar</label><br>
            <div class="select">
                <select id="per_page" onchange="listarHistorialCliente('<?= $url[1] ?>')">

                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Ordenar por</label><br>
            <div class="select">
                <select id="campoOrden" onchange="listarHistorialCliente('<?= $url[1] ?>')">
                    <option value="vent.id_venta">Id</option>
                    <option value="vent.codigo">Codigo Venta</option>
                    <option value="vent.codigo_nota">Codigo Nota</option>
                    <option value="vent.fecha">Fecha venta</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Orden</label><br>
            <div class="select">
                <select id="orden" onchange="listarHistorialCliente('<?= $url[1] ?>')">
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>

                </select>
            </div>
        </div>

    </div>
</div>
<div class="container is-fluid pb-2 pt-2">

    <div class="columns is-multiline">
        <div class="column ">
            <div class="column div-historial-cliente">

            </div>
        </div>
    </div>
</div>