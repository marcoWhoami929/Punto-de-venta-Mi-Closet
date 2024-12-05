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
        $sWhere = "id_sesion != 0";
        if ($datos["estatus"] != "") {
            $sWhere .= " and estado = '" . $datos["estatus"] . "'";
        }
        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " and sess.codigo_sesion LIKE '%$busqueda%' OR caj.nombre LIKE '%$busqueda%' OR usr.nombre LIKE '%$busqueda%'";
        }

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $consulta_datos = "SELECT $campos FROM sesiones_caja as sess INNER JOIN usuario as usr ON sess.id_caja = usr.id_caja INNER JOIN caja as caj ON sess.id_caja = caj.id_caja WHERE $sWhere ORDER BY sess.$campoOrden $orden LIMIT $inicio,$registros";

        $consulta_total = "SELECT COUNT(id_sesion) FROM sesiones_caja as sess INNER JOIN usuario as usr ON sess.id_caja = usr.id_caja INNER JOIN caja as caj ON sess.id_caja = caj.id_caja WHERE $sWhere";

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
                    $modal = "";
                } else {
                    $color = "is-danger";
                    $texto = "Cerrada";
                    $modal = 'onclick=obtenerDetalleCorteCaja("' . $rows["codigo_sesion"] . '")';
                }


                $caja = $this->ejecutarConsulta("SELECT sesion.*,count(pay.codigo_venta) as 'num_ventas',(sesion.efectivo+sesion.transferencia+sesion.tarjeta_debito+sesion.tarjeta_credito) as 'total_ventas',sum(IF(mov.descripcion = 'ENTRADA',mov.monto,0)) as 'entrada_efectivo',sum(IF(mov.descripcion = 'SALIDA',mov.monto,0)) as 'salida_efectivo' FROM `sesiones_caja` as sesion LEFT OUTER JOIN movimiento_caja as mov ON sesion.codigo_sesion = mov.sesion_caja LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago WHERE mov.sesion_caja = '" . $rows["codigo_sesion"]  . "'");
                $caja = $caja->fetch();

                $tabla .= '<div class="column" ' . $modal . ' style="cursor:pointer">
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
                                             <div class="column">
                                                <label class="has-text-centered"><strong style="color:#B99654"></strong></label>
                                                 <a href="' . APP_URL . 'sessionsDetail/' . $rows["codigo_sesion"] . '/" class="button is-link is-rounded is-medium" title="Movimientos Caja ' . $rows['codigo_sesion'] . '" ><i class="fas fa-info"></i></a>
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
    public function listarDetalleSessionControlador($datos)
    {

        $pagina = $this->limpiarCadena($datos["page"]);
        $registros = $this->limpiarCadena($datos["per_page"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);
        $sesion = $this->limpiarCadena($datos["sesion"]);

        $url = $this->limpiarCadena($datos["url"]);
        $url = APP_URL . $url . "/";

        $sWhere = "mov.sesion_caja ='" . $sesion . "'";

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        if ($datos["tipo_movimiento"] != "") {
            $sWhere .= " and mov.tipo_movimiento = '" . $datos["tipo_movimiento"] . "'";
        }
        if ($datos["metodo_pago"] != "") {
            $sWhere .= " and pay.id_metodo_pago = '" . $datos["metodo_pago"] . "'";
        }
        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " AND pay.codigo_pago LIKE '%$busqueda%' OR ven.codigo LIKE '%$busqueda%'";
        }
        $tabla = "";
        $campos = "mov.*,pay.codigo_pago,pay.total_pago,pay.fecha_pago,met.metodo,ven.codigo,ven.total as 'total_venta',ven.pagado as 'total_pagado',ven.pendiente as 'total_pendiente' ";
        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $consulta_datos = "SELECT $campos FROM movimiento_caja as mov LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago LEFT OUTER JOIN metodopago as met ON pay.id_metodo_pago = met.id_metodo_pago LEFT OUTER JOIN venta as ven ON pay.codigo_venta = ven.codigo WHERE $sWhere ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

        $consulta_total = "SELECT COUNT(id_movimiento) FROM movimiento_caja as mov LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago LEFT OUTER JOIN metodopago as met ON pay.id_metodo_pago = met.id_metodo_pago LEFT OUTER JOIN venta as ven ON pay.codigo_venta = ven.codigo WHERE $sWhere ";

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
            <thead >
					<tr>
						<td class="has-text-centered" style="color:#ffffff;background:#16a085" colspan="5">Movimiento</td>
						<td class="has-text-centered" style="color:#ffffff;background:#F14668" colspan="4">Pago</td>
                        <td class="has-text-centered" style="color:#ffffff;background:#B99654" colspan="4">Venta</td>
						
					</tr>
				</thead>
				<thead style="background:#B99654;color:#ffffff;">
					<tr>
						<th style="color:#ffffff">#</th>
						<th style="color:#ffffff">Sesion</th>
						<th style="color:#ffffff">Movimiento</th>
						<th style="color:#ffffff">$ Total</th>
						<th style="color:#ffffff">Fecha</th>
						<th style="color:#ffffff">Codigo</th>
						<th style="color:#ffffff">$ Total</th>
						<th style="color:#ffffff">Fecha</th>
						<th style="color:#ffffff">Forma Pago</th>
						<th style="color:#ffffff">Codigo</th>
						<th style="color:#ffffff">Venta</th>
						<th style="color:#ffffff">Pagado</th>
						<th style="color:#ffffff">Pendiente</th>
					</tr>
				</thead>
				<tbody>
		';

            $totalPagos = 0;
            $totalMovimientos = 0;
            foreach ($datos as $rows) {
                if ($rows["tipo_movimiento"] == "ingreso") {
                    $total_monto = $rows['monto'];
                } else {
                    $total_monto = -$rows['monto'];
                }


                $tabla .= '
						<tr >
							<td>' . $contador . '</td>
							<td><strong>' . $rows['sesion_caja'] . '</strong></td>
							<td>' . $rows['tipo_movimiento'] . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['monto'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . $rows['fecha_movimiento'] . '</td>
							<td><strong>' . $rows["codigo_pago"] . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_pago'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . $rows['fecha_pago'] . '</td>
							<td>' . $rows['metodo'] . '</td>
							<td><strong>' . $rows['codigo'] . '</strong></td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_pagado'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_pendiente'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							
						</tr>
					';
                $totalPagos += $rows['total_pago'];
                $totalMovimientos += $total_monto;
                $contador++;
            }


            $tabla .= '	<tr>
						<td class="has-text-right" style="color:#B99654" colspan="3"></td>
                        <td class="has-text-left" style="color:#B99654" colspan="2">' . MONEDA_SIMBOLO . " " . number_format($totalMovimientos, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
                     
						<td class="has-text-right" style="color:#B99654" colspan="2">' . MONEDA_SIMBOLO . " " . number_format($totalPagos, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
                        <td class="has-text-right" style="color:#B99654" colspan="6"></td>
						
					</tr>';

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
			No hay resultados de su busqueda.
		</div>
	</article>';
            }
        }

        $tabla .= '</tbody></table></div>';

        ### Paginacion ###
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando movimientos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }

        return $tabla;
    }
    public function listarPagosControlador($datos)
    {

        $pagina = $this->limpiarCadena($datos["page"]);
        $registros = $this->limpiarCadena($datos["per_page"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);


        $url = $this->limpiarCadena($datos["url"]);
        $url = APP_URL . $url . "/";

        $sWhere = "mov.id_movimiento != '0'";

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        if ($datos["tipo_movimiento"] != "") {
            $sWhere .= " and mov.tipo_movimiento = '" . $datos["tipo_movimiento"] . "'";
        }
        if ($datos["metodo_pago"] != "") {
            $sWhere .= " and pay.id_metodo_pago = '" . $datos["metodo_pago"] . "'";
        }
        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " AND pay.codigo_pago LIKE '%$busqueda%' OR ven.codigo LIKE '%$busqueda%' OR mov.sesion_caja LIKE '%$busqueda%'";
        }
        $tabla = "";
        $campos = "mov.*,pay.codigo_pago,pay.total_pago,pay.fecha_pago,met.metodo,ven.codigo,ven.total as 'total_venta',ven.pagado as 'total_pagado',ven.pendiente as 'total_pendiente' ";
        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $consulta_datos = "SELECT $campos FROM movimiento_caja as mov LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago LEFT OUTER JOIN metodopago as met ON pay.id_metodo_pago = met.id_metodo_pago LEFT OUTER JOIN venta as ven ON pay.codigo_venta = ven.codigo WHERE $sWhere ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

        $consulta_total = "SELECT COUNT(id_movimiento) FROM movimiento_caja as mov LEFT OUTER JOIN pago as pay ON mov.descripcion = pay.codigo_pago LEFT OUTER JOIN metodopago as met ON pay.id_metodo_pago = met.id_metodo_pago LEFT OUTER JOIN venta as ven ON pay.codigo_venta = ven.codigo WHERE $sWhere ";

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
            <thead >
					<tr>
						<td class="has-text-centered" style="color:#ffffff;background:#16a085" colspan="5">Movimiento</td>
						<td class="has-text-centered" style="color:#ffffff;background:#F14668" colspan="4">Pago</td>
                        <td class="has-text-centered" style="color:#ffffff;background:#B99654" colspan="4">Venta</td>
						
					</tr>
				</thead>
				<thead style="background:#B99654;color:#ffffff;">
					<tr>
						<th style="color:#ffffff">#</th>
						<th style="color:#ffffff">Sesion</th>
						<th style="color:#ffffff">Movimiento</th>
						<th style="color:#ffffff">$ Total</th>
						<th style="color:#ffffff">Fecha</th>
						<th style="color:#ffffff">Codigo</th>
						<th style="color:#ffffff">$ Total</th>
						<th style="color:#ffffff">Fecha</th>
						<th style="color:#ffffff">Forma Pago</th>
						<th style="color:#ffffff">Codigo</th>
						<th style="color:#ffffff">Venta</th>
						<th style="color:#ffffff">Pagado</th>
						<th style="color:#ffffff">Pendiente</th>
					</tr>
				</thead>
				<tbody>
		';

            $totalPagos = 0;
            $totalMovimientos = 0;
            foreach ($datos as $rows) {
                if ($rows["tipo_movimiento"] == "ingreso") {
                    $total_monto = $rows['monto'];
                } else {
                    $total_monto = -$rows['monto'];
                }


                $tabla .= '
						<tr >
							<td>' . $contador . '</td>
							<td><strong>' . $rows['sesion_caja'] . '</strong></td>
							<td>' . $rows['tipo_movimiento'] . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['monto'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . $rows['fecha_movimiento'] . '</td>
							<td><strong>' . $rows["codigo_pago"] . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_pago'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . $rows['fecha_pago'] . '</td>
							<td>' . $rows['metodo'] . '</td>
							<td><strong>' . $rows['codigo'] . '</strong></td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_pagado'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							<td>' . MONEDA_SIMBOLO . " " . number_format($rows['total_pendiente'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
							
						</tr>
					';
                $totalPagos += $rows['total_pago'];
                $totalMovimientos += $total_monto;
                $contador++;
            }


            $tabla .= '	<tr>
						<td class="has-text-right" style="color:#B99654" colspan="3"></td>
                        <td class="has-text-left" style="color:#B99654" colspan="2">' . MONEDA_SIMBOLO . " " . number_format($totalMovimientos, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
                     
						<td class="has-text-right" style="color:#B99654" colspan="2">' . MONEDA_SIMBOLO . " " . number_format($totalPagos, MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '</td>
                        <td class="has-text-right" style="color:#B99654" colspan="6"></td>
						
					</tr>';

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
			No hay resultados de su busqueda
		</div>
	</article>';
            }
        }

        $tabla .= '</tbody></table></div>';

        ### Paginacion ###
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando movimientos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }

        return $tabla;
    }
}
