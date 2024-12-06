<?php

namespace app\controllers;

use app\models\mainModel;

class notesController extends mainModel
{
    public function cargarCarritoNotaControlador()
    {

        $tabla = "";

        echo '<script>
    bulmaTagsinput.attach();
</script>';
        $cc = 1;
        if (isset($_SESSION['datos_producto_nota'])) {
            if (empty($_SESSION['datos_producto_nota'])) {
                $tabla = '
			<article class="message is-warning mt-4 mb-4">
				 <div class="message-header">
					
				 </div>
				<div class="message-body has-text-centered">
                        <p class="has-text-centered  pb-3">
       
                            <i class="fas fa-gifts  fa-8x"></i>
                        </p>
					<i class="fas fa-exclamation-triangle fa-2x"></i><br>
					No hay productos agregados en el carrito
				</div>
			</article>';
            } else {
                foreach ($_SESSION['datos_producto_nota'] as $productos) {


                    if (is_file("../views/productos/" . $productos['foto'])) {
                        $foto = '<img src="' . APP_URL . 'app/views/productos/' . $productos['foto'] . '">';
                    } else {
                        $foto = '<img src="' . APP_URL . 'app/views/productos/default.png">';
                    }


                    $tabla .= '<div class="card pt-4">
								<header class="card-header" style="background:#B99654;color:#ffffff">
								 <p class="card-header-title"><strong style="color:#ffffff">' . $cc . ' - ' . $productos['descripcion'] . '</strong></p>
								   <p class="card-header-title"><strong style="color:#ffffff">' . $productos['codigo'] . '</strong></p>
									<button type="submit" class="button is-danger is-rounded pt-4" onclick="removerProductoCarrito(\'' . $productos['codigo'] . '\')">
														<i class="fas fa-trash fa-fw"></i>
											</button>
										
								</header>
								<div class="card-content">
									<div class="content">
										<div class="columns">
											<div class="column is-one-fifth">
												<figure class="media-left">
													<p class="image is-64x64">
														' . $foto . '
													</p>
												</figure>
											</div>
										 
									
											 <div class="column pt-6">
													 <strong>TALLAS:</strong>
                  
                                                    <input class="input" type="tags" id="tallas" name="tallas" value="' . $productos['tallas'] . '" placeholder="...">
											</div>
											 <div class="column pt-6">
													 <strong>COLORES:</strong>
                  
                                                    <input class="input" type="tags" id="colores" name="colores" value="' . $productos['colores'] . '" placeholder="...">
											</div>
											  
										</div>
										
										
									</div>
								</div>
								<footer class="card-footer">
									
									<a href="#" class="card-footer-item">
										<div class="columns has-text-centered">
										<label><strong>Precio:</strong></label>
										</div>
										<div class="columns pt-4">
										' . MONEDA_SIMBOLO . " " . number_format($productos['precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE . '
										</div>
									</a>
									<a href="#" class="card-footer-item">
									 <div class="columns has-text-centered">
									  <label><strong>Limite:</strong></label>
									</div>
									<div class="columns pt-4">
									' . number_format($productos['limite_nota'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . '
									</div>
									</a>
								</footer>
								</div>';
                    $cc++;
                }
            }
        } else {
            $tabla = '
			<article class="message is-warning mt-4 mb-4">
				 <div class="message-header">
					
				 </div>
				<div class="message-body has-text-centered">
                        <p class="has-text-centered  pb-3">
       
                            <i class="fas fa-gifts  fa-8x"></i>
                        </p>
					<i class="fas fa-exclamation-triangle fa-2x"></i><br>
					No hay productos agregados en el carrito
				</div>
			</article>';
        }


        return $tabla;
    }
    /*---------- Controlador agregar producto a nota de venta ----------*/
    public function agregarProductoNotaControlador()
    {

        /*== Recuperando codigo del producto ==*/
        $codigo = $this->limpiarCadena($_POST['codigo']);

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

        if (empty($_SESSION['datos_producto_nota'][$codigo])) {


            $stock_total = $value['stock_total'];

            if ($stock_total < 0) {

                $alerta = "Lo sentimos, no hay existencias disponibles del producto seleccionado";
                return json_encode($alerta);
                exit();
            }



            $_SESSION['datos_producto_nota'][$codigo] = [
                "id_producto" => $value['cid_producto'],
                "codigo" => $value['codigo'],
                "foto" => $value['foto'],
                "colores" => $value['colores'],
                "tallas" => $value['tallas'],
                "precio_venta" => $value['precio_venta'],
                "limite_nota" => $stock_total,
                "descripcion" => $value['nombre']
            ];


            $alerta = "Se agrego <strong>" . $value['nombre'] . "</strong> a la nota actual";
        } else {
            $alerta = "Ya se encuentra agregado el producto.";
        }

        return json_encode($alerta);
    }
    /*---------- Controlador remover producto de nota ----------*/
    public function removerProductoNotaControlador()
    {

        /*== Recuperando codigo del producto ==*/
        $codigo = $this->limpiarCadena($_POST['codigo']);

        unset($_SESSION['datos_producto_nota'][$codigo]);

        if (empty($_SESSION['datos_producto_nota'][$codigo])) {

            $alerta = "El producto se ha removido de la nota";
        } else {
            $alerta = "No hemos podido remover el producto, por favor intente nuevamente";
        }

        return json_encode($alerta);
    }
    /*----------  Controlador registrar nota  ----------*/
    public function registrarNotaControlador()
    {
        if (isset($_SESSION['datos_producto_nota'])) {
            if (empty($_SESSION['datos_producto_nota'])) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No puede generar una nueva nota sin productos",
                    "icono" => "error"
                ];
            } else {
                # Almacenando datos#
                $folio_nota = $this->limpiarCadena($_POST['folio_nota']);
                $titulo_nota = $this->limpiarCadena($_POST['titulo_nota']);
                $porc_descuento_nota = $this->limpiarCadena($_POST['porc_descuento_nota']);
                $fecha_publicacion = $this->limpiarCadena($_POST['fecha_publicacion']);
                $fecha_expiracion = $this->limpiarCadena($_POST['fecha_expiracion']);
                $route_qr = $this->limpiarCadena($_POST['route_qr']);

                $nota_datos_reg = [
                    [
                        "campo_nombre" => "codigo",
                        "campo_marcador" => ":Codigo",
                        "campo_valor" => $folio_nota
                    ],
                    [
                        "campo_nombre" => "titulo_nota",
                        "campo_marcador" => ":TituloNata",
                        "campo_valor" => $titulo_nota
                    ],
                    [
                        "campo_nombre" => "porc_descuento",
                        "campo_marcador" => ":PorcDescuento",
                        "campo_valor" => $porc_descuento_nota
                    ],
                    [
                        "campo_nombre" => "fecha_publicacion",
                        "campo_marcador" => ":FechaPublicacion",
                        "campo_valor" => $fecha_publicacion
                    ],
                    [
                        "campo_nombre" => "fecha_expiracion",
                        "campo_marcador" => ":FechaExpiracion",
                        "campo_valor" => $fecha_expiracion
                    ],
                    [
                        "campo_nombre" => "qr",
                        "campo_marcador" => ":Qr",
                        "campo_valor" => $route_qr
                    ],
                    [
                        "campo_nombre" => "estatus",
                        "campo_marcador" => ":Estatus",
                        "campo_valor" => '1'
                    ]
                ];

                $registrar_nota = $this->guardarDatos("notas", $nota_datos_reg);

                if ($registrar_nota->rowCount() == 1) {

                    foreach ($_SESSION['datos_producto_nota'] as $nota_detalle) {

                        /*== Preparando datos para enviarlos al modelo ==*/
                        $datos_nota_detalle_reg = [
                            [
                                "campo_nombre" => "codigo_nota",
                                "campo_marcador" => ":CodigoNota",
                                "campo_valor" => $folio_nota
                            ],
                            [
                                "campo_nombre" => "id_producto",
                                "campo_marcador" => ":IdProducto",
                                "campo_valor" => $nota_detalle['id_producto']
                            ],
                            [
                                "campo_nombre" => "codigo",
                                "campo_marcador" => ":Codigo",
                                "campo_valor" => $nota_detalle['codigo']
                            ],
                            [
                                "campo_nombre" => "descripcion",
                                "campo_marcador" => ":Descripcion",
                                "campo_valor" => $nota_detalle['descripcion']
                            ],
                            [
                                "campo_nombre" => "precio_venta",
                                "campo_marcador" => ":PrecioVenta",
                                "campo_valor" => $nota_detalle['precio_venta']
                            ],
                            [
                                "campo_nombre" => "limite_nota",
                                "campo_marcador" => ":LimiteNota",
                                "campo_valor" => $nota_detalle['limite_nota']
                            ],
                            [
                                "campo_nombre" => "colores",
                                "campo_marcador" => ":Colores",
                                "campo_valor" => $nota_detalle['colores']
                            ],
                            [
                                "campo_nombre" => "tallas",
                                "campo_marcador" => ":Tallas",
                                "campo_valor" => $nota_detalle['tallas']
                            ],
                            [
                                "campo_nombre" => "estatus",
                                "campo_marcador" => ":Estatus",
                                "campo_valor" => '1'
                            ]
                        ];

                        $agregar_detalle_nota = $this->guardarDatos("productos_notas", $datos_nota_detalle_reg);

                        if ($agregar_detalle_nota->rowCount() == 1) {
                            unset($_SESSION["datos_producto_nota"]);
                        }
                    }

                    unset($_SESSION["QrNota"]);
                    unset($_SESSION["titulo_nota"]);
                    unset($_SESSION["porc_descuento_nota"]);
                    unset($_SESSION["fecha_publicacion_nota"]);
                    unset($_SESSION["fecha_expiracion_nota"]);
                    $alerta = [
                        "tipo" => "recargar",
                        "titulo" => "Nota registrada",
                        "texto" => "Los datos de la nota se registraron con exito",
                        "icono" => "success"
                    ];
                } else {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "No se pudo registrar los datos de la nota, por favor intente nuevamente",
                        "icono" => "error"
                    ];
                }
            }
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No puede generar una nueva nota sin productos",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
    }
    /*----------  Controlador listar notas  ----------*/
    public function listarNotasControlador($datos)
    {

        $pagina = $this->limpiarCadena($datos["page"]);
        $registros = $this->limpiarCadena($datos["per_page"]);
        $campoOrden = $this->limpiarCadena($datos["campoOrden"]);
        $orden = $this->limpiarCadena($datos["orden"]);

        $url = $this->limpiarCadena($datos["url"]);
        $url = APP_URL . $url . "/";

        $busqueda = $this->limpiarCadena($datos["busqueda"]);
        $sWhere = "nota.id_nota !='0'";

        if (isset($busqueda) && $busqueda != "") {
            $sWhere .= " AND nota.codigo LIKE '%$busqueda%' OR nota.titulo_nota LIKE '%$busqueda%'";
        }

        $campos = "*";
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $consulta_datos = "SELECT $campos FROM notas as nota WHERE $sWhere  ORDER BY $campoOrden $orden LIMIT $inicio,$registros";

        $consulta_total = "SELECT COUNT(id_nota) FROM notas as nota WHERE $sWhere";


        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();

        $numeroPaginas = ceil($total / $registros);

        if ($total >= 1 && $pagina <= $numeroPaginas) {
            $contador = $inicio + 1;
            $pag_inicio = $inicio + 1;
            foreach ($datos as $rows) {

                switch ($rows['estatus']) {
                    case '0':
                        $estado = '<button class="button is-danger">Cancelada</button>';
                        break;
                    case '1':
                        $estado = '<button class="button is-success">Activa</button>';
                        break;
                }

                if (is_file("../ajax/codes/" . $rows['codigo'] . ".png")) {
                    $foto = '<img src="' . APP_URL . 'app/ajax/codes/' . $rows['codigo'] . '.png">';
                } else {
                    $foto = '<img src="' . APP_URL . 'app/views/productos/default.png">';
                }
                $tabla .= '<div class="card  pb-4">
							<header class="card-header" style="background:#B99654;color:#ffffff">
							
								 <p class="card-header-title"><strong style="color:#ffffff">' . $rows['codigo'] . '</strong></p>
								
								
								 <p class="card-header-title"><strong style="color:#ffffff">' . $rows['fecha_publicacion'] . '</strong></p>
								
										' . $estado . '
								
							
							</header>
							<div class="card-content">
								<div class="content">
									<div class="columns">
                                    	<div class="column">
											<figure class="media-left">
												<p class="image is-96x96">
													' . $foto . '
												</p>
												</figure>
										</div>
                                    
										<div class="column">
												<div class="columns">
												<label><strong>Titulo Nota:</strong></label>
												</div>
												<div class="columns">
												' . $rows['titulo_nota']  . '
												 </div>
										</div>
										<div class="column">
												<div class="columns">
												<label><strong>Fecha Expiracion:</strong></label>
												</div>
												<div class="columns">
												' . strtoupper($rows['fecha_expiracion']) . '
												</div>
										</div>
										<div class="column">
												<div class="columns">
												<label><strong>% Descuento:</strong></label>
												</div>
												<div class="columns">
												' . strtoupper($rows['porc_descuento']) . '
												</div>
										</div>
										
									</div>
									
									
								</div>
							</div>
							<footer class="card-footer">
									<div class="columns">
										<div class="column is-full">
											<div class=" card-footer-item">
													<div class="columns">
														<div class="column">
																<button type="button" class="button is-link is-outlined is-rounded is-medium btn-sale-options" onclick="copiarNota(\'' . $rows['qr'] . '\')" title="Copiar url nota" >
                                                                    <i class="fas fa-copy fa-fw"></i>
                                                                </button>
														</div>
														<div class="column">
															  <a href="' . APP_URL . 'noteDetail/' . $rows['codigo'] . '/" class="button is-link is-rounded is-medium" title="Informacion de nota Nro. ' . $rows['id_nota'] . '" >
                                                                    <i class="fas fa-shopping-bag fa-fw"></i>
                                                                </a>
														</div>
															<div class="column">
																 <button type="button" class="button is-danger is-rounded is-medium" title="Eliminar nota Nro. ' . $rows['id_nota'] . '" onclick="eliminarNota(\'' . $rows['id_nota'] . '\',\'' . $rows['fecha_publicacion'] . '\')">
                                                                    <i class="far fa-trash-alt fa-fw"></i>
                                                                </button>
															</div>
													</div>

											</div>
										</div>
										
									</div>

							</footer>
							</div>
								<hr>';
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
			</article>
					';
            }
        }



        ### Paginacion ###
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando notas <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }

        return $tabla;
    }
    public function listarNotasxControlador($pagina, $registros, $url, $busqueda)
    {

        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);

        $url = $this->limpiarCadena($url);
        $url = APP_URL . $url . "/";

        $busqueda = $this->limpiarCadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $value_tablas = "*";

        if (isset($busqueda) && $busqueda != "") {

            $consulta_datos = "SELECT $value_tablas FROM notas  WHERE (notas.codigo='$busqueda') ORDER BY notas.id_nota DESC LIMIT $inicio,$registros";

            $consulta_total = "SELECT COUNT(id_nota) FROM notas WHERE (notas.codigo='$busqueda')";
        } else {

            $consulta_datos = "SELECT $value_tablas FROM notas ORDER BY id_nota DESC LIMIT $inicio,$registros";

            $consulta_total = "SELECT COUNT(id_nota) FROM notas";
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
		            <thead style="background:#B99654">
		                <tr >
		                    <th class="has-text-centered" style="color:#ffffff">NRO.</th>
		                    <th class="has-text-centered" style="color:#ffffff">Codigo</th>
		                    <th class="has-text-centered" style="color:#ffffff">Titulo Nota</th>
		                    <th class="has-text-centered" style="color:#ffffff">Fecha</th>
		                    <th class="has-text-centered" style="color:#ffffff">Publicacion</th>
		                    <th class="has-text-centered" style="color:#ffffff">Expiración</th>
                            <th class="has-text-centered" style="color:#ffffff">% Desc</th>
                            <th class="has-text-centered" style="color:#ffffff">Estatus</th>
		                    <th class="has-text-centered" style="color:#ffffff">Opciones</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

            foreach ($datos as $rows) {
                switch ($rows['estatus']) {
                    case '0':
                        $estado = '<button class="button is-danger">Cancelada</button>';
                        break;
                    case '1':
                        $estado = '<button class="button is-success">Activa</button>';
                        break;
                }
                $tabla .= '
						<tr class="has-text-centered" >
							<td>' . $rows['id_nota'] . '</td>
							<td>' . $rows['codigo'] . '</td>
                            <td>' . $rows['titulo_nota'] . '</td>
                            <td>' . $rows['fecha'] . '</td>
                            <td>' . $rows['fecha_publicacion'] . '</td>
                            <td>' . $rows['fecha_expiracion'] . '</td>
                            <td>' . $rows['porc_descuento'] . '</td>
                            <td>' . $estado . '</td>
							
			                <td>

			                	<button type="button" class="button is-link is-outlined is-rounded is-small btn-sale-options" onclick="copiarNota(\'' . $rows['qr'] . '\')" title="Copiar url nota" >
	                                <i class="fas fa-copy fa-fw"></i>
	                            </button>

			                    <a href="' . APP_URL . 'noteDetail/' . $rows['codigo'] . '/" class="button is-link is-rounded is-small" title="Informacion de nota Nro. ' . $rows['id_nota'] . '" >
			                    	<i class="fas fa-shopping-bag fa-fw"></i>
			                    </a>


			                    	<button type="button" class="button is-danger is-rounded is-small" title="Eliminar nota Nro. ' . $rows['id_nota'] . '" onclick="eliminarNota(\'' . $rows['id_nota'] . '\',\'' . $rows['fecha_publicacion'] . '\')">
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
					No Hay Notas Registradas Actualmente
				</div>
			</article>
					';
            }
        }

        $tabla .= '</tbody></table></div>';

        ### Paginacion ###
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando notas <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }

        return $tabla;
    }
    /*----------  Controlador eliminar nota  ----------*/
    public function eliminarNotaControlador()
    {


        $id = $this->limpiarCadena($_POST['id_nota']);

        # Verificando venta #
        $datos = $this->ejecutarConsulta("SELECT * FROM notas WHERE id_nota='$id'");
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "La nota no se encuentra en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        $consultarVentas = $this->ejecutarConsulta("SELECT * FROM venta WHERE codigo_nota='" . $datos['codigo'] . "'");

        if ($consultarVentas->rowCount() <= 0) {
            # Verificando detalles de nota #

            $check_detalle_nota = $this->ejecutarConsulta("SELECT id_detalle_nota FROM productos_notas WHERE codigo_nota ='" . $datos['codigo'] . "'");
            $check_detalle_nota = $check_detalle_nota->rowCount();

            if ($check_detalle_nota > 0) {

                $eliminarVentaDetalle = $this->eliminarRegistro("productos_notas", "codigo_nota", $datos['codigo']);

                if ($eliminarVentaDetalle->rowCount() != $check_detalle_nota) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "No hemos podido eliminar la nota del sistema, por favor intente nuevamente",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }

            $eliminarNota = $this->eliminarRegistro("notas", "id_nota", $id);

            if ($eliminarNota->rowCount() == 1) {

                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Nota Eliminada",
                    "texto" => "La nota ha sido eliminada del sistema correctamente",
                    "icono" => "success"
                ];
            } else {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No hemos podido eliminar la nota del sistema, por favor intente nuevamente",
                    "icono" => "error"
                ];
            }
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Uppps",
                "texto" => "No se puede eliminar la nota porque tiene ventas asociadas.",
                "icono" => "error"
            ];
        }



        return json_encode($alerta);
    }
    /*----------  Controlador actualizar nota  ----------*/
    public function actualizarNotaControlador()
    {

        $id = $this->limpiarCadena($_POST['id_nota']);

        # Verificando cliente #
        $datos = $this->ejecutarConsulta("SELECT * FROM notas WHERE id_nota ='$id'");
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado la nota en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        # Almacenando datos#

        $titulo_nota = $this->limpiarCadena($_POST['titulo_nota']);
        $fecha_publicacion = $this->limpiarCadena($_POST['fecha_publicacion']);
        $fecha_expiracion = $this->limpiarCadena($_POST['fecha_expiracion']);
        $porc_descuento_nota = $this->limpiarCadena($_POST['porc_descuento_nota']);

        # Verificando campos obligatorios #
        if ($titulo_nota == "" || $fecha_publicacion == "" || $fecha_expiracion == "" || $porc_descuento_nota == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No has llenado todos los campos que son obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $cliente_datos_up = [
            [
                "campo_nombre" => "titulo_nota",
                "campo_marcador" => ":TituloNota",
                "campo_valor" => $titulo_nota
            ],
            [
                "campo_nombre" => "fecha_publicacion",
                "campo_marcador" => ":FechaPublicacion",
                "campo_valor" => $fecha_publicacion
            ],
            [
                "campo_nombre" => "fecha_expiracion",
                "campo_marcador" => ":FechaExpiracion",
                "campo_valor" => $fecha_expiracion
            ],
            [
                "campo_nombre" => "porc_descuento",
                "campo_marcador" => ":PorcDescuento",
                "campo_valor" => $porc_descuento_nota
            ]
        ];

        $condicion = [
            "condicion_campo" => "id_nota",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

        if ($this->actualizarDatos("notas", $cliente_datos_up, $condicion)) {
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Nota Actulizada",
                "texto" => "La nota " . $datos['codigo'] . " se actualizó correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos podido actualizar la nota, por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }
}
