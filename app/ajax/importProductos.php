<?php


require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";


use app\controllers\productController;

$insProducto = new productController();
if (!empty($_FILES["load_productos"])) {

    $file_array = explode(".", $_FILES["load_productos"]["name"]);
    if ($file_array[1] == "xlsx") {

        include("../phpexcel/Classes/PHPExcel/IOFactory.php");

        $object = PHPExcel_IOFactory::load($_FILES["load_productos"]["tmp_name"]);
        foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();



            for ($row = 2; $row <= $highestRow; $row++) {
                $codigo = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                $nombre = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                $unidad = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                $precio_compra = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                $precio_venta = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                $colores = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                $tallas = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                $id_categoria = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $stock = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                $stock_minimo = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                $stock_maximo = $worksheet->getCellByColumnAndRow(10, $row)->getValue();



                $datos = [
                    "codigo" => $codigo,
                    "nombre" => $nombre,
                    "unidad" => $unidad,
                    "precio_compra" => $precio_compra,
                    "precio_venta" => $precio_venta,
                    "colores" => $colores,
                    "tallas" => $tallas,
                    "id_categoria" => $id_categoria,
                    "stock" => $stock,
                    "stock_minimo" => $stock_minimo,
                    "stock_maximo" => $stock_maximo,
                ];

                $registrar = $insProducto->agregarProductosControlador($datos);
            }
        }

        echo "exito";
    } else {
        echo "error";
    }
}
