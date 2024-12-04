<div class="container is-fluid mb-6">
	<h1 class="title">Cajas</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de cajas</h2>
</div>
<div class="container is-fluid pb-2 pt-2">

	<div class="columns">
		<div class="column is-half">
			<label></label><br>
			<div class="field is-grouped">
				<input type="hidden" id="pagina" value="<?= $url[1] ?>">
				<input type="hidden" id="url" value="<?= $url[0] ?>">
				<p class="control is-expanded">
					<input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué estas buscando?" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30" onkeyup="listarCajas()">
				</p>
				<p class="control">
					<button class="button is-info" type="button" onclick="listarCajas()">Buscar</button>
				</p>
			</div>
		</div>
		<div class="column">
			<label>Mostrar</label><br>
			<div class="select">
				<select id="per_page" onchange="listarCajas()">
					<option value="15">15</option>
					<option value="50">50</option>
					<option value="100">100</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Ordenar por</label><br>
			<div class="select">
				<select id="campoOrden" onchange="listarCajas()">
					<option value="nombre">Nombre</option>
					<option value="numero">Numero</option>
					<option value="estado">Estado</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Orden</label><br>
			<div class="select">
				<select id="orden" onchange="listarCajas()">
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
			<div class="column div-cashier">

			</div>
		</div>
	</div>
</div>