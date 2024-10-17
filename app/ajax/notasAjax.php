<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\notesController;

if (isset($_POST['modulo_notas'])) {

    $insNotes = new notesController();

    if ($_POST['modulo_notas'] == "registrar") {
        echo $insEmpresa->registrarEmpresaControlador();
    }

    if ($_POST['modulo_notas'] == "generarQr") {
        generarQrNota();
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}

function generarQrNota()
{
    if (isset($_POST) && !empty($_POST)) {
        $nota = $_POST["route"] . "ajax/codes/" . $_POST["folio"] . '.png';
        if (file_exists($nota)) {
        } else {
            include('../phpqrcode/qrlib.php');
            $codesDir = "codes/";
            $codeFile = $_POST['folio'] . '.png';
            $texto = $_POST['route'] . "notas/" . $_POST['folio'];
            QRcode::png($texto, $codesDir . $codeFile, $_POST['ecc'], $_POST['size']);

            echo '<img class="img-thumbnail" src="../app/ajax/' . $codesDir . $codeFile . '" />';
        }
    }
}
