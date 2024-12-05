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
		$colores = $this->limpiarCadena($_POST['colores']);
		$tallas = $this->limpiarCadena($_POST['tallas']);
		$stock_minimo = $this->limpiarCadena($_POST['producto_stock_minimo']);
		$stock_maximo = $this->limpiarCadena($_POST['producto_stock_maximo']);

		# Verificando campos obligatorios #
		if ($codigo == "" || $nombre == "" || $precio_venta == "" || $stock == "") {
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
		/*
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
*/
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
		$cid_producto = $this->ejecutarConsulta("SELECT IF(MAX(cid_producto)+1 IS NULL,1,MAX(cid_producto)+1) as IdProducto FROM producto");
		$cid_producto = $cid_producto->fetch();

		$producto_datos_reg = [
			[
				"campo_nombre" => "codigo",
				"campo_marcador" => ":Codigo",
				"campo_valor" => $codigo
			],
			[
				"campo_nombre" => "cid_producto",
				"campo_marcador" => ":CidProducto",
				"campo_valor" => $cid_producto['IdProducto']
			],
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "out_stock",
				"campo_marcador" => ":OutStock",
				"campo_valor" => 0
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
				"campo_nombre" => "colores",
				"campo_marcador" => ":Colores",
				"campo_valor" => $colores
			],
			[
				"campo_nombre" => "tallas",
				"campo_marcador" => ":Tallas",
				"campo_valor" => $tallas
			],
			[
				"campo_nombre" => "estado",
				"campo_marcador" => ":Estado",
				"campo_valor" => "1"
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
		$inventario_datos_reg = [

			[
				"campo_nombre" => "id_producto",
				"campo_marcador" => ":IdProducto",
				"campo_valor" => $cid_producto['IdProducto']
			],
			[
				"campo_nombre" => "stock_total",
				"campo_marcador" => ":StockTotal",
				"campo_valor" => $stock
			],
			[
				"campo_nombre" => "stock_minimo",
				"campo_marcador" => ":StockMinimo",
				"campo_valor" => $stock_minimo
			],
			[
				"campo_nombre" => "stock_maximo",
				"campo_marcador" => ":StockMaximo",
				"campo_valor" => $stock_maximo
			]
		];

		$movimiento_datos_reg = [

			[
				"campo_nombre" => "id_producto",
				"campo_marcador" => ":IdProducto",
				"campo_valor" => $cid_producto['IdProducto']
			],
			[
				"campo_nombre" => "tipo_movimiento",
				"campo_marcador" => ":TipoMovimiento",
				"campo_valor" => 'entrada'
			],
			[
				"campo_nombre" => "cantidad",
				"campo_marcador" => ":Cantidad",
				"campo_valor" => $stock
			],
			[
				"campo_nombre" => "descripcion",
				"campo_marcador" => ":Descripcion",
				"campo_valor" => 'inventario inicial'
			]
		];

		$registrar_producto = $this->guardarDatos("producto", $producto_datos_reg);
		$registrar_inventario = $this->guardarDatos("inventario", $inventario_datos_reg);
		$registrar_movimiento = $this->guardarDatos("movimiento_inventario", $movimiento_datos_reg);


		if ($registrar_movimiento->rowCount() == 1) {
			$alerta = [
				"tipo" => "recargar",
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
	public function listarProductosControlador($datos)
	{

		$pagina = $this->limpiarCadena($datos["page"]);
		$registros = $this->limpiarCadena($datos["per_page"]);
		$campoOrden = $this->limpiarCadena($datos["campoOrden"]);
		$orden = $this->limpiarCadena($datos["orden"]);

		$url = $this->limpiarCadena($datos["url"]);
		$url = APP_URL . $url . "/";

		$busqueda = $this->limpiarCadena($datos["busqueda"]);
		$sWhere = "prod.cid_producto!='0'";

		if ($datos["estatus"] != "") {
			$sWhere .= " and estado = '" . $datos["estatus"] . "'";
		}

		if (isset($busqueda) && $busqueda != "") {
			$sWhere .= " AND prod.codigo LIKE '%$busqueda%' OR prod.nombre LIKE '%$busqueda%' OR prod.marca LIKE '%$busqueda%' OR prod.modelo LIKE '%$busqueda%'";
		}

		$campos = "prod.cid_producto,prod.codigo,prod.nombre as 'producto',prod.precio_venta,prod.foto,cat.nombre as 'categoria',inven.stock_total";
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$consulta_datos = "SELECT $campos FROM producto as prod INNER JOIN categoria as cat ON prod.id_categoria=cat.id_categoria INNER JOIN inventario as inven ON prod.cid_producto=inven.id_producto WHERE $sWhere ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

		$consulta_total = "SELECT COUNT(cid_producto) FROM producto as prod INNER JOIN categoria as cat ON prod.id_categoria=cat.id_categoria INNER JOIN inventario as inven ON prod.cid_producto=inven.id_producto WHERE $sWhere";

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);

		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;
			foreach ($datos as $rows) {

				if (is_file("./app/views/productos/" . $rows['foto'])) {
					$foto = '<img src="' . APP_URL . 'app/views/productos/' . $rows['foto'] . '">';
				} else {
					$foto = '<img src="' . APP_URL . 'app/views/productos/default.png">';
				}

				if ($rows['stock_total'] == "0.000") {
					$estatus = 'display:none';
				} else {
					$estatus = "";
				}
				$tabla .= '<div class="card  pb-4">
							<header class="card-header" style="background:#B99654;color:#ffffff">
							 <p class="card-header-title"><strong style="color:#ffffff">' . $rows['producto'] . '</strong></p>
									  <p class="card-header-title"><strong style="color:#ffffff">' . $rows['codigo'] . '</strong></p>
									  <button type="button" class="button is-danger is-rounded pt-4" onclick="eliminarProducto(\'' . $rows['cid_producto'] . '\')">
													<i class="fas fa-trash fa-fw"></i>
										</button>

							</header>
							<div class="card-content">
								<div class="content">
									<div class="columns">
										<div class="column">
											<figure class="media-left">
												<p class="image is-64x64">
													' . $foto . '
												</p>
												</figure>
										</div>
									 
									
										 <div class="column">
												<div class="columns">
												<label><strong>Precio:</strong></label>
												</div>
												<div class="columns">
												' . MONEDA_SIMBOLO . " " . number_format($rows['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '
												 </div>
										</div>
										<div class="column">
												<div class="columns">
												<label><strong>Existencia:</strong></label>
												</div>
												<div class="columns">
												' . $rows['stock_total'] . '
												</div>
										</div>
											<div class="column">
												<div class="columns">
												<label><strong>Categoria:</strong></label>
												</div>
												<div class="columns">
												' . $rows['categoria'] . '
												</div>
										</div>
										  
									</div>
									
									
								</div>
							</div>
							<footer class="card-footer">
									<div class="columns">
										<div class="column is-full">
											<div class=" card-footer-item">
													<div class="columns">
														<div class="column">
															<a href="' . APP_URL . 'productPhoto/' . $rows['cid_producto'] . '/" class="button is-info is-rounded is-small">
																<i class="far fa-image fa-fw"></i> Imagen
															</a>
														</div>
														<div class="column">
															<a href="' . APP_URL . 'productUpdate/' . $rows['cid_producto'] . '/" class="button is-success is-rounded is-small">
																<i class="fas fa-edit fa-fw"></i> Actualizar
															</a>
														</div>
													</div>

											</div>
										</div>
										<div class="column is-full">
												<div class=" card-footer-item">
													<div class="columns">
															<div class="column">
																<button class="button is-warning is-rounded is-small"  onclick="entradaInventario(\'' . $rows["cid_producto"] . '\')">
																<i class="fas fa-boxes fa-fw"></i> Abastecer
															</button>
															</div>
															<div class="column">
																	<button class="button is-danger is-rounded is-small js-modal-trigger"  data-target="modal-salida-inventario" onclick="salidaInventario(\'' . $rows["cid_producto"] . '\')" style="' . $estatus . '">
																<i class="fas fa-recycle fa-fw"></i> Desechar
															</button>
															</div>
															<div class="column">
															<a href="' . APP_URL . 'kardexDetail/' . $rows['cid_producto'] . '/" class="button is-link is-rounded is-small" title="Detalle Movimientos Producto ' . $rows['codigo'] . '" ><i class="fas fa-eye"></i> Detalle Movimientos</a>
															</div>
														</div>

												</div>

										</div>
									</div>

							</footer>
							</div>
								<hr>';
				$contador++;
			}
			$pag_final = $contador - 1;
		} else {
			if ($total >= 1) {
				$tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="5">
			                    <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
			} else {
				$tabla .= '
							<article class="message is-warning mt-4 mb-4">
				 <div class="message-header">
					<p></p>
				 </div>
				<div class="message-body has-text-centered">
					<i class="fas fa-exclamation-triangle fa-5x"></i><br>
					No hay Productos Registrados Actualmente
				</div>
			</article>
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

		$campos = "prod.cid_producto,prod.codigo,prod.nombre as 'producto',prod.precio_venta,prod.foto,cat.nombre as 'categoria',inven.stock_total";

		if (isset($busqueda) && $busqueda != "") {

			$consulta_datos = "SELECT $campos FROM producto as prod INNER JOIN categoria as cat ON prod.id_categoria=cat.id_categoria INNER JOIN inventario as inven ON prod.cid_producto=inven.id_producto WHERE prod.codigo LIKE '%$busqueda%' OR prod.nombre LIKE '%$busqueda%' OR prod.marca LIKE '%$busqueda%' OR prod.modelo LIKE '%$busqueda%' ORDER BY prod.nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(cid_producto) FROM producto WHERE codigo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR marca LIKE '%$busqueda%' OR modelo LIKE '%$busqueda%'";
		} elseif ($categoria > 0) {

			$consulta_datos = "SELECT $campos FROM producto as prod INNER JOIN categoria as cat ON prod.id_categoria=cat.id_categoria INNER JOIN inventario as inven ON prod.cid_producto=inven.id_producto WHERE prod.id_categoria='$categoria' ORDER BY prod.nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(cid_producto) FROM producto WHERE id_categoria='$categoria'";
		} else {

			$consulta_datos = "SELECT $campos FROM producto as prod INNER JOIN categoria as cat ON prod.id_categoria=cat.id_categoria INNER JOIN inventario as inven ON prod.cid_producto=inven.id_producto ORDER BY prod.nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(cid_producto) FROM producto";
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


				if (is_file("./app/views/productos/" . $rows['foto'])) {
					$foto = '<img src="' . APP_URL . 'app/views/productos/' . $rows['foto'] . '">';
				} else {
					$foto = '<img src="' . APP_URL . 'app/views/productos/default.png">';
				}

				$tabla .= '<div class="card  pb-4">
								<header class="card-header" style="background:#B99654;color:#ffffff">
								 <p class="card-header-title"><strong style="color:#ffffff">' . $rows['producto'] . '</strong></p>
										  <p class="card-header-title"><strong style="color:#ffffff">' . $rows['codigo'] . '</strong></p>
										  <button type="submit" class="button is-danger is-rounded pt-4" onclick="eliminarProducto(\'' . $rows['cid_producto'] . '\')">
														<i class="fas fa-trash fa-fw"></i>
											</button>
	
								</header>
								<div class="card-content">
									<div class="content">
										<div class="columns">
											<div class="column">
												<figure class="media-left">
													<p class="image is-64x64">
														' . $foto . '
													</p>
													</figure>
											</div>
										 
										
											 <div class="column">
													<div class="columns">
													<label><strong>Precio:</strong></label>
													</div>
													<div class="columns">
													' . MONEDA_SIMBOLO . " " . number_format($rows['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '
													 </div>
											</div>
											<div class="column">
													<div class="columns">
													<label><strong>Existencia:</strong></label>
													</div>
													<div class="columns">
													' . $rows['stock_total'] . '
													</div>
											</div>
												<div class="column">
													<div class="columns">
													<label><strong>Categoria:</strong></label>
													</div>
													<div class="columns">
													' . $rows['categoria'] . '
													</div>
											</div>
											  
										</div>
										
										
									</div>
								</div>
								<footer class="card-footer">
										<div class="columns">
											<div class="column is-full">
												<div class=" card-footer-item">
														<div class="columns">
															<div class="column">
																<a href="' . APP_URL . 'productPhoto/' . $rows['cid_producto'] . '/" class="button is-info is-rounded is-small">
																	<i class="far fa-image fa-fw"></i> Imagen
																</a>
															</div>
															<div class="column">
																<a href="' . APP_URL . 'productUpdate/' . $rows['cid_producto'] . '/" class="button is-success is-rounded is-small">
																	<i class="fas fa-edit fa-fw"></i> Actualizar
																</a>
															</div>
														</div>
	
												</div>
											</div>
											<div class="column is-full">
													<div class=" card-footer-item">
														<div class="columns">
																<div class="column">
																	<button class="button is-warning is-rounded is-small js-modal-trigger"  data-target="modal-entrada-inventario" onclick="entradaInventario(\'' . $rows["cid_producto"] . '\')">
																	<i class="fas fa-boxes fa-fw"></i> Abastecer
																</button>
																</div>
																<div class="column">
																		<button class="button is-danger is-rounded is-small js-modal-trigger"  data-target="modal-salida-inventario" onclick="salidaInventario(\'' . $rows["cid_producto"] . '\')">
																	<i class="fas fa-recycle fa-fw"></i> Desechar
																</button>
																</div>
															</div>
	
													</div>

											</div>
										</div>

								</footer>
								</div>
									<hr>';



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
						
				<article class="message is-warning mt-4 mb-4">
					 <div class="message-header">
					    <p></p>
					 </div>
				    <div class="message-body has-text-centered">
				    	<i class="fas fa-exclamation-triangle fa-5x"></i><br>
						No hay Productos Registrados Actualmente
				    </div>
				</article>';
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
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE cid_producto='$id'");
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

		$eliminarProducto = $this->eliminarRegistro("producto", "cid_producto", $id);
		$eliminarInventario = $this->eliminarRegistro("inventario", "id_producto", $id);
		$eliminarMovimiento = $this->eliminarRegistro("movimiento_inventario", "id_producto", $id);

		if ($eliminarMovimiento->rowCount() == 1) {

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
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE cid_producto='$id'");
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
		$colores = $this->limpiarCadena($_POST['colores']);
		$tallas = $this->limpiarCadena($_POST['tallas']);


		if (isset($_POST['out_stock'])) {
			$out_stock = 1;
		} else {
			$out_stock = 0;
		}

		# Verificando campos obligatorios #
		if ($codigo == "" || $nombre == ""  || $precio_venta == "" || $stock == "") {
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
		/*
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
*/
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
				"campo_nombre" => "out_Stock",
				"campo_marcador" => ":OutStock",
				"campo_valor" => $out_stock
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
				"campo_nombre" => "colores",
				"campo_marcador" => ":Colores",
				"campo_valor" => $colores
			],
			[
				"campo_nombre" => "tallas",
				"campo_marcador" => ":Tallas",
				"campo_valor" => $tallas
			],
			[
				"campo_nombre" => "id_categoria",
				"campo_marcador" => ":Categoria",
				"campo_valor" => $categoria
			]
		];

		$condicion = [
			"condicion_campo" => "cid_producto",
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
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE cid_producto='$id'");
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
			"condicion_campo" => "cid_producto",
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
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE cid_producto='$id'");
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
			"condicion_campo" => "cid_producto",
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
	/*----------  Controlador entrada producto  ----------*/
	public function entradaProductoControlador()
	{

		$id = $this->limpiarCadena($_POST['id_producto']);
		$unidades = $this->limpiarCadena($_POST['unidades_entrada']);
		$observaciones = $this->limpiarCadena($_POST['observaciones_entrada']);

		# Verificando campos obligatorios #
		if ($unidades == "" || $unidades == "0" || $unidades == "0.00") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Debe ingresar una cantidad mayor a 0 para realizar el movimiento.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando ventas #
		$stock_producto = $this->ejecutarConsulta("SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE prod.cid_producto='$id' ");

		if ($stock_producto->rowCount() < 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto en el sistema.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}
		$stock_producto = $stock_producto->fetch();
		$movimiento_inventario_reg = [
			[
				"campo_nombre" => "id_producto",
				"campo_marcador" => ":Producto",
				"campo_valor" => $id
			],
			[
				"campo_nombre" => "tipo_movimiento",
				"campo_marcador" => ":TipoMovimiento",
				"campo_valor" => 'entrada'
			],
			[
				"campo_nombre" => "documento",
				"campo_marcador" => ":Documento",
				"campo_valor" => NULL
			],
			[
				"campo_nombre" => "cantidad",
				"campo_marcador" => ":Cantidad",
				"campo_valor" =>  $unidades
			],
			[
				"campo_nombre" => "descripcion",
				"campo_marcador" => ":Descripcion",
				"campo_valor" => $observaciones
			]
		];

		$movimiento_inventario = $this->guardarDatos("movimiento_inventario", $movimiento_inventario_reg);


		if ($movimiento_inventario->rowCount() == 1) {

			$stock = $stock_producto['stock_total'] + $unidades;

			$inventario_rs = [
				[
					"campo_nombre" => "stock_total",
					"campo_marcador" => ":StockTotal",
					"campo_valor" => $stock
				]
			];

			$condicion = [
				"condicion_campo" => "id_producto",
				"condicion_marcador" => ":IdProducto",
				"condicion_valor" => $id
			];

			$inventario = $this->actualizarDatos("inventario", $inventario_rs, $condicion);

			$bitacora_reg = [
				[
					"campo_nombre" => "accion",
					"campo_marcador" => ":Accion",
					"campo_valor" => 'Entrada de Inventario de ' . $unidades . ' unidades de ' . $stock_producto['nombre'] . ''
				],
				[
					"campo_nombre" => "id_usuario",
					"campo_marcador" => ":IdUsuario",
					"campo_valor" => $_SESSION["id"]
				]
			];

			$registrar_caja = $this->guardarDatos("bitacora", $bitacora_reg);


			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Producto actualizado",
				"texto" => "El producto " . $stock_producto['nombre'] . " ha sido reabastecido con " . $unidades . " unidades.",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido reabastecer el producto, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
		exit();
	}
	/*----------  Controlador salida producto  ----------*/
	public function salidaProductoControlador()
	{

		$id = $this->limpiarCadena($_POST['id_producto']);
		$unidades = $this->limpiarCadena($_POST['unidades_salida']);
		$observaciones = $this->limpiarCadena($_POST['observaciones_salida']);

		# Verificando campos obligatorios #
		if ($unidades == "" || $unidades == "0" || $unidades == "0.00") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Debe ingresar una cantidad mayor a 0 para realizar el movimiento.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando ventas #
		$stock_producto = $this->ejecutarConsulta("SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE prod.cid_producto='$id' ");

		if ($stock_producto->rowCount() < 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto en el sistema.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}
		$stock_producto = $stock_producto->fetch();

		$stock_actual = $stock_producto['stock_total'];
		if ($unidades > $stock_actual) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Las unidades actuales " . $stock_actual . " no permiten realizar la salida de " . $unidades . " unidades solicitadas.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$movimiento_inventario_reg = [
			[
				"campo_nombre" => "id_producto",
				"campo_marcador" => ":Producto",
				"campo_valor" => $id
			],
			[
				"campo_nombre" => "tipo_movimiento",
				"campo_marcador" => ":TipoMovimiento",
				"campo_valor" => 'salida'
			],
			[
				"campo_nombre" => "documento",
				"campo_marcador" => ":Documento",
				"campo_valor" => NULL
			],
			[
				"campo_nombre" => "cantidad",
				"campo_marcador" => ":Cantidad",
				"campo_valor" =>  $unidades
			],
			[
				"campo_nombre" => "descripcion",
				"campo_marcador" => ":Descripcion",
				"campo_valor" => $observaciones
			]
		];

		$movimiento_inventario = $this->guardarDatos("movimiento_inventario", $movimiento_inventario_reg);


		if ($movimiento_inventario->rowCount() == 1) {

			$stock = $stock_producto['stock_total'] - $unidades;

			$inventario_rs = [
				[
					"campo_nombre" => "stock_total",
					"campo_marcador" => ":StockTotal",
					"campo_valor" => $stock
				]
			];

			$condicion = [
				"condicion_campo" => "id_producto",
				"condicion_marcador" => ":IdProducto",
				"condicion_valor" => $id
			];

			$inventario = $this->actualizarDatos("inventario", $inventario_rs, $condicion);

			$bitacora_reg = [
				[
					"campo_nombre" => "accion",
					"campo_marcador" => ":Accion",
					"campo_valor" => 'Salida de Inventario de ' . $unidades . ' unidades de ' . $stock_producto['nombre'] . ''
				],
				[
					"campo_nombre" => "id_usuario",
					"campo_marcador" => ":IdUsuario",
					"campo_valor" => $_SESSION["id"]
				]
			];

			$registrar_caja = $this->guardarDatos("bitacora", $bitacora_reg);


			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Producto actualizado",
				"texto" => "El producto " . $stock_producto['nombre'] . " ha desechado " . $unidades . " unidades.",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar el producto, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
		exit();
	}
	/*******************KARDEX INVENTARIO */
	public function listarKardexInventarioControlador($datos)
	{

		$pagina = $this->limpiarCadena($datos["page"]);
		$registros = $this->limpiarCadena($datos["per_page"]);
		$campoOrden = $this->limpiarCadena($datos["campoOrden"]);
		$orden = $this->limpiarCadena($datos["orden"]);
		$estatus = $this->limpiarCadena($datos["stock"]);

		$url = $this->limpiarCadena($datos["url"]);
		$url = APP_URL . $url . "/";

		$sWhere = "prod.id_producto !='0'";
		if ($datos["stock"] != "") {
			$sWhere .= " and inv.stock_total = '" . $estatus . "'";
		}
		$busqueda = $this->limpiarCadena($datos["busqueda"]);
		if (isset($busqueda) && $busqueda != "") {
			$sWhere .= " AND prod.nombre LIKE '%$busqueda%' OR prod.codigo LIKE '%$busqueda%'";
		}
		$tabla = "";
		$campos = "prod.cid_producto,prod.nombre,prod.codigo, SUM(IF(mov.tipo_movimiento = 'entrada',cantidad,0)) as 'entradas',SUM(IF(mov.tipo_movimiento = 'salida',cantidad,0)) as 'salidas', inv.stock_total as 'existencias',inv.stock_minimo,inv.stock_maximo,((inv.stock_maximo-inv.stock_minimo)/2) as media,IF(inv.stock_total=0,'0',IF(inv.stock_total<inv.stock_minimo,'1',IF(inv.stock_total>=inv.stock_minimo and inv.stock_total <= ((inv.stock_maximo-inv.stock_minimo)/2),'2',IF(inv.stock_total >((inv.stock_maximo-inv.stock_minimo)/2),'3','' )))) as estatus";
		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$consulta_datos = "SELECT $campos FROM producto as prod LEFT OUTER JOIN movimiento_inventario as mov ON prod.cid_producto = mov.id_producto LEFT OUTER JOIN inventario as inv ON prod.cid_producto = inv.id_producto WHERE $sWhere GROUP by prod.cid_producto,prod.nombre,prod.codigo ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

		$consulta_total = "SELECT COUNT(prod.cid_producto) FROM producto as prod WHERE $sWhere ";

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);


		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;

			$tabla .= '
			<div class="table-container">
			<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
				<thead style="background:#B99654;color:#ffffff;">
					<tr>
						<th style="color:#ffffff">#</th>
						<th style="color:#ffffff">Producto</th>
						<th style="color:#ffffff">Código</th>
						<th class="has-text-centered" style="color:#ffffff">Entradas</th>
						<th class="has-text-centered"style="color:#ffffff">Salidas</th>
						<th class="has-text-centered" style="color:#ffffff">Existencias</th>
						<th class="has-text-centered"style="color:#ffffff">Stock Mínimo</th>
						<th class="has-text-centered"style="color:#ffffff">Stock Máximo</th>
						<th class="has-text-centered"style="color:#ffffff"></th>
						<th class="has-text-centered"style="color:#ffffff">Nivel de Stock</th>
					</tr>
				</thead>
				<tbody>
		';
			$totalEntradas = 0;
			$totalSalidas = 0;
			$totalExistencias = 0;

			foreach ($datos as $rows) {

				switch ($rows['estatus']) {
					case '0':
						$colorProgress = 'is-danger';
						$color = "#ffa3a3";
						break;
					case '1':
						$colorProgress = 'is-warning';
						$color = "#fffa91";
						break;
					case '2':
						$colorProgress = 'is-info';
						$color = "";
						break;
					case '3':
						$colorProgress = 'is-success';
						$color = "";
						break;
				}


				$tabla .= '
						<tr style="background:' . $color . '">
							<td>' . $contador . '</td>
							<td><strong>' . $rows['nombre'] . '</strong></td>
							<td><strong>' . $rows['codigo'] . '</strong></td>
							<td class="has-text-centered">' . $rows['entradas'] . '</td>
							<td class="has-text-centered">' . $rows['salidas'] . '</td>
							<td class="has-text-centered">' . $rows['existencias'] . '</td>
							<td class="has-text-centered">' . $rows['stock_minimo'] . '</td>
							<td class="has-text-centered">' . $rows['stock_maximo'] . '</td>
							<td>
							
							 <a href="' . APP_URL . 'kardexDetail/' . $rows['cid_producto'] . '/" class="button is-link is-rounded is-medium" title="Detalle Movimientos Producto ' . $rows['codigo'] . '" ><i class="fas fa-eye"></i></a>
							</td>
							<td style="width:350px"><progress class="progress ' . $colorProgress . '" value="' . $rows['existencias'] . '" min="0" max="' . $rows['stock_maximo'] . '"></progress></td>
							
						</tr>
					';
				$totalEntradas += $rows['entradas'];
				$totalSalidas += $rows['salidas'];
				$totalExistencias += $rows['existencias'];
				$contador++;
			}
			$tabla .= '
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td class="has-text-right"><strong>' . $totalEntradas . '</strong></td>
							<td class="has-text-right"><strong>' . $totalSalidas . '</strong></td>
							<td class="has-text-right"><strong>' . $totalExistencias . '</strong></td>
							<td class="has-text-right"></td>
							<td class="has-text-centered"></td>
							<td style="width:50px"></td>
							<td style="width:350px"></td>
							
						</tr>
					';
			$pag_final = $contador - 1;
		} else {
			if ($total >= 1) {
				$tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="5">
			                    <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
			} else {
				$tabla .= '
				<article class="message is-warning mt-4 mb-4">
		 <div class="message-header">
			<p></p>
		 </div>
		<div class="message-body has-text-centered">
			<i class="fas fa-exclamation-triangle fa-5x"></i><br>
			No hay resultados de la busqueda.
		</div>
	</article>';
			}
		}

		$tabla .= '</tbody></table></div>';

		### Paginacion ###
		if ($total > 0 && $pagina <= $numeroPaginas) {
			$tabla .= '<p class="has-text-right">Mostrando categorias <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}
	public function listarDetalleKardexControlador($datos)
	{

		$pagina = $this->limpiarCadena($datos["page"]);
		$registros = $this->limpiarCadena($datos["per_page"]);
		$campoOrden = $this->limpiarCadena($datos["campoOrden"]);
		$orden = $this->limpiarCadena($datos["orden"]);
		$id_producto = $this->limpiarCadena($datos["id_producto"]);

		$url = $this->limpiarCadena($datos["url"]);
		$url = APP_URL . $url . "/";

		$sWhere = "mov.id_producto ='" . $id_producto . "'";

		$busqueda = $this->limpiarCadena($datos["busqueda"]);
		if ($datos["tipo_movimiento"] != "") {
			$sWhere .= " and mov.tipo_movimiento = '" . $datos["tipo_movimiento"] . "'";
		}
		if (isset($busqueda) && $busqueda != "") {
			$sWhere .= " AND mov.documento LIKE '%$busqueda%' OR mov.descripcion LIKE '%$busqueda%'";
		}
		$tabla = "";
		$campos = "mov.*,prod.codigo,prod.nombre";
		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$consulta_datos = "SELECT $campos FROM movimiento_inventario as mov INNER JOIN producto as prod ON mov.id_producto = prod.cid_producto WHERE $sWhere ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

		$consulta_total = "SELECT COUNT(id_movimiento_inventario) FROM movimiento_inventario as mov WHERE $sWhere ";

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);


		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;

			$tabla .= '
			<div class="table-container">
			<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
				<thead style="background:#B99654;color:#ffffff;">
					<tr>
						<th style="color:#ffffff">#</th>
						<th style="color:#ffffff">Producto</th>
						<th style="color:#ffffff">Código</th>
						<th style="color:#ffffff">Tipo</th>
						<th style="color:#ffffff">Documento</th>
						<th style="color:#ffffff">Cantidad</th>
						<th style="color:#ffffff">Descripción</th>
						<th style="color:#ffffff">Fecha</th>
					</tr>
				</thead>
				<tbody>
		';

			$totalCantidad = 0;
			foreach ($datos as $rows) {

				if ($rows["tipo_movimiento"] == "entrada") {
					$cantidad =  $rows['cantidad'];
				} else {
					$cantidad =  -$rows['cantidad'];
				}

				$tabla .= '
						<tr >
							<td>' . $contador . '</td>
							<td><strong>' . $rows['nombre'] . '</strong></td>
							<td><strong>' . $rows['codigo'] . '</strong></td>
							<td>' . $rows['tipo_movimiento'] . '</td>
							<td>' . $rows['documento'] . '</td>
							<td>' . $cantidad . '</td>
							<td>' . $rows['descripcion'] . '</td>
							<td>' . $rows['fecha_movimiento'] . '</td>
							
						</tr>
					';

				$contador++;
				$totalCantidad += $cantidad;
			}
			$tabla .= '
						<tr >
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><strong>Existencias:</strong></td>
							<td><strong>' . $totalCantidad . '</td>
							<td></td>
							<td></td>
							
						</tr>
					';
			$pag_final = $contador - 1;
		} else {
			if ($total >= 1) {
				$tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="5">
			                    <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
			} else {
				$tabla .= '
				<article class="message is-warning mt-4 mb-4">
		 <div class="message-header">
			<p></p>
		 </div>
		<div class="message-body has-text-centered">
			<i class="fas fa-exclamation-triangle fa-5x"></i><br>
			No hay movimientos relacionados con el producto
		</div>
	</article>';
			}
		}

		$tabla .= '</tbody></table></div>';

		### Paginacion ###
		if ($total > 0 && $pagina <= $numeroPaginas) {
			$tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}
}
