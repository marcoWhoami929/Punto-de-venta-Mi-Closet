<?php

namespace app\controllers;

use app\models\mainModel;

class clientController extends mainModel
{

	/*----------  Controlador registrar cliente  ----------*/
	public function registrarClienteControlador()
	{

		# Almacenando datos#
		$tipo_cliente = $this->limpiarCadena($_POST['tipo_cliente']);
		$facebook = $this->limpiarCadena($_POST['facebook']);
		$nombre = $this->limpiarCadena($_POST['nombre']);
		$apellidos = $this->limpiarCadena($_POST['apellidos']);
		$usuario = $this->limpiarCadena($_POST['usuario']);
		$email = $this->limpiarCadena($_POST['email']);
		$password = $this->limpiarCadena($_POST['password']);
		$telefono = $this->limpiarCadena($_POST['telefono']);
		$celular = $this->limpiarCadena($_POST['celular']);
		$domicilio = $this->limpiarCadena($_POST['domicilio']);
		$credito = $this->limpiarCadena($_POST['credito']);

		# Verificando campos obligatorios #
		if ($nombre == "" || $apellidos == "" || $tipo_cliente == "" || $usuario == "" || $celular == "" || $domicilio == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No has llenado todos los campos que son obligatorios",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}
		//$clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);

		if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NOMBRE no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($celular != "") {
			if ($this->verificarDatos("[0-9()+]{8,20}", $celular)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El TELEFONO no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Verificando email #
		if ($email != "") {
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$check_email = $this->ejecutarConsulta("SELECT email FROM cliente WHERE email='$email'");
				if ($check_email->rowCount() > 0) {
					$alerta = [
						"tipo" => "simple",
						"titulo" => "Ocurrió un error inesperado",
						"texto" => "El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
						"icono" => "error"
					];
					return json_encode($alerta);
					exit();
				}
			} else {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Ha ingresado un correo electrónico no valido",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}
		$clave = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
		$cliente_datos_reg = [
			[
				"campo_nombre" => "tipo_cliente",
				"campo_marcador" => ":TipoCliente",
				"campo_valor" => $tipo_cliente
			],
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "apellidos",
				"campo_marcador" => ":Apellidos",
				"campo_valor" => $apellidos
			],
			[
				"campo_nombre" => "usuario",
				"campo_marcador" => ":Usuario",
				"campo_valor" => $usuario
			],
			[
				"campo_nombre" => "email",
				"campo_marcador" => ":Email",
				"campo_valor" => $email
			],
			[
				"campo_nombre" => "password",
				"campo_marcador" => ":Password",
				"campo_valor" => $clave
			],
			[
				"campo_nombre" => "telefono",
				"campo_marcador" => ":Telefono",
				"campo_valor" => $telefono
			],
			[
				"campo_nombre" => "celular",
				"campo_marcador" => ":Celular",
				"campo_valor" => $celular
			],
			[
				"campo_nombre" => "domicilio",
				"campo_marcador" => ":Domicilio",
				"campo_valor" => $domicilio
			],
			[
				"campo_nombre" => "facebook",
				"campo_marcador" => ":Facebook",
				"campo_valor" => $facebook
			],
			[
				"campo_nombre" => "credito",
				"campo_marcador" => ":credito",
				"campo_valor" => $credito
			]
		];

		$registrar_cliente = $this->guardarDatos("cliente", $cliente_datos_reg);

		if ($registrar_cliente->rowCount() == 1) {
			$alerta = [
				"tipo" => "limpiar",
				"titulo" => "Cliente registrado",
				"texto" => "El cliente " . $nombre . " " . $apellidos . " se registro con exito",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo registrar el cliente, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador listar cliente  ----------*/
	public function listarClienteControlador($pagina, $registros, $url, $busqueda)
	{

		$pagina = $this->limpiarCadena($pagina);
		$registros = $this->limpiarCadena($registros);

		$url = $this->limpiarCadena($url);
		$url = APP_URL . $url . "/";

		$busqueda = $this->limpiarCadena($busqueda);
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if (isset($busqueda) && $busqueda != "") {

			$consulta_datos = "SELECT * FROM cliente WHERE ((id_cliente!='1') and nombre LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR email LIKE '%$busqueda%') ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_cliente) FROM cliente WHERE ((id_cliente!='1') AND ( nombre LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR email LIKE '%$busqueda%'))";
		} else {

			$consulta_datos = "SELECT * FROM cliente WHERE id_cliente!='1' ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_cliente) FROM cliente WHERE id_cliente!='1'";
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
		                    <th class="has-text-centered" style="color:#ffffff">#</th>
		                    <th class="has-text-centered" style="color:#ffffff">Tipo Cliente</th>
		                    <th class="has-text-centered" style="color:#ffffff">Nombre</th>
		                    <th class="has-text-centered" style="color:#ffffff">Email</th>
							<th class="has-text-centered" style="color:#ffffff">Celular</th>
							<th class="has-text-centered" style="color:#ffffff">Crédito</th>
							<th class="has-text-centered" style="color:#ffffff">Pagado</th>
							<th class="has-text-centered" style="color:#ffffff">Pendiente</th>
		                    <th class="has-text-centered" style="color:#ffffff">Actualizar</th>
		                    <th class="has-text-centered" style="color:#ffffff">Eliminar</th>
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
							<td>' . $contador . '</td>
							<td>' . $rows['tipo_cliente'] . '</td>
							<td>' . $rows['nombre'] . ' ' . $rows['apellidos'] . '</td>
							<td>' . $rows['email'] . '</td>
							<td>' . $rows['celular'] . '</td>
							<td>' . $rows['credito'] . '</td>
							<td>' . $rows['pagado'] . '</td>
							<td>' . $rows['pendiente'] . '</td>
			                <td>
			                    <a href="' . APP_URL . 'clientUpdate/' . $rows['id_cliente'] . '/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="' . APP_URL . 'app/ajax/clienteAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_cliente" value="eliminar">
			                		<input type="hidden" name="id_cliente" value="' . $rows['id_cliente'] . '">

			                    	<button type="submit" class="button is-danger is-rounded is-small">
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
			                <td colspan="6">
			                    <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
			} else {
				$tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="6">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
			}
		}

		$tabla .= '</tbody></table></div>';

		### Paginacion ###
		if ($total > 0 && $pagina <= $numeroPaginas) {
			$tabla .= '<p class="has-text-right">Mostrando clientes <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}


	/*----------  Controlador eliminar cliente  ----------*/
	public function eliminarClienteControlador()
	{

		$id = $this->limpiarCadena($_POST['id_cliente']);

		if ($id == 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar el cliente principal del sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando cliente #
		$datos = $this->ejecutarConsulta("SELECT * FROM cliente WHERE id_cliente='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el cliente en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Verificando ventas #
		$check_ventas = $this->ejecutarConsulta("SELECT id_cliente FROM venta WHERE id_cliente='$id' LIMIT 1");
		if ($check_ventas->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar el cliente del sistema ya que tiene ventas asociadas",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$eliminarCliente = $this->eliminarRegistro("cliente", "id_cliente", $id);

		if ($eliminarCliente->rowCount() == 1) {

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Cliente eliminado",
				"texto" => "El cliente " . $datos['nombre'] . " " . $datos['apellidos'] . " ha sido eliminado del sistema correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido eliminar el cliente " . $datos['nombre'] . " " . $datos['apellidos'] . " del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador actualizar cliente  ----------*/
	public function actualizarClienteControlador()
	{

		$id = $this->limpiarCadena($_POST['id_cliente']);

		# Verificando cliente #
		$datos = $this->ejecutarConsulta("SELECT * FROM cliente WHERE id_cliente='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el cliente en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Almacenando datos#
		$tipo_cliente = $this->limpiarCadena($_POST['tipo_cliente']);
		$facebook = $this->limpiarCadena($_POST['facebook']);
		$nombre = $this->limpiarCadena($_POST['nombre']);
		$apellidos = $this->limpiarCadena($_POST['apellidos']);
		$usuario = $this->limpiarCadena($_POST['usuario']);
		$email = $this->limpiarCadena($_POST['email']);
		$telefono = $this->limpiarCadena($_POST['telefono']);
		$celular = $this->limpiarCadena($_POST['celular']);
		$domicilio = $this->limpiarCadena($_POST['domicilio']);
		$credito = $this->limpiarCadena($_POST['credito']);

		# Verificando campos obligatorios #
		if ($nombre == "" || $apellidos == "" || $celular == "" || $tipo_cliente == "" || $usuario == ""  || $domicilio == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No has llenado todos los campos que son obligatorios",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NOMBRE no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}



		if ($celular != "") {
			if ($this->verificarDatos("[0-9()+]{8,20}", $celular)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El TELEFONO no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}



		# Verificando email #
		if ($email != "" && $datos['email'] != $email) {
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$check_email = $this->ejecutarConsulta("SELECT email FROM cliente WHERE email='$email'");
				if ($check_email->rowCount() > 0) {
					$alerta = [
						"tipo" => "simple",
						"titulo" => "Ocurrió un error inesperado",
						"texto" => "El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
						"icono" => "error"
					];
					return json_encode($alerta);
					exit();
				}
			} else {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Ha ingresado un correo electrónico no valido",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}
		$cliente_datos_up = [
			[
				"campo_nombre" => "tipo_cliente",
				"campo_marcador" => ":TipoCliente",
				"campo_valor" => $tipo_cliente
			],
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "apellidos",
				"campo_marcador" => ":Apellidos",
				"campo_valor" => $apellidos
			],
			[
				"campo_nombre" => "usuario",
				"campo_marcador" => ":Usuario",
				"campo_valor" => $usuario
			],
			[
				"campo_nombre" => "email",
				"campo_marcador" => ":Email",
				"campo_valor" => $email
			],
			[
				"campo_nombre" => "telefono",
				"campo_marcador" => ":Telefono",
				"campo_valor" => $telefono
			],
			[
				"campo_nombre" => "celular",
				"campo_marcador" => ":Celular",
				"campo_valor" => $celular
			],
			[
				"campo_nombre" => "domicilio",
				"campo_marcador" => ":Domicilio",
				"campo_valor" => $domicilio
			],
			[
				"campo_nombre" => "facebook",
				"campo_marcador" => ":Facebook",
				"campo_valor" => $facebook
			],
			[
				"campo_nombre" => "credito",
				"campo_marcador" => ":credito",
				"campo_valor" => $credito
			]
		];

		$condicion = [
			"condicion_campo" => "id_cliente",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("cliente", $cliente_datos_up, $condicion)) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Cliente actualizado",
				"texto" => "Los datos del cliente " . $datos['nombre'] . " " . $datos['apellidos'] . " se actualizaron correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar los datos del cliente " . $datos['nombre'] . " " . $datos['apellidos'] . ", por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
}
