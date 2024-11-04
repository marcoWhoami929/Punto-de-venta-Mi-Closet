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

		if ($datos_venta["estatus_pago"] == 1) {
			$buttonPago = '<p class="has-text-centered ">
			<a href="#" class="button is-link is-rounded is-success is-large">
				<i class="fas fa-check-circle"></i> &nbsp;Pago Realizado </a>
		</p>';
		} else {
			$buttonPago = '<p class="has-text-centered ">
			<a href="#" class="button is-link is-rounded is-danger is-large">
				<i class="fas fa-exclamation-triangle"></i>&nbsp;Sin Pagar </a>
		</p>';
		}


		switch ($datos_venta["estatus"]) {
			case '1':
				$estatus1 = "is-active";
				$estatus2 = "";
				$estatus3 = "";
				$estatus4 = "";
				break;
			case '2':
				$estatus1 = "";
				$estatus2 = "is-active";
				$estatus3 = "";
				$estatus4 = "";
				break;
			case '3':
				$estatus1 = "";
				$estatus2 = "";
				$estatus3 = "is-active";
				$estatus4 = "";
				break;
			case '4':
				$estatus1 = "";
				$estatus2 = "";
				$estatus3 = "";
				$estatus4 = "is-active";
				break;
		}
		echo $buttonPago;

	?>


		<h2 class="title has-text-centered">Datos de la venta <?php echo " (" . $code . ")"; ?></h2>
		<div class="columns">
			<div class="column">
				<ul class="steps is-medium has-content-centered">
					<li class="steps-segment <?php echo  $estatus1  ?>">
						<span class="steps-marker is-primary">
							<span class="icon">
								<i class="fa fa-shopping-cart"></i>
							</span></span>
						<div class="steps-content">
							<code>Recibido</code>
						</div>
					</li>
					<li class="steps-segment <?php echo  $estatus2  ?>">
						<span class="steps-marker is-info">
							<span class="icon">
								<i class="fas fa-dolly-flatbed"></i>
							</span></span>
						</span>
						<div class="steps-content <?php echo $estatus3  ?>">
							<code>En Preparación</code>
						</div>
					</li>
					<li class="steps-segment <?php echo $estatus3 ?>">
						<span class="steps-marker is-success">
							<span class="icon">
								<i class="fas fa-truck-moving"></i>
							</span></span>
						</span>
						<div class="steps-content">
							<code>Enviado</code>
						</div>
					</li>
					<li class="steps-segment <?php echo  $estatus4  ?>">
						<span class="steps-marker is-warning">
							<span class="icon">
								<i class="fas fa-thumbs-up"></i>
							</span></span>
						</span>
						<div class="steps-content">
							<code>Entregado</code>
						</div>
					</li>

				</ul>
			</div>
		</div>

		<div class="columns pb-6 pt-6">
			<div class="column">

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Fecha</div>
					<span class="has-text-link"><?php echo date("d-m-Y", strtotime($datos_venta['fecha_venta'])) . " " . $datos_venta['hora_venta']; ?></span>
				</div>

				<div class="full-width sale-details text-condensedLight">
					<div class="has-text-weight-bold">Nro. de nota</div>
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
								<th class="has-text-centered" style="color:#B99654">#</th>
								<th class="has-text-centered" style="color:#B99654">Producto</th>
								<th class="has-text-centered" style="color:#B99654">Cant.</th>
								<th class="has-text-centered" style="color:#B99654">Talla.</th>
								<th class="has-text-centered" style="color:#B99654">Color.</th>
								<th class="has-text-centered" style="color:#B99654">Precio</th>
								<th class="has-text-centered" style="color:#B99654">Descuento</th>
								<th class="has-text-centered" style="color:#B99654">Subtotal</th>
								<th class="has-text-centered" style="color:#B99654">Total</th>
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
										<td><?php echo $detalle['talla']; ?></td>
										<td><?php echo $detalle['color']; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
										<td><?php echo MONEDA_SIMBOLO . number_format($detalle['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></td>
									</tr>
								<?php
									$cc++;
								}
								?>
								<tr class="has-text-centered">
									<td colspan="7"></td>
									<td class="has-text-weight-bold">
										SUBTOTAL
									</td>
									<td class="has-text-weight-bold">
										<?php echo MONEDA_SIMBOLO . number_format($datos_venta['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
									</td>
								</tr>
								<tr class="has-text-centered">
									<td colspan="7"></td>
									<td class="has-text-weight-bold">
										DESCUENTO
									</td>
									<td class="has-text-weight-bold">
										<?php echo MONEDA_SIMBOLO . number_format($datos_venta['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
									</td>
								</tr>
								<tr class="has-text-centered">
									<td colspan="7"></td>
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
				echo '<button type="button" class="button is-link is-light is-medium" onclick="print_invoice(\'' . APP_URL . 'app/pdf/invoice.php?code=' . $datos_venta['codigo'] . '\')" title="Imprimir nota Nro. ' . $datos_venta['id_venta'] . '" >
			<i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Imprimir nota
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