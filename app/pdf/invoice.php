<?php

$peticion_ajax = true;
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

	$pdf = new PDF_Code128('P', 'mm', 'Letter');
	$pdf->SetMargins(17, 17, 17);
	$pdf->AddPage();
	$pdf->Image(APP_URL . 'app/views/img/logo.png', 165, 12, 35, 35, 'PNG');

	$pdf->SetFont('Arial', 'B', 16);
	$pdf->SetTextColor(185, 150, 84);
	$pdf->Cell(150, 10, iconv("UTF-8", "ISO-8859-1", strtoupper($datos_empresa['empresa_nombre'])), 0, 0, 'L');

	if ($datos_venta["estatus"] == 0) {


		$pdf->SetFont('Arial', 'B', 16);
		$pdf->SetTextColor(255, 78, 78);
		$pdf->Cell(110, 0, iconv("UTF-8", "ISO-8859-1", strtoupper("Cancelada")), 0, 0, 'L');
	} else {

		$pdf->SetFont('Arial', 'B', 16);
		$pdf->SetTextColor(72, 199, 142);
		$pdf->Cell(110, 0, iconv("UTF-8", "ISO-8859-1", strtoupper("Vigente")), 0, 0, 'L');
	}


	$pdf->Ln(9);

	$pdf->SetFont('Arial', '', 10);
	$pdf->SetTextColor(39, 39, 51);
	$pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'L');

	$pdf->Ln(5);

	$pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", $datos_empresa['empresa_direccion']), 0, 0, 'L');

	$pdf->Ln(5);

	$pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $datos_empresa['empresa_telefono']), 0, 0, 'L');

	$pdf->Ln(5);

	$pdf->Cell(150, 9, iconv("UTF-8", "ISO-8859-1", "Email: " . $datos_empresa['empresa_email']), 0, 0, 'L');

	$pdf->Ln(10);

	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(30, 7, iconv("UTF-8", "ISO-8859-1", 'Fecha de emisión:'), 0, 0);
	$pdf->SetTextColor(97, 97, 97);
	$pdf->Cell(116, 7, iconv("UTF-8", "ISO-8859-1", date("d/m/Y", strtotime($datos_venta['fecha_venta'])) . " " . $datos_venta['hora_venta']), 0, 0, 'L');
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetTextColor(39, 39, 51);
	$pdf->Cell(35, 7, iconv("UTF-8", "ISO-8859-1", strtoupper('Nota Nro.')), 0, 0, 'C');

	$pdf->Ln(7);

	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(12, 7, iconv("UTF-8", "ISO-8859-1", 'Cajero:'), 0, 0, 'L');
	$pdf->SetTextColor(97, 97, 97);
	$pdf->Cell(134, 7, iconv("UTF-8", "ISO-8859-1", $datos_venta['nombreCajero']), 0, 0, 'L');
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetTextColor(97, 97, 97);
	$pdf->Cell(35, 7, iconv("UTF-8", "ISO-8859-1", strtoupper($datos_venta['id_venta'])), 0, 0, 'C');

	$pdf->Ln(10);

	if ($datos_venta['id_cliente'] == 1) {
		$pdf->SetFont('Arial', '', 10);
		$pdf->SetTextColor(39, 39, 51);
		$pdf->Cell(13, 7, iconv("UTF-8", "ISO-8859-1", 'Cliente:'), 0, 0);
		$pdf->SetTextColor(97, 97, 97);
		$pdf->Cell(60, 7, iconv("UTF-8", "ISO-8859-1", "N/A"), 0, 0, 'L');
		$pdf->SetTextColor(39, 39, 51);
		$pdf->Cell(8, 7, iconv("UTF-8", "ISO-8859-1", ": "), 0, 0, 'L');
		$pdf->SetTextColor(97, 97, 97);
		$pdf->Cell(60, 7, iconv("UTF-8", "ISO-8859-1", "N/A"), 0, 0, 'L');
		$pdf->SetTextColor(39, 39, 51);
		$pdf->Cell(7, 7, iconv("UTF-8", "ISO-8859-1", 'Tel:'), 0, 0, 'L');
		$pdf->SetTextColor(97, 97, 97);
		$pdf->Cell(35, 7, iconv("UTF-8", "ISO-8859-1", "N/A"), 0, 0);
		$pdf->SetTextColor(39, 39, 51);

		$pdf->Ln(7);

		$pdf->SetTextColor(39, 39, 51);
		$pdf->Cell(6, 7, iconv("UTF-8", "ISO-8859-1", 'Dir:'), 0, 0);
		$pdf->SetTextColor(97, 97, 97);
		$pdf->Cell(109, 7, iconv("UTF-8", "ISO-8859-1", "N/A"), 0, 0);
	} else {
		$pdf->SetFont('Arial', '', 10);
		$pdf->SetTextColor(39, 39, 51);
		$pdf->Cell(13, 7, iconv("UTF-8", "ISO-8859-1", 'Cliente:'), 0, 0);
		$pdf->SetTextColor(97, 97, 97);
		$pdf->Cell(60, 7, iconv("UTF-8", "ISO-8859-1", $datos_venta['nombreCliente'] . " " . $datos_venta['apellidos']), 0, 0, 'L');
		$pdf->SetTextColor(39, 39, 51);
		$pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", 'Celular:'), 0, 0, 'L');
		$pdf->SetTextColor(97, 97, 97);
		$pdf->Cell(35, 7, iconv("UTF-8", "ISO-8859-1", $datos_venta['celular']), 0, 0);
		$pdf->SetTextColor(39, 39, 51);

		$pdf->Ln(7);

		$pdf->SetTextColor(39, 39, 51);
		$pdf->Cell(17, 7, iconv("UTF-8", "ISO-8859-1", 'Domicilio:'), 0, 0);
		$pdf->SetTextColor(97, 97, 97);
		$pdf->Cell(109, 7, iconv("UTF-8", "ISO-8859-1",  $datos_venta['domicilio']), 0, 0);
	}

	$pdf->Ln(9);

	$pdf->SetFillColor(185, 150, 84);
	$pdf->SetDrawColor(185, 150, 84);
	$pdf->SetTextColor(255, 255, 255);
	$pdf->Cell(100, 8, iconv("UTF-8", "ISO-8859-1", 'Descripción'), 1, 0, 'C', true);
	$pdf->Cell(15, 8, iconv("UTF-8", "ISO-8859-1", 'Cant.'), 1, 0, 'C', true);
	$pdf->Cell(32, 8, iconv("UTF-8", "ISO-8859-1", 'Precio'), 1, 0, 'C', true);
	$pdf->Cell(34, 8, iconv("UTF-8", "ISO-8859-1", 'Subtotal'), 1, 0, 'C', true);

	$pdf->Ln(8);

	$pdf->SetFont('Arial', '', 9);
	$pdf->SetTextColor(39, 39, 51);

	/*----------  Seleccionando detalles de la venta  ----------*/
	$venta_detalle = $ins_venta->seleccionarDatos("Normal", "venta_detalle WHERE codigo='" . $datos_venta['codigo'] . "'", "*", 0);
	$venta_detalle = $venta_detalle->fetchAll();

	foreach ($venta_detalle as $detalle) {
		$pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", $ins_venta->limitarCadena($detalle['descripcion'], 80, "...")), 'L', 0, 'C');
		$pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", $detalle['cantidad']), 'L', 0, 'C');
		$pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($detalle['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR)), 'L', 0, 'C');
		$pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($detalle['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR)), 'LR', 0, 'C');
		$pdf->Ln(7);
	}

	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", ''), 'T', 0, 'C');
	$pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", ''), 'T', 0, 'C');

	$pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", 'TOTAL A PAGAR'), 'T', 0, 'C');
	$pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($datos_venta['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), 'T', 0, 'C');

	$pdf->Ln(7);

	$pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
	$pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
	$pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", 'TOTAL PAGADO'), '', 0, 'C');
	$pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($datos_venta['pagado'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), '', 0, 'C');

	$pdf->Ln(7);

	$pdf->Cell(100, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
	$pdf->Cell(15, 7, iconv("UTF-8", "ISO-8859-1", ''), '', 0, 'C');
	$pdf->Cell(32, 7, iconv("UTF-8", "ISO-8859-1", 'CAMBIO'), '', 0, 'C');
	$pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO . number_format($datos_venta['cambio'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE), '', 0, 'C');

	$pdf->Ln(12);

	$pdf->SetFont('Arial', '', 9);

	$pdf->SetTextColor(39, 39, 51);
	$pdf->MultiCell(0, 9, iconv("UTF-8", "ISO-8859-1", "*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar esta nota ***"), 0, 'C', false);

	$pdf->Ln(9);

	$pdf->SetFillColor(39, 39, 51);
	$pdf->SetDrawColor(23, 83, 201);
	$pdf->Code128(72, $pdf->GetY(), $datos_venta['codigo'], 70, 20);
	$pdf->SetXY(12, $pdf->GetY() + 21);
	$pdf->SetFont('Arial', '', 12);
	$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", $datos_venta['codigo']), 0, 'C', false);

	$pdf->Output("I", "Nota_Nro" . $datos_venta['id_venta'] . ".pdf", true);
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