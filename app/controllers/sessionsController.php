<?php

namespace app\controllers;

use app\models\mainModel;

class sessionsController extends mainModel
{

    /*----------  Controlador listar sesiones  ----------*/
    public function listarSesionesControlador($datos)
    {

        $pagina = $this->limpiarCadena($datos["page"]);
        $registros = $this->limpiarCadena($datos["per_page"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);

        $url = $this->limpiarCadena($datos["url"]);
        $url = APP_URL . $url . "/";

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        $campos = "sess.*,usr.nombre as 'nombreUsuario', caj.nombre as 'nombreCaja' ";


        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {

            $consulta_datos = "SELECT $campos FROM sesiones_caja as sess INNER JOIN usuario as usr ON sess.id_caja = usr.id_caja INNER JOIN caja as caj ON sess.id_caja = caj.id_caja WHERE sess.codigo_sesion LIKE '%$busqueda%' OR caj.nombre LIKE '%$busqueda%' OR usr.nombre LIKE '%$busqueda%' ORDER BY sess.$campoOrden $orden LIMIT $inicio,$registros";

            $consulta_total = "SELECT COUNT(id_sesion) FROM sesiones_caja as sess INNER JOIN usuario as usr ON sess.id_caja = usr.id_caja INNER JOIN caja as caj ON sess.id_caja = caj.id_caja WHERE sess.codigo_sesion LIKE '%$busqueda%' OR caj.nombre LIKE '%$busqueda%' OR usr.nombre LIKE '%$busqueda%'";
        } else {

            $consulta_datos = "SELECT $campos FROM sesiones_caja as sess INNER JOIN usuario as usr ON sess.id_caja = usr.id_caja INNER JOIN caja as caj ON sess.id_caja = caj.id_caja ORDER BY sess.$campoOrden $orden LIMIT $inicio,$registros";

            $consulta_total = "SELECT COUNT(id_sesion) FROM sesiones_caja as sess INNER JOIN usuario as usr ON sess.id_caja = usr.id_caja INNER JOIN caja as caj ON sess.id_caja = caj.id_caja";
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

            foreach ($datos as $rows) {


                $tabla .= '
                     
                                <div class="card mb-3">
                                <header class="card-header" style="background:#B99654">
                                    <p class="card-header-title" style="color:#ffffff">' . $rows["codigo_sesion"] . '</p>
                                    <button class="card-header-icon" aria-label="more options">
                                    <span class="tag is-success is-large">Sesión Activa</span>
                                    </button>
                                </header>
                                <div class="card-content">
                                    <div class="content">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec
                                    iaculis mauris.
                                    <a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>
                                    <br />
                                    <time datetime="2016-1-1">11:09 PM - 1 Jan 2016</time>
                                    </div>
                                </div>
                                <footer class="card-footer">
                                    <a href="#" class="card-footer-item">Save</a>
                                    <a href="#" class="card-footer-item">Edit</a>
                                    <a href="#" class="card-footer-item">Delete</a>
                                </footer>
                                </div>
                          
                    
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
					No hay Sesiones Registradas Actualmente
				</div>
			</article>
					';
            }
        }


        ### Paginacion ###
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando sesiones <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }

        return $tabla;
    }
}
