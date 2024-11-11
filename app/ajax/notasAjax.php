<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";


use app\controllers\notesController;

if (isset($_POST['modulo_notas'])) {

    $insNotes = new notesController();

    if ($_POST['modulo_notas'] == "registrar") {
        echo $insNotes->registrarNotaControlador();
    }

    if ($_POST['modulo_notas'] == "generarQr") {
        generarQrNota();
    }
    /*--------- Cargar carrito notas ---------*/
    if ($_POST['modulo_notas'] == "carrito_nota") {
        echo $insNotes->cargarCarritoNotaControlador();
    }
    /*--------- Agregar producto a nota ---------*/
    if ($_POST['modulo_notas'] == "datos_nota") {
        guardarDatosNota();
    }
    /*--------- Agregar producto a nota ---------*/
    if ($_POST['modulo_notas'] == "agregar_producto_nota") {
        echo $insNotes->agregarProductoNotaControlador();
    }
    /*--------- Remover producto de de nota ---------*/
    if ($_POST['modulo_notas'] == "remover_producto_nota") {
        echo $insNotes->removerProductoNotaControlador();
    }
    /*--------- eliminar nota ---------*/
    if ($_POST['modulo_notas'] == "eliminar_nota") {
        echo $insNotes->eliminarNotaControlador();
    }
    /*--------- actualizar nota ---------*/
    if ($_POST['modulo_notas'] == "actualizar_nota") {
        echo $insNotes->actualizarNotaControlador();
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}

function generarQrNota()
{
    if (isset($_POST) && !empty($_POST)) {
        if (isset($_SESSION["QrNota"])) {
            $codesDir = "codes/";
            $codeFile = $_POST['folio'] . '.png';
            $nota = $_POST["route"] . "ajax/codes/" . $_POST["folio"] . '.png';
            echo '<img class="img-thumbnail" src="../app/ajax/' . $codesDir . $codeFile . '" />';
        } else {
            include('../phpqrcode/qrlib.php');
            $codesDir = "codes/";
            $codeFile = $_POST['folio'] . '.png';
            $texto = $_POST['route'] . "detalleNota/" . $_POST['folio'];
            $_SESSION["QrNota"] =  $_POST['folio'];
            QRcode::png($texto, $codesDir . $codeFile, $_POST['ecc'], $_POST['size']);

            echo '<img class="img-thumbnail" src="../app/ajax/' . $codesDir . $codeFile . '" />';
        }
    }
}
function guardarDatosNota()
{
    $_SESSION["titulo_nota"] = $_POST["titulo_nota"];
    $_SESSION["porc_descuento_nota"] = $_POST["porc_descuento_nota"];
    $_SESSION["fecha_publicacion_nota"] = $_POST["fecha_publicacion_nota"];
    $_SESSION["fecha_expiracion_nota"] = $_POST["fechaExpiracion_nota"];
}
