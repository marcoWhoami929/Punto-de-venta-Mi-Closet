<div class="container is-fluid mb-6">
    <h1 class="title">Estatus</h1>
    <h2 class="subtitle"><i class="fas fa-spinner fa-fw"></i> &nbsp; Estatus Ventas</h2>
</div>
<div class="container is-fluid pb-2 pt-2">

    <div class="columns ">
        <div class="column is-half">
            <label></label><br>
            <div class="field is-grouped">
                <input type="hidden" id="pagina" value="<?= $url[1] ?>">
                <input type="hidden" id="url" value="<?= $url[0] ?>">
                <p class="control is-expanded">
                    <input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué venta estás buscando?" onkeyup="listarEstatusVentas()">
                </p>
                <p class="control">
                    <button class="button is-info" type="button" onclick="listarEstatusVentas()">Actualizar</button>
                </p>
            </div>
        </div>
        <div class="column">
            <label>Mostrar</label><br>
            <div class="select">
                <select id="per_page" onchange="listarEstatusVentas()">
                    <option value="15">15</option>
                    <option value="50">50</option>
                    <option value="100">100</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Ordenar por</label><br>
            <div class="select">
                <select id="campoOrden" onchange="listarEstatusVentas()">
                    <option value="id_venta">Id</option>
                    <option value="fecha_venta">Fecha Venta</option>
                    <option value="estatus_pago">Estatus pago</option>

                </select>
            </div>
        </div>
        <div class="column">
            <label>Orden</label><br>
            <div class="select">
                <select id="orden" onchange="listarEstatusVentas()">
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
        <div class="column div-estatus-ventas">

        </div>
    </div>
</div>
<!-- main-panel ends -->
<style>
    .steps .step {
        display: block;
        width: 100%;
        margin-bottom: 35px;
        text-align: center
    }

    .steps .step .step-icon-wrap {
        display: block;
        position: relative;
        width: 100%;
        height: 80px;
        text-align: center
    }

    .steps .step .step-icon-wrap::before,
    .steps .step .step-icon-wrap::after {
        display: block;
        position: absolute;
        top: 50%;
        width: 50%;
        height: 13px;
        margin-top: -1px;
        background-color: #B99654;
        content: '';
        z-index: 1
    }

    .steps .step .step-icon-wrap::before {
        left: 0
    }

    .steps .step .step-icon-wrap::after {
        right: 0
    }

    .steps .step .step-icon {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
        border: 1px solid #e1e7ec;
        border-radius: 50%;
        background-color: #f5f5f5;
        color: #374250;
        font-size: 38px;
        line-height: 81px;
        z-index: 5
    }

    .steps .step .step-title {
        margin-top: 16px;
        margin-bottom: 0;
        color: #606975;
        font-size: 14px;
        font-weight: 500
    }

    .steps .step:first-child .step-icon-wrap::before {
        display: none
    }

    .steps .step:last-child .step-icon-wrap::after {
        display: none
    }

    .steps .step.completed .step-icon-wrap::before,
    .steps .step.completed .step-icon-wrap::after {
        background-color: #0da9ef
    }

    .steps .step.completed .step-icon {
        border-color: #0da9ef;
        background-color: #0da9ef;
        color: #fff
    }

    @media (max-width: 576px) {

        .flex-sm-nowrap .step .step-icon-wrap::before,
        .flex-sm-nowrap .step .step-icon-wrap::after {}
    }

    @media (max-width: 768px) {

        .flex-md-nowrap .step .step-icon-wrap::before,
        .flex-md-nowrap .step .step-icon-wrap::after {}
    }

    @media (max-width: 991px) {

        .flex-lg-nowrap .step .step-icon-wrap::before,
        .flex-lg-nowrap .step .step-icon-wrap::after {}
    }

    @media (max-width: 1200px) {

        .flex-xl-nowrap .step .step-icon-wrap::before,
        .flex-xl-nowrap .step .step-icon-wrap::after {}
    }

    .bg-faded,
    .bg-secondary {
        background-color: #f5f5f5 !important;
    }

    /***RIBBON CSS */
    .ribbon {
        --f: 15px;
        /* control the folded part */
        height: 50px;
        position: absolute;
        top: 0;
        color: #fff;
        padding: .6em 1.8em;
        background: var(--c, #45ADA8);
        border-bottom: var(--f) solid #0007;
        clip-path: polygon(100% calc(100% - var(--f)), 100% 100%, calc(100% - var(--f)) calc(100% - var(--f)), var(--f) calc(100% - var(--f)), 0 100%, 0 calc(100% - var(--f)), 999px calc(100% - var(--f) - 999px), calc(100% - 999px) calc(100% - var(--f) - 999px))
    }

    .right {
        right: 0;
        transform: translate(calc((1 - cos(45deg))*100%), -100%) rotate(45deg);
        transform-origin: 0% 100%;
    }
</style>