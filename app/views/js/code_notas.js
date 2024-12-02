$(function () {
  urlPath = window.location.pathname;
  url = "http://localhost/pos/";

  ruta = urlPath.split("/");
  switch (ruta[2]) {
    case "dashboard":
      cargarAperturaCaja();
    break;
    case "sessionsList":
      listarSesiones();
    break;
    case "clientList":
      listarClientes();
    break;
    case "cashierList":
      listarCajas();
    break;
    case "productList":
      listarProductos();
    break;
    case "saleNew":
      cargarListaCamaras();
      cargarCatalogoProductos(1);
      cargarCatalogoClientes(1);
      cargarCarritoVenta();
     break;
    case "notesNew":
      generarQrNotas();
      cargarListaCamaras();
      cargarCatalogoProductos(1);
      cargarCarritoNota();
   
  
      break;
   
 
  }
});
function cargarAperturaCaja(){
  var sesion_caja = localStorage.session_caja;
  if(sesion_caja == null){
    $('#btn-apertura-caja').click();
  }else{
    
  }

}

function generarQrNotas() {
  var route = $("#route").val();
  var folio_nota = $("#folio_nota").val();
  $.ajax({
    url: "../app/ajax/notasAjax.php",
    type: "POST",
    data: {
      ecc: "H",
      size: "5",
      folio: folio_nota,
      route: route,
      modulo_notas: "generarQr",
    },
    success: function (response) {
      $(".showQRCode").html(response);
    },
  });
}
function actualizarCarrito() {
  var porcentaje = $("#descuento").val();
  let datos = new FormData();
  datos.append("porc_descuento", porcentaje);
  datos.append("modulo_venta", "actualizar_carrito");

  new Promise(function (resolve) {
    resolve(
      fetch(url + "app/ajax/ventaAjax.php", {
        method: "POST",
        body: datos,
      })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
          $(".alerta_producto").html('<div class="notification is-success is-light ">'+respuesta+'</div>');
        })
    );
  }).then(function (result) {
   
    cargarCarritoVenta();
    setTimeout(function() {
      
      document.getElementById("sale-barcode-input").value = "";
      $(".alerta_producto").html('').fadeIn("slow");
    }, 2000);
  });

  
}
function datosNota() {
  var titulo_nota = $("#titulo_nota").val();
  var porc_descuento = $("#porc_descuento_nota").val();
  var fecha_publicacion = $("#fecha_publicacion").val();
  var fechaExpiracion = $("#fecha_expiracion").val();

  let datos = new FormData();
  datos.append("titulo_nota", titulo_nota);
  datos.append("porc_descuento_nota", porc_descuento);
  datos.append("fecha_publicacion_nota", fecha_publicacion);
  datos.append("fechaExpiracion_nota", fechaExpiracion);
  datos.append("modulo_notas", "datos_nota");

  fetch(url + "app/ajax/notasAjax.php", {
    method: "POST",
    body: datos,
  })
    .then((respuesta) => respuesta.json())
    .then((respuesta) => {
      return alertas_ajax(respuesta);
    });
}
function copiarNota(route_qr) {
  navigator.clipboard.writeText(route_qr);
}

/***
 * CARGAR VISTAS POR AJAX
 */
function listarCajas() {

  var busqueda = $("#busqueda").val();
  var campoOrden = $("#campoOrden").val();
  var orden = $("#orden").val();
  var per_page = $("#per_page").val();
  var page = $("#pagina").val();
  var url = $("#url").val();

  $.ajax({
    url: "../app/ajax/posAjax.php",
    type: "POST",
    data: {
      busqueda:busqueda,
      campoOrden:campoOrden,
      orden:orden,
      per_page:per_page,
      page:page,
      url:url,
      modulo_pos: "listarCajas",
    },
    success: function (response) {
      $(".div-cashier").html(response);
    },
  });
}

function listarProductos() {

  var busqueda = $("#busqueda").val();
  var campoOrden = $("#campoOrden").val();
  var orden = $("#orden").val();
  var per_page = $("#per_page").val();
  var page = $("#pagina").val();
  var url = $("#url").val();

  $.ajax({
    url: "../app/ajax/posAjax.php",
    type: "POST",
    data: {
      busqueda:busqueda,
      campoOrden:campoOrden,
      orden:orden,
      per_page:per_page,
      page:page,
      url:url,
      modulo_pos: "listarProductos",
    },
    success: function (response) {
      $(".div-productos").html(response);
    },
  });
}
function listarSesiones() {

  var busqueda = $("#busqueda").val();
  var campoOrden = $("#campoOrden").val();
  var orden = $("#orden").val();
  var per_page = $("#per_page").val();
  var page = $("#pagina").val();
  var estatus = $("#estatus").val();
  var url = $("#url").val();

  $.ajax({
    url: "../app/ajax/posAjax.php",
    type: "POST",
    data: {
      busqueda:busqueda,
      campoOrden:campoOrden,
      orden:orden,
      per_page:per_page,
      page:page,
      url:url,
      estatus:estatus,
      modulo_pos: "listarSesiones",
    },
    success: function (response) {
      $(".div-sesiones").html(response);
    },
  });
}
function listarClientes() {

  var busqueda = $("#busqueda").val();
  var campoOrden = $("#campoOrden").val();
  var orden = $("#orden").val();
  var per_page = $("#per_page").val();
  var page = $("#pagina").val();
  var estatus = $("#estatus").val();
  var url = $("#url").val();

  $.ajax({
    url: "../app/ajax/posAjax.php",
    type: "POST",
    data: {
      busqueda:busqueda,
      campoOrden:campoOrden,
      orden:orden,
      per_page:per_page,
      page:page,
      url:url,
      estatus:estatus,
      modulo_pos: "listarClientes",
    },
    success: function (response) {
      $(".div-clientes").html(response);
    },
  });
}
/*************************************************************** */
function eliminarCaja(id_caja){
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
function cargarListaCamaras(){
  // This method will trigger user permissions
Html5Qrcode.getCameras().then(camaras => {

  if (camaras && camaras.length) {
    let camaraId = camaras[0].id;
    let select = document.getElementById("listaCamaras");
    let html = `<option value="" selected>Seleccione una camara</option>`;

    camaras.forEach(camara => {
      html += `<option value="${camara.id}">${camara.label}</option>`
    });

    select.innerHTML = html;
  }
}).catch(err => {
  // handle err
});
}
let html5QrCode = null;

function lecturaCorrecta(codigoTexto, codigoObjeto) {
  // handle the scanned code as you like, for example:
  
  $("#sale-barcode-input").val(codigoTexto);
}

function errorLectura(error) {
  // handle scan failure, usually better to ignore and keep scanning.
  // for example:
  //console.warn(`Code scan error = ${error}`);
}
const camaraSeleccionada = (elemento) => {

  let idCamaraSeleccionada = elemento.value;



    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
      idCamaraSeleccionada, 
      {
        fps: 10,    // Optional, frame per seconds for qr code scanning
        qrbox: { width: 250, height: 100 }  // Optional, if you want bounded box UI
      },lecturaCorrecta,errorLectura)
    .catch((err) => {
      // Start failed, handle it.
    });

}

const detenerCamara = () =>{

  html5QrCode.stop().then((ignore) => {
    // QR Code scanning is stopped.
    document.getElementById("imagenReferencial").style.display = "block";
    document.getElementById("listaCamaras").value = "";
  }).catch((err) => {
    // Stop failed, handle it.
  });

}

/* para imagenes */

const html5QrCode2 = new Html5Qrcode("reader-file");
// File based scanning
const fileinput = document.getElementById('qr-input-file');
fileinput.addEventListener('change', e => {
  if (e.target.files.length == 0) {
    // No file selected, ignore 
    return;
  }

  const imageFile = e.target.files[0];
  // Scan QR Code
  html5QrCode2.scanFile(imageFile, true)
  .then(lecturaCorrecta)
  .catch(err => {
    // failure, handle it.
    console.log(`Error scanning file. Reason: ${err}`)
  });
});
/***
 * Cargar Catalogo Productos
 */
function cargarCatalogoProductos(page){
  let datos = new FormData();
    var busqueda = $("#input_codigo").val();
    datos.append("buscar_codigo", busqueda);
    datos.append("page", page);
    datos.append("vista", "cargarCatalogoProductos");
    datos.append("modulo_venta", "listado_productos");

    fetch(url + "app/ajax/ventaAjax.php", {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.text())
      .then((respuesta) => {
        let tabla_productos = document.querySelector("#tabla_productos");
        tabla_productos.innerHTML = respuesta;
      });
}
function cargarCatalogoClientes(page){
  let datos = new FormData();
    var busqueda = $("#input_cliente").val();
    datos.append("buscar_cliente", busqueda);
    datos.append("page", page);
    datos.append("vista", "cargarCatalogoClientes");
    datos.append("modulo_venta", "listado_clientes");

    fetch(url + "app/ajax/ventaAjax.php", {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.text())
      .then((respuesta) => {
        let table_clientes = document.querySelector("#tabla_clientes");
        table_clientes.innerHTML = respuesta;
      });
}
function incrementarCarrito(codigo,token) {
  var actual = $("#cantidadCarrito" + token).val();

  var cantidad = Number.parseInt(actual) + 1;
  if (cantidad == 0) {
  } else {
    actualizarProductoCarrito(
      codigo,
      cantidad,
      token
    );
  }
}
function decrementarCarrito(codigo,token) {
  var actual = $("#cantidadCarrito" + token).val();

  var cantidad = Number.parseInt(actual) - 1;

  if (cantidad == 0) {
  } else {
    actualizarProductoCarrito(
      codigo,
      cantidad,
      token
    );
  }
}
function totalesCarritoVenta() {
  $.ajax({
    url: "../app/ajax/ventaAjax.php",
    type: "POST",
    data: {
      modulo_venta: "totales_carrito_venta",
    },
    success: function (response) {
      $(".container-totales").html(response);
    },
  })

}
function cargarCarritoVenta() {

  new Promise(function (resolve) {
    resolve(
      $.ajax({
        url: "../app/ajax/ventaAjax.php",
        type: "POST",
        data: {
          modulo_venta: "carrito_venta",
        },
        success: function (response) {
          $(".container-carrito").html(response);
        },
      })
    );
  }).then(function (result) {
    totalesCarritoVenta();
  });
 
}
function cargarCarritoNota() {
  
  $.ajax({
    url: "../app/ajax/notasAjax.php",
    type: "POST",
    data: {
      modulo_notas: "carrito_nota",
    },
    success: function (response) {
      $(".container-carrito-notas").html(response);
    },
  })
 
}
function actualizarNota(id_nota,fecha_publicacion){
 
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Quieres realizar la acción solicitada",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#B99654",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, realizar",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      var fecha = fecha_publicacion;
      var publicacion = new Date(fecha).getTime();
      var now = new Date();
      var diferencia = publicacion - now;
      if(diferencia<0){

        var respuesta = {
          tipo: "simple",
          titulo: "La nota no puede ser actualizada porque se encuentra en circulación.",
          text: "",
          icono: "error",
        };
        return alertas_ajax(respuesta);

      }else{
        var titulo_nota = $("#titulo_nota").val();
        var fecha_inicio = $("#fecha_publicacion").val();
        var fecha_expiracion = $("#fecha_expiracion").val();
        var porc_descuento_nota = $("#porc_descuento_nota").val();
  
        let datos = new FormData();
        datos.append("id_nota", id_nota);
        datos.append("titulo_nota",titulo_nota);
        datos.append("fecha_publicacion",fecha_inicio);
        datos.append("fecha_expiracion",fecha_expiracion);
        datos.append("porc_descuento_nota",porc_descuento_nota);
        datos.append("modulo_notas", "actualizar_nota");
  
        fetch(url + "app/ajax/notasAjax.php", {
          method: "POST",
          body: datos,
        })
          .then((respuesta) => respuesta.json())
          .then((respuesta) => {
            return alertas_ajax(respuesta);
          });

      }
    }
  });
}
function eliminarNota(id_nota,fecha_publicacion){
 
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Quieres realizar la acción solicitada",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#B99654",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, realizar",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      var fecha = fecha_publicacion;
      var publicacion = new Date(fecha).getTime();
      var now = new Date();
      var diferencia = publicacion - now;
      if(diferencia<0){

        var respuesta = {
          tipo: "simple",
          titulo: "La nota no puede ser eliminada porque se encuentra en circulación.",
          text: "",
          icono: "error",
        };
        return alertas_ajax(respuesta);

      }else{
  
        let datos = new FormData();
        datos.append("id_nota", id_nota);
        datos.append("modulo_notas", "eliminar_nota");
  
        fetch(url + "app/ajax/notasAjax.php", {
          method: "POST",
          body: datos,
        })
          .then((respuesta) => respuesta.json())
          .then((respuesta) => {
            return alertas_ajax(respuesta);
          });

      }
    }
  });
}
function registrarVenta(){
  var sesion_caja = localStorage.session_caja;
  if(sesion_caja == undefined) {
    
    Swal.fire({
      icon: "error",
      title: "No se ha iniciado la sesión de caja",
      text: "Para realizar una venta debe aperturar su sesión de caja",
      confirmButtonText: "Aperturar Caja",
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url+"dashboard/";
      }
    });

    
  }else{

    Swal.fire({
      title: "¿Desea Registrar La Venta?",
      text: "",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#B99654",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, Registrar",
      cancelButtonText: "No, cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        var forma_pago = $("#forma_pago_venta").val();
        var total_pago = $("#total_pagar_venta").val();
        var total_pagado = $("#total_pagado_venta").val();
        var total_Cambio = $("#total_cambio_venta").val();
        var referencia_venta = $("#referencia_venta").val();
        var venta_caja = $("#venta_caja").val();

        if (forma_pago == "1") {
          var venta_abono = total_pagado;
          var referencia = "";
        }else{
          var venta_abono = total_pago;
          var referencia = referencia_venta;
        }

        let datos = new FormData();
        datos.append("venta_caja", venta_caja);
        datos.append("forma_pago", forma_pago);
        datos.append("total_pago", total_pago);
        datos.append("total_pagado", venta_abono);
        datos.append("total_cambio", total_Cambio);
        datos.append("referencia_venta", referencia);
        datos.append("modulo_venta", "registrar_venta");
        fetch(url + "app/ajax/ventaAjax.php", {
          method: "POST",
          body: datos,
        }).then((respuesta) => respuesta.json())
        .then((respuesta) => {
          cargarCarritoVenta();
          $("#btn-close-pago").click();
          return alertas_ajax(respuesta);
        })
            
      }
    });
    
  }
 
}

function aperturarCaja(){
  var saldo_inicial = $("#saldo_inicial").val();
  var notas_apertura = $("#notas_apertura").val();
  let datos = new FormData();
        datos.append("saldo_inicial", saldo_inicial);
        datos.append("notas_apertura", notas_apertura);
        datos.append("modulo_caja", "aperturar_caja");

          new Promise(function (resolve) {
            resolve(
              fetch(url + "app/ajax/cajaAjax.php", {
                method: "POST",
                body: datos,
              })
                .then((respuesta) => respuesta.json())
                .then((respuesta) => {
                  return alertas_ajax(respuesta);
                })
            );
          }).then(function (result) {
           
              localStorage.setItem("session_caja","abierta");
          
              setTimeout(function() {
                window.location.href = url+"saleNew/";
              }, 2000);
          });
}
function obtenerDatosCorteCaja(sesion){
  $.ajax({
    url: "../app/ajax/cajaAjax.php",
    type: "POST",
    data: {
      sesion_caja:sesion,
      modulo_caja: "datos_corte_caja",
    },
    success: function (response) {
      var datos = JSON.parse(response)
     document.getElementById('field-ordenes').innerHTML = "<label class='label' style='color:#B99654'>"+datos.num_ventas+" Ventas: <span>$ "+datos.total_ventas+"</span></label>";
     document.getElementById('field-saldo-inicial').innerHTML = "$ "+datos.saldo_inicial+"";
     document.getElementById('field-efectivo').innerHTML = "$ "+datos.efectivo+"";
     document.getElementById('field-transferencia').innerHTML = "$ "+datos.transferencia+"";
     document.getElementById('field-td').innerHTML = "$ "+datos.tarjeta_debito+"";
     document.getElementById('field-tc').innerHTML = "$ "+datos.tarjeta_credito+"";
     document.getElementById('field-entrada-efectivo').innerHTML = "$ "+datos.entrada_efectivo+"";
     document.getElementById('field-salida-efectivo').innerHTML = "$ "+datos.salida_efectivo+"";
     var total_caja = (parseFloat(datos.saldo_inicial)+parseFloat(datos.efectivo)+parseFloat(datos.entrada_efectivo))-parseFloat(datos.salida_efectivo);
     document.getElementById('field-total-caja').innerHTML = total_caja.toFixed(2);
    },
  })
  
}
function calcularDiferenciaCaja() {
  var efectivo = $("#saldo_final_corte").val();
  var total_caja = $("#field-total-caja").text();
  
  var diferencia_caja = parseFloat(efectivo)-parseFloat(total_caja);
  $("#field-diferencia-caja").val(diferencia_caja.toFixed(2));

}
function cerrarCaja(sesion){

  Swal.fire({
    title: "¿Desea Cerrar La Caja?",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#B99654",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Cerrar",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      var saldo_final = $("#saldo_final_corte").val();
      var diferencia = $("#field-diferencia-caja").val();
      var observaciones = $("#observaciones_corte").val();
      var dif_dn_1 = $("#dif_dn_1").val();
      var dif_dn_2 = $("#dif_dn_2").val();
      var dif_dn_3 = $("#dif_dn_3").val();
      var dif_dn_4 = $("#dif_dn_4").val();
      var dif_dn_5 = $("#dif_dn_5").val();
      var dif_dn_6 = $("#dif_dn_6").val();
      var dif_dn_7 = $("#dif_dn_7").val();
      var dif_dn_8 = $("#dif_dn_8").val();
      var dif_dn_9 = $("#dif_dn_9").val();
      var dif_dn_10 = $("#dif_dn_10").val();
      var dif_dn_11 = $("#dif_dn_11").val();
      var dif_dn_12 = $("#dif_dn_12").val();
      var dif_dn_13 = $("#dif_dn_13").val();

      var total_denominaciones_caja = $("#total_denominaciones_caja").val();
     

      let datos = new FormData();
      datos.append("saldo_final", saldo_final);
      datos.append("diferencia", diferencia);
      datos.append("dif_dn_1", dif_dn_1);
      datos.append("dif_dn_2", dif_dn_2);
      datos.append("dif_dn_3", dif_dn_3);
      datos.append("dif_dn_4", dif_dn_4);
      datos.append("dif_dn_5", dif_dn_5);
      datos.append("dif_dn_6", dif_dn_6);
      datos.append("dif_dn_7", dif_dn_7);
      datos.append("dif_dn_8", dif_dn_8);
      datos.append("dif_dn_9", dif_dn_9);
      datos.append("dif_dn_10", dif_dn_10);
      datos.append("dif_dn_11", dif_dn_11);
      datos.append("dif_dn_12", dif_dn_12);
      datos.append("dif_dn_13", dif_dn_13);
      datos.append("observaciones", observaciones);
      datos.append("total_denominaciones_caja", total_denominaciones_caja);
      datos.append("sesion_caja", sesion);

      datos.append("modulo_caja", "cerrar_caja");
      fetch(url + "app/ajax/cajaAjax.php", {
        method: "POST",
        body: datos,
      }).then((respuesta) => respuesta.json())
      .then((respuesta) => {
        localStorage.removeItem("session_caja");
        return alertas_ajax(respuesta);
      })
          
    }
  });

}
function salidaEfectivo(sesion){
  var efectivo = $("#salida_efectivo").val();
  var motivo = $("#salida_motivo").val();

  let datos = new FormData();
  datos.append("efectivo", efectivo);
  datos.append("motivo", motivo);
  datos.append("sesion_caja", sesion);
  datos.append("modulo_caja", "salida_efectivo_caja");
  fetch(url + "app/ajax/cajaAjax.php", {
    method: "POST",
    body: datos,
  }).then((respuesta) => respuesta.json())
  .then((respuesta) => {
    
    return alertas_ajax(respuesta);
  })
}

function entradaEfectivo(sesion){

  var efectivo = $("#entrada_efectivo").val();
  var motivo = $("#entrada_motivo").val();

  let datos = new FormData();
  datos.append("efectivo", efectivo);
  datos.append("motivo", motivo);
  datos.append("sesion_caja", sesion);
  datos.append("modulo_caja", "entrada_efectivo_caja");
  fetch(url + "app/ajax/cajaAjax.php", {
    method: "POST",
    body: datos,
  }).then((respuesta) => respuesta.json())
  .then((respuesta) => {

    return alertas_ajax(respuesta);
  })

}

function obtenerDetallePago(codigo_venta){
  $.ajax({
    url: url+"app/ajax/cajaAjax.php",
    type: "POST",
    data: {
      codigo_venta:codigo_venta,
      modulo_caja: "detalle_pago",
    },
    success: function (response) {
      var datos = JSON.parse(response);
    
      var forma_pago = document.getElementById("forma_pago_venta");

      var option = document.createElement("option");
      option.text = datos.metodo;
      option.value = datos.id_pago;
      forma_pago.add(option);
      eleccionFormaPago(datos.id_metodo_pago);
      if(datos.id_metodo_pago == 1){
        $("#total_pagar_venta").val(datos.total_pago);
        $("#total_pagado_venta").val(datos.total_pagado);
        $("#total_cambio_venta").val(datos.total_cambio);
      }else{
        $("#total_pagar_venta").val(datos.total_pago);
        $("#referencia_venta").val(datos.referencia);
      }
  
      
    },
  })
  
}