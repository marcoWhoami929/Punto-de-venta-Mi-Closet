<?php

namespace app\controllers;

use app\models\mainModel;

class loginController extends mainModel
{

	/*----------  Controlador iniciar sesion  ----------*/
	public function iniciarSesionControlador()
	{

		$usuario = $this->limpiarCadena($_POST['login_usuario']);
		$clave = $this->limpiarCadena($_POST['login_clave']);

		# Verificando campos obligatorios #
		if ($usuario == "" || $clave == "") {
			echo '<article class="message is-danger">
				  <div class="message-body">
				    <strong>Ocurrió un error inesperado</strong><br>
				    No has llenado todos los campos que son obligatorios
				  </div>
				</article>';
		} else {

			# Verificando integridad de los datos #
			if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
				echo '<article class="message is-danger">
					  <div class="message-body">
					    <strong>Ocurrió un error inesperado</strong><br>
					    El USUARIO no coincide con el formato solicitado
					  </div>
					</article>';
			} else {

				# Verificando integridad de los datos #
				if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
					echo '<article class="message is-danger">
						  <div class="message-body">
						    <strong>Ocurrió un error inesperado</strong><br>
						    La CLAVE no coincide con el formato solicitado
						  </div>
						</article>';
				} else {

					# Verificando usuario #
					$check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario='$usuario'");

					if ($check_usuario->rowCount() == 1) {

						$check_usuario = $check_usuario->fetch();

						if ($check_usuario['usuario'] == $usuario && password_verify($clave, $check_usuario['password'])) {

							$_SESSION['id'] = $check_usuario['id_usuario'];
							$_SESSION['nombre'] = $check_usuario['nombre'];
							$_SESSION['usuario'] = $check_usuario['usuario'];
							$_SESSION['foto'] = $check_usuario['foto'];
							$_SESSION['caja'] = $check_usuario['id_caja'];


							if (headers_sent()) {
								echo "<script> window.location.href='" . APP_URL . "dashboard/'; </script>";
							} else {
								header("Location: " . APP_URL . "dashboard/");
							}
						} else {
							echo '<article class="message is-danger">
								  <div class="message-body">
								    <strong>Ocurrió un error inesperado</strong><br>
								    Usuario o clave incorrectos
								  </div>
								</article>';
						}
					} else {
						echo '<article class="message is-danger">
							  <div class="message-body">
							    <strong>Ocurrió un error inesperado</strong><br>
							    Usuario o clave incorrectos
							  </div>
							</article>';
					}
				}
			}
		}
	}


	/*----------  Controlador cerrar sesion  ----------*/
	public function cerrarSesionControlador()
	{

		session_destroy();

		if (headers_sent()) {
			echo "<script> window.location.href='" . APP_URL . "'; </script>";
		} else {
			header("Location: " . APP_URL . "");
		}
	}
}
