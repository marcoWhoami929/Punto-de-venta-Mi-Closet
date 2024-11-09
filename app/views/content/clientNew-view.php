<div class="container is-fluid mb-6">
	<h1 class="title">Clientes</h1>
	<h2 class="subtitle"><i class="fas fa-male fa-fw"></i> &nbsp; Nuevo cliente</h2>
</div>

<div class="container is-fluid pb-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off">

		<input type="hidden" name="modulo_cliente" value="registrar">

		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Tipo de Usuario <?php echo CAMPO_OBLIGATORIO; ?></label><br>
					<div class="select">
						<select name="tipo_cliente">
							<option value="" selected="">Seleccione una opción</option>
							<?php
							echo $insLogin->generarSelect(TIPO_USUARIOS, "VACIO");
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Usuario Facebook</label>
					<input class="input" type="text" name="facebook" pattern="[a-zA-Z0-9-]{7,30}" maxlength="30">
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="80" required>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="apellidos" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="80" required>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Nombre Usuario <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="usuario" maxlength="120" required>
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Email</label>
					<input class="input" type="email" name="email" maxlength="70">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Contraseña <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="password" name="password" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required>
				</div>
			</div>

		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Teléfono</label>
					<input class="input" type="tel" name="telefono" placeholder="1234567891" pattern="[0-9()+]{8,20}" maxlength="20">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Celular <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="tel" name="celular" placeholder="1234567891" pattern="[0-9()+]{8,20}" maxlength="20" required>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Crédito Cliente</label>
					<input class="input" type="text" name="credito" pattern="[0-9.]{1,25}" maxlength="25" value="0.00">
				</div>
			</div>


		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Domicilio <?php echo CAMPO_OBLIGATORIO; ?></label>
					<input class="input" type="text" name="domicilio" required>
				</div>
			</div>
		</div>
		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
			<button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
		</p>
		<p class="has-text-centered pt-6">
			<small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
		</p>
	</form>
</div>