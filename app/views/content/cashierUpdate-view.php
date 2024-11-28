<div class="container is-fluid mb-6">
	<h1 class="title">Cajas</h1>
	<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar caja</h2>
</div>

<div class="container is-fluid pb-6">
	<?php

	include "./app/views/inc/btn_back.php";

	$id = $insLogin->limpiarCadena($url[1]);

	$datos = $insLogin->seleccionarDatos("Unico", "caja", "id_caja", $id);

	if ($datos->rowCount() == 1) {
		$datos = $datos->fetch();
	?>

		<h2 class="title has-text-centered"><?php echo $datos['nombre'] . " #" . $datos['numero']; ?></h2>

		<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/cajaAjax.php" method="POST" autocomplete="off">

			<input type="hidden" name="modulo_caja" value="actualizar">
			<input type="hidden" name="id_caja" value="<?php echo $datos['id_caja']; ?>">

			<div class="columns">
				<div class="column">
					<div class="control">
						<label>Numero de caja <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" name="numero" pattern="[0-9]{1,5}" maxlength="5" value="<?php echo $datos['numero']; ?>" required>
					</div>
				</div>
				<div class="column">
					<div class="control">
						<label>Nombre o código de caja <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" name="nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ:# ]{3,70}" maxlength="70" value="<?php echo $datos['nombre']; ?>" required>
					</div>
				</div>

			</div>
			<p class="has-text-centered">
				<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
			</p>
			<p class="has-text-centered pt-6">
				<small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
			</p>
		</form>
	<?php
	} else {
		include "./app/views/inc/error_alert.php";
	}
	?>
</div>