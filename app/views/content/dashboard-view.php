<div class="container is-fluid">
	<h1 class="title">Tablero</h1>
	<div class="columns is-flex is-justify-content-center">
		<figure class="image is-128x128">
			<?php
			if (is_file("./app/views/fotos/" . $_SESSION['foto'])) {
				echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/' . $_SESSION['foto'] . '">';
			} else {
				echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/default.png">';
			}


			if (isset($_SESSION["sesion_caja"])) {
				echo '<script>localStorage.setItem("session_caja", "abierta");</script>';
			}

			?>
		</figure>
	</div>
	<div class="columns is-flex is-justify-content-center">
		<h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre'] ?>!</h2>
	</div>
</div>
<?php
$total_cajas = $insLogin->seleccionarDatos("Normal", "caja", "id_caja", 0);

$total_usuarios = $insLogin->seleccionarDatos("Normal", "usuario WHERE id_usuario!='1' AND id_usuario!='" . $_SESSION['id'] . "'", "id_usuario", 0);

$total_clientes = $insLogin->seleccionarDatos("Normal", "cliente WHERE id_cliente!='1'", "id_cliente", 0);

$total_categorias = $insLogin->seleccionarDatos("Normal", "categoria", "id_categoria", 0);

$total_productos = $insLogin->seleccionarDatos("Normal", "producto", "id_producto", 0);

$total_ventas = $insLogin->seleccionarDatos("Normal", "venta", "id_venta", 0);
?>
<div class="container container is-widescreen pb-2 pt-6">
	<div class="columns is-multiline">
		<div class="column is-two-quarter-desktop is-half-tablet">
			<div class="card">
				<canvas id="myChart"></canvas>
			</div>
		</div>
		<div class="column is-two-quarter-desktop is-half-tablet">
			<div class="card">
				<canvas id="myChart2"></canvas>
			</div>
		</div>

	</div>
</div>
<div class="container pb-2 pt-6">
	<div class="columns is-multiline">
		<div class="column is-two-quarter-desktop is-half-tablet">
			<div class="card">
				<canvas id="myChart3"></canvas>
			</div>
		</div>
		<div class="column is-two-quarter-desktop is-half-tablet">
			<div class="card">
				<canvas id="myChart4"></canvas>
			</div>
		</div>

	</div>
</div>

<div class="container is-fluid pb-6">

	<div class="columns pb-6">
		<div class="column">
			<nav class="level is-mobile">
				<div class="level-item has-text-centered">
					<a href="<?php echo APP_URL; ?>cashierList/">
						<p class="heading"><i class="fas fa-cash-register fa-fw"></i> &nbsp; Cajas</p>
						<p class="title"><?php echo $total_cajas->rowCount(); ?></p>
					</a>
				</div>
				<div class="level-item has-text-centered">
					<a href="<?php echo APP_URL; ?>userList/">
						<p class="heading"><i class="fas fa-users fa-fw"></i> &nbsp; Usuarios</p>
						<p class="title"><?php echo $total_usuarios->rowCount(); ?></p>
					</a>
				</div>
				<div class="level-item has-text-centered">
					<a href="<?php echo APP_URL; ?>clientList/">
						<p class="heading"><i class="fas fa-address-book fa-fw"></i> &nbsp; Clientes</p>
						<p class="title"><?php echo $total_clientes->rowCount(); ?></p>
					</a>
				</div>
			</nav>
		</div>
	</div>

	<div class="columns pt-6">
		<div class="column">
			<nav class="level is-mobile">
				<div class="level-item has-text-centered">
					<a href="<?php echo APP_URL; ?>categoryList/">
						<p class="heading"><i class="fas fa-tags fa-fw"></i> &nbsp; Categorías</p>
						<p class="title"><?php echo $total_categorias->rowCount(); ?></p>
					</a>
				</div>
				<div class="level-item has-text-centered">
					<a href="<?php echo APP_URL; ?>productList/">
						<p class="heading"><i class="fas fa-cubes fa-fw"></i> &nbsp; Productos</p>
						<p class="title"><?php echo $total_productos->rowCount(); ?></p>
					</a>
				</div>
				<div class="level-item has-text-centered">
					<a href="<?php echo APP_URL; ?>saleList/">
						<p class="heading"><i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Ventas</p>
						<p class="title"><?php echo $total_ventas->rowCount(); ?></p>
					</a>
				</div>
			</nav>
		</div>
	</div>

</div>
<button class="js-modal-trigger" data-target="modal-apertura-caja" id="btn-apertura-caja" style="display:none">

</button>
<div class="modal fade is-large" id="modal-apertura-caja" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-background"></div>
	<div class="modal-card">
		<header class="modal-card-head" style="background:#B99654">
			<p class="modal-card-title is-uppercase" style="color:#ffffff">
				<i class="fas fa-cash-register"></i> &nbsp; Apertura de Caja
			</p>
			<button class="delete" aria-label="close"></button>
		</header>
		<section class="modal-card-body">

			<div class="columns">
				<div class="column">
					<div class="field">
						<label class="label">Efectivo de apertura:</label>
						<div class="control has-icons-left has-icons-right">
							<input class="input is-large" type="text" placeholder="0.00" style="font-size:50px;font-weight:bold" id="saldo_inicial" value="0.00" />
							<span class="icon is-medium is-left " style="margin-top:20px">
								<i class="fas fa-dollar-sign fa-3x"></i>
							</span>

						</div>
					</div>
				</div>

			</div>

			<div class="columns  mb-6">
				<div class="column">
					<div class="field">
						<label class="label">Notas de apertura</label>
						<div class="control has-icons-left has-icons-right">
							<textarea class="textarea is-large" type="text" placeholder="Notas de apertura de caja" style="font-size:12px;font-weight:bold" id="notas_apertura"></textarea>


						</div>
					</div>
				</div>

			</div>
			<div class="columns">
				<div class="column">
					<p class="has-text-centered">
						<button type="button" class="button is-success button-lg" onclick="aperturarCaja()"><i class="fas fa-cash-register"></i> &nbsp; Abrir Caja</button>
					</p>
				</div>
			</div>



		</section>
	</div>
</div>