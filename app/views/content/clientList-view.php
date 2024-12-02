<div class="container is-fluid mb-6">
	<h1 class="title">Clientes</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de clientes</h2>
</div>
<div class="container is-fluid pb-2 pt-2">

	<div class="columns">
		<div class="column is-half">
			<label></label><br>
			<div class="field is-grouped">
				<input type="hidden" id="pagina" value="<?= $url[1] ?>">
				<input type="hidden" id="url" value="<?= $url[0] ?>">
				<p class="control is-expanded">
					<input class="input is-rounded" type="text" id="busqueda" placeholder="¿Qué cliente estás buscando?" onkeyup="listarClientes()">
				</p>
				<p class="control">
					<button class="button is-info" type="button" onclick="listarClientes()">Actualizar</button>
				</p>
			</div>
		</div>
		<div class="column">
			<label>Mostrar</label><br>
			<div class="select">
				<select id="per_page" onchange="listarClientes()">
					<option value="15">15</option>
					<option value="50">50</option>
					<option value="100">100</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Filtro Estatus</label><br>
			<div class="select">
				<select id="estatus" onchange="listarClientes()">
					<option value="">Todos</option>
					<option value="1">Activo</option>
					<option value="0">Inactivo</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Ordenar por</label><br>
			<div class="select">
				<select id="campoOrden" onchange="listarClientes()">
					<option value="id_cliente">Id</option>
					<option value="nombre">Nombre</option>
					<option value="fecha_registro">Fecha Registro</option>

				</select>
			</div>
		</div>
		<div class="column">
			<label>Orden</label><br>
			<div class="select">
				<select id="orden" onchange="listarClientes()">
					<option value="asc">Asc</option>
					<option value="desc">Desc</option>

				</select>
			</div>
		</div>
	</div>
</div>
<div class="container is-fluid pb-2 pt-2 ">
	<div class="columns">
		<div class="column div-clientes">

		</div>
	</div>
</div>