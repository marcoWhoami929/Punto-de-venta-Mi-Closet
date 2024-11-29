<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de productos</h2>
</div>
<div class="container is-fluid pb-6">

	<div class="form-rest mb-6 mt-6"></div>

	<?php

	use app\controllers\productController;

	$insProducto = new productController();

	echo $insProducto->listarProductoControlador($url[1], 10, $url[0], "", 0);
	?>
</div>
<div class="modal fade is-large" id="modal-abastecer-inventario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-background"></div>
	<div class="modal-card ">
		<header class="modal-card-head" style="background:#B99654">
			<p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cash-register"></i> &nbsp; Abastecer Inventario</p>
			<button class="delete" aria-label="close" id="btn-close-pago"></button>
		</header>
		<section class="modal-card-body">

			<div class="columns  mb-6">
				<div class="column">
					<div class="field">
						<label class="label">Unidades a Ingresar:</label>
						<div class="control has-icons-left has-icons-right">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:50px;font-weight:bold" id="unidades_inventario" />
							<span class="icon is-medium is-left " style="margin-top:20px">
								<i class="fas fa-dollar-sign fa-3x"></i>
							</span>

						</div>
					</div>
				</div>

			</div>

			<div class="columns  mb-6" style="display:none" id="div-payment-transferencia">
				<div class="column">
					<div class="field">
						<label class="label">Referencia Pago</label>
						<div class="control">

							<textarea class="textarea is-large" type="text" placeholder="Capturar descripción del movimiento" style="font-size:12px;font-weight:bold" id="descripcion_inventario"></textarea>


						</div>
					</div>
				</div>

			</div>
			<div class="columns">
				<div class="column">
					<p class="has-text-centered">
						<button type="button" class="button is-success button-lg" onclick="registrarVenta()"><i class="fas fa-money-check-alt"></i> &nbsp; Confirmar Pago</button>
					</p>
				</div>
			</div>



		</section>
	</div>
</div>