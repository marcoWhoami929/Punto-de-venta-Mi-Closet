<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\cashierController;
use app\controllers\categoryController;
use app\controllers\clientController;
use app\controllers\notesController;
use app\controllers\productController;
use app\controllers\saleController;
use app\controllers\sessionsController;

if (isset($_POST['modulo_pos'])) {
    if ($_POST['modulo_pos'] == "listarCajas") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "url" => $_POST["url"],
        );

        $cashier = new cashierController();

        echo $cashier->listarCajaControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarProductos") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "url" => $_POST["url"],
        );

        $product = new productController();

        echo $product->listarProductosControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarSesiones") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "estatus" => $_POST["estatus"],
            "url" => $_POST["url"],
        );

        $sessions = new sessionsController();

        echo $sessions->listarSesionesControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarClientes") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "estatus" => $_POST["estatus"],
            "url" => $_POST["url"],
        );

        $clients = new clientController();

        echo $clients->listarClientesControlador($datos);
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}
