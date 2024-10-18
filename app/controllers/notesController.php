<?php

namespace app\controllers;

use app\models\mainModel;

class notesController extends mainModel
{
    /*---------- Controlador agregar producto a nota de venta ----------*/
    public function agregarProductoNotaControlador()
    {

        /*== Recuperando codigo del producto ==*/
        $codigo = $this->limpiarCadena($_POST['codigo']);

        if ($codigo == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "Debes de introducir el código de barras del producto",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }



        /*== Comprobando producto en la DB ==*/
        $check_producto = $this->ejecutarConsulta("SELECT * FROM producto WHERE codigo='$codigo'");
        if ($check_producto->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado el producto con código de barras : '$codigo'",
                "icono" => "error"
            ];
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
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Lo sentimos, no hay existencias disponibles del producto seleccionado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }



            $_SESSION['datos_producto_nota'][$codigo] = [
                "id_producto" => $value['id_producto'],
                "codigo" => $value['codigo'],
                "foto" => $value['foto'],
                "colores" => $value['colores'],
                "tallas" => $value['tallas'],
                "precio_venta" => $value['precio_venta'],
                "limite_nota" => $stock_total,
                "descripcion" => $value['nombre']
            ];

            $_SESSION['alerta_producto_agregado'] = "Se agrego <strong>" . $value['nombre'] . "</strong> a la nota actual";
        } else {
            /*
			$detalle_cantidad = 1;

			$stock_total = $value['stock_total'];

			if ($stock_total < 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Lo sentimos, no hay existencias disponibles del producto seleccionado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}

			$porc_descuento = $_POST["porc_descuento"];
			$descuento = (($value['precio_venta'] * $detalle_cantidad) * $porc_descuento) / 100;
			$subtotal = ($detalle_cantidad * $value['precio_venta']);
			$subtotal = number_format($subtotal, MONEDA_DECIMALES, '.', '');
			$total = ($subtotal - $descuento);
			$total = number_format($total, MONEDA_DECIMALES, '.', '');


			$_SESSION['datos_producto_venta'][$codigo] = [
				"id_producto" => $value['id_producto'],
				"codigo" => $value['codigo'],
				"stock_total" => $stock_total,
				"stock_total_old" => $value['stock_total'],
				"precio_venta" => $value['precio_venta'],
				"porc_descuento" => $porc_descuento,
				"descuento" => $descuento,
				"limite_venta" => $stock_total,
				"subtotal" => $subtotal,
				"total" => $total,
				"descripcion" => $value['nombre']
			];

			$_SESSION['alerta_producto_agregado'] = "Se agrego +1 <strong>" . $value['nombre'] . "</strong> a la venta. Total en carrito: <strong>$detalle_cantidad</strong>";
			*/
        }

        $alerta = [
            "tipo" => "redireccionar",
            "url" => APP_URL . "notesNew/"
        ];

        return json_encode($alerta);
    }
}
