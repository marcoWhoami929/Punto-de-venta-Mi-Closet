<?php

namespace app\controllers;

use app\models\mainModel;

class saleController extends mainModel
{

	/*---------- Controlador buscar codigo de producto ----------*/
	public function buscarCodigoVentaControlador()
	{

		/*== Recuperando codigo de busqueda ==*/
		$producto = $this->limpiarCadena($_POST['buscar_codigo']);

		/*== Comprobando que no este vacio el campo ==*/
		if ($producto == "") {
			return '
				<article class="message is-warning mt-4 mb-4">
					 <div class="message-header">
					    <p>¡Ocurrio un error inesperado!</p>
					 </div>
				    <div class="message-body has-text-centered">
				    	<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						Debes de introducir el Nombre, Marca o Modelo del producto
				    </div>
				</article>';
			exit();
		}

		/*== Seleccionando productos en la DB ==*/
		$datos_productos = $this->ejecutarConsulta("SELECT * FROM producto WHERE (nombre LIKE '%$producto%' OR marca LIKE '%$producto%' OR modelo LIKE '%$producto%') ORDER BY nombre ASC");

		if ($datos_productos->rowCount() >= 1) {

			$datos_productos = $datos_productos->fetchAll();

			$tabla = '<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';

			foreach ($datos_productos as $rows) {
				$tabla .= '
					<tr class="has-text-left" >
                        <td><i class="fas fa-box fa-fw"></i> &nbsp; ' . $rows['nombre'] . '</td>
                        <td class="has-text-centered">
                            <button type="button" class="button is-link is-rounded is-small" onclick="agregar_codigo(\'' . $rows['codigo'] . '\')"><i class="fas fa-plus-circle"></i></button>
                        </td>
                    </tr>
                    ';
			}

			$tabla .= '</tbody></table></div>';
			return $tabla;
		} else {
			return '<article class="message is-warning mt-4 mb-4">
					 <div class="message-header">
					    <p>¡Ocurrio un error inesperado!</p>
					 </div>
				    <div class="message-body has-text-centered">
				    	<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						No hemos encontrado ningún producto en el sistema que coincida con <strong>“' . $producto . '”
				    </div>
				</article>';

			exit();
		}
	}


	/*---------- Controlador agregar producto a venta ----------*/
	public function agregarProductoCarritoControlador()
	{

		/*== Recuperando codigo del producto ==*/
		$codigo = $this->limpiarCadena($_POST['codigo']);

		if ($codigo == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Debes de introducir el código de barras del producto",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== Verificando integridad de los datos ==*/
		if ($this->verificarDatos("[a-zA-Z0-9- ]{1,70}", $codigo)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El código de barras no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== Comprobando producto en la DB ==*/
		$check_producto = $this->ejecutarConsulta("SELECT * FROM producto WHERE codigo='$codigo'");
		if ($check_producto->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto con código de barras : '$codigo'",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$campos = $check_producto->fetch();
		}

		/*== Codigo de producto ==*/
		$codigo = $campos['codigo'];

		if (empty($_SESSION['datos_producto_venta'][$codigo])) {

			$detalle_cantidad = 1;

			$stock_total = $campos['stock_total'] - $detalle_cantidad;

			if ($stock_total < 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Lo sentimos, no hay existencias disponibles del producto seleccionado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}

			$detalle_total = $detalle_cantidad * $campos['precio_venta'];
			$detalle_total = number_format($detalle_total, MONEDA_DECIMALES, '.', '');

			$_SESSION['datos_producto_venta'][$codigo] = [
				"id_producto" => $campos['id_producto'],
				"codigo" => $campos['codigo'],
				"stock_total" => $stock_total,
				"stock_total_old" => $campos['stock_total'],
				"venta_detalle_precio_compra" => $campos['precio_compra'],
				"venta_detalle_precio_venta" => $campos['precio_venta'],
				"venta_detalle_cantidad" => 1,
				"venta_detalle_total" => $detalle_total,
				"venta_detalle_descripcion" => $campos['nombre']
			];

			$_SESSION['alerta_producto_agregado'] = "Se agrego <strong>" . $campos['nombre'] . "</strong> a la venta";
		} else {
			$detalle_cantidad = ($_SESSION['datos_producto_venta'][$codigo]['venta_detalle_cantidad']) + 1;

			$stock_total = $campos['stock_total'] - $detalle_cantidad;

			if ($stock_total < 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Lo sentimos, no hay existencias disponibles del producto seleccionado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}

			$detalle_total = $detalle_cantidad * $campos['precio_venta'];
			$detalle_total = number_format($detalle_total, MONEDA_DECIMALES, '.', '');

			$_SESSION['datos_producto_venta'][$codigo] = [
				"id_producto" => $campos['id_producto'],
				"codigo" => $campos['codigo'],
				"stock_total" => $stock_total,
				"stock_total_old" => $campos['stock_total'],
				"venta_detalle_precio_compra" => $campos['precio_compra'],
				"venta_detalle_precio_venta" => $campos['precio_venta'],
				"venta_detalle_cantidad" => $detalle_cantidad,
				"venta_detalle_total" => $detalle_total,
				"venta_detalle_descripcion" => $campos['nombre']
			];

			$_SESSION['alerta_producto_agregado'] = "Se agrego +1 <strong>" . $campos['nombre'] . "</strong> a la venta. Total en carrito: <strong>$detalle_cantidad</strong>";
		}

		$alerta = [
			"tipo" => "redireccionar",
			"url" => APP_URL . "saleNew/"
		];

		return json_encode($alerta);
	}


	/*---------- Controlador remover producto de venta ----------*/
	public function removerProductoCarritoControlador()
	{

		/*== Recuperando codigo del producto ==*/
		$codigo = $this->limpiarCadena($_POST['codigo']);

		unset($_SESSION['datos_producto_venta'][$codigo]);

		if (empty($_SESSION['datos_producto_venta'][$codigo])) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "¡Producto removido!",
				"texto" => "El producto se ha removido de la venta",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido remover el producto, por favor intente nuevamente",
				"icono" => "error"
			];
		}
		return json_encode($alerta);
	}


	/*---------- Controlador actualizar producto de venta ----------*/
	public function actualizarProductoCarritoControlador()
	{

		/*== Recuperando codigo & cantidad del producto ==*/
		$codigo = $this->limpiarCadena($_POST['codigo']);
		$cantidad = $this->limpiarCadena($_POST['producto_cantidad']);

		/*== comprobando campos vacios ==*/
		if ($codigo == "" || $cantidad == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos actualizar la cantidad de productos debido a que faltan algunos parámetros de configuración",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== comprobando cantidad de productos ==*/
		if ($cantidad <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Debes de introducir una cantidad mayor a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== Comprobando producto en la DB ==*/
		$check_producto = $this->ejecutarConsulta("SELECT * FROM producto WHERE codigo='$codigo'");
		if ($check_producto->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto con código de barras : '$codigo'",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$campos = $check_producto->fetch();
		}

		/*== comprobando producto en carrito ==*/
		if (!empty($_SESSION['datos_producto_venta'][$codigo])) {

			if ($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"] == $cantidad) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No has modificado la cantidad de productos",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}

			if ($cantidad > $_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]) {
				$diferencia_productos = "agrego +" . ($cantidad - $_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]);
			} else {
				$diferencia_productos = "quito -" . ($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"] - $cantidad);
			}


			$detalle_cantidad = $cantidad;

			$stock_total = $campos['stock_total'] - $detalle_cantidad;

			if ($stock_total < 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Lo sentimos, no hay existencias suficientes del producto seleccionado. Existencias disponibles: " . ($stock_total + $detalle_cantidad) . "",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}

			$detalle_total = $detalle_cantidad * $campos['precio_venta'];
			$detalle_total = number_format($detalle_total, MONEDA_DECIMALES, '.', '');

			$_SESSION['datos_producto_venta'][$codigo] = [
				"id_producto" => $campos['id_producto'],
				"codigo" => $campos['codigo'],
				"stock_total" => $stock_total,
				"stock_total_old" => $campos['stock_total'],
				"venta_detalle_precio_compra" => $campos['precio_compra'],
				"venta_detalle_precio_venta" => $campos['precio_venta'],
				"venta_detalle_cantidad" => $detalle_cantidad,
				"venta_detalle_total" => $detalle_total,
				"venta_detalle_descripcion" => $campos['nombre']
			];

			$_SESSION['alerta_producto_agregado'] = "Se $diferencia_productos <strong>" . $campos['nombre'] . "</strong> a la venta. Total en carrito <strong>$detalle_cantidad</strong>";

			$alerta = [
				"tipo" => "redireccionar",
				"url" => APP_URL . "saleNew/"
			];

			return json_encode($alerta);
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el producto que desea actualizar en el carrito",
				"icono" => "error"
			];
			return json_encode($alerta);
		}
	}


	/*---------- Controlador buscar cliente ----------*/
	public function buscarClienteVentaControlador()
	{

		/*== Recuperando termino de busqueda ==*/
		$cliente = $this->limpiarCadena($_POST['buscar_cliente']);

		/*== Comprobando que no este vacio el campo ==*/
		if ($cliente == "") {
			return '
				<article class="message is-warning mt-4 mb-4">
					 <div class="message-header">
					    <p>¡Ocurrio un error inesperado!</p>
					 </div>
				    <div class="message-body has-text-centered">
				    	<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						Debes de introducir el Numero de documento, Nombre, Apellido o Teléfono del cliente
				    </div>
				</article>';
			exit();
		}

		/*== Seleccionando clientes en la DB ==*/
		$datos_cliente = $this->ejecutarConsulta("SELECT * FROM cliente WHERE (id_cliente!='1') AND (cliente_numero_documento LIKE '%$cliente%' OR nombre LIKE '%$cliente%' OR cliente_apellido LIKE '%$cliente%' OR telefono LIKE '%$cliente%') ORDER BY nombre ASC");

		if ($datos_cliente->rowCount() >= 1) {

			$datos_cliente = $datos_cliente->fetchAll();

			$tabla = '<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';

			foreach ($datos_cliente as $rows) {
				$tabla .= '
					<tr>
                        <td class="has-text-left" ><i class="fas fa-male fa-fw"></i> &nbsp; ' . $rows['nombre'] . ' ' . $rows['cliente_apellido'] . ' (' . $rows['cliente_tipo_documento'] . ': ' . $rows['cliente_numero_documento'] . ')</td>
                        <td class="has-text-centered" >
                            <button type="button" class="button is-link is-rounded is-small" onclick="agregar_cliente(' . $rows['id_cliente'] . ')"><i class="fas fa-user-plus"></i></button>
                        </td>
                    </tr>
                    ';
			}

			$tabla .= '</tbody></table></div>';
			return $tabla;
		} else {
			return '
				<article class="message is-warning mt-4 mb-4">
					 <div class="message-header">
					    <p>¡Ocurrio un error inesperado!</p>
					 </div>
				    <div class="message-body has-text-centered">
				    	<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						No hemos encontrado ningún cliente en el sistema que coincida con <strong>“' . $cliente . '”</strong>
				    </div>
				</article>';
			exit();
		}
	}


	/*---------- Controlador agregar cliente ----------*/
	public function agregarClienteVentaControlador()
	{

		/*== Recuperando id del cliente ==*/
		$id = $this->limpiarCadena($_POST['id_cliente']);

		/*== Comprobando cliente en la DB ==*/
		$check_cliente = $this->ejecutarConsulta("SELECT * FROM cliente WHERE id_cliente='$id'");
		if ($check_cliente->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido agregar el cliente debido a un error, por favor intente nuevamente",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$campos = $check_cliente->fetch();
		}

		if ($_SESSION['datos_cliente_venta']['id_cliente'] == 1) {
			$_SESSION['datos_cliente_venta'] = [
				"id_cliente" => $campos['id_cliente'],
				"cliente_tipo_documento" => $campos['cliente_tipo_documento'],
				"cliente_numero_documento" => $campos['cliente_numero_documento'],
				"nombre" => $campos['nombre'],
				"cliente_apellido" => $campos['cliente_apellido']
			];

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "¡Cliente agregado!",
				"texto" => "El cliente se agregó para realizar una venta",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido agregar el cliente debido a un error, por favor intente nuevamente",
				"icono" => "error"
			];
		}
		return json_encode($alerta);
	}


	/*---------- Controlador remover cliente ----------*/
	public function removerClienteVentaControlador()
	{

		unset($_SESSION['datos_cliente_venta']);

		if (empty($_SESSION['datos_cliente_venta'])) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "¡Cliente removido!",
				"texto" => "Los datos del cliente se han quitado de la venta",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido remover el cliente, por favor intente nuevamente",
				"icono" => "error"
			];
		}
		return json_encode($alerta);
	}


	/*---------- Controlador registrar venta ----------*/
	public function registrarVentaControlador()
	{

		$caja = $this->limpiarCadena($_POST['venta_caja']);
		$pagado = $this->limpiarCadena($_POST['venta_abono']);

		/*== Comprobando integridad de los datos ==*/
		if ($this->verificarDatos("[0-9.]{1,25}", $pagado)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El total pagado por el cliente no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($_SESSION['total'] <= 0 || (!isset($_SESSION['datos_producto_venta']) && count($_SESSION['datos_producto_venta']) <= 0)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No ha agregado productos a esta venta",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if (!isset($_SESSION['datos_cliente_venta'])) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No ha seleccionado ningún cliente para realizar esta venta",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		/*== Comprobando cliente en la DB ==*/
		$check_cliente = $this->ejecutarConsulta("SELECT id_cliente FROM cliente WHERE id_cliente='" . $_SESSION['datos_cliente_venta']['id_cliente'] . "'");
		if ($check_cliente->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el cliente registrado en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		/*== Comprobando caja en la DB ==*/
		$check_caja = $this->ejecutarConsulta("SELECT * FROM caja WHERE id_caja='$caja'");
		if ($check_caja->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "La caja no está registrada en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos_caja = $check_caja->fetch();
		}


		/*== Formateando variables ==*/
		$pagado = number_format($pagado, MONEDA_DECIMALES, '.', '');
		$total = number_format($_SESSION['total'], MONEDA_DECIMALES, '.', '');

		$fecha_venta = date("Y-m-d");
		$hora_venta = date("h:i a");

		$total_final = $total;
		$total_final = number_format($total_final, MONEDA_DECIMALES, '.', '');


		/*== Calculando el cambio ==*/
		if ($pagado < $total_final) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Esta es una venta al contado, el total a pagar por el cliente no puede ser menor al total a pagar",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$cambio = $pagado - $total_final;
		$cambio = number_format($cambio, MONEDA_DECIMALES, '.', '');


		/*== Calculando total en caja ==*/
		$movimiento_cantidad = $pagado - $cambio;
		$movimiento_cantidad = number_format($movimiento_cantidad, MONEDA_DECIMALES, '.', '');

		$total_caja = $datos_caja['efectivo'] + $movimiento_cantidad;
		$total_caja = number_format($total_caja, MONEDA_DECIMALES, '.', '');


		/*== Actualizando productos ==*/
		$errores_productos = 0;
		foreach ($_SESSION['datos_producto_venta'] as $productos) {

			/*== Obteniendo datos del producto ==*/
			$check_producto = $this->ejecutarConsulta("SELECT * FROM producto WHERE id_producto='" . $productos['id_producto'] . "' AND codigo='" . $productos['codigo'] . "'");
			if ($check_producto->rowCount() < 1) {
				$errores_productos = 1;
				break;
			} else {
				$datos_producto = $check_producto->fetch();
			}

			/*== Respaldando datos de BD para poder restaurar en caso de errores ==*/
			$_SESSION['datos_producto_venta'][$productos['codigo']]['stock_total'] = $datos_producto['stock_total'] - $_SESSION['datos_producto_venta'][$productos['codigo']]['venta_detalle_cantidad'];

			$_SESSION['datos_producto_venta'][$productos['codigo']]['stock_total_old'] = $datos_producto['stock_total'];

			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_producto_up = [
				[
					"campo_nombre" => "stock_total",
					"campo_marcador" => ":Stock",
					"campo_valor" => $_SESSION['datos_producto_venta'][$productos['codigo']]['stock_total']
				]
			];

			$condicion = [
				"condicion_campo" => "id_producto",
				"condicion_marcador" => ":ID",
				"condicion_valor" => $productos['id_producto']
			];

			/*== Actualizando producto ==*/
			if (!$this->actualizarDatos("producto", $datos_producto_up, $condicion)) {
				$errores_productos = 1;
				break;
			}
		}

		/*== Reestableciendo DB debido a errores ==*/
		if ($errores_productos == 1) {

			foreach ($_SESSION['datos_producto_venta'] as $producto) {

				$datos_producto_rs = [
					[
						"campo_nombre" => "stock_total",
						"campo_marcador" => ":Stock",
						"campo_valor" => $producto['stock_total_old']
					]
				];

				$condicion = [
					"condicion_campo" => "id_producto",
					"condicion_marcador" => ":ID",
					"condicion_valor" => $producto['id_producto']
				];

				$this->actualizarDatos("producto", $datos_producto_rs, $condicion);
			}

			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar los productos en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== generando codigo de venta ==*/
		$correlativo = $this->ejecutarConsulta("SELECT id_venta FROM venta");
		$correlativo = ($correlativo->rowCount()) + 1;
		$codigo_venta = $this->generarCodigoAleatorio(10, $correlativo);

		/*== Preparando datos para enviarlos al modelo ==*/
		$datos_venta_reg = [
			[
				"campo_nombre" => "codigo",
				"campo_marcador" => ":Codigo",
				"campo_valor" => $codigo_venta
			],
			[
				"campo_nombre" => "fecha_venta",
				"campo_marcador" => ":Fecha",
				"campo_valor" => $fecha_venta
			],
			[
				"campo_nombre" => "hora_venta",
				"campo_marcador" => ":Hora",
				"campo_valor" => $hora_venta
			],
			[
				"campo_nombre" => "total",
				"campo_marcador" => ":Total",
				"campo_valor" => $total_final
			],
			[
				"campo_nombre" => "pagado",
				"campo_marcador" => ":Pagado",
				"campo_valor" => $pagado
			],
			[
				"campo_nombre" => "cambio",
				"campo_marcador" => ":Cambio",
				"campo_valor" => $cambio
			],
			[
				"campo_nombre" => "id_usuario",
				"campo_marcador" => ":Usuario",
				"campo_valor" => $_SESSION['id']
			],
			[
				"campo_nombre" => "id_cliente",
				"campo_marcador" => ":Cliente",
				"campo_valor" => $_SESSION['datos_cliente_venta']['id_cliente']
			],
			[
				"campo_nombre" => "id_caja",
				"campo_marcador" => ":Caja",
				"campo_valor" => $caja
			]
		];

		/*== Agregando venta ==*/
		$agregar_venta = $this->guardarDatos("venta", $datos_venta_reg);

		if ($agregar_venta->rowCount() != 1) {
			foreach ($_SESSION['datos_producto_venta'] as $producto) {

				$datos_producto_rs = [
					[
						"campo_nombre" => "stock_total",
						"campo_marcador" => ":Stock",
						"campo_valor" => $producto['stock_total_old']
					]
				];

				$condicion = [
					"condicion_campo" => "id_producto",
					"condicion_marcador" => ":ID",
					"condicion_valor" => $producto['id_producto']
				];

				$this->actualizarDatos("producto", $datos_producto_rs, $condicion);
			}

			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido registrar la venta, por favor intente nuevamente. Código de error: 001",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== Agregando detalles de la venta ==*/
		$errores_venta_detalle = 0;
		foreach ($_SESSION['datos_producto_venta'] as $venta_detalle) {

			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_venta_detalle_reg = [
				[
					"campo_nombre" => "venta_detalle_cantidad",
					"campo_marcador" => ":Cantidad",
					"campo_valor" => $venta_detalle['venta_detalle_cantidad']
				],
				[
					"campo_nombre" => "venta_detalle_precio_compra",
					"campo_marcador" => ":PrecioCompra",
					"campo_valor" => $venta_detalle['venta_detalle_precio_compra']
				],
				[
					"campo_nombre" => "venta_detalle_precio_venta",
					"campo_marcador" => ":PrecioVenta",
					"campo_valor" => $venta_detalle['venta_detalle_precio_venta']
				],
				[
					"campo_nombre" => "venta_detalle_total",
					"campo_marcador" => ":Total",
					"campo_valor" => $venta_detalle['venta_detalle_total']
				],
				[
					"campo_nombre" => "venta_detalle_descripcion",
					"campo_marcador" => ":Descripcion",
					"campo_valor" => $venta_detalle['venta_detalle_descripcion']
				],
				[
					"campo_nombre" => "codigo",
					"campo_marcador" => ":VentaCodigo",
					"campo_valor" => $codigo_venta
				],
				[
					"campo_nombre" => "id_producto",
					"campo_marcador" => ":Producto",
					"campo_valor" => $venta_detalle['id_producto']
				]
			];

			$agregar_detalle_venta = $this->guardarDatos("venta_detalle", $datos_venta_detalle_reg);

			if ($agregar_detalle_venta->rowCount() != 1) {
				$errores_venta_detalle = 1;
				break;
			}
		}

		/*== Reestableciendo DB debido a errores ==*/
		if ($errores_venta_detalle == 1) {

			$this->eliminarRegistro("venta_detalle", "codigo", $codigo_venta);
			$this->eliminarRegistro("venta", "codigo", $codigo_venta);

			foreach ($_SESSION['datos_producto_venta'] as $producto) {

				$datos_producto_rs = [
					[
						"campo_nombre" => "stock_total",
						"campo_marcador" => ":Stock",
						"campo_valor" => $producto['stock_total_old']
					]
				];

				$condicion = [
					"condicion_campo" => "id_producto",
					"condicion_marcador" => ":ID",
					"condicion_valor" => $producto['id_producto']
				];

				$this->actualizarDatos("producto", $datos_producto_rs, $condicion);
			}

			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido registrar la venta, por favor intente nuevamente. Código de error: 002",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== Actualizando efectivo en caja ==*/
		$datos_caja_up = [
			[
				"campo_nombre" => "efectivo",
				"campo_marcador" => ":Efectivo",
				"campo_valor" => $total_caja
			]
		];

		$condicion_caja = [
			"condicion_campo" => "id_caja",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $caja
		];

		if (!$this->actualizarDatos("caja", $datos_caja_up, $condicion_caja)) {

			$this->eliminarRegistro("venta_detalle", "codigo", $codigo_venta);
			$this->eliminarRegistro("venta", "codigo", $codigo_venta);

			foreach ($_SESSION['datos_producto_venta'] as $producto) {

				$datos_producto_rs = [
					[
						"campo_nombre" => "stock_total",
						"campo_marcador" => ":Stock",
						"campo_valor" => $producto['stock_total_old']
					]
				];

				$condicion = [
					"condicion_campo" => "id_producto",
					"condicion_marcador" => ":ID",
					"condicion_valor" => $producto['id_producto']
				];

				$this->actualizarDatos("producto", $datos_producto_rs, $condicion);
			}

			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido registrar la venta, por favor intente nuevamente. Código de error: 003",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== Vaciando variables de sesion ==*/
		unset($_SESSION['total']);
		unset($_SESSION['datos_cliente_venta']);
		unset($_SESSION['datos_producto_venta']);

		$_SESSION['codigo_factura'] = $codigo_venta;

		$alerta = [
			"tipo" => "recargar",
			"titulo" => "¡Venta registrada!",
			"texto" => "La venta se registró con éxito en el sistema",
			"icono" => "success"
		];
		return json_encode($alerta);
		exit();
	}


	/*----------  Controlador listar venta  ----------*/
	public function listarVentaControlador($pagina, $registros, $url, $busqueda)
	{

		$pagina = $this->limpiarCadena($pagina);
		$registros = $this->limpiarCadena($registros);

		$url = $this->limpiarCadena($url);
		$url = APP_URL . $url . "/";

		$busqueda = $this->limpiarCadena($busqueda);
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$campos_tablas = "venta.id_venta,venta.codigo,venta.fecha_venta,venta.hora_venta,venta.total,venta.id_usuario,venta.id_cliente,venta.id_caja,usuario.id_usuario,usuario.nombre,usuario.usuario_apellido,cliente.id_cliente,cliente.nombre,cliente.cliente_apellido";

		if (isset($busqueda) && $busqueda != "") {

			$consulta_datos = "SELECT $campos_tablas FROM venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario WHERE (venta.codigo='$busqueda') ORDER BY venta.id_venta DESC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_venta) FROM venta WHERE (venta.codigo='$busqueda')";
		} else {

			$consulta_datos = "SELECT $campos_tablas FROM venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario ORDER BY venta.id_venta DESC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_venta) FROM venta";
		}

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);

		$tabla .= '
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead>
		                <tr>
		                    <th class="has-text-centered">NRO.</th>
		                    <th class="has-text-centered">Codigo</th>
		                    <th class="has-text-centered">Fecha</th>
		                    <th class="has-text-centered">Cliente</th>
		                    <th class="has-text-centered">Vendedor</th>
		                    <th class="has-text-centered">Total</th>
		                    <th class="has-text-centered">Opciones</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;
			foreach ($datos as $rows) {
				$tabla .= '
						<tr class="has-text-centered" >
							<td>' . $rows['id_venta'] . '</td>
							<td>' . $rows['codigo'] . '</td>
							<td>' . date("d-m-Y", strtotime($rows['fecha_venta'])) . ' ' . $rows['hora_venta'] . '</td>
							<td>' . $this->limitarCadena($rows['nombre'] . ' ' . $rows['cliente_apellido'], 30, "...") . '</td>
							<td>' . $this->limitarCadena($rows['nombre'] . ' ' . $rows['usuario_apellido'], 30, "...") . '</td>
							<td>' . MONEDA_SIMBOLO . number_format($rows['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE . '</td>
			                <td>

			                	<button type="button" class="button is-link is-outlined is-rounded is-small btn-sale-options" onclick="print_invoice(\'' . APP_URL . 'app/pdf/invoice.php?code=' . $rows['codigo'] . '\')" title="Imprimir factura Nro. ' . $rows['id_venta'] . '" >
	                                <i class="fas fa-file-invoice-dollar fa-fw"></i>
	                            </button>

                                <button type="button" class="button is-link is-outlined is-rounded is-small btn-sale-options" onclick="print_ticket(\'' . APP_URL . 'app/pdf/ticket.php?code=' . $rows['codigo'] . '\')" title="Imprimir ticket Nro. ' . $rows['id_venta'] . '" >
                                    <i class="fas fa-receipt fa-fw"></i>
                                </button>

			                    <a href="' . APP_URL . 'saleDetail/' . $rows['codigo'] . '/" class="button is-link is-rounded is-small" title="Informacion de venta Nro. ' . $rows['id_venta'] . '" >
			                    	<i class="fas fa-shopping-bag fa-fw"></i>
			                    </a>

			                	<form class="FormularioAjax is-inline-block" action="' . APP_URL . 'app/ajax/ventaAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_venta" value="eliminar_venta">
			                		<input type="hidden" name="id_venta" value="' . $rows['id_venta'] . '">

			                    	<button type="submit" class="button is-danger is-rounded is-small" title="Eliminar venta Nro. ' . $rows['id_venta'] . '" >
			                    		<i class="far fa-trash-alt fa-fw"></i>
			                    	</button>
			                    </form>

			                </td>
						</tr>
					';
				$contador++;
			}
			$pag_final = $contador - 1;
		} else {
			if ($total >= 1) {
				$tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="7">
			                    <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
			} else {
				$tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="7">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
			}
		}

		$tabla .= '</tbody></table></div>';

		### Paginacion ###
		if ($total > 0 && $pagina <= $numeroPaginas) {
			$tabla .= '<p class="has-text-right">Mostrando ventas <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}


	/*----------  Controlador eliminar venta  ----------*/
	public function eliminarVentaControlador()
	{

		$id = $this->limpiarCadena($_POST['id_venta']);

		# Verificando venta #
		$datos = $this->ejecutarConsulta("SELECT * FROM venta WHERE id_venta='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado la venta en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Verificando detalles de venta #
		$check_detalle_venta = $this->ejecutarConsulta("SELECT venta_detalle_id FROM venta_detalle WHERE codigo='" . $datos['codigo'] . "'");
		$check_detalle_venta = $check_detalle_venta->rowCount();

		if ($check_detalle_venta > 0) {

			$eliminarVentaDetalle = $this->eliminarRegistro("venta_detalle", "codigo", $datos['codigo']);

			if ($eliminarVentaDetalle->rowCount() != $check_detalle_venta) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No hemos podido eliminar la venta del sistema, por favor intente nuevamente",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}


		$eliminarVenta = $this->eliminarRegistro("venta", "id_venta", $id);

		if ($eliminarVenta->rowCount() == 1) {

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Venta eliminada",
				"texto" => "La venta ha sido eliminada del sistema correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido eliminar la venta del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
}
