<div class="container is-fluid mb-6">
    <h1 class="title">Kardex Inventario</h1>
    <h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Kardex</h2>
</div>
<div class="container is-fluid pb-2 pt-2">

    <div class="columns">
        <div class="column is-half">
            <label></label><br>
            <div class="field is-grouped">
                <input type="hidden" id="pagina" value="<?= $url[1] ?>">
                <input type="hidden" id="url" value="<?= $url[0] ?>">
                <p class="control is-expanded">
                    <input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué producto estas buscando?" maxlength="30" onkeyup="listarKardex()">
                </p>
                <p class="control">
                    <button class="button is-info" type="button" onclick="listarKardex()">Actualizar</button>
                </p>
            </div>
        </div>
        <div class="column">
            <label>Filtro Stock</label><br>
            <div class="select">
                <select id="stock" onchange="listarKardex()">
                    <option value="">Todos</option>
                    <option value="0">Agotados</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Mostrar</label><br>
            <div class="select">
                <select id="per_page" onchange="listarKardex()">

                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Ordenar por</label><br>
            <div class="select">
                <select id="campoOrden" onchange="listarKardex()">
                    <option value="id_categoria">Id</option>
                    <option value="nombre">Nombre</option>
                    <option value="ubicacion">Ubicacion</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Orden</label><br>
            <div class="select">
                <select id="orden" onchange="listarKardex()">
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
            <div class="column div-kardex">

            </div>
        </div>
    </div>
</div>