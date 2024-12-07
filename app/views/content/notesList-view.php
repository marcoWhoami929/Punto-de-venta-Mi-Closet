<div class="container is-fluid mb-6">
    <h1 class="title">Notas</h1>
    <h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de Notas</h2>
</div>
<div class="container is-fluid pb-2 pt-2">
    <button class="button is-danger is-light js-modal-trigger" data-target="modal-pago-venta" id="btn-modal-pago" style="display:none">Sin Pagar</button>
    <div class="columns ">
        <div class="column is-half">
            <label></label><br>
            <div class="field is-grouped">
                <input type="hidden" id="pagina" value="<?= $url[1] ?>">
                <input type="hidden" id="url" value="<?= $url[0] ?>">
                <p class="control is-expanded">
                    <input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué nota estás buscando?" onkeyup="listarNotas()">
                </p>
                <p class="control">
                    <button class="button is-info" type="button" onclick="generarReporte('notas')"><i class="fas fa-file-excel"></i></button>
                </p>
            </div>
        </div>
        <div class="column">
            <label>Mostrar</label><br>
            <div class="select">
                <select id="per_page" onchange="listarNotas()">
                    <option value="15">15</option>
                    <option value="50">50</option>
                    <option value="100">100</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Ordenar por</label><br>
            <div class="select">
                <select id="campoOrden" onchange="listarNotas()">
                    <option value="codigo">Codigo</option>
                    <option value="fecha_publicacion">Fecha Publilcacion</option>
                    <option value="estatus">Estatus</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Orden</label><br>
            <div class="select">
                <select id="orden" onchange="listarNotas()">
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>

                </select>
            </div>
        </div>

    </div>
    <div class="columns">

    </div>
</div>
<div class="container is-fluid pb-2 pt-2 ">
    <div class="columns">
        <div class="column div-notas">

        </div>
    </div>
</div>