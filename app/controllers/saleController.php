<?php

namespace app\controllers;

use app\models\mainModel;



class saleController extends mainModel
{


	/*---------- Catalogo de Productos ----------*/
	public function catalogoProductosControlador($datos)
	{

		/*== Recuperando codigo de busqueda ==*/
		$producto = $this->limpiarCadena($datos['busqueda']);

		$pagina = $this->limpiarCadena($datos["page"]);
		$vista = $this->limpiarCadena($datos["vista"]);
		$registros = "5";
		$campoOrden = "codigo";
		$orden = "Asc";
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if (isset($producto) && $producto != "") {

			$consulta_datos = "SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE (prod.nombre LIKE '%$producto%' OR prod.marca LIKE '%$producto%' OR prod.modelo LIKE '%$producto%' OR prod.codigo LIKE '%$producto%') ORDER BY prod.$campoOrden $orden LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(cid_producto) FROM producto WHERE (nombre LIKE '%$producto%' OR marca LIKE '%$producto%' OR modelo LIKE '%$producto%' OR codigo LIKE '%$producto%')";
		} else {

			$consulta_datos = "SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto ORDER BY prod.$campoOrden $orden LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(cid_producto) FROM producto";
		}

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);

		if ($total >= 1 && $pagina <= $numeroPaginas) {

			$tabla = '<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;
			$tabla .= '<thead style="background:#B99654;color:#ffffff">
			<th style="color:#ffffff"></th>
			<th style="color:#ffffff">Código</th>
			<th style="color:#ffffff">Producto</th>
			<th style="color:#ffffff">Precio</th>
			<th style="color:#ffffff">Stock</th>
			<th style="color:#ffffff"></th>
			</thead>';
			foreach ($datos as $rows) {

				if (is_file('../views/productos/' . $rows['foto'])) {
					$foto = '<img class="img-responsive" src="' . APP_URL . 'app/views/productos/' . $rows['foto'] . '">';
				} else {
					$foto = '<img class="img-responsive" src="' . APP_URL . 'app/views/productos/default.png">';
				}

				$tabla .= '
					<tr class="has-text-left" >
						<td><figure class="full-width mb-3" style="max-width: 70px;">
							' . $foto . '
				
			</figure></td>
					    <td><i class="fas fa-barcode fa-fw"></i> &nbsp; ' . $rows['codigo'] . '</td>
                        <td><i class="fas fa-box fa-fw"></i> &nbsp; ' . $rows['nombre'] . '</td>
						<td><i class="fas fa-dollar-sign fa-fw"></i> &nbsp; ' . $rows['precio_venta'] . '</td>
							<td><i class="fas fa-cubes fa-fw"></i> &nbsp; ' . $rows['stock_total'] . '</td>
                        <td class="has-text-centered">
                            <button type="button" class="button is-link is-rounded is-medium" onclick="agregar_codigo(\'' . $rows['codigo'] . '\')">Agregar&nbsp;<i class="fas fa-plus-circle"></i></button>
                        </td>
                    </tr>
                    ';
				$contador++;
			}
			$pag_final = $contador - 1;
			$tabla .= '</tbody></table></div>';
			### Paginacion ###
			if ($total > 0 && $pagina <= $numeroPaginas) {
				$tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

				$tabla .= $this->paginadorTablasListado($pagina, $vista, $numeroPaginas, 7);
			}

			return $tabla;
		} else {
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
	}
	/*---------- Catalogo de Clientes ----------*/
	public function catalogoClientesControlador($datos)
	{

		/*== Recuperando codigo de busqueda ==*/
		$cliente = $this->limpiarCadena($datos['busqueda']);

		$pagina = $this->limpiarCadena($datos["page"]);
		$vista = $this->limpiarCadena($datos["vista"]);
		$registros = "5";
		$campoOrden = "nombre";
		$orden = "Asc";
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if (isset($cliente) && $cliente != "") {

			$consulta_datos = "SELECT * FROM cliente WHERE (id_cliente!='1') AND nombre LIKE '%$cliente%' OR apellidos LIKE '%$cliente%' OR celular LIKE '%$cliente%' ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_cliente) FROM cliente WHERE (id_cliente!='1') AND nombre LIKE '%$cliente%' OR apellidos LIKE '%$cliente%' OR celular LIKE '%$cliente%'";
		} else {

			$consulta_datos = "SELECT * FROM cliente WHERE (id_cliente!='1') ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_cliente) FROM cliente WHERE (id_cliente!='1')";
		}

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);

		if ($total >= 1 && $pagina <= $numeroPaginas) {

			$tabla = '<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;
			$tabla .= '<thead style="background:#B99654;color:#ffffff">
			<th style="color:#ffffff">Usuario</th>
			<th style="color:#ffffff">Nombre Completo</th>
			<th style="color:#ffffff">Celular</th>
			<th style="color:#ffffff">Credito</th>
			<th style="color:#ffffff">Pagado</th>
			<th style="color:#ffffff">Saldo Pendiente</th>
			<th style="color:#ffffff"></th>
			</thead>';
			foreach ($datos as $rows) {

				$tabla .= '
					<tr class="has-text-left" >
					
					    <td>' . $rows['usuario'] . ' </td>
                        <td>' . $rows['nombre'] . ' ' . $rows['apellidos'] . '</td>
						<td>' . $rows['celular'] . '</td>
						<td>' . MONEDA_SIMBOLO . number_format($rows['credito'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE . '</td>
						<td>' . MONEDA_SIMBOLO . number_format($rows['pagado'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE . '</td>
					    <td>' . MONEDA_SIMBOLO . number_format($rows['pendiente'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE . '</td>
                        <td class="has-text-centered">
                            <button type="button" class="button is-link is-rounded is-medium" onclick="agregar_cliente(' . $rows['id_cliente'] . ')"><i class="fas fa-user-plus"></i></button>
                        </td>
                    </tr>
                    ';
				$contador++;
			}
			$pag_final = $contador - 1;
			$tabla .= '</tbody></table></div>';
			### Paginacion ###
			if ($total > 0 && $pagina <= $numeroPaginas) {
				$tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

				$tabla .= $this->paginadorTablasListado($pagina, $vista, $numeroPaginas, 7);
			}

			return $tabla;
		} else {
			return '
				<article class="message is-warning mt-4 mb-4">
					 <div class="message-header">
					    <p></p>
					 </div>
				    <div class="message-body has-text-centered">
				    	<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						No hay Clientes Registrados Actualmente
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
		$token = $this->generarCodigoAleatorioProducto(8);

		if ($codigo == "") {

			$alerta = "Debes de introducir el código de barras del producto";
			return json_encode($alerta);
			exit();
		}


		/*== Comprobando producto en la DB ==*/
		$check_producto = $this->ejecutarConsulta("SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE prod.codigo='$codigo'");
		if ($check_producto->rowCount() <= 0) {

			$alerta = "No hemos encontrado el producto con código de barras : '$codigo'";
			return json_encode($alerta);
			exit();
		} else {
			$value = $check_producto->fetch();
		}

		/*== Codigo de producto ==*/
		$codigo = $value['codigo'];

		if (empty($_SESSION['datos_producto_venta'][$codigo])) {

			$detalle_cantidad = 1;

			$stock_total = $value['stock_total'] - $detalle_cantidad;

			if ($stock_total < 0) {

				$alerta =  "Lo sentimos, no hay existencias disponibles del producto seleccionado";
				return json_encode($alerta);
				exit();
			}


			$porc_descuento = $_POST["porc_descuento"];
			$descuento = (($value['precio_venta'] * $detalle_cantidad) * $porc_descuento) / 100;
			$subtotal = ($detalle_cantidad * $value['precio_venta']);
			$subtotal = number_format($subtotal, MONEDA_DECIMALES, '.', '');
			$total = ($subtotal - $descuento);
			$total = number_format($total, MONEDA_DECIMALES, '.', '');

			$_SESSION['datos_producto_venta'][$codigo] = [
				"id_producto" => $value['cid_producto'],
				"codigo" => $value['codigo'],
				"foto" => $value['foto'],
				"out_stock" => $value['out_stock'],
				"token" => $token,
				"stock_total" => $stock_total,
				"stock_total_old" => $value['stock_total'],
				"precio_compra" => $value['precio_compra'],
				"precio_venta" => $value['precio_venta'],
				"porc_descuento" => $porc_descuento,
				"descuento" => $descuento,
				"cantidad" => $detalle_cantidad,
				"subtotal" => $subtotal,
				"total" => $total,
				"error_stock" => "",
				"descripcion" => $value['nombre']
			];

			$alerta = "Se agrego <strong>" . $value['nombre'] . "</strong> a la venta";
		} else {
			$detalle_cantidad = ($_SESSION['datos_producto_venta'][$codigo]['cantidad']) + 1;

			$stock_total = $value['stock_total'] - $detalle_cantidad;

			if ($stock_total < 0) {
				$alerta =  "Lo sentimos, no hay existencias disponibles del producto seleccionado";
				return json_encode($alerta);

				exit();
			}

			$porc_descuento = $_POST["porc_descuento"];
			$descuento = (($value['precio_venta'] * $detalle_cantidad) * $porc_descuento) / 100;
			$subtotal = ($detalle_cantidad * $value['precio_venta']);
			$subtotal = number_format($subtotal, MONEDA_DECIMALES, '.', '');
			$total = ($subtotal - $descuento);
			$total = number_format($total, MONEDA_DECIMALES, '.', '');


			$_SESSION['datos_producto_venta'][$codigo] = [
				"id_producto" => $value['cid_producto'],
				"codigo" => $value['codigo'],
				"foto" => $value['foto'],
				"token" => $token,
				"out_stock" => $value['out_stock'],
				"stock_total" => $stock_total,
				"stock_total_old" => $value['stock_total'],
				"precio_compra" => $value['precio_compra'],
				"precio_venta" => $value['precio_venta'],
				"porc_descuento" => $porc_descuento,
				"descuento" => $descuento,
				"cantidad" => $detalle_cantidad,
				"subtotal" => $subtotal,
				"total" => $total,
				"error_stock" => "",
				"descripcion" => $value['nombre']
			];

			$alerta = "Se agrego +1 <strong>" . $value['nombre'] . "</strong> a la venta. Total en carrito: <strong>$detalle_cantidad</strong>";
		}



		return json_encode($alerta);
	}


	/*---------- Controlador remover producto de venta ----------*/
	public function removerProductoCarritoControlador()
	{

		/*== Recuperando codigo del producto ==*/
		$codigo = $this->limpiarCadena($_POST['codigo']);

		unset($_SESSION['datos_producto_venta'][$codigo]);

		if (empty($_SESSION['datos_producto_venta'][$codigo])) {

			$alerta = "El producto con el código " . $codigo . " se ha removido de la venta";
		} else {
			$alerta = "No hemos podido remover el producto, por favor intente nuevamente";
		}
		return json_encode($alerta);
	}


	/*---------- Controlador actualizar producto de venta ----------*/
	public function actualizarProductoCarritoControlador()
	{

		/*== Recuperando codigo & cantidad del producto ==*/
		$codigo = $this->limpiarCadena($_POST['codigo']);
		$cantidad = $this->limpiarCadena($_POST['cantidad']);
		$porc_descuento = $_SESSION["porc_descuento"];


		/*== comprobando campos vacios ==*/
		if ($codigo == "" || $cantidad == "") {
			$alerta = "No podemos actualizar la cantidad de productos debido a que faltan algunos parámetros de configuración";
			return json_encode($alerta);
			exit();
		}

		/*== comprobando cantidad de productos ==*/
		if ($cantidad <= 0) {

			$alerta = "Debes de introducir una cantidad mayor a 0";
			return json_encode($alerta);
			exit();
		}

		/*== Comprobando producto en la DB ==*/
		$check_producto = $this->ejecutarConsulta("SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE prod.codigo='$codigo'");
		if ($check_producto->rowCount() <= 0) {

			$alerta = "No hemos encontrado el producto con código de barras : '$codigo'";
			return json_encode($alerta);
			exit();
		} else {
			$value = $check_producto->fetch();
		}

		/*== comprobando producto en carrito ==*/
		if (!empty($_SESSION['datos_producto_venta'][$codigo])) {

			if ($_SESSION['datos_producto_venta'][$codigo]["cantidad"] == $cantidad) {
				$alerta = "No has modificado la cantidad de productos";
				return json_encode($alerta);
				exit();
			}
			$detalle_cantidad = floatval($cantidad);
			$stock = $value['stock_total'];

			if ($detalle_cantidad > $stock) {
				$stock_total = $value['stock_total'] - $_SESSION['datos_producto_venta'][$codigo]["cantidad"];
				$_SESSION['datos_producto_venta'][$codigo]["stock_total"] = $stock_total;
				$_SESSION['datos_producto_venta'][$codigo]["cantidad"] =  $_SESSION['datos_producto_venta'][$codigo]["cantidad"];
			} else {
				$stock_total = $value['stock_total'] - $detalle_cantidad;
				$_SESSION['datos_producto_venta'][$codigo]["stock_total"] = $stock_total;


				if ($detalle_cantidad > $_SESSION['datos_producto_venta'][$codigo]["cantidad"]) {
					$diferencia_productos = "agrego + 1";
				} else {
					$diferencia_productos = "quito - 1";
				}
				$_SESSION['datos_producto_venta'][$codigo]["cantidad"] = $detalle_cantidad;
			}
			$_SESSION['datos_producto_venta'][$codigo]["error_stock"] = "";

			$_SESSION['datos_producto_venta'][$codigo]["stock_total_old"] = $value['stock_total'];

			if ($detalle_cantidad > $stock) {

				$alerta = "Lo sentimos, no podemos agregar mas unidades al carrito. Existencias disponibles: " . ($stock) . "";
				return json_encode($alerta);
				exit();
			}

			$descuento = (($value['precio_venta'] * $detalle_cantidad) * $porc_descuento) / 100;
			$descuento = number_format($descuento, MONEDA_DECIMALES, '.', '');
			$subtotal = ($value['precio_venta'] * $detalle_cantidad);
			$subtotal = number_format($subtotal, MONEDA_DECIMALES, '.', '');
			$total = ($subtotal - $descuento);
			$total = number_format($total, MONEDA_DECIMALES, '.', '');

			$_SESSION['datos_producto_venta'][$codigo]["stock_total"] = $stock_total;
			$_SESSION['datos_producto_venta'][$codigo]["stock_total_old"] = $value['stock_total'];
			$_SESSION['datos_producto_venta'][$codigo]["subtotal"] = $subtotal;
			$_SESSION['datos_producto_venta'][$codigo]["descuento"] = $descuento;
			$_SESSION['datos_producto_venta'][$codigo]["total"] = $total;

			$alerta = "Se $diferencia_productos <strong>" . $value['nombre'] . "</strong> a la venta. Total en carrito <strong>$detalle_cantidad</strong>";

			return json_encode($alerta);
		} else {

			$alerta = "No hemos encontrado el producto que desea actualizar en el carrito";
			return json_encode($alerta);
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
			$value = $check_cliente->fetch();
		}

		if ($_SESSION['datos_cliente_venta']['id_cliente'] == 1) {
			$_SESSION['datos_cliente_venta'] = [
				"id_cliente" => $value['id_cliente'],
				"nombre" => $value['nombre'],
				"apellidos" => $value['apellidos']
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
		$forma_pago = $this->limpiarCadena($_POST['forma_pago']);
		$total_pago = $this->limpiarCadena($_POST['total_pago']);
		$total_pagado = $this->limpiarCadena($_POST['total_pagado']);
		$total_cambio = $this->limpiarCadena($_POST['total_cambio']);
		$referencia_venta = $this->limpiarCadena($_POST['referencia_venta']);

		/*== Comprobando integridad de los datos ==*/
		if ($this->verificarDatos("[0-9.]{1,25}", $total_pagado)) {
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
		$check_caja = $this->ejecutarConsulta("SELECT * FROM sesiones_caja WHERE id_caja='$caja'");
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
		$pagado = number_format($total_pagado, MONEDA_DECIMALES, '.', '');
		$total = number_format($_SESSION['total'], MONEDA_DECIMALES, '.', '');
		$subtotal = number_format($_SESSION['subtotal'], MONEDA_DECIMALES, '.', '');
		$descuento = number_format($_SESSION['descuento'], MONEDA_DECIMALES, '.', '');
		$porc_descuento = number_format($_SESSION['porc_descuento'], MONEDA_DECIMALES, '.', '');

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
		$contador = 0;
		foreach ($_SESSION['datos_producto_venta'] as $productos) {

			/*== Obteniendo datos del producto ==*/
			$check_producto = $this->ejecutarConsulta("SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE prod.cid_producto='" . $productos['id_producto'] . "' AND prod.codigo='" . $productos['codigo'] . "'");
			if ($check_producto->rowCount() < 1) {
				$errores_productos = 1;
				break;
			} else {
				$datos_producto = $check_producto->fetch();
			}

			/*== Respaldando datos de BD para poder restaurar en caso de errores ==*/
			$stockActual = $datos_producto['stock_total'] - $_SESSION['datos_producto_venta'][$productos['codigo']]['cantidad'];


			/*== Preparando datos para enviarlos al modelo ==*/

			if ($stockActual < 0) {
				$contador++;
				$precio_venta = $_SESSION['datos_producto_venta'][$productos['codigo']]['precio_venta'];

				$_SESSION['datos_producto_venta'][$productos['codigo']]["error_stock"] = "error";
				$descuento = (($precio_venta * $datos_producto['stock_total']) * $porc_descuento) / 100;
				$descuento = number_format($descuento, MONEDA_DECIMALES, '.', '');
				$subtotal = ($precio_venta * $datos_producto['stock_total']);
				$subtotal = number_format($subtotal, MONEDA_DECIMALES, '.', '');
				$total = ($subtotal - $descuento);
				$total = number_format($total, MONEDA_DECIMALES, '.', '');


				$_SESSION['datos_producto_venta'][$productos['codigo']]["cantidad"] = $datos_producto['stock_total'];
				$_SESSION['datos_producto_venta'][$productos['codigo']]["stock_total"] = $datos_producto['stock_total'] - $datos_producto['stock_total'];
				$_SESSION['datos_producto_venta'][$productos['codigo']]["stock_total_old"] = $datos_producto['stock_total'];
				$_SESSION['datos_producto_venta'][$productos['codigo']]["subtotal"] = $subtotal;
				$_SESSION['datos_producto_venta'][$productos['codigo']]["descuento"] = $descuento;
				$_SESSION['datos_producto_venta'][$productos['codigo']]["total"] = $total;
			} else {

				$_SESSION['datos_producto_venta'][$productos['codigo']]["error_stock"] = "";
			}
		}

		if ($contador > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Algunos productos marcados en rojo no cuentan con existencias suficientes, se actualizará el carrito a las existencias actuales.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/******************************************************************************************************/
		/****
		 *Registrar Venta
		 */
		/*== generando codigo de venta ==*/
		$correlativo = $this->ejecutarConsulta("SELECT id_venta FROM venta");
		$correlativo = ($correlativo->rowCount()) + 1;
		$codigo_venta = $this->generarCodigoAleatorio('SALE', 22, $correlativo);

		/*== Preparando datos para enviarlos al modelo ==*/
		$datos_venta_reg = [
			[
				"campo_nombre" => "tipo_venta",
				"campo_marcador" => ":TipoVenta",
				"campo_valor" => 'directa',
			],
			[
				"campo_nombre" => "tipo_entrega",
				"campo_marcador" => ":TipoEntrega",
				"campo_valor" => 'recoleccion',
			],
			[
				"campo_nombre" => "forma_pago",
				"campo_marcador" => ":FormaPago",
				"campo_valor" => $forma_pago,
			],
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
				"campo_nombre" => "subtotal",
				"campo_marcador" => ":Subtotal",
				"campo_valor" => $subtotal
			],
			[
				"campo_nombre" => "descuento",
				"campo_marcador" => ":Descuento",
				"campo_valor" => $descuento
			],
			[
				"campo_nombre" => "porc_descuento",
				"campo_marcador" => ":PorcDescuento",
				"campo_valor" => $porc_descuento
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
			],
			[
				"campo_nombre" => "estatus",
				"campo_marcador" => ":Estatus",
				"campo_valor" => '1'
			],
			[
				"campo_nombre" => "estatus_pago",
				"campo_marcador" => ":EstatusPago",
				"campo_valor" => '1'
			]
		];
		$agregar_venta = $this->guardarDatos("venta", $datos_venta_reg);


		if ($agregar_venta->rowCount() == 1) {


			/****
			 * SESSIONES CAJA
			 */

			$check_caja = $this->ejecutarConsulta("SELECT * FROM sesiones_caja  WHERE codigo_sesion = '" . $_SESSION["sesion_caja"] . "' and estado = 'abierta'");

			if ($check_caja->rowCount() < 1) {

				$alerta = [
					"tipo" => "simple",
					"titulo" => "La sesion de la caja no ha sido encontrada.",
					"texto" => "",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			} else {

				/*== generando codigo de pago ==*/
				$correlativo = $this->ejecutarConsulta("SELECT id_pago FROM pago");
				$correlativo = ($correlativo->rowCount()) + 1;
				$caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$longitud = 22;
				$prefijo = 'PAY';
				$codigo_pago =  strtoupper($prefijo . "-" . substr(str_shuffle($caracteres_permitidos), 0, $longitud) . "-" . $correlativo);


				$datos_pago_reg = [
					[
						"campo_nombre" => "codigo_pago",
						"campo_marcador" => ":CodigoPago",
						"campo_valor" => $codigo_pago
					],
					[
						"campo_nombre" => "codigo_venta",
						"campo_marcador" => ":CodigoVenta",
						"campo_valor" => $codigo_venta
					],
					[
						"campo_nombre" => "id_metodo_pago",
						"campo_marcador" => ":MetodoPago",
						"campo_valor" => $forma_pago
					],
					[
						"campo_nombre" => "total_pago",
						"campo_marcador" => ":TotalPago",
						"campo_valor" => $total_pago
					],
					[
						"campo_nombre" => "total_pagado",
						"campo_marcador" => ":TotalPagado",
						"campo_valor" => $total_pagado
					],
					[
						"campo_nombre" => "total_cambio",
						"campo_marcador" => ":TotalCambio",
						"campo_valor" => $total_cambio
					],
					[
						"campo_nombre" => "referencia",
						"campo_marcador" => ":Referencia",
						"campo_valor" => $referencia_venta
					],

				];

				/*== Agregando venta ==*/
				$generar_pago = $this->guardarDatos("pago", $datos_pago_reg);

				$datos_caja = $check_caja->fetch();
				switch ($forma_pago) {
					case '1':
						$campo_nombre = "efectivo";
						$campo_marcador = ':Efectivo';
						break;
					case '2':
						$campo_nombre = "transferencia";
						$campo_marcador = ':Transferencia';
						break;
					case '3':
						$campo_nombre = "tarjeta_credito";
						$campo_marcador = ':TarjetaCredito';
						break;
					case '4':
						$campo_nombre = "tarjeta_debito";
						$campo_marcador = ':TarjetaDebito';
						break;
				}

				$datos_caja_rs = [
					[
						"campo_nombre" => $campo_nombre,
						"campo_marcador" => $campo_marcador,
						"campo_valor" => $datos_caja[$campo_nombre] + $total_final
					]
				];

				$condicion = [
					"condicion_campo" => "codigo_sesion",
					"condicion_marcador" => ":CodigoSesion",
					"condicion_valor" => $_SESSION["sesion_caja"]
				];

				$saldo_caja = $this->actualizarDatos("sesiones_caja", $datos_caja_rs, $condicion);

				/*== Preparando datos para enviarlos al modelo ==*/
				$datos_movimiento_caja_reg = [
					[
						"campo_nombre" => "id_caja",
						"campo_marcador" => ":Caja",
						"campo_valor" => $caja
					],
					[
						"campo_nombre" => "tipo_movimiento",
						"campo_marcador" => ":TipoMovimiento",
						"campo_valor" => 'ingreso'
					],
					[
						"campo_nombre" => "monto",
						"campo_marcador" => ":Monto",
						"campo_valor" => $total_final
					],
					[
						"campo_nombre" => "descripcion",
						"campo_marcador" => ":Descripcion",
						"campo_valor" => $codigo_pago
					]


				];
				$movimiento_caja = $this->guardarDatos("movimiento_caja", $datos_movimiento_caja_reg);

				/***
				 * Registrar Productos Venta
				 */
				/*== Agregando detalles de la venta ==*/
				$errores_venta_detalle = 0;
				foreach ($_SESSION['datos_producto_venta'] as $venta_detalle) {

					/*== Preparando datos para enviarlos al modelo ==*/
					$datos_venta_detalle_reg = [
						[
							"campo_nombre" => "cantidad",
							"campo_marcador" => ":Cantidad",
							"campo_valor" => $venta_detalle['cantidad']
						],
						[
							"campo_nombre" => "precio_compra",
							"campo_marcador" => ":PrecioCompra",
							"campo_valor" => $venta_detalle['precio_compra']
						],
						[
							"campo_nombre" => "precio_venta",
							"campo_marcador" => ":PrecioVenta",
							"campo_valor" => $venta_detalle['precio_venta']
						],
						[
							"campo_nombre" => "total",
							"campo_marcador" => ":Total",
							"campo_valor" => $venta_detalle['total']
						],
						[
							"campo_nombre" => "subtotal",
							"campo_marcador" => ":Subtotal",
							"campo_valor" => $venta_detalle['subtotal']
						],
						[
							"campo_nombre" => "descuento",
							"campo_marcador" => ":Descuento",
							"campo_valor" => $venta_detalle['descuento']
						],
						[
							"campo_nombre" => "porc_descuento",
							"campo_marcador" => ":PorcDescuento",
							"campo_valor" => $venta_detalle['porc_descuento']
						],
						[
							"campo_nombre" => "descripcion",
							"campo_marcador" => ":Descripcion",
							"campo_valor" => $venta_detalle['descripcion']
						],
						[
							"campo_nombre" => "codigo",
							"campo_marcador" => ":Codigo",
							"campo_valor" => $codigo_venta
						],
						[
							"campo_nombre" => "token",
							"campo_marcador" => ":Token",
							"campo_valor" => $venta_detalle['token']
						],
						[
							"campo_nombre" => "id_producto",
							"campo_marcador" => ":IdProducto",
							"campo_valor" => $venta_detalle['id_producto']
						]
					];

					if ($venta_detalle['cantidad'] != 0) {

						$agregar_detalle_venta = $this->guardarDatos("venta_detalle", $datos_venta_detalle_reg);

						$stock_producto = $this->ejecutarConsulta("SELECT prod.*,inven.stock_total FROM producto as prod INNER JOIN inventario as inven ON prod.cid_producto = inven.id_producto WHERE prod.cid_producto='" . $venta_detalle['id_producto'] . "' AND prod.codigo='" . $venta_detalle['codigo'] . "'");
						$stock_producto = $stock_producto->fetch();

						/*== Respaldando datos de BD para poder restaurar en caso de errores ==*/
						$stock = $stock_producto['stock_total'] - $venta_detalle['cantidad'];

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
							"condicion_valor" => $venta_detalle['id_producto']
						];

						$inventario = $this->actualizarDatos("inventario", $inventario_rs, $condicion);

						$movimiento_inventario_reg = [
							[
								"campo_nombre" => "id_producto",
								"campo_marcador" => ":Producto",
								"campo_valor" => $venta_detalle['id_producto']
							],
							[
								"campo_nombre" => "tipo_movimiento",
								"campo_marcador" => ":TipoMovimiento",
								"campo_valor" => 'salida'
							],
							[
								"campo_nombre" => "documento",
								"campo_marcador" => ":Documento",
								"campo_valor" => $codigo_venta
							],
							[
								"campo_nombre" => "cantidad",
								"campo_marcador" => ":Cantidad",
								"campo_valor" =>  $venta_detalle['cantidad']
							],
							[
								"campo_nombre" => "descripcion",
								"campo_marcador" => ":Descripcion",
								"campo_valor" =>  'Venta de producto'
							]
						];

						$movimiento_inventario = $this->guardarDatos("movimiento_inventario", $movimiento_inventario_reg);
					}
				}
				unset($_SESSION['total']);
				unset($_SESSION['subtotal']);
				unset($_SESSION['descuento']);
				unset($_SESSION['porc_descuento']);
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
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "La venta no pudo registrarse, intente mas tarde.",
				"texto" => "",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}
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

		$value_tablas = "venta.forma_pago,venta.tipo_entrega,venta.estatus_pago,venta.id_venta,venta.estatus,venta.subtotal,venta.descuento,venta.tipo_venta,venta.codigo,venta.fecha_venta,venta.hora_venta,venta.total,venta.id_usuario,venta.id_cliente,venta.id_caja,usuario.id_usuario,usuario.nombre as 'nombreVendedor',cliente.id_cliente,cliente.nombre as 'nombreCliente',cliente.apellidos";



		if (isset($busqueda) && $busqueda != "") {

			$consulta_datos = "SELECT $value_tablas FROM venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario WHERE (venta.codigo='$busqueda')  ORDER BY venta.id_venta DESC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_venta) FROM venta WHERE (venta.codigo='$busqueda')";
		} else {

			$consulta_datos = "SELECT $value_tablas FROM venta INNER JOIN cliente ON venta.id_cliente=cliente.id_cliente INNER JOIN usuario ON venta.id_usuario=usuario.id_usuario WHERE venta.id_venta != 0  ORDER BY venta.id_venta DESC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_venta) FROM venta";
		}

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);
		$tabla = "";

		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$tabla .= '
			<div class="table-container" style="margin-top:50px">
			<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
				<thead style="background:#B99654;color:#ffffff;">
					<tr>
						<th class="has-text-centered" style="color:#ffffff">Tipo Entrega</th>		
						<th class="has-text-centered" style="color:#ffffff">Estatus Pago</th>
						<th class="has-text-centered" style="color:#ffffff">Estatus Venta</th>
						<th class="has-text-centered" style="color:#ffffff">NRO.</th>
						<th class="has-text-centered" style="color:#ffffff">Tipo</th>
						<th class="has-text-centered" style="color:#ffffff">Codigo</th>
						<th class="has-text-centered" style="color:#ffffff">Fecha</th>
						<th class="has-text-centered" style="color:#ffffff">Cliente</th>
						<th class="has-text-centered" style="color:#ffffff">Vendedor</th>
						<th class="has-text-centered" style="color:#ffffff">Subtotal</th>
						<th class="has-text-centered" style="color:#ffffff">Descuento</th>
						<th class="has-text-centered" style="color:#ffffff">Total</th>
						<th class="has-text-centered" style="color:#ffffff">Opciones</th>
					</tr>
				</thead>
				<tbody>
		';

			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;
			foreach ($datos as $rows) {
				if ($rows["estatus"] == 0) {
					$modalPago = '';
				} else {
					$modalPago = 'data-target="modal-pago-venta"';
				}
				if ($rows['estatus_pago'] == 0) {
					$estatus_pago = '<button class="button is-danger is-light js-modal-trigger" ' . $modalPago . ' onclick="establecerFormaPago(' . $rows['forma_pago'] . ',' . $rows['total'] . ',' . $rows['codigo'] . ',' . $rows['estatus'] . ')">Sin Pagar</button>';
					$disabled = "";
				} else {
					$estatus_pago = '<button class="button is-success">Pagado</button>';
					$disabled = "display:none";
				}
				switch ($rows['estatus']) {
					case '0':
						$estatus = '<button class="button is-danger" >Cancelado</button>';

						break;
					case '1':
						$estatus = '<button class="button is-info  " onclick="actualizarEstatus(\'venta\',' . $rows['id_venta'] . ',\'2\',' . $rows['estatus_pago'] . ')">Recibido</button>';

						break;
					case '2':
						$estatus = '<button class="button is-warning" onclick="actualizarEstatus(\'venta\',' . $rows['id_venta'] . ',\'3\',' . $rows['estatus_pago'] . ')">En Preparación</button>';

						break;
					case '3':
						$estatus = '<button class="button is-primary" onclick="actualizarEstatus(\'venta\',' . $rows['id_venta'] . ',\'4\',' . $rows['estatus_pago'] . ')">Enviado</button>';

						break;
					case '4':
						$estatus = '<button class="button is-success">Entregado</button>';

						break;
				}
				$tabla .= '
						<tr class="has-text-centered" >
							<td>' . ucfirst($rows['tipo_entrega']) . '</td>
							<td>' . $estatus_pago . '</td>
						    <td>' . $estatus . '</td>
							<td>' . $rows['id_venta'] . '</td>
							<td>' . strtoupper($rows['tipo_venta']) . '</td>
							<td>' . $rows['codigo'] . '</td>
							<td>' . date("d-m-Y", strtotime($rows['fecha_venta'])) . ' ' . $rows['hora_venta'] . '</td>
							<td>' . $this->limitarCadena($rows['nombreCliente'] . ' ' . $rows['apellidos'], 30, "...") . '</td>
							<td>' . $this->limitarCadena($rows['nombreVendedor'], 30, "...") . '</td>
							<td>' . MONEDA_SIMBOLO . number_format($rows['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE . '</td>
							<td>' . MONEDA_SIMBOLO . number_format($rows['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE . '</td>
							<td>' . MONEDA_SIMBOLO . number_format($rows['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . ' ' . MONEDA_NOMBRE . '</td>
			                <td>

			                	<button type="button" class="button is-link is-outlined is-rounded is-small btn-sale-options" onclick="print_invoice(\'' . APP_URL . 'app/pdf/invoice.php?code=' . $rows['codigo'] . '\')" title="Imprimir nota Nro. ' . $rows['id_venta'] . '" >
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

			                    	<button type="submit" class="button is-danger is-rounded is-small" style="' . $disabled . '" title="Cancelar venta Nro. ' . $rows['id_venta'] . '" >
			                    		<i class="fas fa-times fa-fw"></i>
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
						<article class="message is-warning mt-4 mb-4">
				 <div class="message-header">
					
				 </div>
				<div class="message-body has-text-centered">
					<i class="fas fa-exclamation-triangle fa-5x"></i><br>
					No Hay Ventas Registradas Actualmente
				</div>
			</article>
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
		$datos = $this->ejecutarConsulta("SELECT * FROM venta WHERE id_venta='$id' and estatus <=1");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se puede cancelar la venta porque se encuentra en preparación.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Verificando detalles de venta #
		/*
		$check_detalle_venta = $this->ejecutarConsulta("SELECT id_detalle FROM venta_detalle WHERE codigo='" . $datos['codigo'] . "'");
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
			*/


		$cancelarVenta = $this->cancelarRegistro("venta", "estatus", "id_venta", $id);

		if ($cancelarVenta->rowCount() == 1) {

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Venta Cancelada",
				"texto" => "La venta ha sido cancelada del sistema correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido cancelar la venta del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
	public function actualizarCarritoVentaControlador()
	{
		$_SESSION["porc_descuento"] = $_POST["porc_descuento"];
		$productos = $_SESSION['datos_producto_venta'];
		$sumaTotal = 0;

		foreach ($productos as $key => $value) {
			$descuento = (($value['precio_venta'] * $value['cantidad']) * $_POST["porc_descuento"]) / 100;
			$subtotal = ($value['precio_venta'] * $value['cantidad']);
			$total = ($subtotal - $descuento);

			$_SESSION['datos_producto_venta'][$value["codigo"]]["subtotal"] = $subtotal;
			$_SESSION['datos_producto_venta'][$value["codigo"]]["descuento"] = $descuento;
			$_SESSION['datos_producto_venta'][$value["codigo"]]["total"] = $total;
			$sumaTotal += $total;
		}
		$alerta = "Carrito actualizado. Total en carrito: <strong>$" . number_format($sumaTotal, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, "") . "</strong>";


		return json_encode($alerta);
	}
	/*----------  Controlador actualizar estatus venta  ----------*/
	public function actualizarEstatusVentaControlador()
	{

		$id = $this->limpiarCadena($_POST['id_venta']);
		$estatus = $this->limpiarCadena($_POST['estatus']);
		$tabla = $this->limpiarCadena($_POST['tabla']);
		$estatus_pago = $this->limpiarCadena($_POST['estatus_pago']);

		if ($estatus_pago == "0") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Upps!",
				"texto" => "Para actualizar el estatus, la venta debe estar pagada.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			# Verificando venta #
			$datos = $this->ejecutarConsulta("SELECT * FROM $tabla WHERE id_venta='$id'");
			if ($datos->rowCount() <= 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No se puede actualizar la venta porque la venta no se encuentra.",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			} else {
				$datos = $datos->fetch();
			}
		}



		$actualizarEstatus = $this->actualizarRegistro($tabla, "estatus", "id_venta", $id, $estatus);

		if ($actualizarEstatus->rowCount() == 1) {

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Venta Actualizada",
				"texto" => "Estatus de venta actualizado correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar el estatus de la venta del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
	/*----------  Controlador actualizar estatus de pago de  venta  ----------*/
	public function generarPagoVentaControlador()
	{

		$codigo_venta = $this->limpiarCadena($_POST['codigo_venta']);
		$id_metodo_pago = $this->limpiarCadena($_POST['forma_pago']);
		$total_pago = $this->limpiarCadena($_POST['total_pago']);
		$total_pagado = $this->limpiarCadena($_POST['total_pagado']);
		$total_cambio = $this->limpiarCadena($_POST['total_cambio']);
		$referencia = $this->limpiarCadena($_POST['referencia_venta']);
		# Verificando venta #
		$datos = $this->ejecutarConsulta("SELECT * FROM venta WHERE codigo='$codigo_venta'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se puede actualizar la venta porque la venta no se encuentra.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}


		$fecha_pago = date("Y-m-d H:i:s");
		$datos_venta = [
			[
				"campo_nombre" => "forma_pago",
				"campo_marcador" => ":FormaPago",
				"campo_valor" => $id_metodo_pago
			],
			[
				"campo_nombre" => "estatus_pago",
				"campo_marcador" => ":EstatusPago",
				"campo_valor" => 1,
			],

			[
				"campo_nombre" => "pagado",
				"campo_marcador" => ":Pagado",
				"campo_valor" => $total_pagado,
			],
			[
				"campo_nombre" => "cambio",
				"campo_marcador" => ":Cambio",
				"campo_valor" => $total_cambio,
			]
		];

		$condicion = [
			"condicion_campo" => "codigo",
			"condicion_marcador" => ":Codigo",
			"condicion_valor" => $codigo_venta
		];
		$actualizarEstatus = $this->actualizarDatos("venta", $datos_venta, $condicion);
		/*== generando codigo de pago ==*/
		$correlativo = $this->ejecutarConsulta("SELECT id_pago FROM pago");
		$correlativo = ($correlativo->rowCount()) + 1;
		$caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$longitud = 22;
		$prefijo = 'PAY';
		$codigo_pago =  strtoupper($prefijo . "-" . substr(str_shuffle($caracteres_permitidos), 0, $longitud) . "-" . $correlativo);

		if ($actualizarEstatus->rowCount() == 1) {

			$datos_pago_reg = [
				[
					"campo_nombre" => "codigo_pago",
					"campo_marcador" => ":CodigoPago",
					"campo_valor" => $codigo_pago
				],
				[
					"campo_nombre" => "codigo_venta",
					"campo_marcador" => ":CodigoVenta",
					"campo_valor" => $codigo_venta
				],
				[
					"campo_nombre" => "id_metodo_pago",
					"campo_marcador" => ":MetodoPago",
					"campo_valor" => $id_metodo_pago
				],
				[
					"campo_nombre" => "total_pago",
					"campo_marcador" => ":TotalPago",
					"campo_valor" => $total_pago
				],
				[
					"campo_nombre" => "total_pagado",
					"campo_marcador" => ":TotalPagado",
					"campo_valor" => $total_pagado
				],
				[
					"campo_nombre" => "total_cambio",
					"campo_marcador" => ":TotalCambio",
					"campo_valor" => $total_cambio
				],
				[
					"campo_nombre" => "referencia",
					"campo_marcador" => ":Referencia",
					"campo_valor" => $referencia
				],

			];

			/*== Agregando venta ==*/
			$generar_pago = $this->guardarDatos("pago", $datos_pago_reg);

			if ($generar_pago->rowCount() == 1) {

				$alerta = [
					"tipo" => "recargar",
					"titulo" => "Pago Generado Exitosamente",
					"texto" => "Estatus de pago actualizado correctamente",
					"icono" => "success"
				];
			} else {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No hemos podido actualizar el estatus de pago de la venta del sistema, por favor intente nuevamente",
					"icono" => "error"
				];
			}
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar el estatus de pago de la venta del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
	/*----------  Controlador Carrito Venta  ----------*/
	public function cargarCarritoVentaControlador()
	{

		$tabla = "";

		$_SESSION['total'] = 0;
		$_SESSION['subtotal'] = 0;
		$_SESSION['descuento'] = 0;
		$cc = 1;
		if (isset($_SESSION['datos_producto_venta'])) {
			if (empty($_SESSION['datos_producto_venta'])) {
				$tabla = '
			<article class="message is-warning mt-4 mb-4">
				 <div class="message-header">
					
				 </div>
				<div class="message-body has-text-centered">
					<i class="fas fa-exclamation-triangle fa-2x"></i><br>
					No hay productos agregados en el carrito
				</div>
			</article>';
			} else {
				foreach ($_SESSION['datos_producto_venta'] as $productos) {

					if (is_file("../views/productos/" . $productos['foto'])) {
						$foto = '<img src="' . APP_URL . 'app/views/productos/' . $productos['foto'] . '">';
					} else {
						$foto = '<img src="' . APP_URL . 'app/views/productos/default.png">';
					}

					if ($productos['error_stock'] == "") {
						$flag = "#B99654";
					} else {
						$flag = "#ff6565";
					}

					$tabla .= '<div class="card pt-4">
								<header class="card-header" style="background:' . $flag . ';color:#ffffff">
								 <p class="card-header-title"><strong style="color:#ffffff">' . $productos['descripcion'] . '</strong></p>
								   <p class="card-header-title"><strong style="color:#ffffff">' . $productos['codigo'] . '</strong></p>
									<button type="submit" class="button is-danger is-rounded pt-4" onclick="removerProductoCarrito(\'' . $productos['codigo'] . '\')">
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
										 
											<div class="column is-two-fifths">
												<div class="columns ">
													<div class="column">
													<div class="field has-addons">
													<div class="control">
														  <button type="button" class="button is-info is-rounded" onclick="decrementarCarrito(\'' . $productos['codigo'] . '\',\'' . $productos['token'] . '\')" >
																<i class="fas fa-minus "></i>
														 </button>
													</div>
													<div class="control">
														
														<input class="input has-text-centered" value="' . $productos['cantidad'] . '" id="cantidadCarrito' . $productos['token'] . '"  type="text"  >
													</div>
													<div class="control">
														 <button type="button" class="button is-info is-rounded" onclick="incrementarCarrito(\'' . $productos['codigo'] . '\',\'' . $productos['token'] . '\')" >
																<i class="fas fa-plus "></i>
														 </button>
													</div>
													</div>
					
				
													</div>
													
												</div>
											</div>
											 <div class="column">
													<div class="columns">
													<label><strong>Precio:</strong></label>
													</div>
													<div class="columns">
													' . MONEDA_SIMBOLO . " " . number_format($productos['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '
													 </div>
											</div>
											<div class="column">
													<div class="columns">
													<label><strong>Descuento:</strong></label>
													</div>
													<div class="columns">
													' . MONEDA_SIMBOLO . " " . number_format($productos['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '
													</div>
											</div>
											  
										</div>
										
										
									</div>
								</div>
								<footer class="card-footer">
									
									<a href="#" class="card-footer-item">
										<div class="columns has-text-centered">
										<label><strong>Existencias:</strong></label>
										</div>
										<div class="columns pt-4">
										
										' . $productos['stock_total_old'] . '
										</div>
									</a>
									<a href="#" class="card-footer-item">
									 <div class="columns has-text-centered">
									  <label><strong>Total:</strong></label>
									</div>
									<div class="columns pt-4">
									' . MONEDA_SIMBOLO . " " . number_format($productos['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '
									</div>
									</a>
										<a href="#" class="card-footer-item">
									 <div class="columns has-text-centered">
									  <label><strong>Total:</strong></label>
									</div>
									<div class="columns pt-4">
									' . MONEDA_SIMBOLO . " " . number_format($productos['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '
									</div>
									</a>
								</footer>
								</div>';
					$cc++;
					$_SESSION['subtotal'] += $productos['subtotal'];
					$_SESSION['descuento'] += $productos['descuento'];
					$_SESSION['total'] += $productos['total'];
				}
			}
		} else {
			$tabla = '
			<article class="message is-warning mt-4 mb-4">
				 <div class="message-header">
					
				 </div>
				<div class="message-body has-text-centered">
					<i class="fas fa-exclamation-triangle fa-2x"></i><br>
					No hay productos agregados en el carrito
				</div>
			</article>';
		}


		return $tabla;
	}
	/*----------  Controlador Totales Carrito Venta  ----------*/
	public function cargarTotalesCarritoVentaControlador()
	{
?>
		<div class="columns is-full">
			<div class="card">
				<header class="card-header">
					<div class="columns">
						<div class="column is-two-fifths">
							<p class="card-header-title">SUBTOTAL:</p>
						</div>
						<div class="column  is-three-fifths">
							<P class="card-header-title"><?php echo MONEDA_SIMBOLO . " " . number_format($_SESSION['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></P>
						</div>
					</div>
				</header>
				<header class="card-header">
					<div class="columns">
						<div class="column is-two-fifths">
							<p class="card-header-title">DESCUENTO:</p>
						</div>
						<div class="column is-three-fifths">
							<P class="card-header-title"><?php echo MONEDA_SIMBOLO . " " . number_format($_SESSION['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></P>
						</div>
					</div>

				</header>
				<header class="card-header">
					<div class="columns">
						<div class="column is-two-fifths">
							<p class="card-header-title">TOTAL A PAGAR:</p>
						</div>
						<div class="column is-three-fifths">
							<h4 class="card-header-title"><?php echo MONEDA_SIMBOLO . " " . number_format($_SESSION['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></h4>
						</div>
					</div>

				</header>
				<input type="hidden" value="<?php echo number_format($_SESSION['total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, ""); ?>" id="total_hidden">
				<input type="hidden" value="<?php echo number_format($_SESSION['subtotal'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, ""); ?>" id="subtotal_hidden">
				<input type="hidden" value="<?php echo number_format($_SESSION['descuento'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, ""); ?>" id="descuento_hidden">
				<script type="text/javascript">
					$("#total_pagar_venta").val(parseFloat(<?php echo $_SESSION['total'] ?>).toFixed(2));
				</script>
			</div>
		</div>

<?php


	}
}
