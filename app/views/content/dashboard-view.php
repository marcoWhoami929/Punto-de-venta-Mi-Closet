<div class="container is-fluid">
	<h1 class="title">Home</h1>
	<div class="columns is-flex is-justify-content-center">
		<figure class="image is-128x128">
			<?php
			if (is_file("./app/views/fotos/" . $_SESSION['foto'])) {
				echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/' . $_SESSION['foto'] . '">';
			} else {
				echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/default.png">';
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

<div class="container pb-6 pt-6">

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
<script>
	//--------------------------------------------------
	const ctx = document.getElementById('myChart');
	new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
			datasets: [{
				label: '# of Votes',
				data: [12, 19, 3, 5, 2, 3],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	//--------------------------------------------------
	const ctx2 = document.getElementById('myChart2');
	Chart.defaults.datasets.line.showLine = true;
	const data = {
		labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
		datasets: [{
			label: 'Looping tension',
			data: [65, 59, 80, 81, 26, 55, 40],
			fill: false,
			borderColor: 'rgb(75, 192, 192)',
		}]
	};
	const chart = new Chart(ctx2, {
		type: 'line',
		data: data,
		options: {
			animations: {
				tension: {
					duration: 1000,
					easing: 'linear',
					from: 1,
					to: 0,
					loop: true
				}
			},
			scales: {
				y: {
					min: 0,
					max: 100
				}
			}
		}
	});
	//--------------------------------------------------
	const ctx3 = document.getElementById('myChart3');
	new Chart(ctx3, {
		type: 'doughnut',
		data: {
			labels: [
				'Red',
				'Blue',
				'Yellow'
			],
			datasets: [{
				label: 'My First Dataset',
				data: [300, 50, 100],
				backgroundColor: [
					'rgb(255, 99, 132)',
					'rgb(54, 162, 235)',
					'rgb(255, 205, 86)'
				],
				hoverOffset: 4
			}]
		},
	});
	//--------------------------------------------------
	const ctx4 = document.getElementById('myChart4');
	new Chart(ctx4, {
		type: 'scatter',
		data: {
			labels: [
				'January',
				'February',
				'March',
				'April'
			],
			datasets: [{
				type: 'bar',
				label: 'Bar Dataset',
				data: [10, 20, 30, 40],
				borderColor: 'rgb(255, 99, 132)',
				backgroundColor: 'rgba(255, 99, 132, 0.2)'
			}, {
				type: 'line',
				label: 'Line Dataset',
				data: [50, 50, 50, 50],
				fill: false,
				borderColor: 'rgb(54, 162, 235)'
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
</script>