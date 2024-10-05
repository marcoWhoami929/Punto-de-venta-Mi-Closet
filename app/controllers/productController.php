<?php

namespace app\controllers;

use app\models\mainModel;

class productController extends mainModel
{

	/*----------  Controlador registrar producto  ----------*/
	public function registrarProductoControlador()
	{

		# Almacenando datos#
		$codigo = $this->limpiarCadena($_POST['codigo']);
		$nombre = $this->limpiarCadena($_POST['nombre']);

		$precio_compra = $this->limpiarCadena($_POST['precio_compra']);
		$precio_venta = $this->limpiarCadena($_POST['precio_venta']);
		$stock = $this->limpiarCadena($_POST['producto_stock']);

		$marca = $this->limpiarCadena($_POST['marca']);
		$modelo = $this->limpiarCadena($_POST['modelo']);
		$unidad = $this->limpiarCadena($_POST['producto_unidad']);
		$categoria = $this->limpiarCadena($_POST['producto_categoria']);

		# Verificando campos obligatorios #
		if ($codigo == "" || $nombre == "" || $precio_compra == "" || $precio_venta == "" || $stock == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No has llenado todos los campos que son obligatorios",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando integridad de los datos #
		if ($this->verificarDatos("[a-zA-Z0-9- ]{1,77}", $codigo)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El CODIGO no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,100}", $nombre)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NOMBRE no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9.]{1,25}", $precio_compra)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE COMPRA no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9.]{1,25}", $precio_venta)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE VENTA no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9]{1,22}", $stock)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El STOCK O EXISTENCIAS no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($marca != "") {
			if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}", $marca)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La MARCA no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		if ($modelo != "") {
			if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}", $modelo)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El MODELO no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Comprobando presentacion del producto #
		if (!in_array($unidad, PRODUCTO_UNIDAD)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "La PRESENTACION DEL PRODUCTO no es correcta o no la ha seleccionado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando categoria #
		$check_categoria = $this->ejecutarConsulta("SELECT id_categoria FROM categoria WHERE id_categoria='$categoria'");
		if ($check_categoria->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "La categoría seleccionada no existe en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando stock total o existencias #
		if ($stock <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No puedes registrar un producto con stock o existencias en 0, debes de agregar al menos una unidad",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando precio de compra del producto #
		$precio_compra = number_format($precio_compra, MONEDA_DECIMALES, '.', '');
		if ($precio_compra <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE COMPRA no puede ser menor o igual a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando precio de venta del producto #
		$precio_venta = number_format($precio_venta, MONEDA_DECIMALES, '.', '');
		if ($precio_venta <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE VENTA no puede ser menor o igual a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando precio de compra y venta del producto #
		if ($precio_compra > $precio_venta) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El precio de compra del producto no puede ser mayor al precio de venta",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando codigo de producto #
		$check_codigo = $this->ejecutarConsulta("SELECT codigo FROM producto WHERE codigo='$codigo'");
		if ($check_codigo->rowCount() >= 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El código de producto que ha ingresado ya se encuentra registrado en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando nombre de producto #
		$check_nombre = $this->ejecutarConsulta("SELECT nombre FROM producto WHERE codigo='$codigo' AND nombre='$nombre'");
		if ($check_nombre->rowCount() >= 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Ya existe un producto registrado con el mismo nombre y código de barras",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Directorios de imagenes #
		$img_dir = '../views/productos/';

		# Comprobar si se selecciono una imagen #
		if ($_FILES['foto']['name'] != "" && $_FILES['foto']['size'] > 0) {

			# Creando directorio #
			if (!file_exists($img_dir)) {
				if (!mkdir($img_dir, 0777)) {
					$alerta = [
						"tipo" => "simple",
						"titulo" => "Ocurrió un error inesperado",
						"texto" => "Error al crear el directorio",
						"icono" => "error"
					];
					return json_encode($alerta);
					exit();
				}
			}

			# Verificando formato de imagenes #
			if (mime_content_type($_FILES['foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['foto']['tmp_name']) != "image/png") {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La imagen que ha seleccionado es de un formato no permitido",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}

			# Verificando peso de imagen #
			if (($_FILES['foto']['size'] / 1024) > 5120) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La imagen que ha seleccionado supera el peso permitido",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}

			# Nombre de la foto #
			$foto = $codigo . "_" . rand(0, 100);

			# Extension de la imagen #
			switch (mime_content_type($_FILES['foto']['tmp_name'])) {
				case 'image/jpeg':
					$foto = $foto . ".jpg";
					break;
				case 'image/png':
					$foto = $foto . ".png";
					break;
			}

			chmod($img_dir, 0777);

			# Moviendo imagen al directorio #
			if (!move_uploaded_file($_FILES['foto']['tmp_name'], $img_dir . $foto)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No podemos subir la imagen al sistema en este momento",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		} else {
			$foto = "";
		}

		$producto_datos_reg = [
			[
				"campo_nombre" => "codigo",
				"campo_marcador" => ":Codigo",
				"campo_valor" => $codigo
			],
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "stock_total",
				"campo_marcador" => ":Stock",
				"campo_valor" => $stock
			],
			[
				"campo_nombre" => "tipo_unidad",
				"campo_marcador" => ":Unidad",
				"campo_valor" => $unidad
			],
			[
				"campo_nombre" => "precio_compra",
				"campo_marcador" => ":PrecioCompra",
				"campo_valor" => $precio_compra
			],
			[
				"campo_nombre" => "precio_venta",
				"campo_marcador" => ":PrecioVenta",
				"campo_valor" => $precio_venta
			],
			[
				"campo_nombre" => "marca",
				"campo_marcador" => ":Marca",
				"campo_valor" => $marca
			],
			[
				"campo_nombre" => "modelo",
				"campo_marcador" => ":Modelo",
				"campo_valor" => $modelo
			],
			[
				"campo_nombre" => "estado",
				"campo_marcador" => ":Estado",
				"campo_valor" => "Habilitado"
			],
			[
				"campo_nombre" => "foto",
				"campo_marcador" => ":Foto",
				"campo_valor" => $foto
			],
			[
				"campo_nombre" => "id_categoria",
				"campo_marcador" => ":Categoria",
				"campo_valor" => $categoria
			]
		];

		$registrar_producto = $this->guardarDatos("producto", $producto_datos_reg);

		if ($registrar_producto->rowCount() == 1) {
			$alerta = [
				"tipo" => "limpiar",
				"titulo" => "Producto registrado",
				"texto" => "El producto " . $nombre . " se registro con exito",
				"icono" => "success"
			];
		} else {

			if (is_file($img_dir . $foto)) {
				chmod($img_dir . $foto, 0777);
				unlink($img_dir . $foto);
			}

			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo registrar el producto, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador listar producto  ----------*/
	public function listarProductoControlador($pagina, $registros, $url, $busqueda, $categoria)
	{

		$pagina = $this->limpiarCadena($pagina);
		$registros = $this->limpiarCadena($registros);
		$categoria = $this->limpiarCadena($categoria);

		$url = $this->limpiarCadena($url);
		if ($categoria > 0) {
			$url = APP_URL . $url . "/" . $categoria . "/";
		} else {
			$url = APP_URL . $url . "/";
		}

		$busqueda = $this->limpiarCadena($busqueda);
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$campos = "producto.id_producto,producto.codigo,producto.nombre,stock_total,producto.precio_venta,producto.foto,categoria.nombre";

		if (isset($busqueda) && $busqueda != "") {

			$consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.id_categoria=categoria.id_categoria WHERE codigo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR marca LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%' ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_producto) FROM producto WHERE codigo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR marca LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%'";
		} elseif ($categoria > 0) {

			$consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.id_categoria=categoria.id_categoria WHERE producto.id_categoria='$categoria' ORDER BY producto.nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_producto) FROM producto WHERE id_categoria='$categoria'";
		} else {

			$consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.id_categoria=categoria.id_categoria ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_producto) FROM producto";
		}

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);

		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;
			foreach ($datos as $rows) {
				$tabla .= '
		            <article class="media pb-3 pt-3">
		                <figure class="media-left">
		                    <p class="image is-64x64">';
				if (is_file("./app/views/productos/" . $rows['foto'])) {
					$tabla .= '<img src="' . APP_URL . 'app/views/productos/' . $rows['foto'] . '">';
				} else {
					$tabla .= '<img src="' . APP_URL . 'app/views/productos/default.png">';
				}
				$tabla .= '</p>
		                </figure>
		                <div class="media-content">
		                    <div class="content">
		                        <p>
		                            <strong>' . $contador . ' - ' . $rows['nombre'] . '</strong><br>
		                            <strong>CODIGO:</strong> ' . $rows['codigo'] . ', 
		                            <strong>PRECIO:</strong> $' . $rows['precio_venta'] . ', 
		                            <strong>STOCK:</strong> ' . $rows['stock_total'] . ', 
		                            <strong>CATEGORIA:</strong> ' . $rows['nombre'] . '
		                        </p>
		                    </div>
		                    <div class="has-text-right">
		                        <a href="' . APP_URL . 'productPhoto/' . $rows['id_producto'] . '/" class="button is-info is-rounded is-small">
			                    	<i class="far fa-image fa-fw"></i>
			                    </a>

		                        <a href="' . APP_URL . 'productUpdate/' . $rows['id_producto'] . '/" class="button is-success is-rounded is-small">
		                        	<i class="fas fa-sync fa-fw"></i>
		                        </a>

		                        <form class="FormularioAjax is-inline-block" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_producto" value="eliminar">
			                		<input type="hidden" name="id_producto" value="' . $rows['id_producto'] . '">

			                    	<button type="submit" class="button is-danger is-rounded is-small">
			                    		<i class="far fa-trash-alt fa-fw"></i>
			                    	</button>
			                    </form>
		                    </div>
		                </div>
		            </article>


		            <hr>
		            ';
				$contador++;
			}
			$pag_final = $contador - 1;
		} else {
			if ($total >= 1) {
				$tabla .= '
						<p class="has-text-centered pb-6"><i class="far fa-hand-point-down fa-5x"></i></p>
			            <p class="has-text-centered">
			                <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                    Haga clic acá para recargar el listado
			                </a>
			            </p>
					';
			} else {
				$tabla .= '
						<p class="has-text-centered pb-6"><i class="far fa-grin-beam-sweat fa-5x"></i></p>
						<p class="has-text-centered">No hay productos registrados en esta categoría</p>
					';
			}
		}

		### Paginacion ###
		if ($total > 0 && $pagina <= $numeroPaginas) {
			$tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}


	/*----------  Controlador eliminar producto  ----------*/
	public function eliminarProductoControlador()
	{

		$id = $this->limpiarCadena($_POST['id_producto']);

		# Verificando producto #
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE id_producto='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Verificando ventas #
		$check_ventas = $this->ejecutarConsulta("SELECT id_producto FROM venta_detalle WHERE id_producto='$id' LIMIT 1");
		if ($check_ventas->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar el producto del sistema ya que tiene ventas asociadas",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$eliminarProducto = $this->eliminarRegistro("producto", "id_producto", $id);

		if ($eliminarProducto->rowCount() == 1) {

			if (is_file("../views/productos/" . $datos['foto'])) {
				chmod("../views/productos/" . $datos['foto'], 0777);
				unlink("../views/productos/" . $datos['foto']);
			}

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Producto eliminado",
				"texto" => "El producto '" . $datos['nombre'] . "' ha sido eliminado del sistema correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido eliminar el producto '" . $datos['nombre'] . "' del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador actualizar producto  ----------*/
	public function actualizarProductoControlador()
	{

		$id = $this->limpiarCadena($_POST['id_producto']);

		# Verificando producto #
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE id_producto='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Almacenando datos#
		$codigo = $this->limpiarCadena($_POST['codigo']);
		$nombre = $this->limpiarCadena($_POST['nombre']);

		$precio_compra = $this->limpiarCadena($_POST['precio_compra']);
		$precio_venta = $this->limpiarCadena($_POST['precio_venta']);
		$stock = $this->limpiarCadena($_POST['producto_stock']);

		$marca = $this->limpiarCadena($_POST['marca']);
		$modelo = $this->limpiarCadena($_POST['modelo']);
		$unidad = $this->limpiarCadena($_POST['producto_unidad']);
		$categoria = $this->limpiarCadena($_POST['producto_categoria']);

		# Verificando campos obligatorios #
		if ($codigo == "" || $nombre == "" || $precio_compra == "" || $precio_venta == "" || $stock == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No has llenado todos los campos que son obligatorios",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando integridad de los datos #
		if ($this->verificarDatos("[a-zA-Z0-9- ]{1,77}", $codigo)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El CODIGO no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,100}", $nombre)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NOMBRE no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9.]{1,25}", $precio_compra)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE COMPRA no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9.]{1,25}", $precio_venta)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE VENTA no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9]{1,22}", $stock)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El STOCK O EXISTENCIAS no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($marca != "") {
			if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}", $marca)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La MARCA no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		if ($modelo != "") {
			if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}", $modelo)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El MODELO no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Comprobando presentacion del producto #
		if (!in_array($unidad, PRODUCTO_UNIDAD)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "La PRESENTACION DEL PRODUCTO no es correcta o no la ha seleccionado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando categoria #
		if ($datos['id_categoria'] != $categoria) {
			$check_categoria = $this->ejecutarConsulta("SELECT id_categoria FROM categoria WHERE id_categoria='$categoria'");
			if ($check_categoria->rowCount() <= 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La categoría seleccionada no existe en el sistema",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Verificando stock total o existencias #
		if ($stock <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No puedes registrar un producto con stock o existencias en 0, debes de agregar al menos una unidad",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando precio de compra del producto #
		$precio_compra = number_format($precio_compra, MONEDA_DECIMALES, '.', '');
		if ($precio_compra <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE COMPRA no puede ser menor o igual a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando precio de venta del producto #
		$precio_venta = number_format($precio_venta, MONEDA_DECIMALES, '.', '');
		if ($precio_venta <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El PRECIO DE VENTA no puede ser menor o igual a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando precio de compra y venta del producto #
		if ($precio_compra > $precio_venta) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El precio de compra del producto no puede ser mayor al precio de venta",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando codigo de producto #
		if ($datos['codigo'] != $codigo) {
			$check_codigo = $this->ejecutarConsulta("SELECT codigo FROM producto WHERE codigo='$codigo'");
			if ($check_codigo->rowCount() >= 1) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El código de producto que ha ingresado ya se encuentra registrado en el sistema",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Comprobando nombre de producto #
		if ($datos['nombre'] != $nombre) {
			$check_nombre = $this->ejecutarConsulta("SELECT nombre FROM producto WHERE codigo='$codigo' AND nombre='$nombre'");
			if ($check_nombre->rowCount() >= 1) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Ya existe un producto registrado con el mismo nombre y código de barras",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}


		$producto_datos_up = [
			[
				"campo_nombre" => "codigo",
				"campo_marcador" => ":Codigo",
				"campo_valor" => $codigo
			],
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "stock_total",
				"campo_marcador" => ":Stock",
				"campo_valor" => $stock
			],
			[
				"campo_nombre" => "tipo_unidad",
				"campo_marcador" => ":Unidad",
				"campo_valor" => $unidad
			],
			[
				"campo_nombre" => "precio_compra",
				"campo_marcador" => ":PrecioCompra",
				"campo_valor" => $precio_compra
			],
			[
				"campo_nombre" => "precio_venta",
				"campo_marcador" => ":PrecioVenta",
				"campo_valor" => $precio_venta
			],
			[
				"campo_nombre" => "marca",
				"campo_marcador" => ":Marca",
				"campo_valor" => $marca
			],
			[
				"campo_nombre" => "modelo",
				"campo_marcador" => ":Modelo",
				"campo_valor" => $modelo
			],
			[
				"campo_nombre" => "id_categoria",
				"campo_marcador" => ":Categoria",
				"campo_valor" => $categoria
			]
		];

		$condicion = [
			"condicion_campo" => "id_producto",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Producto actualizado",
				"texto" => "Los datos del producto '" . $datos['nombre'] . "' se actualizaron correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar los datos del producto '" . $datos['nombre'] . "', por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador eliminar foto producto  ----------*/
	public function eliminarFotoProductoControlador()
	{

		$id = $this->limpiarCadena($_POST['id_producto']);

		# Verificando producto #
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE id_producto='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Directorio de imagenes #
		$img_dir = "../views/productos/";

		chmod($img_dir, 0777);

		if (is_file($img_dir . $datos['foto'])) {

			chmod($img_dir . $datos['foto'], 0777);

			if (!unlink($img_dir . $datos['foto'])) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Error al intentar eliminar la foto del producto, por favor intente nuevamente",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado la foto del producto en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$producto_datos_up = [
			[
				"campo_nombre" => "foto",
				"campo_marcador" => ":Foto",
				"campo_valor" => ""
			]
		];

		$condicion = [
			"condicion_campo" => "id_producto",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto eliminada",
				"texto" => "La foto del producto '" . $datos['nombre'] . "' se elimino correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto eliminada",
				"texto" => "No hemos podido actualizar algunos datos del producto '" . $datos['nombre'] . "', sin embargo la foto ha sido eliminada correctamente",
				"icono" => "warning"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador actualizar foto producto  ----------*/
	public function actualizarFotoProductoControlador()
	{

		$id = $this->limpiarCadena($_POST['id_producto']);

		# Verificando producto #
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE id_producto='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Directorio de imagenes #
		$img_dir = "../views/productos/";

		# Comprobar si se selecciono una imagen #
		if ($_FILES['foto']['name'] == "" && $_FILES['foto']['size'] <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No ha seleccionado una foto para el producto",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Creando directorio #
		if (!file_exists($img_dir)) {
			if (!mkdir($img_dir, 0777)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Error al crear el directorio",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Verificando formato de imagenes #
		if (mime_content_type($_FILES['foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['foto']['tmp_name']) != "image/png") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "La imagen que ha seleccionado es de un formato no permitido",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando peso de imagen #
		if (($_FILES['foto']['size'] / 1024) > 5120) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "La imagen que ha seleccionado supera el peso permitido",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Nombre de la foto #
		if ($datos['foto'] != "") {
			$foto = explode(".", $datos['foto']);
			$foto = $foto[0];
		} else {
			$foto = $datos['codigo'] . "_" . rand(0, 100);
		}


		# Extension de la imagen #
		switch (mime_content_type($_FILES['foto']['tmp_name'])) {
			case 'image/jpeg':
				$foto = $foto . ".jpg";
				break;
			case 'image/png':
				$foto = $foto . ".png";
				break;
		}

		chmod($img_dir, 0777);

		# Moviendo imagen al directorio #
		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $img_dir . $foto)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos subir la imagen al sistema en este momento",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Eliminando imagen anterior #
		if (is_file($img_dir . $datos['foto']) && $datos['foto'] != $foto) {
			chmod($img_dir . $datos['foto'], 0777);
			unlink($img_dir . $datos['foto']);
		}

		$producto_datos_up = [
			[
				"campo_nombre" => "foto",
				"campo_marcador" => ":Foto",
				"campo_valor" => $foto
			]
		];

		$condicion = [
			"condicion_campo" => "id_producto",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto actualizada",
				"texto" => "La foto del producto '" . $datos['nombre'] . "' se actualizo correctamente",
				"icono" => "success"
			];
		} else {

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto actualizada",
				"texto" => "No hemos podido actualizar algunos datos del producto '" . $datos['nombre'] . "', sin embargo la foto ha sido actualizada",
				"icono" => "warning"
			];
		}

		return json_encode($alerta);
	}
}
