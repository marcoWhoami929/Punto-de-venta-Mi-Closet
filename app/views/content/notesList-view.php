<div class="container is-fluid mb-6">
    <h1 class="title">Notas</h1>
    <h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de Notas</h2>
</div>
<div class="container is-fluid pb-6">

    <div class="form-rest mb-6 mt-6"></div>

    <?php

    use app\controllers\notesController;

    $insVenta = new notesController();

    echo $insVenta->listarNotasControlador($url[1], 15, $url[0], "");

    ?>
</div>