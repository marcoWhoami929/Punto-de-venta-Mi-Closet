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

function entradaInventario(id_producto){
    $("#btn-modal-entrada").click();
    if(id_producto == 0){

      var id_producto = $("#id_producto_entrada_inventario").val();
      var unidades = $("#unidades_entrada_inventario").val();
      var observaciones = $("#descripcion_entrada_inventario").val();

      let datos = new FormData();
      datos.append("unidades_entrada", unidades);
      datos.append("observaciones_entrada", observaciones);
      datos.append("id_producto", id_producto);
      datos.append("modulo_producto", "entradaProducto");
      fetch(url + "app/ajax/productoAjax.php", {
        method: "POST",
        body: datos,
      }).then((respuesta) => respuesta.json())
      .then((respuesta) => {
        return alertas_ajax(respuesta);
      }) 

    }else{
      $("#id_producto_entrada_inventario").val(id_producto);
    }

}
function salidaInventario(id_producto){
  $("#btn-modal-salida").click();
  if(id_producto == 0){

    var id_producto = $("#id_producto_salida_inventario").val();
    var unidades = $("#unidades_salida_inventario").val();
    var observaciones = $("#descripcion_salida_inventario").val();

    let datos = new FormData();
    datos.append("unidades_salida", unidades);
    datos.append("observaciones_salida", observaciones);
    datos.append("id_producto", id_producto);
    datos.append("modulo_producto", "salidaProducto");
    fetch(url + "app/ajax/productoAjax.php", {
      method: "POST",
      body: datos,
    }).then((respuesta) => respuesta.json())
    .then((respuesta) => {
      return alertas_ajax(respuesta);
    }) 

  }else{
    $("#id_producto_salida_inventario").val(id_producto);
  }
}