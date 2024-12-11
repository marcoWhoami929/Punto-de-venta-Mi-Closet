<?php

include("../models/mainModel.php");

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\models\mainModel;

$insLogin = new mainModel();
if (!empty($_FILES["load_productos_nota"])) {

    $file_array = explode(".", $_FILES["load_productos_nota"]["name"]);
    if ($file_array[1] == "xlsx") {

        include("../phpexcel/Classes/PHPExcel/IOFactory.php");

        $object = PHPExcel_IOFactory::load($_FILES["load_productos_nota"]["tmp_name"]);
        foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();


            for ($row = 2; $row <= $highestRow; $row++) {
                $codigo = $worksheet->getCellByColumnAndRow(0, $row)->getValue();

                $datos = $insLogin->seleccionarDatos("Normal", "producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE prod.codigo='$codigo'", "prod.*,inven.stock_total", 0);
                if ($datos->rowCount() == 1) {
                    $datos = $datos->fetch();
                    $codigo = $datos['codigo'];

                    if (empty($_SESSION['datos_producto_nota'][$codigo])) {


                        $stock_total = $datos['stock_total'];

                        if ($stock_total < 0) {
                        } else {

                            $_SESSION['datos_producto_nota'][$codigo] = [
                                "id_producto" => $datos['cid_producto'],
                                "codigo" => $datos['codigo'],
                                "foto" => $datos['foto'],
                                "colores" => $datos['colores'],
                                "tallas" => $datos['tallas'],
                                "precio_venta" => $datos['precio_venta'],
                                "limite_nota" => $stock_total,
                                "descripcion" => $datos['nombre']
                            ];
                        }
                    }
                }
            }
        }

        echo "exito";
    } else {
        echo "error";
    }
}
