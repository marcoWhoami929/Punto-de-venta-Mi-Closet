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
            die('This example should only be run from a Web Bvalueer');

        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Ventas mi closet")
            ->setLastModifiedBy("")
            ->setTitle("Reporte Kardex");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Producto')
            ->setCellValue('B1', 'Codigo')
            ->setCellValue('C1', 'Entradas')
            ->setCellValue('D1', 'Salidas')
            ->setCellValue('E1', 'Existencias')
            ->setCellValue('F1', 'Stock Minimo')
            ->setCellValue('G1', 'Stock Maximo');

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


        // Redirect output to a client’s web bvalueer (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Kardex.xlsx"');
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
    function reporteClientes($datos)
    {
        ob_start();
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('America/Mexico_City');

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);

        $sWhere = "clien.id_cliente !='0'";
        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " and clien.nombre LIKE '%$busqueda%' OR clien.apellidos LIKE '%$busqueda%' OR clien.email LIKE '%$busqueda%'";
        }

        $campos = "clien.*,ven.fecha_venta,MAX(ven.fecha_venta) as 'UltimaCompra',sum(ven.total) as 'Compras',sum(ven.pagado) as 'Pagado',sum(ven.pendiente) as 'Pendiente' ";

        $consulta_datos = "SELECT $campos FROM cliente as clien LEFT OUTER JOIN venta as ven ON clien.id_cliente = ven.id_cliente WHERE $sWhere GROUP by clien.id_cliente,clien.nombre,clien.apellidos,clien.celular ORDER BY $campoOrden $orden";

        $consulta_total = "SELECT COUNT(id_cliente) FROM cliente as clien WHERE $sWhere ";

        $datos_consulta = $this->ejecutarConsulta($consulta_datos);
        $datos_consulta = $datos_consulta->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();



        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Bvalueer');

        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Ventas mi closet")
            ->setLastModifiedBy("")
            ->setTitle("Reporte Clientes");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Tipo Cliente')
            ->setCellValue('B1', 'Nombre')
            ->setCellValue('C1', 'Apellidos')
            ->setCellValue('D1', 'Nombre Usuario')
            ->setCellValue('E1', 'Email')
            ->setCellValue('F1', 'Telefono')
            ->setCellValue('G1', 'Celular')
            ->setCellValue('H1', 'Calle')
            ->setCellValue('I1', '#')
            ->setCellValue('J1', 'Ciudad')
            ->setCellValue('K1', 'Estado')
            ->setCellValue('L1', 'Cp')
            ->setCellValue('M1', 'Facebook')
            ->setCellValue('N1', 'Ulima Compra')
            ->setCellValue('O1', 'Comprado')
            ->setCellValue('P1', 'Pagado')
            ->setCellValue('Q1', 'Pendiente')
            ->setCellValue('R1', 'Fecha Alta');


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




        $objPHPExcel->getActiveSheet()->getStyle("A1:R1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:R1")->getFont()->getColor()->setRGB("ffffff");
        $objPHPExcel->getActiveSheet()->getStyle("A1:R1")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle("A1:R1")->getFont()->setName("Verdana");
        $objPHPExcel->getActiveSheet()->getStyle("A1:R1")->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('B99654');
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);


        $i = 2;
        foreach ($datos_consulta as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A$i", $value['tipo_cliente'])
                ->setCellValue("B$i", $value['nombre'])
                ->setCellValue("C$i", $value['apellidos'])
                ->setCellValue("D$i", $value['usuario'])
                ->setCellValue("E$i", $value['email'])
                ->setCellValue("F$i", $value['telefono'])
                ->setCellValue("G$i", $value['celular'])
                ->setCellValue("H$i", $value['calle'])
                ->setCellValue("I$i", $value['numero'])
                ->setCellValue("J$i", $value['ciudad'])
                ->setCellValue("K$i", $value['estado'])
                ->setCellValue("L$i", $value['cp'])
                ->setCellValue("M$i", $value['facebook'])
                ->setCellValue("N$i", $value['UltimaCompra'])
                ->setCellValue("O$i", $value['Compras'])
                ->setCellValue("P$i", $value['Pagado'])
                ->setCellValue("Q$i", $value['Pendiente'])
                ->setCellValue("R$i", $value['fecha_registro']);


            $objPHPExcel->getActiveSheet()->getStyle("O$i:Q$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

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

        $objPHPExcel->getActiveSheet()->setTitle('Reporte cilentes');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web bvalueer (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Clientes.xlsx"');
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
    function reporteProductos($datos)
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
            $sWhere .= " AND prod.codigo LIKE '%$busqueda%' OR prod.nombre LIKE '%$busqueda%' OR prod.marca LIKE '%$busqueda%' OR prod.modelo LIKE '%$busqueda%'";
        }

        $campos = "prod.*,cat.nombre as 'categoria',inven.stock_total";

        $consulta_datos = "SELECT $campos FROM producto as prod INNER JOIN categoria as cat ON prod.id_categoria=cat.id_categoria INNER JOIN inventario as inven ON prod.cid_producto=inven.id_producto WHERE $sWhere  ORDER BY $campoOrden $orden";

        $consulta_total = "SELECT COUNT(prod.cid_producto) FROM producto as prod INNER JOIN categoria as cat ON prod.id_categoria=cat.id_categoria INNER JOIN inventario as inven ON prod.cid_producto=inven.id_producto WHERE $sWhere ";

        $datos_consulta = $this->ejecutarConsulta($consulta_datos);
        $datos_consulta = $datos_consulta->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();



        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Bvalueer');

        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Ventas mi closet")
            ->setLastModifiedBy("")
            ->setTitle("Reporte Productos");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Id Producto')
            ->setCellValue('B1', 'Codigo')
            ->setCellValue('C1', 'Nombre Producto')
            ->setCellValue('D1', 'Categoria')
            ->setCellValue('E1', 'Existencia')
            ->setCellValue('F1', 'Unidad')
            ->setCellValue('G1', 'Precio Compra')
            ->setCellValue('H1', 'Precio Venta')
            ->setCellValue('I1', 'Marca')
            ->setCellValue('J1', 'Modelo')
            ->setCellValue('K1', 'Colores')
            ->setCellValue('L1', 'Tallas')
            ->setCellValue('M1', 'Estatus');


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




        $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->getColor()->setRGB("ffffff");
        $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->setName("Verdana");
        $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('B99654');
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);


        $i = 2;
        foreach ($datos_consulta as $key => $value) {

            if ($value["estado"] == '1') {
                $estado = "Activo";
            } else {
                $estado = "Inactivo";
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A$i", $value['cid_producto'])
                ->setCellValue("B$i", $value['codigo'])
                ->setCellValue("C$i", $value['nombre'])
                ->setCellValue("D$i", $value['categoria'])
                ->setCellValue("E$i", $value['stock_total'])
                ->setCellValue("F$i", $value['tipo_unidad'])
                ->setCellValue("G$i", $value['precio_compra'])
                ->setCellValue("H$i", $value['precio_venta'])
                ->setCellValue("I$i", $value['marca'])
                ->setCellValue("J$i", $value['modelo'])
                ->setCellValue("K$i", $value['colores'])
                ->setCellValue("L$i", $value['tallas'])
                ->setCellValue("M$i", $estado);
            $objPHPExcel->getActiveSheet()->getStyle("G$i:H$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

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

        $objPHPExcel->getActiveSheet()->setTitle('Reporte productos');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web bvalueer (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Productos.xlsx"');
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
    function reporteVentas($datos)
    {
        ob_start();
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('America/Mexico_City');

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);

        $sWhere = "venta.id_venta !='0'";
        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " AND venta.codigo LIKE '%$busqueda%' OR venta.codigo_nota LIKE '%$busqueda%' OR usuario.nombre LIKE '%$busqueda%' OR cliente.nombre LIKE '%$busqueda%'";
        }

        $campos = "met.metodo as 'formaPago',venta.pendiente,venta.pagado,venta.forma_pago,venta.tipo_entrega,venta.estatus_pago,venta.id_venta,venta.estatus,venta.subtotal,venta.descuento,venta.tipo_venta,venta.codigo,venta.fecha_venta,venta.hora_venta,venta.fecha_registro,venta.total,venta.id_usuario,venta.id_cliente,venta.id_caja,usuario.id_usuario,usuario.nombre as 'nombreVendedor',cliente.id_cliente,cliente.nombre as 'nombreCliente',cliente.apellidos";

        $consulta_datos = "SELECT $campos FROM venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN metodopago as met ON venta.forma_pago=met.id_metodo_pago INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario WHERE $sWhere  ORDER BY $campoOrden $orden";

        $consulta_total = "SELECT COUNT(id_venta)FROM venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN metodopago as met ON venta.forma_pago=met.id_metodo_pago INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario WHERE $sWhere ";

        $datos_consulta = $this->ejecutarConsulta($consulta_datos);
        $datos_consulta = $datos_consulta->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();



        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Bvalueer');

        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Ventas mi closet")
            ->setLastModifiedBy("")
            ->setTitle("Reporte Ventas");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Id Venta')
            ->setCellValue('B1', 'Tipo Venta')
            ->setCellValue('C1', 'Tipo Entrega')
            ->setCellValue('D1', 'Forma Pago')
            ->setCellValue('E1', 'Codigo Venta')
            ->setCellValue('F1', 'Codigo Nota')
            ->setCellValue('G1', 'Cliente')
            ->setCellValue('H1', 'Fecha Venta')
            ->setCellValue('I1', '% Descuento')
            ->setCellValue('J1', 'Subtotal')
            ->setCellValue('K1', 'Descuento')
            ->setCellValue('L1', 'Total')
            ->setCellValue('M1', 'Pagado')
            ->setCellValue('N1', 'Pendiente')
            ->setCellValue('O1', 'Estatus Venta')
            ->setCellValue('P1', 'Estatus Pago')
            ->setCellValue('Q1', 'Vendedor');


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




        $objPHPExcel->getActiveSheet()->getStyle("A1:Q1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:Q1")->getFont()->getColor()->setRGB("ffffff");
        $objPHPExcel->getActiveSheet()->getStyle("A1:Q1")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle("A1:Q1")->getFont()->setName("Verdana");
        $objPHPExcel->getActiveSheet()->getStyle("A1:Q1")->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('B99654');
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);


        $i = 2;
        foreach ($datos_consulta as $key => $value) {


            if ($value["estatus"] != 0) {
                if ($value["estatus_pago"] == 0) {
                    $estatus_pago = 'Sin Pagar';
                    $estatus = "Vigente";
                } else {
                    if ($value["pendiente"] != '0.00') {
                        $estatus_pago = 'Pago Parcial';
                        $estatus = "Vigente";
                    } else {
                        $estatus_pago = 'Pagada';
                        $estatus = "Vigente";
                    }
                }
            } else {
                $estatus_pago = 'Cancelada';
                $estatus = "Cancelada";
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A$i", $value['id_venta'])
                ->setCellValue("B$i", $value['tipo_venta'])
                ->setCellValue("C$i", $value['tipo_entrega'])
                ->setCellValue("D$i", $value['formaPago'])
                ->setCellValue("E$i", $value['codigo'])
                ->setCellValue("F$i", $value['codigo_nota'])
                ->setCellValue("G$i", $value['nombreCliente'] . " " . $value['apellidos'])
                ->setCellValue("H$i", $value['fecha_venta'] . " " . $value['hora_venta'])
                ->setCellValue("I$i", $value['porc_descuento'])
                ->setCellValue("J$i", $value['subtotal'])
                ->setCellValue("K$i", $value['descuento'])
                ->setCellValue("L$i", $value['total'])
                ->setCellValue("M$i", $value['pagado'])
                ->setCellValue("N$i", $value['pendiente'])
                ->setCellValue("O$i", $estatus_pago)
                ->setCellValue("P$i", $estatus)
                ->setCellValue("Q$i", $value["nombreVendedor"]);

            $objPHPExcel->getActiveSheet()->getStyle("J$i:N$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

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

        $objPHPExcel->getActiveSheet()->setTitle('Reporte Ventas');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web bvalueer (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Ventas.xlsx"');
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
    function reporteNotas($datos)
    {
        ob_start();
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('America/Mexico_City');

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);

        $sWhere = "nota.id_nota !='0'";
        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " AND nota.codigo LIKE '%$busqueda%' OR nota.titulo_nota LIKE '%$busqueda%'";
        }

        $campos = "*";

        $consulta_datos = "SELECT $campos FROM notas as nota WHERE $sWhere  ORDER BY $campoOrden $orden";

        $consulta_total = "SELECT COUNT(id_nota) FROM notas as nota WHERE $sWhere";

        $datos_consulta = $this->ejecutarConsulta($consulta_datos);
        $datos_consulta = $datos_consulta->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();



        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Bvalueer');

        // Create new PHPExcel object

        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Ventas mi closet")
            ->setLastModifiedBy("")
            ->setTitle("Reporte Kardex");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Id Nota')
            ->setCellValue('B1', 'Codigo Nota')
            ->setCellValue('C1', 'Titulo')
            ->setCellValue('D1', 'Fecha Publicacion')
            ->setCellValue('E1', 'Fecha Expiracion')
            ->setCellValue('F1', '% Descuento')
            ->setCellValue('G1', 'Estatus')
            ->setCellValue('H1', 'Fecha Creada');


        $alineacion2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );




        $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFont()->getColor()->setRGB("ffffff");
        $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFont()->setName("Verdana");
        $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('B99654');
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);


        $i = 2;
        foreach ($datos_consulta as $key => $value) {
            switch ($value['estatus']) {
                case '0':
                    $estatus = 'Cancelada';
                    break;
                case '1':
                    $estatus = 'Activa';
                    break;
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A$i", $value['id_nota'])
                ->setCellValue("B$i", $value['codigo'])
                ->setCellValue("C$i", $value['titulo_nota'])
                ->setCellValue("D$i", $value['fecha_publicacion'])
                ->setCellValue("E$i", $value['fecha_expiracion'])
                ->setCellValue("F$i", $value['porc_descuento'])
                ->setCellValue("G$i", $estatus)
                ->setCellValue("H$i", $value['fecha']);


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

        $objPHPExcel->getActiveSheet()->setTitle('Reporte Notas');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web bvalueer (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Notas.xlsx"');
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
    if ($_GET['modulo_reporte'] == "clientes") {

        $reporte = new reportesGenerales();
        $datos = array(
            "busqueda" => $_GET["busqueda"],
            "campoOrden" => $_GET["campoOrden"],
            "orden" => $_GET["orden"],
            "page" => $_GET["page"],
            "per_page" => $_GET["per_page"],
            "url" => $_GET["url"],
        );
        $reporte->reporteClientes($datos);
    }
    if ($_GET['modulo_reporte'] == "productos") {

        $reporte = new reportesGenerales();
        $datos = array(
            "busqueda" => $_GET["busqueda"],
            "campoOrden" => $_GET["campoOrden"],
            "orden" => $_GET["orden"],
            "page" => $_GET["page"],
            "per_page" => $_GET["per_page"],
            "url" => $_GET["url"],
        );
        $reporte->reporteProductos($datos);
    }
    if ($_GET['modulo_reporte'] == "ventas") {

        $reporte = new reportesGenerales();
        $datos = array(
            "busqueda" => $_GET["busqueda"],
            "campoOrden" => $_GET["campoOrden"],
            "orden" => $_GET["orden"],
            "page" => $_GET["page"],
            "per_page" => $_GET["per_page"],
            "url" => $_GET["url"],
        );
        $reporte->reporteVentas($datos);
    }
    if ($_GET['modulo_reporte'] == "notas") {

        $reporte = new reportesGenerales();
        $datos = array(
            "busqueda" => $_GET["busqueda"],
            "campoOrden" => $_GET["campoOrden"],
            "orden" => $_GET["orden"],
            "page" => $_GET["page"],
            "per_page" => $_GET["per_page"],
            "url" => $_GET["url"],
        );
        $reporte->reporteNotas($datos);
    }
}
