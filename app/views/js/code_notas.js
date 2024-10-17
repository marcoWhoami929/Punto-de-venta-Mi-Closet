$(function () {
  urlPath = window.location.pathname;
  ruta = urlPath.split("/");
  switch (ruta[2]) {
    case "notesNew": {
      generarQrNotas();
    }
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

  fetch(urlPath + "app/ajax/ventaAjax.php", {
    method: "POST",
    body: datos,
  })
    .then((respuesta) => respuesta.json())
    .then((respuesta) => {
      return alertas_ajax(respuesta);
    });
}
