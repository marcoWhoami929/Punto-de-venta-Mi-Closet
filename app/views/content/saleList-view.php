<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de Ventas</h2>
</div>
<div class="container pb-6 pt-6">

	<div class="form-rest mb-6 mt-6"></div>

	<?php

	use app\controllers\saleController;

	$insVenta = new saleController();

	echo $insVenta->listarVentaControlador($url[1], 15, $url[0], "");

	include "./app/views/inc/print_invoice_script.php";
	?>
</div>
<div class="modal fade" id="modal-pago-venta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-background"></div>
	<div class="modal-card">
		<header class="modal-card-head">
			<p class="modal-card-title is-uppercase"><i class="fas fa-cashier"></i> &nbsp; Confirmar Pago</p>
			<button class="delete" aria-label="close"></button>
		</header>
		<section class="modal-card-body">
			<div class="field mt-6 mb-6">
				<label class="label">Nombre, marca, modelo, código</label>
				<div class="control">
					<input class="input" type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" placeholder="Ingresa el nombre del producto,marca o código del producto" name="input_codigo" autofocus="autofocus" onkeyup="buscar_codigo()" id="input_codigo" maxlength="30">
				</div>
			</div>

			<p class="has-text-centered">
				<button type="button" class="button is-link is-light" onclick="buscar_codigo()"><i class="fas fa-search"></i> &nbsp; Buscar</button>
			</p>
		</section>
	</div>
</div>