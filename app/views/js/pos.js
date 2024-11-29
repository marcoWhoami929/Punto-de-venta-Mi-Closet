/** 
 * FUNCIONES MODULO PRODUCTOS
 */

function eliminarProducto(id_producto){


    Swal.fire({
        title: "¿Desea Eliminar El Producto?",
        text: "",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#B99654",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, Eliminar",
        cancelButtonText: "No, cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
            let datos = new FormData();
            datos.append("id_producto", id_producto);
            datos.append("modulo_producto", "eliminar");
            fetch(url + "app/ajax/productoAjax.php", {
              method: "POST",
              body: datos,
            }).then((respuesta) => respuesta.json())
            .then((respuesta) => {
              return alertas_ajax(respuesta);
            }) 
        }
      });
}

function reabastecerInventario(id_producto){

}
function desecharInventario(id_producto){

}