<?php

namespace app\controllers;

use app\models\mainModel;

class categoryController extends mainModel
{

	/*----------  Controlador registrar categoria  ----------*/
	public function registrarCategoriaControlador()
	{

		# Almacenando datos#
		$nombre = $this->limpiarCadena($_POST['nombre']);
		$ubicacion = $this->limpiarCadena($_POST['ubicacion']);

		# Verificando campos obligatorios #
		if ($nombre == "") {
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
		if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NOMBRE no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		# Verificando nombre #
		$check_nombre = $this->ejecutarConsulta("SELECT nombre FROM categoria WHERE nombre='$nombre'");
		if ($check_nombre->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NOMBRE ingresado ya se encuentra registrado, por favor elija otro",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		$categoria_datos_reg = [
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "ubicacion",
				"campo_marcador" => ":Ubicacion",
				"campo_valor" => $ubicacion
			]
		];

		$registrar_categoria = $this->guardarDatos("categoria", $categoria_datos_reg);

		if ($registrar_categoria->rowCount() == 1) {
			$alerta = [
				"tipo" => "limpiar",
				"titulo" => "Categoría registrada",
				"texto" => "La categoría " . $nombre . " se registro con exito",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No se pudo registrar la categoría, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador listar categoria  ----------*/

	public function listarCategoriasControlador($datos)
	{

		$pagina = $this->limpiarCadena($datos["page"]);
		$registros = $this->limpiarCadena($datos["per_page"]);
		$campoOrden = $this->limpiarCadena($datos["campoOrden"]);
		$orden = $this->limpiarCadena($datos["orden"]);

		$url = $this->limpiarCadena($datos["url"]);
		$url = APP_URL . $url . "/";

		$sWhere = "id_categoria !='0'";
		$busqueda = $this->limpiarCadena($datos["busqueda"]);
		if (isset($busqueda) && $busqueda != "") {
			$sWhere .= " AND nombre LIKE '%$busqueda%' OR ubicacion LIKE '%$busqueda%'";
		}
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		$consulta_datos = "SELECT * FROM categoria WHERE $sWhere ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

		$consulta_total = "SELECT COUNT(id_categoria) FROM categoria WHERE $sWhere ";

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
						<th class="has-text-centered" style="color:#ffffff">#</th>
						<th class="has-text-centered" style="color:#ffffff">Nombre</th>
						<th class="has-text-centered" style="color:#ffffff">Ubicacion</th>
						<th class="has-text-centered" style="color:#ffffff">Productos</th>
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
							<td>' . $rows['ubicacion'] . '</td>
							<td>
			                    <a href="' . APP_URL . 'productCategory/' . $rows['id_categoria'] . '/" class="button is-info is-rounded is-small">
			                    	<i class="fas fa-boxes fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                    <a href="' . APP_URL . 'categoryUpdate/' . $rows['id_categoria'] . '/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="' . APP_URL . 'app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_categoria" value="eliminar">
			                		<input type="hidden" name="id_categoria" value="' . $rows['id_categoria'] . '">

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
	public function listarCategoriaControlador($pagina, $registros, $url, $busqueda)
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

			$consulta_datos = "SELECT * FROM categoria WHERE nombre LIKE '%$busqueda%' OR ubicacion LIKE '%$busqueda%' ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_categoria) FROM categoria WHERE nombre LIKE '%$busqueda%' OR ubicacion LIKE '%$busqueda%'";
		} else {

			$consulta_datos = "SELECT * FROM categoria ORDER BY nombre ASC LIMIT $inicio,$registros";

			$consulta_total = "SELECT COUNT(id_categoria) FROM categoria";
		}

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
		                    <th class="has-text-centered" style="color:#ffffff">Ubicacion</th>
		                    <th class="has-text-centered" style="color:#ffffff">Productos</th>
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
							<td>' . $rows['ubicacion'] . '</td>
							<td>
			                    <a href="' . APP_URL . 'productCategory/' . $rows['id_categoria'] . '/" class="button is-info is-rounded is-small">
			                    	<i class="fas fa-boxes fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                    <a href="' . APP_URL . 'categoryUpdate/' . $rows['id_categoria'] . '/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="' . APP_URL . 'app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_categoria" value="eliminar">
			                		<input type="hidden" name="id_categoria" value="' . $rows['id_categoria'] . '">

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
						
				<article class="message is-warning mt-4 mb-4">
					 <div class="message-header">
					    <p></p>
					 </div>
				    <div class="message-body has-text-centered">
				    	<i class="fas fa-exclamation-triangle fa-5x"></i><br>
						No hay Categorias Registradas Actualmente
				    </div>
				</article>';
			}
		}

		$tabla .= '</tbody></table></div>';

		### Paginacion ###
		if ($total > 0 && $pagina <= $numeroPaginas) {
			$tabla .= '<p class="has-text-right">Mostrando categorías <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

			$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
		}

		return $tabla;
	}


	/*----------  Controlador eliminar categoria  ----------*/
	public function eliminarCategoriaControlador()
	{

		$id = $this->limpiarCadena($_POST['id_categoria']);

		# Verificando categoria #
		$datos = $this->ejecutarConsulta("SELECT * FROM categoria WHERE id_categoria='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado la categoría en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Verificando productos #
		$check_productos = $this->ejecutarConsulta("SELECT id_categoria FROM producto WHERE id_categoria='$id' LIMIT 1");
		if ($check_productos->rowCount() > 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No podemos eliminar la categoría del sistema ya que tiene productos asociados",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}

		$eliminarCategoria = $this->eliminarRegistro("categoria", "id_categoria", $id);

		if ($eliminarCategoria->rowCount() == 1) {

			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Categoría eliminada",
				"texto" => "La categoría " . $datos['nombre'] . " ha sido eliminada del sistema correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido eliminar la categoría " . $datos['nombre'] . " del sistema, por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}


	/*----------  Controlador actualizar categoria  ----------*/
	public function actualizarCategoriaControlador()
	{

		$id = $this->limpiarCadena($_POST['id_categoria']);

		# Verificando categoria #
		$datos = $this->ejecutarConsulta("SELECT * FROM categoria WHERE id_categoria='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos encontrado la categoría en el sistema",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Almacenando datos#
		$nombre = $this->limpiarCadena($_POST['nombre']);
		$ubicacion = $this->limpiarCadena($_POST['ubicacion']);

		# Verificando campos obligatorios #
		if ($nombre == "") {
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
		if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)) {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "El NOMBRE no coincide con el formato solicitado",
				"icono" => "error"
			];
			return json_encode($alerta);
			exit();
		}


		# Verificando nombre #
		if ($datos['nombre'] != $nombre) {
			$check_nombre = $this->ejecutarConsulta("SELECT nombre FROM categoria WHERE nombre='$nombre'");
			if ($check_nombre->rowCount() > 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El NOMBRE ingresado ya se encuentra registrado, por favor elija otro",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		}


		$categoria_datos_up = [
			[
				"campo_nombre" => "nombre",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "ubicacion",
				"campo_marcador" => ":Ubicacion",
				"campo_valor" => $ubicacion
			]
		];

		$condicion = [
			"condicion_campo" => "id_categoria",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarDatos("categoria", $categoria_datos_up, $condicion)) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "Categoría actualizada",
				"texto" => "Los datos de la categoría " . $datos['nombre'] . " se actualizaron correctamente",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido actualizar los datos de la categoría " . $datos['nombre'] . ", por favor intente nuevamente",
				"icono" => "error"
			];
		}

		return json_encode($alerta);
	}
}
