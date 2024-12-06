<?php

require_once "../config/app.php";
require_once "views/inc/session_start.php";
require_once "../autoload.php";

use app\models\mainModel;

/** Include PHPExcel */
require_once 'phpExcel/Classes/PHPExcel.php';


class reportesGenerales extends mainModel
{

    function reporteKardex($datos)
    {
        ob_start();
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('America/Mexico_City');

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);

        $sWhere = "prod.id_producto !='0'";
        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " AND prod.nombre LIKE '%$busqueda%' OR prod.codigo LIKE '%$busqueda%'";
        }

        $campos = "prod.cid_producto,prod.nombre,prod.codigo, SUM(IF(mov.tipo_movimiento = 'entrada',cantidad,0)) as 'entradas',SUM(IF(mov.tipo_movimiento = 'salida',cantidad,0)) as 'salidas', inv.stock_total as 'existencias',inv.stock_minimo,inv.stock_maximo,((inv.stock_maximo-inv.stock_minimo)/2) as media,IF(inv.stock_total=0,'0',IF(inv.stock_total<inv.stock_minimo,'1',IF(inv.stock_total>=inv.stock_minimo and inv.stock_total <= ((inv.stock_maximo-inv.stock_minimo)/2),'2',IF(inv.stock_total >((inv.stock_maximo-inv.stock_minimo)/2),'3','' )))) as estatus";

        $consulta_datos = "SELECT $campos FROM producto as prod LEFT OUTER JOIN movimiento_inventario as mov ON prod.cid_producto = mov.id_producto LEFT OUTER JOIN inventario as inv ON prod.cid_producto = inv.id_producto WHERE $sWhere GROUP by prod.cid_producto,prod.nombre,prod.codigo ORDER BY $campoOrden $orden";

        $consulta_total = "SELECT COUNT(prod.cid_producto) FROM producto as prod WHERE $sWhere ";

        $datos_consulta = $this->ejecutarConsulta($consulta_datos);
        $datos_consulta = $datos_consulta->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();



        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Ventas mi closet")
            ->setLastModifiedBy("")
            ->setTitle("Reporte kardex");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Producto')
            ->setCellValue('B1', 'Codigo')
            ->setCellValue('C1', 'Entradas')
            ->setCellValue('D1', 'Salidas')
            ->setCellValue('E1', 'Existencias')
            ->setCellValue('F1', 'Stock Minimo')
            ->setCellValue('G1', 'Stock Maximo');


        $estilo = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'color' => array('rgb' => 'FFFFFF'),
                'name'  => 'Verdana'
            ),
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => '000000'
            )
        );
        $alineacion = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
        );
        $alineacion2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );




        $objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFont()->getColor()->setRGB("ffffff");
        $objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFont()->setName("Verdana");
        $objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('B99654');
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);


        $i = 2;
        foreach ($datos_consulta as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A$i", $value['nombre'])
                ->setCellValue("B$i", $value['codigo'])
                ->setCellValue("C$i", $value['entradas'])
                ->setCellValue("D$i", $value['salidas'])
                ->setCellValue("E$i", $value['existencias'])
                ->setCellValue("F$i", $value['stock_minimo'])
                ->setCellValue("G$i", $value['stock_maximo']);


            $objPHPExcel->getActiveSheet()->getStyle("B$i")
                ->getNumberFormat()
                ->setFormatCode(
                    PHPExcel_Style_NumberFormat::FORMAT_TEXT
                );

            $objPHPExcel->getActiveSheet()->getStyle("C$i")->applyFromArray($alineacion2);
            $objPHPExcel->getActiveSheet()->getStyle("D$i")->applyFromArray($alineacion2);
            $objPHPExcel->getActiveSheet()->getStyle("E$i")->applyFromArray($alineacion2);
            $objPHPExcel->getActiveSheet()->getStyle("F$i")->applyFromArray($alineacion2);
            $objPHPExcel->getActiveSheet()->getStyle("G$i")->applyFromArray($alineacion2);

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

                $sheet = $objPHPExcel->getActiveSheet();
                $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);
                /** @var PHPExcel_Cell $cell */
                foreach ($cellIterator as $cell) {
                    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                }
            }
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Reporte kardex');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="kardex.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }
}
if (isset($_GET['modulo_reporte'])) {

    if ($_GET['modulo_reporte'] == "kardex") {

        $reporte = new reportesGenerales();
        $datos = array(
            "busqueda" => $_GET["busqueda"],
            "campoOrden" => $_GET["campoOrden"],
            "orden" => $_GET["orden"],
            "page" => $_GET["page"],
            "per_page" => $_GET["per_page"],
            "url" => $_GET["url"],
        );
        $reporte->reporteKardex($datos);
    }
}
