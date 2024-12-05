<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de Ventas</h2>
</div>

<div class="container is-fluid pb-2 pt-2">
	<button class="button is-danger is-light js-modal-trigger" data-target="modal-pago-venta" id="btn-modal-pago" style="display:none">Sin Pagar</button>
	<div class="columns ">
		<div class="column is-half">
			<label></label><br>
			<div class="field is-grouped">
				<input type="hidden" id="pagina" value="<?= $url[1] ?>">
				<input type="hidden" id="url" value="<?= $url[0] ?>">
				<p class="control is-expanded">
					<input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué venta estás buscando?" onkeyup="listarVentas()">
				</p>
				<p class="control">
					<button class="button is-info" type="button" onclick="listarVentas()">Actualizar</button>
				</p>
			</div>
		</div>
		<div class="column">
			<label>Mostrar</label><br>
			<div class="select">
				<select id="per_page" onchange="listarVentas()">
					<option value="15">15</option>
					<option value="50">50</option>
					<option value="100">100</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Filtro Tipo Venta</label><br>
			<div class="select">
				<select id="tipo_venta" onchange="listarVentas()">
					<option value="">Todos</option>
					<option value="directa">Directa</option>
					<option value="nota">Nota</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Filtro Forma Pago</label><br>
			<div class="select">
				<select id="forma_pago" onchange="listarVentas()">
					<option value="">Todos</option>
					<option value="1">Efectivo</option>
					<option value="2">Transferencia Electrónica</option>
					<option value="3">Tarjeta de Crédito</option>
					<option value="4">Tarjeta de Débito</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Filtro Pago</label><br>
			<div class="select">
				<select id="estatus_pago" onchange="listarVentas()">
					<option value="">Todos</option>
					<option value="1">Pagado</option>
					<option value="0">Sin Pagar</option>

				</select>
			</div>
		</div>

	</div>
	<div class="columns">


		<div class="column">
			<label>Filtro Tipo Entrega</label><br>
			<div class="select">
				<select id="tipo_entrega" onchange="listarVentas()">
					<option value="">Todos</option>
					<option value="recoleccion">Recolección</option>
					<option value="envio">Envio</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Ordenar por</label><br>
			<div class="select">
				<select id="campoOrden" onchange="listarVentas()">
					<option value="codigo">Codigo</option>
					<option value="total">Total</option>
					<option value="fecha_registro">Fecha Registro</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Orden</label><br>
			<div class="select">
				<select id="orden" onchange="listarVentas()">
					<option value="asc">Asc</option>
					<option value="desc">Desc</option>

				</select>
			</div>
		</div>
	</div>
</div>
<div class="container is-fluid pb-2 pt-2 ">
	<div class="columns">
		<div class="column div-ventas">

		</div>
	</div>
</div>
<div class="modal fade is-large" id="modal-pago-venta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-background"></div>
	<div class="modal-card">
		<header class="modal-card-head" style="background:#B99654">
			<p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cashier"></i> &nbsp; Confirmar Pago</p>
			<button class="delete" aria-label="close"></button>
		</header>
		<section class="modal-card-body">
			<div class="columns">
				<div class="column">
					<div class="field mt-2 mb-2">
						<label class="label">Elegir Forma de Pago</label>
						<div class="control has-text-centered">
							<div class="select is-primary  is-rounded is-large">
								<select onchange="eleccionFormaPago(0)" id="forma_pago_venta">
									<option value="1">Efectivo</option>
									<option value="2">Transferencia Electrónica</option>
									<option value="3">Tarjeta de Crédito</option>
									<option value="4">Tarjeta de Débito</option>
									<option value="5">Crédito</option>

								</select>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="columns  mb-6">
				<div class="column">
					<div class="field">
						<label class="label">Total a Pagar:</label>
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:50px;font-weight:bold" id="total_pagar_venta" disabled />
							<span class="icon is-medium is-left " style="margin-top:20px">
								<i class="fas fa-dollar-sign fa-3x"></i>
							</span>

						</div>
					</div>
				</div>

			</div>
			<div class="columns  mb-6" id="div-payment-efectivo-1">
				<div class="column">
					<div class="field">
						<label class="label">Su Pago</label>
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:40px;font-weight:bold" id="total_pagado_venta" onkeyup="calcularCambio()" value="0.00" />
							<span class="icon is-medium is-left " style="margin-top:20px">
								<i class="fas fa-dollar-sign fa-3x"></i>
							</span>

						</div>
					</div>
				</div>

			</div>
			<div class="columns  mb-6" id="div-payment-efectivo-2">

				<div class="column">
					<div class="field">
						<label class="label">Cambio</label>
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:40px;font-weight:bold" id="total_cambio_venta" disabled value="0.00" />
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
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="Capturar referencia de pago" style="font-size:20px;font-weight:bold" id="referencia_venta" />
							<span class="icon is-medium is-left">

								<i class="fas fa-receipt"></i>
							</span>

						</div>
					</div>
				</div>

			</div>
			<div class="columns">
				<div class="column">
					<p class="has-text-centered">
						<button type="button" class="button is-success button-lg" onclick="confirmacionPago()"><i class="fas fa-money-check-alt"></i> &nbsp; Confirmar Pago</button>
					</p>
				</div>
			</div>



		</section>
	</div>
</div>
<div class="modal fade is-large" id="modal-detalle-pago" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-background"></div>
	<div class="modal-card ">
		<header class="modal-card-head" style="background:#B99654">
			<p class="modal-card-title is-uppercase" style="color:#ffffff"><i class="fas fa-cash-register"></i> &nbsp; Detalle Pago</p>
			<button class="delete" aria-label="close"></button>
		</header>
		<section class="modal-card-body">
			<div class="columns">
				<div class="column">
					<div class="field mt-2 mb-2">
						<label class="label">Forma de Pago</label>
						<div class="control has-text-centered">
							<div class="select is-primary  is-rounded is-large">
								<select id="forma_pago_venta">
								</select>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="columns  mb-6">
				<div class="column">
					<div class="field">
						<label class="label">Total a Pagar:</label>
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:50px;font-weight:bold" id="total_pagar_venta" disabled />
							<span class="icon is-medium is-left " style="margin-top:20px">
								<i class="fas fa-dollar-sign fa-3x"></i>
							</span>

						</div>
					</div>
				</div>

			</div>
			<div class="columns  mb-6" id="div-payment-efectivo-1">
				<div class="column">
					<div class="field">
						<label class="label">Pagado</label>
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:40px;font-weight:bold" id="total_pagado_venta" value="0.00" disabled />
							<span class="icon is-medium is-left " style="margin-top:20px">
								<i class="fas fa-dollar-sign fa-3x"></i>
							</span>

						</div>
					</div>
				</div>

			</div>
			<div class="columns  mb-6" id="div-payment-efectivo-2">

				<div class="column">
					<div class="field">
						<label class="label">Cambio</label>
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:40px;font-weight:bold" id="total_cambio_venta" disabled value="0.00" />
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
						<div class="control has-icons-left">
							<input class="input is-large" type="text" placeholder="Capturar referencia de pago" style="font-size:20px;font-weight:bold" id="referencia_venta" disabled />
							<span class="icon is-medium is-left">

								<i class="fas fa-receipt"></i>
							</span>

						</div>
					</div>
				</div>

			</div>
		</section>
	</div>
</div>
<?php
include "./app/views/inc/print_invoice_script.php";
?>