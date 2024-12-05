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

		$caja_datos_up = [
			[
				"campo_nombre" => "codigo_sesion",
				"campo_marcador" => ":CodigoSesion",
				"campo_valor" => $codigo_sesion
			]
		];

		$condicion = [
			"condicion_campo" => "id_usuario",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $_SESSION["id"]
		];

		$actualizar_sesion = $this->actualizarDatos("usuario", $caja_datos_up, $condicion);

		if ($registrar_caja->rowCount() == 1) {
			$_SESSION['sesion_caja'] = $codigo_sesion;
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
	/*----------  Controlador cerrar caja  ----------*/
	public function cerrarCajaControlador()
	{

		# Almacenando datos#

		$saldo_final = $this->limpiarCadena($_POST["saldo_final"]);
		$diferencia = $this->limpiarCadena($_POST["diferencia"]);
		$observaciones = $this->limpiarCadena($_POST["observaciones"]);
		$codigo_sesion = $this->limpiarCadena($_POST["sesion_caja"]);
		$dif_dn_1 = $this->limpiarCadena($_POST["dif_dn_1"]);
		$dif_dn_2 = $this->limpiarCadena($_POST["dif_dn_2"]);
		$dif_dn_3 = $this->limpiarCadena($_POST["dif_dn_3"]);
		$dif_dn_4 = $this->limpiarCadena($_POST["dif_dn_4"]);
		$dif_dn_5 = $this->limpiarCadena($_POST["dif_dn_5"]);
		$dif_dn_6 = $this->limpiarCadena($_POST["dif_dn_6"]);
		$dif_dn_7 = $this->limpiarCadena($_POST["dif_dn_7"]);
		$dif_dn_8 = $this->limpiarCadena($_POST["dif_dn_8"]);
		$dif_dn_9 = $this->limpiarCadena($_POST["dif_dn_9"]);
		$dif_dn_10 = $this->limpiarCadena($_POST["dif_dn_10"]);
		$dif_dn_11 = $this->limpiarCadena($_POST["dif_dn_11"]);
		$dif_dn_12 = $this->limpiarCadena($_POST["dif_dn_12"]);
		$dif_dn_13 = $this->limpiarCadena($_POST["dif_dn_13"]);
		$total_denominaciones_caja = $this->limpiarCadena($_POST["total_denominaciones_caja"]);

		# Verificando campos obligatorios #
		if ($saldo_final == "" || $saldo_final == '0' || $saldo_final == '0.00') {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El saldo final no puede estar vacio",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Comprobando que el efectivo sea mayor o igual a 0 #
		$saldo_final = number_format($saldo_final, 2, '.', '');
		if ($saldo_final < 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No puedes colocar una cantidad de efectivo menor a 0",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}
		date_default_timezone_set('America/Mexico_City');
		$fecha_cierre = date('Y/m/d H:i:s');

		$caja_datos_up = [
			[
				"campo_nombre" => "saldo_final",
				"campo_marcador" => ":SaldoFinal",
				"campo_valor" => $saldo_final
			],
			[
				"campo_nombre" => "diferencia",
				"campo_marcador" => ":Diferencia",
				"campo_valor" => $diferencia
			],
			[
				"campo_nombre" => "observaciones",
				"campo_marcador" => ":Observaciones",
				"campo_valor" => $observaciones
			],
			[
				"campo_nombre" => "fecha_cierre",
				"campo_marcador" => ":FechaCierre",
				"campo_valor" => $fecha_cierre
			],
			[
				"campo_nombre" => "estado",
				"campo_marcador" => ":Estado",
				"campo_valor" => "cerrada"
			]

		];

		$condicion = [
			"condicion_campo" => "codigo_sesion",
			"condicion_marcador" => ":CodigoSesion",
			"condicion_valor" => $codigo_sesion
		];

		$actualizar_sesion = $this->actualizarDatos("sesiones_caja", $caja_datos_up, $condicion);
		if ($total_denominaciones_caja != '0.00') {


			$denominaciones_reg = [
				[
					"campo_nombre" => "codigo_sesion",
					"campo_marcador" => ":Codigo",
					"campo_valor" => $codigo_sesion
				],
				[
					"campo_nombre" => "id_caja",
					"campo_marcador" => ":IdCaja",
					"campo_valor" => $_SESSION["caja"]
				],
				[
					"campo_nombre" => "dn1",
					"campo_marcador" => ":Dn1",
					"campo_valor" => $dif_dn_1
				],
				[
					"campo_nombre" => "dn2",
					"campo_marcador" => ":Dn2",
					"campo_valor" => $dif_dn_2
				],
				[
					"campo_nombre" => "dn3",
					"campo_marcador" => ":Dn3",
					"campo_valor" => $dif_dn_3
				],
				[
					"campo_nombre" => "dn4",
					"campo_marcador" => ":Dn4",
					"campo_valor" => $dif_dn_4
				],
				[
					"campo_nombre" => "dn5",
					"campo_marcador" => ":Dn5",
					"campo_valor" => $dif_dn_5
				],
				[
					"campo_nombre" => "dn6",
					"campo_marcador" => ":Dn6",
					"campo_valor" => $dif_dn_6
				],
				[
					"campo_nombre" => "dn7",
					"campo_marcador" => ":Dn7",
					"campo_valor" => $dif_dn_7
				],
				[
					"campo_nombre" => "dn8",
					"campo_marcador" => ":Dn8",
					"campo_valor" => $dif_dn_8
				],
				[
					"campo_nombre" => "dn9",
					"campo_marcador" => ":Dn9",
					"campo_valor" => $dif_dn_9
				],
				[
					"campo_nombre" => "dn10",
					"campo_marcador" => ":Dn10",
					"campo_valor" => $dif_dn_10
				],
				[
					"campo_nombre" => "dn11",
					"campo_marcador" => ":Dn11",
					"campo_valor" => $dif_dn_11
				],
				[
					"campo_nombre" => "dn12",
					"campo_marcador" => ":Dn12",
					"campo_valor" => $dif_dn_12
				],
				[
					"campo_nombre" => "dn13",
					"campo_marcador" => ":Dn13",
					"campo_valor" => $dif_dn_13
				],
				[
					"campo_nombre" => "total_caja",
					"campo_marcador" => ":TotalCaja",
					"campo_valor" => $total_denominaciones_caja
				]
			];

			$registrar_denominaciones = $this->guardarDatos("corte_caja", $denominaciones_reg);
		}

		if ($actualizar_sesion->rowCount() == 1) {

			unset($_SESSION['sesion_caja']);

			$usuario_up = [
				[
					"campo_nombre" => "codigo_sesion",
					"campo_marcador" => ":CodigoSesion",
					"campo_valor" => NULL
				]
			];

			$condicion = [
				"condicion_campo" => "id_usuario",
				"condicion_marcador" => ":IdUsuaro",
				"condicion_valor" => $_SESSION["id"]
			];

			$actualizar_sesion = $this->actualizarDatos("usuario", $usuario_up, $condicion);

			$bitacora_reg = [
				[
					"campo_nombre" => "accion",
					"campo_marcador" => ":Accion",
					"campo_valor" => 'Cierre de Caja Con N° de Sesion ' . $codigo_sesion . ''
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
				"titulo" => "Sesion Finalizada",
				"texto" => "El cierre de Caja Con " . " #" . $codigo_sesion . " se ha finalizado con exito",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo cerrar la sesion de caja, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
	/*----------  Controlador registrar caja  ----------*/
	public function entradaEfectivoCajaControlador()
	{

		# Almacenando datos#
		$efectivo = $this->limpiarCadena($_POST['efectivo']);
		$motivo = $this->limpiarCadena($_POST['motivo']);
		$sesion_caja = $this->limpiarCadena($_POST['sesion_caja']);
		$id_caja = $_SESSION["caja"];

		# Verificando campos obligatorios #
		if ($efectivo == "" || $efectivo == "0" || $efectivo == "0.00") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El campo efectivo no puede estar vacio",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		$caja_datos_reg = [
			[
				"campo_nombre" => "sesion_caja",
				"campo_marcador" => ":SesionCaja",
				"campo_valor" => $sesion_caja
			],
			[
				"campo_nombre" => "id_caja",
				"campo_marcador" => ":IdCaja",
				"campo_valor" => $id_caja
			],
			[
				"campo_nombre" => "tipo_movimiento",
				"campo_marcador" => ":TipoMovimiento",
				"campo_valor" => 'ingreso'
			],
			[
				"campo_nombre" => "monto",
				"campo_marcador" => ":Monto",
				"campo_valor" => $efectivo
			],
			[
				"campo_nombre" => "descripcion",
				"campo_marcador" => ":Descripcion",
				"campo_valor" => 'ENTRADA'
			]
		];

		$registrar_movimiento_caja = $this->guardarDatos("movimiento_caja", $caja_datos_reg);


		if ($registrar_movimiento_caja->rowCount() == 1) {
			$bitacora_reg = [
				[
					"campo_nombre" => "accion",
					"campo_marcador" => ":Accion",
					"campo_valor" => 'Ingreso de Efectivo a Caja en la Sesion N° ' . $sesion_caja . ''
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
				"titulo" => "Exitoso",
				"texto" => "La entrada de efectivo se ha realizado con exito",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo registrar el ingreso de efectivo, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
	public function salidaEfectivoCajaControlador()
	{

		# Almacenando datos#
		$efectivo = $this->limpiarCadena($_POST['efectivo']);
		$motivo = $this->limpiarCadena($_POST['motivo']);
		$sesion_caja = $this->limpiarCadena($_POST['sesion_caja']);
		$id_caja = $_SESSION["caja"];

		# Verificando campos obligatorios #
		if ($efectivo == "" || $efectivo == "0" || $efectivo == "0.00") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El campo efectivo no puede estar vacio",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		$caja_datos_reg = [
			[
				"campo_nombre" => "sesion_caja",
				"campo_marcador" => ":SesionCaja",
				"campo_valor" => $sesion_caja
			],
			[
				"campo_nombre" => "id_caja",
				"campo_marcador" => ":IdCaja",
				"campo_valor" => $id_caja
			],
			[
				"campo_nombre" => "tipo_movimiento",
				"campo_marcador" => ":TipoMovimiento",
				"campo_valor" => 'egreso'
			],
			[
				"campo_nombre" => "monto",
				"campo_marcador" => ":Monto",
				"campo_valor" => $efectivo
			],
			[
				"campo_nombre" => "descripcion",
				"campo_marcador" => ":Descripcion",
				"campo_valor" => 'SALIDA'
			]
		];

		$registrar_movimiento_caja = $this->guardarDatos("movimiento_caja", $caja_datos_reg);


		if ($registrar_movimiento_caja->rowCount() == 1) {
			$bitacora_reg = [
				[
					"campo_nombre" => "accion",
					"campo_marcador" => ":Accion",
					"campo_valor" => 'Salida de Efectivo de Caja en la Sesion N° ' . $sesion_caja . ''
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
				"titulo" => "Exitoso",
				"texto" => "La salida de efectivo se ha realizado con exito",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo registrar la salida de efectivo, por favor intente nuevamente",
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

		$sWhere = "id_caja!='0'";
		$busqueda = $this->limpiarCadena($datos["busqueda"]);
		if (isset($busqueda) && $busqueda != "") {
			$sWhere .= " AND numero LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%'";
		}
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$consulta_datos = "SELECT * FROM caja WHERE $sWhere ORDER BY $campoOrden $orden LIMIT $inicio,$registros";
		$consulta_total = "SELECT COUNT(id_caja) FROM caja WHERE $sWhere";

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
						<thead>
							<tr>
								<th class="has-text-centered is-info">Numero</th>
								<th class="has-text-centered is-info">Nombre</th>
								<th class="has-text-centered is-info">Actualizar</th>
								<th class="has-text-centered is-info">Eliminar</th>
							</tr>
						</thead>
						<tbody>
				';
			foreach ($datos as $rows) {
				$tabla .= '
						<tr class="has-text-centered" >
							<td>' . $rows['numero'] . '</td>
							<td>' . $rows['nombre'] . '</td>

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
	public function obtenerDatosCorteCaja()
	{
		$sesion = $this->limpiarCadena($_POST['sesion_caja']);

		$datos = $this->ejecutarConsulta("SELECT sesion.*,count(pay.codigo_venta) as 'num_ventas',(sesion.efectivo+sesion.transferencia+sesion.tarjeta_debito+sesion.tarjeta_credito) as 'total_ventas',sum(IF(mov.descripcion = 'ENTRADA',mov.monto,0)) as 'entrada_efectivo',sum(IF(mov.descripcion = 'SALIDA',mov.monto,0)) as 'salida_efectivo' FROM `sesiones_caja` as sesion LEFT OUTER JOIN movimiento_caja as mov ON sesion.codigo_sesion = mov.sesion_caja LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago WHERE sesion.codigo_sesion = '" . $sesion . "'");
		$datos = $datos->fetch();
		return json_encode($datos);
	}
	public function obtenerDetallePago()
	{
		$codigo_venta = $this->limpiarCadena($_POST['codigo_venta']);

		$datos = $this->ejecutarConsulta("SELECT venta.*,met.metodo FROM `venta` INNER JOIN `metodopago` as met ON venta.forma_pago = met.id_metodo_pago WHERE venta.codigo ='" . $codigo_venta . "'");

		$datos = $datos->fetch();

		return json_encode($datos);
	}
	public function obtenerDetalleCorteCaja()
	{
		$sesion = $this->limpiarCadena($_POST['sesion_caja']);

		$datos = $this->ejecutarConsulta("SELECT sesion.*,count(pay.codigo_venta) as 'num_ventas',(sesion.efectivo+sesion.transferencia+sesion.tarjeta_debito+sesion.tarjeta_credito) as 'total_ventas',sum(IF(mov.descripcion = 'ENTRADA',mov.monto,0)) as 'entrada_efectivo',sum(IF(mov.descripcion = 'SALIDA',mov.monto,0)) as 'salida_efectivo' FROM `sesiones_caja` as sesion LEFT OUTER JOIN movimiento_caja as mov ON sesion.codigo_sesion = mov.sesion_caja LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago WHERE sesion.codigo_sesion = '" . $sesion . "'");
		$datos = $datos->fetch();
		return json_encode($datos);
	}
	public function obtenerDetalleDenominaciones()
	{
		$sesion = $this->limpiarCadena($_POST['sesion_caja']);

		$datos = $this->ejecutarConsulta("SELECT (dn1/1000) as 'det_dn1',(dn2/500) as 'det_dn2',(dn3/200) as 'det_dn3',(dn4/100) as 'det_dn4',(dn5/50) as 'det_dn5',(dn6/20) as 'det_dn6',(dn7/10) as 'det_dn7',(dn8/5) as 'det_dn8',(dn9/2) as 'det_dn9',(dn10/1) as 'det_dn10',(dn11/0.50) as 'det_dn11',(dn12/0.20) as 'det_dn12',(dn13/0.10) as 'det_dn13',dn1,dn2,dn3,dn4,dn5,dn6,dn7,dn8,dn9,dn10,dn11,dn12,dn13,total_caja FROM `corte_caja` WHERE codigo_sesion = '" . $sesion . "'");
		$datos = $datos->fetch();
		return json_encode($datos);
	}
}
