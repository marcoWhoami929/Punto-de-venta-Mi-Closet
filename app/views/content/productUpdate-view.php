<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar producto</h2>
</div>

<div class="container  is-fluid pb-6">
	<?php

	include "./app/views/inc/btn_back.php";

	$id = $insLogin->limpiarCadena($url[1]);

	$datos = $insLogin->seleccionarDatos("Unico", "producto", "id_producto", $id);

	if ($datos->rowCount() == 1) {
		$datos = $datos->fetch();
	?>

		<div class="columns is-flex is-justify-content-center">
			<svg id="barras">
				<script>
					JsBarcode("#barras", "<?= $datos['codigo'] ?>", {
						format: "CODE128"
					});
				</script>
			</svg>
			<!--
			<figure class="full-width mb-3" style="max-width: 170px;">
				<?php
				if (is_file("./app/views/productos/" . $datos['foto'])) {
					echo '<img class="img-responsive" src="' . APP_URL . 'app/views/productos/' . $datos['foto'] . '">';
				} else {
					echo '<img class="img-responsive" src="' . APP_URL . 'app/views/productos/default.png">';
				}
				?>
			</figure>
			-->
		</div>

		<h2 class="title has-text-centered"><?php echo $datos['nombre'] . " (Stock: " . $datos['stock_total'] . " " . $datos['tipo_unidad'] . ")"; ?></h2>

		<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off">

			<input type="hidden" name="modulo_producto" value="actualizar">
			<input type="hidden" name="id_producto" value="<?php echo $datos['id_producto']; ?>">

			<div class="columns">
				<div class="column">
					<div class="control">
						<label>Código de barra <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" name="codigo" value="<?php echo $datos['codigo']; ?>" pattern="[a-zA-Z0-9- ]{1,77}" maxlength="77">
					</div>
				</div>
				<div class="column">
					<div class="control">
						<label>Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" name="nombre" value="<?php echo $datos['nombre']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,100}" maxlength="100" required>
					</div>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<div class="control">
						<label>Precio de compra <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" name="precio_compra" value="<?php echo $datos['precio_compra']; ?>" pattern="[0-9.]{1,25}" maxlength="25" value="0.00" required>
					</div>
				</div>
				<div class="column">
					<div class="control">
						<label>Precio de venta <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" name="precio_venta" value="<?php echo $datos['precio_venta']; ?>" pattern="[0-9.]{1,25}" maxlength="25" value="0.00" required>
					</div>
				</div>
				<div class="column">
					<div class="control">
						<label>Stock o existencias <?php echo CAMPO_OBLIGATORIO; ?></label>
						<input class="input" type="text" name="producto_stock" value="<?php echo $datos['stock_total']; ?>" maxlength="22" readonly>
					</div>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<div class="control">
						<label>Marca</label>
						<input class="input" type="text" name="marca" value="<?php echo $datos['marca']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}" maxlength="30">
					</div>
				</div>
				<div class="column">
					<div class="control">
						<label>Modelo</label>
						<input class="input" type="text" name="modelo" value="<?php echo $datos['modelo']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}" maxlength="30">
					</div>
				</div>
				<div class="column">
					<div class="control">
						<label>Presentación del producto <?php echo CAMPO_OBLIGATORIO; ?></label><br>
						<div class="select">
							<select name="producto_unidad">
								<?php
								echo $insLogin->generarSelect(PRODUCTO_UNIDAD, $datos['tipo_unidad']);
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="column">
					<label>Categoría <?php echo CAMPO_OBLIGATORIO; ?></label><br>
					<div class="select">
						<select name="producto_categoria">
							<?php
							$datos_categorias = $insLogin->seleccionarDatos("Normal", "categoria", "*", 0);

							$cc = 1;
							while ($campos_categoria = $datos_categorias->fetch()) {
								if ($campos_categoria['id_categoria'] == $datos['id_categoria']) {
									echo '<option value="' . $campos_categoria['id_categoria'] . '" selected="" >' . $cc . ' - ' . $campos_categoria['nombre'] . ' (Actual)</option>';
								} else {
									echo '<option value="' . $campos_categoria['id_categoria'] . '">' . $cc . ' - ' . $campos_categoria['nombre'] . '</option>';
								}
								$cc++;
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<div class="control">
						<label>Colores</label>

						<input class="input" type="tags" id="colores" name="colores" value="<?php echo $datos['colores']; ?>" placeholder="Escribir colores disponibles">

					</div>
				</div>
				<div class="column">
					<div class="control">
						<label>Tallas</label>
						<input class="input" type="tags" id="tallas" name="tallas" value="<?php echo $datos['tallas']; ?>" placeholder="Escribir tallas disponibles">
					</div>
				</div>


			</div>
			<div class="columns  pt-6">
				<div class="column">
					<p class="has-text-centered">
						<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
					</p>
					<p class="has-text-centered pt-6">
						<small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
					</p>
				</div>


			</div>

		</form>
	<?php
	} else {
		include "./app/views/inc/error_alert.php";
	}
	?>
</div>
<style>
	#barras {

		width: 300px;
		border-radius: 10px;
		margin: 10px 0px;
	}
</style>
<script>
	bulmaTagsinput.attach();
</script>