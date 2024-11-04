$(function () {
  urlPath = window.location.pathname;
  url = "http://localhost/pos/";
  ruta = urlPath.split("/");
  switch (ruta[2]) {
    case "notesNew":
      generarQrNotas();
      break;
    case "saleSearch":
      break;
  }
});
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

  fetch(url + "app/ajax/ventaAjax.php", {
    method: "POST",
    body: datos,
  })
    .then((respuesta) => respuesta.json())
    .then((respuesta) => {
      return alertas_ajax(respuesta);
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
