<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de Ventas</h2>
</div>

<div class="container is-fluid pb-2 pt-2">

	<div class="columns">
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

	</div>
	<div class="columns">

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