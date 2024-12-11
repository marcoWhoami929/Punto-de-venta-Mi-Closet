<?php

include("../models/mainModel.php");

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\models\mainModel;

$insLogin = new mainModel();
if (!empty($_FILES["load_productos"])) {

    $file_array = explode(".", $_FILES["load_productos"]["name"]);
    if ($file_array[1] == "xlsx") {

        include("../phpexcel/Classes/PHPExcel/IOFactory.php");

        $object = PHPExcel_IOFactory::load($_FILES["load_productos"]["tmp_name"]);
        foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();


            for ($row = 2; $row <= $highestRow; $row++) {
            }
        }

        echo "exito";
    } else {
        echo "error";
    }
}
