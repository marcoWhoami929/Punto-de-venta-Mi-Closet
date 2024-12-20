<?php

namespace app\controllers;

use app\models\mainModel;

class userController extends mainModel
{

	/*----------  Controlador registrar usuario  ----------*/
	public function registrarUsuarioControlador()
	{

		# Almacenando datos#
		$nombre = $this->limpiarCadena($_POST['nombre']);
		$usuario = $this->limpiarCadena($_POST['usuario']);
		$email = $this->limpiarCadena($_POST['email']);
		$clave1 = $this->limpiarCadena($_POST['password_1']);
		$clave2 = $this->limpiarCadena($_POST['password_2']);
		$caja = $this->limpiarCadena($_POST['usuario_caja']);
		$perfil = $this->limpiarCadena($_POST['perfil_usuario']);


		# Verificando campos obligatorios #
		if ($nombre == "" || $usuario == "" || $clave1 == "" || $clave2 == "" || $perfil == "" || $caja == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No has llenado todos los campos que son obligatorios",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El USUARIO no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Las CLAVES no coinciden con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando email #
		if ($email != "") {
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$check_email = $this->ejecutarConsulta("SELECT email FROM usuario WHERE email='$email'");
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

		# Verificando claves #
		if ($clave1 != $clave2) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Las contraseñas que acaba de ingresar no coinciden, por favor verifique e intente nuevamente",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
		}

		# Verificando usuario #
		$check_usuario = $this->ejecutarConsulta("SELECT usuario FROM usuario WHERE usuario='$usuario'");
		if ($check_usuario->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El USUARIO ingresado ya se encuentra registrado, por favor elija otro",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando caja #
		$check_caja = $this->ejecutarConsulta("SELECT id_caja FROM caja WHERE id_caja='$caja'");
		if ($check_caja->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "La caja seleccionada no existe en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Directorio de imagenes #
		$img_dir = "../views/fotos/";

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
			$foto = str_ireplace(" ", "_", $nombre);
			$foto = $foto . "_" . rand(0, 100);

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


		$usuario_datos_reg = [
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
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
				"campo_marcador" => ":Clave",
				"campo_valor" => $clave
			],
			[
				"campo_nombre" => "foto",
				"campo_marcador" => ":Foto",
				"campo_valor" => $foto
			],
			[
				"campo_nombre" => "id_caja",
				"campo_marcador" => ":Caja",
				"campo_valor" => $caja
			],
			[
				"campo_nombre" => "perfil",
				"campo_marcador" => ":Perfil",
				"campo_valor" => $perfil
			],
			[
				"campo_nombre" => "estatus",
				"campo_marcador" => ":Estatus",
				"campo_valor" => '1'
			]
		];

		$registrar_usuario = $this->guardarDatos("usuario", $usuario_datos_reg);

		if ($registrar_usuario->rowCount() == 1) {
			$alerta = [
				"tipo" => "limpiar",
				"titulo" => "Usuario registrado",
				"texto" => "El usuario " . $nombre . " se registro con exito",
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
				"texto" => "No se pudo registrar el usuario, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}



	/*----------  Controlador listar usuario  ----------*/

	public function listarUsuariosControlador($datos)
	{

		$pagina = $this->limpiarCadena($datos["page"]);
		$registros = $this->limpiarCadena($datos["per_page"]);
		$campoOrden = $this->limpiarCadena($datos["campoOrden"]);
		$orden = $this->limpiarCadena($datos["orden"]);

		$url = $this->limpiarCadena($datos["url"]);
		$url = APP_URL . $url . "/";

		$busqueda = $this->limpiarCadena($datos["busqueda"]);

		$sWhere = "id_usuario!='" . $_SESSION['id'] . "' and id_usuario!='1'";
		if ($datos["estatus"] != "") {
			$sWhere .= " and estatus = '" . $datos["estatus"] . "'";
		}
		if (isset($busqueda) && $busqueda != "") {
			$sWhere .= " AND nombre LIKE '%$busqueda%' OR email LIKE '%$busqueda%' OR usuario LIKE '%$busqueda%'";
		}

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$consulta_datos = "SELECT * FROM usuario WHERE $sWhere ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

		$consulta_total = "SELECT COUNT(id_usuario) FROM usuario WHERE $sWhere";

		$datos = $this->ejecutarConsulta($consulta_datos);
		$datos = $datos->fetchAll();

		$total = $this->ejecutarConsulta($consulta_total);
		$total = (int) $total->fetchColumn();

		$numeroPaginas = ceil($total / $registros);
		$tabla = "";
		if ($total >= 1 && $pagina <= $numeroPaginas) {
			$contador = $inicio + 1;
			$pag_inicio = $inicio + 1;
			$tabla .= '
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead style="background:#B99654;color:#ffffff;">
		                <tr>
		                    <th class="has-text-centered" style="color:#ffffff">#</th>
		                    <th class="has-text-centered" style="color:#ffffff">Nombre</th>
		                    <th class="has-text-centered" style="color:#ffffff">Usuario</th>
		                    <th class="has-text-centered" style="color:#ffffff">Email</th>
		                    <th class="has-text-centered" style="color:#ffffff">Foto</th>
		                    <th class="has-text-centered" style="color:#ffffff">Actualizar</th>
		                    <th class="has-text-centered" style="color:#ffffff">Eliminar</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

			foreach ($datos as $rows) {
				$tabla .= '
						<tr class="has-text-centered" >
							<td>' . $contador . '</td>
							<td>' . $rows['nombre'] . '</td>
							<td>' . $rows['usuario'] . '</td>
							<td>' . $rows['email'] . '</td>
							<td>
			                    <a href="' . APP_URL . 'userPhoto/' . $rows['id_usuario'] . '/" class="button is-info is-rounded is-small">
			                    	<i class="fas fa-camera fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                    <a href="' . APP_URL . 'userUpdate/' . $rows['id_usuario'] . '/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	
			                    	<button type="button" class="button is-danger is-rounded is-small" onclick="eliminarUsuario(\'' . $rows['id_usuario'] . '\')">
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
			$tabla .= '<p class="has-text-right">Mostrando usuarios <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}
	public function listarUsuarioControlador($pagina, $registros, $url, $busqueda)
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

			$consulta_datos = "SELECT * FROM usuario WHERE ((id_usuario!='" . $_SESSION['id'] . "' AND id_usuario!='1') AND (nombre LIKE '%$busqueda%' OR email LIKE '%$busqueda%' OR usuario LIKE '%$busqueda%')) ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_usuario) FROM usuario WHERE ((id_usuario!='" . $_SESSION['id'] . "' AND id_usuario!='1') AND (nombre LIKE '%$busqueda%' OR email LIKE '%$busqueda%' OR usuario LIKE '%$busqueda%'))";
		} else {

			$consulta_datos = "SELECT * FROM usuario WHERE id_usuario!='" . $_SESSION['id'] . "' AND id_usuario!='1' ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_usuario) FROM usuario WHERE id_usuario!='" . $_SESSION['id'] . "' AND id_usuario!='1'";
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
		                    <th class="has-text-centered" style="color:#ffffff">Nombre</th>
		                    <th class="has-text-centered" style="color:#ffffff">Usuario</th>
		                    <th class="has-text-centered" style="color:#ffffff">Email</th>
		                    <th class="has-text-centered" style="color:#ffffff">Foto</th>
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
							<td>' . $rows['nombre'] . '</td>
							<td>' . $rows['usuario'] . '</td>
							<td>' . $rows['email'] . '</td>
							<td>
			                    <a href="' . APP_URL . 'userPhoto/' . $rows['id_usuario'] . '/" class="button is-info is-rounded is-small">
			                    	<i class="fas fa-camera fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                    <a href="' . APP_URL . 'userUpdate/' . $rows['id_usuario'] . '/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="' . APP_URL . 'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_usuario" value="eliminar">
			                		<input type="hidden" name="id_usuario" value="' . $rows['id_usuario'] . '">

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
			$tabla .= '<p class="has-text-right">Mostrando usuarios <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}


	/*----------  Controlador eliminar usuario  ----------*/
	public function eliminarUsuarioControlador()
	{

		$id = $this->limpiarCadena($_POST['id_usuario']);

		if ($id == 1) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar el usuario principal del sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando usuario #
		$datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE id_usuario='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el usuario en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Verificando ventas #
		$check_ventas = $this->ejecutarConsulta("SELECT id_usuario FROM venta WHERE id_usuario='$id' LIMIT 1");
		if ($check_ventas->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar el usuario del sistema ya que tiene ventas asociadas",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando sesiones #
		$check_ventas = $this->ejecutarConsulta("SELECT id_usuario FROM sesiones_caja WHERE id_usuario='$id' LIMIT 1");
		if ($check_ventas->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar el usuario del sistema ya que tiene sesiones de caja asociadas",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		$eliminarUsuario = $this->eliminarRegistro("usuario", "id_usuario", $id);

		if ($eliminarUsuario->rowCount() == 1) {

			if (is_file("../views/fotos/" . $datos['foto'])) {
				chmod("../views/fotos/" . $datos['foto'], 0777);
				unlink("../views/fotos/" . $datos['foto']);
			}

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Usuario eliminado",
				"texto" => "El usuario " . $datos['nombre'] . " ha sido eliminado del sistema correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido eliminar el usuario " . $datos['nombre'] . " del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador actualizar usuario  ----------*/
	public function actualizarUsuarioControlador()
	{

		$id = $this->limpiarCadena($_POST['id_usuario']);

		# Verificando usuario #
		$datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE id_usuario='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el usuario en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		$admin_usuario = $this->limpiarCadena($_POST['administrador_usuario']);
		$admin_clave = $this->limpiarCadena($_POST['administrador_clave']);

		# Verificando campos obligatorios admin #
		if ($admin_usuario == "" || $admin_clave == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No ha llenado todos los campos que son obligatorios, que corresponden a su USUARIO y CLAVE",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $admin_usuario)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Su USUARIO no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $admin_clave)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "Su CLAVE no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando administrador #
		$check_admin = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario='$admin_usuario' AND id_usuario='" . $_SESSION['id'] . "'");
		if ($check_admin->rowCount() == 1) {

			$check_admin = $check_admin->fetch();

			if ($check_admin['usuario'] != $admin_usuario || !password_verify($admin_clave, $check_admin['password'])) {

				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "USUARIO o CLAVE de administrador incorrectos",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "USUARIO o CLAVE de administrador incorrectos",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		# Almacenando datos#
		$nombre = $this->limpiarCadena($_POST['nombre']);
		$usuario = $this->limpiarCadena($_POST['usuario']);
		$email = $this->limpiarCadena($_POST['email']);
		$clave1 = $this->limpiarCadena($_POST['password_1']);
		$clave2 = $this->limpiarCadena($_POST['password_2']);
		$caja = $this->limpiarCadena($_POST['usuario_caja']);
		$perfil = $this->limpiarCadena($_POST['perfil_usuario']);
		if (isset($_POST['estatus'])) {
			$estatus = 1;
		} else {
			$estatus = 0;
		}

		# Verificando campos obligatorios #
		if ($nombre == ""  || $usuario == "") {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No has llenado todos los campos que son obligatorios",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El USUARIO no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando email #
		if ($email != "" && $datos['email'] != $email) {
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$check_email = $this->ejecutarConsulta("SELECT email FROM usuario WHERE email='$email'");
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

		# Verificando claves #
		if ($clave1 != "" || $clave2 != "") {
			if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)) {

				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Las CLAVES no coinciden con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			} else {
				if ($clave1 != $clave2) {

					$alerta = [
						"tipo" => "simple",
						"titulo" => "Ocurrió un error inesperado",
						"texto" => "Las nuevas CLAVES que acaba de ingresar no coinciden, por favor verifique e intente nuevamente",
						"icono" => "error"
					];
					return json_encode($alerta);
					exit();
				} else {
					$clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
				}
			}
		} else {
			$clave = $datos['password'];
		}

		# Verificando usuario #
		if ($datos['usuario'] != $usuario) {
			$check_usuario = $this->ejecutarConsulta("SELECT usuario FROM usuario WHERE usuario='$usuario'");
			if ($check_usuario->rowCount() > 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El USUARIO ingresado ya se encuentra registrado, por favor elija otro",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		# Verificando caja #
		if ($datos['id_caja'] != $caja) {
			$check_caja = $this->ejecutarConsulta("SELECT id_caja FROM caja WHERE id_caja='$caja'");
			if ($check_caja->rowCount() <= 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La caja seleccionada no existe en el sistema",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}

		$usuario_datos_up = [
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
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
				"campo_marcador" => ":Clave",
				"campo_valor" => $clave
			],
			[
				"campo_nombre" => "id_caja",
				"campo_marcador" => ":Caja",
				"campo_valor" => $caja
			],
			[
				"campo_nombre" => "perfil",
				"campo_marcador" => ":Perfil",
				"campo_valor" => $perfil
			],
			[
				"campo_nombre" => "estatus",
				"campo_marcador" => ":Estatus",
				"campo_valor" => $estatus
			]
		];

		$condicion = [
			"condicion_campo" => "id_usuario",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)) {

			if ($id == $_SESSION['id']) {
				$_SESSION['nombre'] = $nombre;
				$_SESSION['usuario'] = $usuario;
			}

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Usuario actualizado",
				"texto" => "Los datos del usuario " . $datos['nombre'] . " se actualizaron correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar los datos del usuario " . $datos['nombre'] . ", por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador eliminar foto usuario  ----------*/
	public function eliminarFotoUsuarioControlador()
	{

		$id = $this->limpiarCadena($_POST['id_usuario']);

		# Verificando usuario #
		$datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE id_usuario='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el usuario en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Directorio de imagenes #
		$img_dir = "../views/fotos/";

		chmod($img_dir, 0777);

		if (is_file($img_dir . $datos['foto'])) {

			chmod($img_dir . $datos['foto'], 0777);

			if (!unlink($img_dir . $datos['foto'])) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Error al intentar eliminar la foto del usuario, por favor intente nuevamente",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado la foto del usuario en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$usuario_datos_up = [
			[
				"campo_nombre" => "foto",
				"campo_marcador" => ":Foto",
				"campo_valor" => ""
			]
		];

		$condicion = [
			"condicion_campo" => "id_usuario",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)) {

			if ($id == $_SESSION['id']) {
				$_SESSION['foto'] = "";
			}

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto eliminada",
				"texto" => "La foto del usuario " . $datos['nombre'] . "  se elimino correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto eliminada",
				"texto" => "No hemos podido actualizar algunos datos del usuario " . $datos['nombre'] . ", sin embargo la foto ha sido eliminada correctamente",
				"icono" => "warning"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador actualizar foto usuario  ----------*/
	public function actualizarFotoUsuarioControlador()
	{

		$id = $this->limpiarCadena($_POST['id_usuario']);

		# Verificando usuario #
		$datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE id_usuario='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado el usuario en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Directorio de imagenes #
		$img_dir = "../views/fotos/";

		# Comprobar si se selecciono una imagen #
		if ($_FILES['foto']['name'] == "" && $_FILES['foto']['size'] <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No ha seleccionado una foto para el usuario",
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
			$foto = str_ireplace(" ", "_", $datos['nombre']);
			$foto = $foto . "_" . rand(0, 100);
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

		$usuario_datos_up = [
			[
				"campo_nombre" => "foto",
				"campo_marcador" => ":Foto",
				"campo_valor" => $foto
			]
		];

		$condicion = [
			"condicion_campo" => "id_usuario",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)) {

			if ($id == $_SESSION['id']) {
				$_SESSION['foto'] = $foto;
			}

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto actualizada",
				"texto" => "La foto del usuario " . $datos['nombre'] . " se actualizo correctamente",
				"icono" => "success"
			];
		} else {

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Foto actualizada",
				"texto" => "No hemos podido actualizar algunos datos del usuario " . $datos['nombre'] . "  , sin embargo la foto ha sido actualizada",
				"icono" => "warning"
			];
		}

		return json_encode($alerta);
	}
}
