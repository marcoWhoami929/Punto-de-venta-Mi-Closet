<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Información de venta</h2>
</div>

<div class="container pb-6 pt-6">
	<?php

	include "./app/views/inc/btn_back.php";

	$code = $insLogin->limpiarCadena($url[1]);

	//$datos = $insLogin->seleccionarDatos("Normal", "venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario INNER JOIN caja ON venta.id_caja=caja.id_caja WHERE (codigo='" . $code . "')", "*", 0);
	$datos = $insLogin->seleccionarDatos("Normal", "venta as ven INNER JOIN cliente as cli ON ven.id_cliente=cli.id_cliente INNER JOIN usuario as usu ON ven.id_usuario=usu.id_usuario INNER JOIN caja as caja ON ven.id_caja=caja.id_caja WHERE (codigo='$code')", "ven.*,caja.*,cli.nombre as 'nombreCliente',cli.celular,cli.apellidos,cli.domicilio,usu.nombre as 'nombreCajero'", 0);

	if ($datos->rowCount() == 1) {
		$datos_venta = $datos->fetch();
	?>
		<h2 class="title has-text-centered">Datos de la venta <?php echo " (" . $code . ")"; ?></h2>
		<div class="columns pb-6 pt-6">
			<div class="column">

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Fecha</div>
					<span class="has-text-link"><?php echo date("d-m-Y", strtotime($datos_venta['fecha_venta'])) . " " . $datos_venta['hora_venta']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Nro. de factura</div>
					<span class="has-text-link"><?php echo $datos_venta['id_venta']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Código de venta</div>
					<span class="has-text-link"><?php echo $datos_venta['codigo']; ?></span>
				</div>

			</div>

			<div class="column">

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Caja</div>
					<span class="has-text-link">Nro. <?php echo $datos_venta['numero'] . " (" . $datos_venta['nombre']; ?>)</span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Vendedor</div>
					<span class="has-text-link"><?php echo $datos_venta['nombreCajero']  ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Cliente</div>
					<span class="has-text-link"><?php echo $datos_venta['nombreCliente'] . " " . $datos_venta['apellidos']; ?></span>
				</div>

			</div>

			<div class="column">

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Total</div>
					<span class="has-text-link"><?php echo MONEDA_SIMBOLO . number_format($datos_venta['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Pagado</div>
					<span class="has-text-link"><?php echo MONEDA_SIMBOLO . number_format($datos_venta['pagado'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Cambio</div>
					<span class="has-text-link"><?php echo MONEDA_SIMBOLO . number_format($datos_venta['cambio'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE; ?></span>
				</div>

			</div>

		</div>

		<div class="columns pb-6 pt-6">
			<div class="column">
				<div class="table-container">
					<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
						<thead>
							<tr>
								<th class="has-text-centered">#</th>
								<th class="has-text-centered">Producto</th>
								<th class="has-text-centered">Cant.</th>
								<th class="has-text-centered">Precio</th>
								<th class="has-text-centered">Subtotal</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$detalle_venta = $insLogin->seleccionarDatos("Normal", "venta_detalle WHERE codigo='" . $datos_venta['codigo'] . "'", "*", 0);

							if ($detalle_venta->rowCount() >= 1) {

								$detalle_venta = $detalle_venta->fetchAll();
								$cc = 1;

								foreach ($detalle_venta as $detalle) {
							?>
									<tr class="has-text-centered">
										<td><?php echo $cc; ?></td>
										<td><?php echo $detalle['descripcion']; ?></td>
										<td><?php echo $detalle['cantidad']; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
									</tr>
								<?php
									$cc++;
								}
								?>
								<tr class="has-text-centered">
									<td colspan="3"></td>
									<td class="has-text-weight-bold">
										TOTAL
									</td>
									<td class="has-text-weight-bold">
										<?php echo MONEDA_SIMBOLO . number_format($datos_venta['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
									</td>
								</tr>
							<?php
							} else {
							?>
								<tr class="has-text-centered">
									<td colspan="8">
										No hay productos agregados
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="columns pb-6 pt-6">
			<p class="has-text-centered full-width">
				<?php
				echo '<button type="button" class="button is-link is-light is-medium" onclick="print_invoice(\'' . APP_URL . 'app/pdf/invoice.php?code=' . $datos_venta['codigo'] . '\')" title="Imprimir factura Nro. ' . $datos_venta['id_venta'] . '" >
			<i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Imprimir factura
			</button> &nbsp;&nbsp; 

			<button type="button" class="button is-link is-light is-medium" onclick="print_ticket(\'' . APP_URL . 'app/pdf/ticket.php?code=' . $datos_venta['codigo'] . '\')" title="Imprimir ticket Nro. ' . $datos_venta['id_venta'] . '" ><i class="fas fa-receipt fa-fw"></i> &nbsp; Imprimir ticket</button>';
				?>
			</p>
		</div>
	<?php
		include "./app/views/inc/print_invoice_script.php";
	} else {
		include "./app/views/inc/error_alert.php";
	}
	?>
</div>