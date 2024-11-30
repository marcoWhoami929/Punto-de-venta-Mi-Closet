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
                    <button class="button is-info" type="button" onclick="listarSesiones()">Buscar</button>
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

    <div class="columns is-multiline">
        <div class="column ">
            <div class="card">
                <div class="column div-sesiones">

                </div>

            </div>
        </div>
    </div>
</div>