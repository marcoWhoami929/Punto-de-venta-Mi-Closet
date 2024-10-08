<?php

$code = (isset($_GET['code'])) ? $_GET['code'] : 0;

/*---------- Incluyendo configuraciones ----------*/
require_once "../../config/app.php";
require_once "../../autoload.php";

/*---------- Instancia al controlador venta ----------*/

use app\controllers\saleController;

$ins_venta = new saleController();

$datos_venta = $ins_venta->seleccionarDatos("Normal", "venta as ven INNER JOIN cliente as cli ON ven.id_cliente=cli.id_cliente INNER JOIN usuario as usu ON ven.id_usuario=usu.id_usuario INNER JOIN caja as caja ON ven.id_caja=caja.id_caja WHERE (codigo='$code')", "ven.*,caja.*,cli.nombre as 'nombreCliente',cli.celular,cli.apellidos,cli.domicilio,usu.nombre as 'nombreCajero'", 0);

if ($datos_venta->rowCount() == 1) {

    /*---------- Datos de la venta ----------*/
    $datos_venta = $datos_venta->fetch();

    /*---------- Seleccion de datos de la empresa ----------*/
    $datos_empresa = $ins_venta->seleccionarDatos("Normal", "empresa LIMIT 1", "*", 0);
    $datos_empresa = $datos_empresa->fetch();


    require "./code128.php";

    $pdf = new PDF_Code128('P', 'mm', array(80, 258));
    $pdf->SetMargins(4, 10, 4);
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper($datos_empresa['empresa_nombre'])), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", $datos_empresa['empresa_direccion']), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $datos_empresa['empresa_telefono']), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Email: " . $datos_empresa['empresa_email']), 0, 'C', false);

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Fecha: " . date("d/m/Y", strtotime($datos_venta['fecha_venta'])) . " " . $datos_venta['hora_venta']), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Caja Nro: " . $datos_venta['numero']), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Vendedor: " . $datos_venta['nombreCajero']), 0, 'C', false);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper("Ticket Nro: " . $datos_venta['id_venta'])), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    if ($datos_venta['id_cliente'] == 1) {
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Cliente: N/A"), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: N/A"), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: N/A"), 0, 'C', false);
    } else {
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Cliente: " . $datos_venta['nombreCliente'] . " " . $datos_venta['apellidos']), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Celular: " . $datos_venta['celular']), 0, 'C', false);
        $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: " .  $datos_venta['domicilio']), 0, 'C', false);
    }

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(3);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", "Cant."), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "Precio"), 0, 0, 'C');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "Total"), 0, 0, 'C');

    $pdf->Ln(3);
    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(3);

    /*----------  Seleccionando detalles de la venta  ----------*/
    $venta_detalle = $ins_venta->seleccionarDatos("Normal", "venta_detalle WHERE codigo='" . $datos_venta['codigo'] . "'", "*", 0);
    $venta_detalle = $venta_detalle->fetchAll();

    foreach ($venta_detalle as $detalle) {
        $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", $detalle['descripcion']), 0, 'C', false);
        $pdf->Cell(18, 4, iconv("UTF-8", "ISO-8859-1", $detalle['cantidad']), 0, 0, 'C');
        $pdf->Cell(22, 4, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($detalle['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR)), 0, 0, 'C');
        $pdf->Cell(32, 4, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($detalle['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR)), 0, 0, 'C');
        $pdf->Ln(4);
        $pdf->Ln(3);
    }

    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');

    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "TOTAL A PAGAR"), 0, 0, 'C');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($datos_venta['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), 0, 0, 'C');

    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "TOTAL PAGADO"), 0, 0, 'C');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($datos_venta['pagado'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), 0, 0, 'C');

    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "CAMBIO"), 0, 0, 'C');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($datos_venta['cambio'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), 0, 0, 'C');

    $pdf->Ln(10);

    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"), 0, 'C', false);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 7, iconv("UTF-8", "ISO-8859-1", "Gracias por su compra"), '', 0, 'C');

    $pdf->Ln(9);

    $pdf->Code128(5, $pdf->GetY(), $datos_venta['codigo'], 70, 20);
    $pdf->SetXY(0, $pdf->GetY() + 21);
    $pdf->SetFont('Arial', '', 14);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", $datos_venta['codigo']), 0, 'C', false);

    $pdf->Output("I", "Ticket_Nro" . $datos_venta['id_venta'] . ".pdf", true);
} else {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <title><?php echo APP_NAME; ?></title>
        <?php include '../views/inc/head.php'; ?>
    </head>

    <body>
        <div class="main-container">
            <section class="hero-body">
                <div class="hero-body">
                    <p class="has-text-centered has-text-white pb-3">
                        <i class="fas fa-rocket fa-5x"></i>
                    </p>
                    <p class="title has-text-white">¡Ocurrió un error!</p>
                    <p class="subtitle has-text-white">No hemos encontrado datos de la venta</p>
                </div>
            </section>
        </div>
        <?php include '../views/inc/script.php'; ?>
    </body>

    </html>
<?php } ?>