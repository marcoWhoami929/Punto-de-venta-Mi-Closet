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
            
        }

        $alerta = [
            "tipo" => "redireccionar",
            "url" => APP_URL . "notesNew/"
        ];

        return json_encode($alerta);
    }
    /*---------- Controlador remover producto de nota ----------*/
	public function removerProductoNotaControlador()
	{

		/*== Recuperando codigo del producto ==*/
		$codigo = $this->limpiarCadena($_POST['codigo']);

		unset($_SESSION['datos_producto_nota'][$codigo]);

		if (empty($_SESSION['datos_producto_nota'][$codigo])) {
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "¡Producto removido!",
				"texto" => "El producto se ha removido de la nota",
				"icono" => "success"
			];
		} else {
			$alerta = [
				"tipo" => "simple",
				"titulo" => "Ocurrió un error inesperado",
				"texto" => "No hemos podido remover el producto, por favor intente nuevamente",
				"icono" => "error"
			];
		}
		return json_encode($alerta);
	}
}
