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

if (isset($_POST['modulo_pos'])) {
    if ($_POST['modulo_pos'] == "listarNotas") {

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
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}