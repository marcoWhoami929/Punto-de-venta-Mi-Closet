<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de productos</h2>
</div>
<div class="container is-fluid pb-2 pt-2">

	<div class="columns">
		<div class="column is-two-fifths">
			<label></label><br>
			<div class="field is-grouped">
				<input type="hidden" id="pagina" value="<?= $url[1] ?>">
				<input type="hidden" id="url" value="<?= $url[0] ?>">
				<p class="control is-expanded">
					<input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué producto estás buscando?" onkeyup="listarProductos()">
				</p>
				<p class="control">
					<button class="button is-info" type="button" onclick="generarReporte('productos')"><i class="fas fa-file-excel"></i></button>
				</p>
			</div>
		</div>
		<div class="column">
			<form method="post" id="form_load_productos">
				<label></label><br>
				<div class="file is-info has-name">
					<label class="file-label">
						<input class="file-input" type="file" name="load_productos" id="load_productos" />
						<span class="file-cta">
							<span class="file-icon">
								<i class="fas fa-upload"></i>
							</span>
							<span class="file-label">Subir Productos </span>
						</span>
						<span class="file-name"></span>
					</label>
				</div>
			</form>
		</div>
		<div class="column">
			<label>Filtro Estatus</label><br>
			<div class="select">
				<select id="estatus" onchange="listarProductos()">
					<option value="">Todos</option>
					<option value="1">Activo</option>
					<option value="0">Inactivo</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Mostrar</label><br>
			<div class="select">
				<select id="per_page" onchange="listarProductos()">
					<option value="15">15</option>
					<option value="50">50</option>
					<option value="100">100</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Ordenar por</label><br>
			<div class="select">
				<select id="campoOrden" onchange="listarProductos()">
					<option value="prod.nombre">Nombre</option>
					<option value="inven.stock_total">Stock</option>
					<option value="prod.cid_producto">Id Producto</option>
					<option value="prod.precio_venta">Precio</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Orden</label><br>
			<div class="select">
				<select id="orden" onchange="listarProductos()">
					<option value="asc">Asc</option>
					<option value="desc">Desc</option>

				</select>
			</div>
		</div>
	</div>
</div>
<div class="container is-fluid pb-2 pt-2">

	<div class="columns is-multiline">
		<div class="column ">
			<div class="card">
				<div class="column div-productos">

				</div>

			</div>
		</div>
	</div>
</div>
<button class="js-modal-trigger" data-target="modal-entrada-inventario" id="btn-modal-entrada" style="display:none"></button>
<button class="js-modal-trigger" data-target="modal-salida-inventario" id="btn-modal-salida" style="display:none"></button>
<script>
	$(document).ready(function() {
		$('#load_productos').change(function() {
			$('#form_load_productos').submit();
		});
		$('#form_load_productos').on('submit', function(event) {
			event.preventDefault();

			$.ajax({
				url: url + "app/ajax/importProductos.php",
				method: "POST",
				data: new FormData(this),
				contentType: false,
				processData: false,
				success: function(data) {
					if (data == "exito") {
						Swal.fire({
							icon: "success",
							title: "Alta de productos exitosa",
							text: "",
							confirmButtonText: "Entendido",
						}).then((result) => {
							if (result.isConfirmed) {
								listarProductos(1);
							}
						});
					}

				}
			});
		});
	});
</script>