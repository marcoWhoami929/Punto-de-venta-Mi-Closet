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
use app\controllers\userController;

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
            "estatus" => $_POST["estatus"],
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
    if ($_POST['modulo_pos'] == "listarUsuarios") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "estatus" => $_POST["estatus"],
            "url" => $_POST["url"],
        );

        $user = new userController();

        echo $user->listarUsuariosControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarVentas") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "tipo_venta" => $_POST["tipo_venta"],
            "forma_pago" => $_POST["forma_pago"],
            "estatus_pago" => $_POST["estatus_pago"],
            "tipo_entrega" => $_POST["tipo_entrega"],
            "url" => $_POST["url"],
        );

        $sales = new saleController();

        echo $sales->listarVentasControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarCategorias") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "url" => $_POST["url"],
        );

        $category = new categoryController();

        echo $category->listarCategoriasControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarKardex") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "stock" => $_POST["stock"],
            "url" => $_POST["url"],
        );

        $inventory = new productController();

        echo $inventory->listarKardexInventarioControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarDetalleKardex") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "id_producto" => $_POST["id_producto"],
            "tipo_movimiento" => $_POST["tipo_movimiento"],
            "url" => $_POST["url"],
        );

        $inventory = new productController();

        echo $inventory->listarDetalleKardexControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarDetalleSession") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "sesion" => $_POST["sesion"],
            "tipo_movimiento" => $_POST["tipo_movimiento"],
            "metodo_pago" => $_POST["metodo_pago"],
            "url" => $_POST["url"],
        );

        $session = new sessionsController();

        echo $session->listarDetalleSessionControlador($datos);
    }
    if ($_POST['modulo_pos'] == "listarPagos") {

        $datos = array(
            "busqueda" => $_POST["busqueda"],
            "campoOrden" => $_POST["campoOrden"],
            "orden" => $_POST["orden"],
            "page" => $_POST["page"],
            "per_page" => $_POST["per_page"],
            "tipo_movimiento" => $_POST["tipo_movimiento"],
            "metodo_pago" => $_POST["metodo_pago"],
            "url" => $_POST["url"],
        );

        $session = new sessionsController();

        echo $session->listarPagosControlador($datos);
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}
