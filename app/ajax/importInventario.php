<?php


require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";


use app\controllers\productController;

$insProducto = new productController();
if (!empty($_FILES["load_inventario"])) {

    $file_array = explode(".", $_FILES["load_inventario"]["name"]);
    if ($file_array[1] == "xlsx") {

        include("../phpexcel/Classes/PHPExcel/IOFactory.php");

        $object = PHPExcel_IOFactory::load($_FILES["load_inventario"]["tmp_name"]);
        foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();



            for ($row = 2; $row <= $highestRow; $row++) {
                $codigo = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                $cantidad = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                $movimiento = $worksheet->getCellByColumnAndRow(3, $row)->getValue();

                $datos = [
                    "codigo" => $codigo,
                    "cantidad" => $cantidad,
                    "movimiento" => $movimiento,
                ];

                $actualizar = $insProducto->actualizarInventarioControlador($datos);
            }
        }

        echo "exito";
    } else {
        echo "error";
    }
}
