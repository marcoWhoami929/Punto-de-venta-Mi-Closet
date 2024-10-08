<div class="container is-fluid mb-6">
	<h1 class="title">Clientes</h1>
	<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar cliente</h2>
</div>

<div class="container pb-6 pt-6">
	<?php

	include "./app/views/inc/btn_back.php";

	$id = $insLogin->limpiarCadena($url[1]);

	$datos = $insLogin->seleccionarDatos("Unico", "cliente", "id_cliente", $id);

	if ($datos->rowCount() == 1) {
		$datos = $datos->fetch();
	?>

		<h2 class="title has-text-centered"><?php echo $datos['nombre'] . " " . $datos['apellidos'] . ""; ?></h2>

		<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off">

			<input type="hidden" name="modulo_cliente" value="actualizar">
			<input type="hidden" name="id_cliente" value="<?php echo $datos['id_cliente']; ?>">
			<div class="columns">
			<div class="column">
				<div class="control">
					<label>Tipo de Usuario <?php echo CAMPO_OBLIGATORIO; ?></label><br>
					<div class="select">
						<select name="tipo_cliente">
							<option value="" selected="">Seleccione una opción</option>
							<?php
							echo $insLogin->generarSelect(TIPO_USUARIOS,  $datos['tipo_cliente']);
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Usuario Facebook</label>
					<input class="input" type="text" name="facebook" value="<?php echo $datos['facebook']; ?>" pattern="[a-zA-Z0-9-]{7,30}" maxlength="30">
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="nombre" value="<?php echo $datos['nombre']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="80" required>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="apellidos" value="<?php echo $datos['apellidos']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="80" required>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Nombre Usuario <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="usuario" value="<?php echo $datos['usuario']; ?>" maxlength="120" required>
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Email</label>
					<input class="input" type="email" name="email" value="<?php echo $datos['email']; ?>" maxlength="70">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Teléfono</label>
					<input class="input" type="tel" name="telefono" value="<?php echo $datos['telefono']; ?>" placeholder="1234567891" pattern="[0-9()+]{8,20}" maxlength="20">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Celular <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="tel" name="celular" value="<?php echo $datos['celular']; ?>" placeholder="1234567891" pattern="[0-9()+]{8,20}" maxlength="20" required>
				</div>
			</div>

		</div>
		<div class="columns">
			
			<div class="column">
				<div class="control">
					<label>Crédito Cliente</label>
					<input class="input" type="text" name="credito" value="<?php echo $datos['credito']; ?>" pattern="[0-9.]{1,25}" maxlength="25" >
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Domicilio <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="domicilio" value="<?php echo $datos['domicilio']; ?>" required>
				</div>
			</div>

		</div>
		<div class="columns">
			
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