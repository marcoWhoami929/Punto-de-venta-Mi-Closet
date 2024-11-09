<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle"><i class="far fa-image"></i> &nbsp; Actualizar foto de producto</h2>
</div>
<div class="container is-fluid pb-6">
	<?php

	include "./app/views/inc/btn_back.php";

	$id = $insLogin->limpiarCadena($url[1]);

	$datos = $insLogin->seleccionarDatos("Unico", "producto", "id_producto", $id);

	if ($datos->rowCount() == 1) {
		$datos = $datos->fetch();
	?>

		<h2 class="title has-text-centered has-text-link"><?php echo $datos['nombre']; ?></h2>

		<div class="columns">
			<div class="column is-two-fifths">
				<h4 class="subtitle is-4 has-text-centered pb-6">Foto actual del producto</h4>
				<?php if (is_file("./app/views/productos/" . $datos['foto'])) { ?>
					<figure class="image mb-6">
						<img class="is-photo" src="<?php echo APP_URL; ?>app/views/productos/<?php echo $datos['foto']; ?>">
					</figure>

					<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off">

						<input type="hidden" name="modulo_producto" value="eliminarFoto">
						<input type="hidden" name="id_producto" value="<?php echo $datos['id_producto']; ?>">

						<p class="has-text-centered">
							<button type="submit" class="button is-danger is-rounded"><i class="far fa-trash-alt"></i> &nbsp; Eliminar foto</button>
						</p>
					</form>
				<?php } else { ?>
					<figure class="image mb-6">
						<img class="is-photo" src="<?php echo APP_URL; ?>app/views/productos/default.png">
					</figure>
				<?php } ?>
			</div>


			<div class="column">
				<h4 class="subtitle is-4 has-text-centered pb-6">Actualizar foto de producto</h4>
				<form class="mb-6 has-text-centered FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" enctype="multipart/form-data" autocomplete="off">

					<input type="hidden" name="modulo_producto" value="actualizarFoto">
					<input type="hidden" name="id_producto" value="<?php echo $datos['id_producto']; ?>">

					<label>Foto o imagen del producto</label><br>

					<div class="file has-name is-boxed is-justify-content-center mb-6">
						<label class="file-label">
							<input class="file-input" type="file" name="foto" accept=".jpg, .png, .jpeg">
							<span class="file-cta">
								<span class="file-label">
									Seleccione una foto
								</span>
							</span>
							<span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
						</label>
					</div>
					<p class="has-text-centered">
						<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar foto</button>
					</p>
				</form>
			</div>
		</div>
	<?php
	} else {
		include "./app/views/inc/error_alert.php";
	}
	?>
</div>