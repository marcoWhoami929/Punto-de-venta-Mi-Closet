<?php

namespace app\controllers;

use app\models\mainModel;


class cashierController extends mainModel
{

	/*----------  Controlador registrar caja  ----------*/
	public function registrarCajaControlador()
	{

		# Almacenando datos#
		$numero = $this->limpiarCadena($_POST['numero']);
		$nombre = $this->limpiarCadena($_POST['nombre']);


		# Verificando campos obligatorios #
		if ($numero == "" || $nombre == "") {
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
		if ($this->verificarDatos("[0-9]{1,5}", $numero)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NUMERO DE CAJA no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando numero de caja #
		$check_numero = $this->ejecutarConsulta("SELECT numero FROM caja WHERE numero='$numero'");
		if ($check_numero->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El número de caja ingresado ya se encuentra registrado en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando nombre de caja #
		$check_nombre = $this->ejecutarConsulta("SELECT nombre FROM caja WHERE nombre='$nombre'");
		if ($check_nombre->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El nombre o código de caja ingresado ya se encuentra registrado en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando que el efectivo sea mayor o igual a 0 #



		$caja_datos_reg = [
			[
				"campo_nombre" => "numero",
				"campo_marcador" => ":Numero",
				"campo_valor" => $numero
			],
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			]
		];

		$registrar_caja = $this->guardarDatos("caja", $caja_datos_reg);

		if ($registrar_caja->rowCount() == 1) {
			$alerta = [
				"tipo" => "limpiar",
				"titulo" => "Caja registrada",
				"texto" => "La caja " . $nombre . " #" . $numero . " se registro con exito",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo registrar la caja, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}

	/*----------  Controlador registrar caja  ----------*/
	public function aperturarCajaControlador()
	{

		# Almacenando datos#
		$saldo_inicial = $this->limpiarCadena($_POST['saldo_inicial']);
		$notas_apertura = $this->limpiarCadena($_POST['notas_apertura']);
		$id_caja = $_SESSION["caja"];

		# Verificando campos obligatorios #
		if ($saldo_inicial == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El saldo inicial no puede estar vacio",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando que el efectivo sea mayor o igual a 0 #
		$saldo_inicial = number_format($saldo_inicial, 2, '.', '');
		if ($saldo_inicial < 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No puedes colocar una cantidad de efectivo menor a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		/*== consultar si hay sesion de caja abierta ==
		$consulta_caja = $this->ejecutarConsulta("SELECT id_sesion,codigo_sesion FROM sesiones_caja WHERE id_caja ='" . $id_caja . "' and estado = 'abierta'");
		$datos = $consulta_caja->fetch();

		if ($consulta_caja->rowCount() == 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Sesion Iniciada",
				"texto" => "Existe una Sesión abierta con " . " #" . $datos["codigo_sesion"] . ", no se puede usar la misma caja.",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}
			*/
		#
		/*== generando codigo de apertura de caja ==*/
		$correlativo = $this->ejecutarConsulta("SELECT id_sesion FROM sesiones_caja");
		$correlativo = ($correlativo->rowCount()) + 1;
		$codigo_sesion = $this->generarCodigoAleatorio('POS', 10, $correlativo);

		$caja_datos_reg = [
			[
				"campo_nombre" => "codigo_sesion",
				"campo_marcador" => ":CodigoSesion",
				"campo_valor" => $codigo_sesion
			],
			[
				"campo_nombre" => "id_usuario",
				"campo_marcador" => ":IdUsuario",
				"campo_valor" => $_SESSION["id"]
			],
			[
				"campo_nombre" => "id_caja",
				"campo_marcador" => ":IdCaja",
				"campo_valor" => $id_caja
			],
			[
				"campo_nombre" => "notas_apertura",
				"campo_marcador" => ":Notas",
				"campo_valor" => $notas_apertura
			],
			[
				"campo_nombre" => "saldo_inicial",
				"campo_marcador" => ":SaldoInicial",
				"campo_valor" => $saldo_inicial
			],
			[
				"campo_nombre" => "estado",
				"campo_marcador" => ":Estado",
				"campo_valor" => 'abierta'
			]
		];

		$registrar_caja = $this->guardarDatos("sesiones_caja", $caja_datos_reg);

		if ($registrar_caja->rowCount() == 1) {
			$alerta = [
				"tipo" => "limpiar",
				"titulo" => "Sesion Iniciada",
				"texto" => "La Apertura de Caja Con " . " #" . $codigo_sesion . " se ha iniciado con exito",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo registrar la sesion de apertura, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
	/*----------  Controlador listar cajas  ----------*/
	public function listarCajaControlador($datos)
	{

		$pagina = $this->limpiarCadena($datos["page"]);
		$registros = $this->limpiarCadena($datos["per_page"]);
		$campoOrden = $this->limpiarCadena($datos["campoOrden"]);
		$orden = $this->limpiarCadena($datos["orden"]);

		$url = $this->limpiarCadena($datos["url"]);
		$url = APP_URL . $url . "/";

		$busqueda = $this->limpiarCadena($datos["busqueda"]);
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if (isset($busqueda) && $busqueda != "") {

			$consulta_datos = "SELECT * FROM caja WHERE numero LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_caja) FROM caja WHERE numero LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%'";
		} else {

			$consulta_datos = "SELECT * FROM caja ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_caja) FROM caja";
		}

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);

		$tabla .= '
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead style="background:#B99654;color:#ffffff;">
		                <tr>
		                    <th class="has-text-centered is-primary">Numero</th>
		                    <th class="has-text-centered is-primary">Nombre</th>
		                    <th class="has-text-centered is-primary">Saldo Inicial</th>
		                    <th class="has-text-centered is-primary">Actualizar</th>
		                    <th class="has-text-centered is-primary">Eliminar</th>
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
							<td>' . $rows['numero'] . '</td>
							<td>' . $rows['nombre'] . '</td>
							<td>' . $rows['saldo_inicial'] . '</td>
			                <td>
			                    <a href="' . APP_URL . 'cashierUpdate/' . $rows['id_caja'] . '/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	
			                    	<button type="submit" class="button is-danger is-rounded is-small" onclick="eliminarCaja(' . $rows['id_caja'] . ')">
			                    		<i class="far fa-trash-alt fa-fw"></i>
			                    	</button>
			                   
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
			                <td colspan="5">
			                    <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
			} else {
				$tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="5">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
			}
		}

		$tabla .= '</tbody></table></div>';

		### Paginacion ###
		if ($total > 0 && $pagina <= $numeroPaginas) {
			$tabla .= '<p class="has-text-right">Mostrando cajas <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}


	/*----------  Controlador eliminar caja  ----------*/
	public function eliminarCajaControlador()
	{

		$id = $this->limpiarCadena($_POST['id_caja']);

		if ($id == 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar la caja principal del sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando caja #
		$datos = $this->ejecutarConsulta("SELECT * FROM caja WHERE id_caja='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado la caja en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Verificando ventas #
		$check_ventas = $this->ejecutarConsulta("SELECT id_caja FROM sesiones_caja WHERE id_caja='$id' LIMIT 1");
		if ($check_ventas->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar la caja del sistema ya que tiene sesiones asociadas",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando usuarios #
		$check_usuarios = $this->ejecutarConsulta("SELECT id_caja FROM usuario WHERE id_caja='$id' LIMIT 1");
		if ($check_usuarios->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar la caja del sistema ya que tiene usuarios asociados",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$eliminarCaja = $this->eliminarRegistro("caja", "id_caja", $id);

		if ($eliminarCaja->rowCount() == 1) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Caja eliminada",
				"texto" => "La caja " . $datos['nombre'] . " #" . $datos['numero'] . " ha sido eliminada del sistema correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido eliminar la caja " . $datos['nombre'] . " #" . $datos['numero'] . " del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador actualizar caja  ----------*/
	public function actualizarCajaControlador()
	{

		$id = $this->limpiarCadena($_POST['id_caja']);

		# Verificando caja #
		$datos = $this->ejecutarConsulta("SELECT * FROM caja WHERE id_caja='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado la caja en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Almacenando datos#
		$numero = $this->limpiarCadena($_POST['numero']);
		$nombre = $this->limpiarCadena($_POST['nombre']);
		$saldo_inicial = $this->limpiarCadena($_POST['saldo_inicial']);

		# Verificando campos obligatorios #
		if ($numero == "" || $nombre == "" || $saldo_inicial == "") {
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
		if ($this->verificarDatos("[0-9]{1,5}", $numero)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NUMERO DE CAJA no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		if ($this->verificarDatos("[0-9.]{1,25}", $saldo_inicial)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El EFECTIVO DE CAJA no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando numero de caja #
		if ($datos['numero'] != $numero) {
			$check_numero = $this->ejecutarConsulta("SELECT numero FROM caja WHERE numero='$numero'");
			if ($check_numero->rowCount() > 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El número de caja ingresado ya se encuentra registrado en el sistema",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Comprobando nombre de caja #
		if ($datos['nombre'] != $nombre) {
			$check_nombre = $this->ejecutarConsulta("SELECT nombre FROM caja WHERE nombre='$nombre'");
			if ($check_nombre->rowCount() > 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El nombre o código de caja ingresado ya se encuentra registrado en el sistema",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Comprobando que el efectivo sea mayor o igual a 0 #
		$saldo_inicial = number_format($saldo_inicial, 2, '.', '');
		if ($saldo_inicial < 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No puedes colocar una cantidad de efectivo menor a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$caja_datos_up = [
			[
				"campo_nombre" => "numero",
				"campo_marcador" => ":Numero",
				"campo_valor" => $numero
			],
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "saldo_inicial",
				"campo_marcador" => ":Saldo_inicial",
				"campo_valor" => $saldo_inicial
			]
		];

		$condicion = [
			"condicion_campo" => "id_caja",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("caja", $caja_datos_up, $condicion)) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Caja actualizada",
				"texto" => "Los datos de la caja " . $datos['nombre'] . " #" . $datos['numero'] . " se actualizaron correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar los datos de la caja " . $datos['nombre'] . " #" . $datos['numero'] . ", por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
}
