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
                if ($rows["estado"] == "abierta") {
                    $color = "is-success";
                    $texto = "Activa";
                } else {
                    $color = "is-danger";
                    $texto = "Cerrada";
                }
                $caja = $this->ejecutarConsulta("SELECT sesion.*,count(pay.codigo_venta) as 'num_ventas',(sesion.efectivo+sesion.transferencia+sesion.tarjeta_debito+sesion.tarjeta_credito) as 'total_ventas',sum(IF(mov.descripcion = 'ENTRADA',mov.monto,0)) as 'entrada_efectivo',sum(IF(mov.descripcion = 'SALIDA',mov.monto,0)) as 'salida_efectivo' FROM `sesiones_caja` as sesion LEFT OUTER JOIN movimiento_caja as mov ON sesion.codigo_sesion = mov.sesion_caja LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago WHERE mov.sesion_caja = '" . $rows["codigo_sesion"]  . "'");
                $caja = $caja->fetch();

                $tabla .= '<div class="column">
                                <div class="card">
                                <header class="card-header" style="background:#B99654">
                                    <p class="card-header-title" style="color:#ffffff">' . $rows["codigo_sesion"] . '</p>
                                    <p class="card-header-title" style="color:#ffffff">' . $rows["fecha_cierre"] . '</p>
                                    <button class="card-header-icon" aria-label="more options">
                                    <span class="tag ' . $color . ' is-large">' . $texto . '</span>
                                    </button>
                                </header>
                                <div class="card-content">
                                    <div class="content">
                                        <div class="columns">
                                            <div class="column">
                                                <label class="has-text-centered"><strong style="color:#B99654">Fecha Apertura</strong></label>
                                                <h4 class="is-info">' . $rows["fecha_apertura"] . '</h4>
                                            </div>
                                            <div class="column">
                                                <label class="has-text-centered"><strong style="color:#B99654">Usuario</strong></label>
                                                <h4 class="is-info">' . $rows["nombreUsuario"] . '</h4>
                                            </div>
                                            <div class="column">
                                                <label class="has-text-centered"><strong style="color:#B99654">Saldo Actual</strong></label>
                                                <h4 class="is-info">' . MONEDA_SIMBOLO . " " . number_format($caja['total_ventas'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <footer class="card-footer">
                                    <div class="content  is-full" style="padding-left:20px">
                                 
                                    <div class="columns">
                                        <div class="column is-full">
                                        <label class="has-text-centered"><strong>Saldo Inicial:</strong></label>
                                                <h4 class="has-text-info" style="font-size:22px">' . MONEDA_SIMBOLO . " " . number_format($rows['saldo_inicial'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</h4>
                                            </div>
                                        <div class="column  is-full">
                                         <label class="has-text-centered"><strong>Saldo Final:</strong></label>
                                                <h4 class="has-text-info" style="font-size:22px">' . MONEDA_SIMBOLO . " " . number_format($rows['saldo_final'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</h4>
                                        </div>
                                        <div class="column  is-full">
                                                               <label class="has-text-centered"><strong>Diferencia:</strong></label>
                                                <h4 class="has-text-info" style="font-size:22px">' . MONEDA_SIMBOLO . " " . number_format($rows['diferencia'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</h4>
                                        </div>
                                    </div>
                                   
                                  
                                     </div>
                                </footer>
                                </div>
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
