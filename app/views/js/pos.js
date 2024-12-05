/** 
 * FUNCIONES MODULO POS
 * 
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

function eliminarUsuario(id_producto){


  Swal.fire({
      title: "¿Desea Eliminar El Usuario?",
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
          datos.append("id_usuario", id_producto);
          datos.append("modulo_usuario", "eliminar");
          fetch(url + "app/ajax/usuarioAjax.php", {
            method: "POST",
            body: datos,
          }).then((respuesta) => respuesta.json())
          .then((respuesta) => {
            return alertas_ajax(respuesta);
          }) 
      }
    });
}
function eliminarCliente(id_cliente){


  Swal.fire({
      title: "¿Desea Eliminar El Cliente?",
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
          datos.append("id_cliente", id_cliente);
          datos.append("modulo_cliente", "eliminar");
          fetch(url + "app/ajax/clienteAjax.php", {
            method: "POST",
            body: datos,
          }).then((respuesta) => respuesta.json())
          .then((respuesta) => {
            return alertas_ajax(respuesta);
          }) 
      }
    });
}


function eliminarCaja(id_caja){

  Swal.fire({
    title: "¿Desea Eliminar La Caja?",
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
      datos.append("id_caja", id_caja);
      datos.append("modulo_caja", "eliminar");
    
      fetch(url + "app/ajax/cajaAjax.php", {
        method: "POST",
        body: datos,
      })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
          return alertas_ajax(respuesta);
        });
    }
  });
  
}
function cancelarVenta(id_venta,codigo_venta){


  Swal.fire({
      title: "¿Desea Cancelar La Venta?",
      text: "",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#B99654",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, Cancelar",
      cancelButtonText: "No, cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
          let datos = new FormData();
          datos.append("id_venta", id_venta);
          datos.append("codigo_venta", codigo_venta);
          datos.append("modulo_venta", "cancelar_venta");
          fetch(url + "app/ajax/ventaAjax.php", {
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

function calcularDenominacion(id_denominacion,denominacion){
    var cantidad = $("#dn"+id_denominacion+"").val();
    var total = parseFloat(cantidad*denominacion);
     $("#dif_dn_"+id_denominacion+"").val(total.toFixed(2));
     calcularTotalDenominaciones();
}
function calcularTotalDenominaciones(){

  var suma = 0;
  $('.input-denominacion').each(function(){
         suma += parseFloat($(this).val());
  });
 
  $("#total_denominaciones_caja").val(suma.toFixed(2));
  $("#saldo_final_corte").val(suma.toFixed(2));
  calcularDiferenciaCaja();
}
function  obtenerDetalleCorteCaja(sesion){
  $("#btn-detalle-corte-caja").click();
  $.ajax({
    url: "../app/ajax/cajaAjax.php",
    type: "POST",
    data: {
      sesion_caja:sesion,
      modulo_caja: "detalle_corte_caja",
    },
    success: function (response) {
      var datos = JSON.parse(response)
     document.getElementById('field-ordenes-detalle').innerHTML = "<label class='label' style='color:#B99654'>"+datos.num_ventas+" Ventas: <span>$ "+datos.total_ventas+"</span></label>";
     document.getElementById('field-saldo-inicial-detalle').innerHTML = "$ "+datos.saldo_inicial+"";
     document.getElementById('field-efectivo-detalle').innerHTML = "$ "+datos.efectivo+"";
     document.getElementById('field-transferencia-detalle').innerHTML = "$ "+datos.transferencia+"";
     document.getElementById('field-td-detalle').innerHTML = "$ "+datos.tarjeta_debito+"";
     document.getElementById('field-tc-detalle').innerHTML = "$ "+datos.tarjeta_credito+"";
     document.getElementById('field-entrada-efectivo-detalle').innerHTML = "$ "+datos.entrada_efectivo+"";
     document.getElementById('field-salida-efectivo-detalle').innerHTML = "$ "+datos.salida_efectivo+"";
     var total_caja = (parseFloat(datos.saldo_inicial)+parseFloat(datos.efectivo)+parseFloat(datos.entrada_efectivo))-parseFloat(datos.salida_efectivo);
     document.getElementById('field-total-caja-detalle').innerHTML = total_caja.toFixed(2);
     $("#saldo_final_corte_detalle").val(parseFloat(datos.saldo_final).toFixed(2));
     $("#field-diferencia-caja-detalle").val(parseFloat(datos.diferencia).toFixed(2));
     $("#observaciones_corte_detalle").val(datos.observaciones);
     obtenerDetalleDenominaciones(sesion);     
    },
  })
}
function  obtenerDetalleDenominaciones(sesion){

  $.ajax({
    url: "../app/ajax/cajaAjax.php",
    type: "POST",
    data: {
      sesion_caja:sesion,
      modulo_caja: "detalle_denominaciones",
    },
    success: function (response) {
      var datos = JSON.parse(response);
      $("#detalle_denominaciones").val(datos.total_caja);
      $("#det_dn1").val(parseFloat(datos.det_dn1).toFixed(0));
      $("#det_dn2").val(parseFloat(datos.det_dn2).toFixed(0));
      $("#det_dn3").val(parseFloat(datos.det_dn3).toFixed(0));
      $("#det_dn4").val(parseFloat(datos.det_dn4).toFixed(0));
      $("#det_dn5").val(parseFloat(datos.det_dn5).toFixed(0));
      $("#det_dn6").val(parseFloat(datos.det_dn6).toFixed(0));
      $("#det_dn7").val(parseFloat(datos.det_dn7).toFixed(0));
      $("#det_dn8").val(parseFloat(datos.det_dn8).toFixed(0));
      $("#det_dn9").val(parseFloat(datos.det_dn9).toFixed(0));
      $("#det_dn10").val(parseFloat(datos.det_dn10).toFixed(0));
      $("#det_dn11").val(parseFloat(datos.det_dn11).toFixed(0));
      $("#det_dn12").val(parseFloat(datos.det_dn12).toFixed(0));
      $("#det_dn13").val(parseFloat(datos.det_dn13).toFixed(0));
      $("#det_dif_dn_1").val(parseFloat(datos.dn1).toFixed(2));
      $("#det_dif_dn_2").val(parseFloat(datos.dn2).toFixed(2));
      $("#det_dif_dn_3").val(parseFloat(datos.dn3).toFixed(2));
      $("#det_dif_dn_4").val(parseFloat(datos.dn4).toFixed(2));
      $("#det_dif_dn_5").val(parseFloat(datos.dn5).toFixed(2));
      $("#det_dif_dn_6").val(parseFloat(datos.dn6).toFixed(2));
      $("#det_dif_dn_7").val(parseFloat(datos.dn7).toFixed(2));
      $("#det_dif_dn_8").val(parseFloat(datos.dn8).toFixed(2));
      $("#det_dif_dn_9").val(parseFloat(datos.dn9).toFixed(2));
      $("#det_dif_dn_10").val(parseFloat(datos.dn10).toFixed(2));
      $("#det_dif_dn_11").val(parseFloat(datos.dn11).toFixed(2));
      $("#det_dif_dn_12").val(parseFloat(datos.dn12).toFixed(2));
      $("#det_dif_dn_13").val(parseFloat(datos.dn13).toFixed(2));
     
    },
  })
}