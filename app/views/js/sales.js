var urlPathNew = "http://localhost/pos/";
/* Detectar cuando se envia el formulario para agregar producto */
let sale_form_barcode = document.querySelector("#sale-barcode-form");
sale_form_barcode.addEventListener("submit", function (event) {
  event.preventDefault();
  setTimeout("agregar_producto()", 100);
});

/* Detectar cuando escanea un codigo en formulario para agregar producto */
let sale_input_barcode = document.querySelector("#sale-barcode-input");
sale_input_barcode.addEventListener("paste", function () {
  setTimeout("agregar_producto()", 100);
});

/* Agregar producto */
function agregar_producto() {
  var tipo_busqueda = $("#tipo_busqueda").val();
  let codigo_producto = document.querySelector("#sale-barcode-input").value;

  codigo_producto = codigo_producto.trim();

  if (codigo_producto != "") {
    let datos = new FormData();

    if (tipo_busqueda == "nota") {
      var descuento = $("#porc_descuento").val();
      var path = "app/ajax/notasAjax.php";
      datos.append("codigo", codigo_producto);
      datos.append("porc_descuento", descuento);
      datos.append("modulo_notas", "agregar_producto_nota");
    } else {
      var descuento = $("#descuento").val();
      var path = "app/ajax/ventaAjax.php";
      datos.append("codigo", codigo_producto);
      datos.append("porc_descuento", descuento);
      datos.append("modulo_venta", "agregar_producto");
    }
     new Promise(function (resolve) {
    resolve(
      fetch(urlPathNew + path, {
        method: "POST",
        body: datos,
      })
        .then((respuesta) => respuesta.json())
      .then((respuesta) => {
        $("#btn-close-productos").click();
        $(".alerta_producto").html('<div class="notification is-success is-light ">'+respuesta+'</div>');
      })
    );
  }).then(function (result) {
    if (tipo_busqueda == "nota") {
      cargarCarritoNota();

    }else{
      cargarCarritoVenta();
    }
   
    setTimeout(function() {
      document.getElementById("sale-barcode-input").value = "";
      $(".alerta_producto").html('').fadeIn("slow");;
    }, 2000);
  });
    
  } else {
    Swal.fire({
      icon: "error",
      title: "Ocurrió un error inesperado",
      text: "Debes de introducir el código del producto",
      confirmButtonText: "Aceptar",
    });
  }
}



/*----------  Agregar codigo  ----------*/
function agregar_codigo($codigo) {
  document.querySelector("#sale-barcode-input").value = $codigo;
  setTimeout("agregar_producto()", 100);
}

function actualizarProductoCarrito(codigo,cantidad,token){
  var tipo_busqueda = $("#tipo_busqueda").val();
  let datos = new FormData();
  var actual = cantidad

  var cantidad = Number.parseInt(actual);
  if (tipo_busqueda == "nota") {
   
    var path = "app/ajax/notasAjax.php";
    datos.append("cantidad", cantidad);
    datos.append("codigo", codigo);
    datos.append("modulo_notas", "actualizar_producto_nota");
  } else {
  
    var path = "app/ajax/ventaAjax.php";
    datos.append("cantidad", cantidad);
    datos.append("codigo", codigo);
    datos.append("modulo_venta", "actualizar_producto");
  }
   new Promise(function (resolve) {
  resolve(
    fetch(urlPathNew + path, {
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
    $(".alerta_producto").html('').fadeIn("slow");;
  }, 2000);
});
}
function removerProductoCarrito(codigo){
  let datos = new FormData();
  var tipo_busqueda = $("#tipo_busqueda").val();
  if (tipo_busqueda == "nota") {
   
    var path = "app/ajax/notasAjax.php";
    datos.append("codigo", codigo);
    datos.append("modulo_notas", "remover_producto_nota");
  } else {
  
    var path = "app/ajax/ventaAjax.php";
    datos.append("codigo", codigo);
    datos.append("modulo_venta", "remover_producto");
  }
   new Promise(function (resolve) {
  resolve(
    fetch(urlPathNew + path, {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.json())
    .then((respuesta) => {
      $(".alerta_producto").html('<div class="notification is-success is-light ">'+respuesta+'</div>');
    })
  );
}).then(function (result) {
  if (tipo_busqueda == "nota") {
   
    cargarCarritoNota();
  } else {
    cargarCarritoVenta();
  }
  
 
  setTimeout(function() {

    $(".alerta_producto").html('').fadeIn("slow");;
  }, 2000);
});

}
/*----------  Buscar cliente  ----------*/
function buscar_cliente() {
  let input_cliente = document.querySelector("#input_cliente").value;

  input_cliente = input_cliente.trim();

  if (input_cliente != "") {
    let datos = new FormData();
    datos.append("buscar_cliente", input_cliente);
    datos.append("modulo_venta", "buscar_cliente");

    fetch(urlPathNew + "app/ajax/ventaAjax.php", {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.text())
      .then((respuesta) => {
        let tabla_clientes = document.querySelector("#tabla_clientes");
        tabla_clientes.innerHTML = respuesta;
      });
  } else {
    Swal.fire({
      icon: "error",
      title: "Ocurrió un error inesperado",
      text: "Debes de introducir el Numero de documento, Nombre, Apellido o Teléfono del cliente",
      confirmButtonText: "Aceptar",
    });
  }
}

/*----------  Agregar cliente  ----------*/
function agregar_cliente(id) {
  let datos = new FormData();
  datos.append("id_cliente", id);
  datos.append("modulo_venta", "agregar_cliente");

  fetch(urlPathNew + "app/ajax/ventaAjax.php", {
    method: "POST",
    body: datos,
  })
    .then((respuesta) => respuesta.json())
    .then((respuesta) => {
      $("#btn-close-clientes").click();
      return alertas_ajax(respuesta);
    });
}

/*----------  Remover cliente  ----------*/
function remover_cliente(id) {
  Swal.fire({
    title: "¿Quieres remover este cliente?",
    text: "Se va a quitar el cliente seleccionado de la venta",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#B99654",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, remover",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      let datos = new FormData();
      datos.append("id_cliente", id);
      datos.append("modulo_venta", "remover_cliente");

      fetch(urlPathNew + "app/ajax/ventaAjax.php", {
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

/*----------  Calcular cambio  ----------*/
let venta_abono_input = document.querySelector("#venta_abono");
venta_abono_input.addEventListener("keyup", function (e) {
  e.preventDefault();

  let abono = document.querySelector("#venta_abono").value;
  abono = abono.trim();
  abono = parseFloat(abono);

  let total = document.querySelector("#total_hidden").value;
  total = total.trim();
  total = parseFloat(total);

  if (abono >= total) {
    cambio = abono - total;
    cambio = parseFloat(cambio).toFixed(2);
    document.querySelector("#cambio").value = cambio;
  } else {
    document.querySelector("#cambio").value = "0.00";
  }
});

/*----------  Calcular descuento  ----------*/
let descuento_input = document.querySelector("#descuento");
descuento_input.addEventListener("keyup", function (e) {
  e.preventDefault();

  let descuento = document.querySelector("#descuento").value;
  descuento = descuento.trim();
  descuento = parseFloat(descuento);

  let total = document.querySelector("#total_hidden").value;
  total = total.trim();
  total = parseFloat(total);

  if (descuento <= total) {
    desc = total / descuento;
    desc = parseFloat(desc).toFixed(2);
    document.querySelector("#descuento_hidden").value = desc;
  } else {
    document.querySelector("#cambio").value = "0.00";
  }
});
function actualizarEstatus(tabla, id, estatus, estatus_pago) {
  let datos = new FormData();
  datos.append("tabla", tabla);
  datos.append("id_venta", id);
  datos.append("estatus", estatus);
  datos.append("estatus_pago", estatus_pago);
  datos.append("modulo_venta", "actualizar_estatus");

  fetch(urlPathNew + "app/ajax/ventaAjax.php", {
    method: "POST",
    body: datos,
  })
    .then((respuesta) => respuesta.json())
    .then((respuesta) => {
      return alertas_ajax(respuesta);
    });
}

function establecerFormaPago(formaPago, total_pago, codigo_venta, estatus,pendiente,pagado) {
  $("#btn-modal-pago").click();
  localStorage.setItem("codigo_venta", codigo_venta);
  $("#forma_pago_venta").val(formaPago);
 
  $("#total_pagar_venta").val(parseFloat(pendiente).toFixed(2));
  if (estatus == 0) {
    var respuesta = {
      tipo: "simple",
      titulo: "La Venta se encuentra cancelada",
      text: "",
      icono: "error",
    };

    return alertas_ajax(respuesta);
  } else {
    new Promise(function (resolve) {
      resolve(eleccionFormaPago(0));
    }).then(function (result) {
      calcularCambio();
    });
  }
}
function eleccionFormaPago(forma_pago) {

  if(forma_pago == 0){
    var forma_pago = $("#forma_pago_venta").val();
  }else{
    var forma_pago = forma_pago;
  }
  
  if (forma_pago === "1") {
    document.getElementById("div-payment-efectivo-1").style.display = "";
    document.getElementById("div-payment-efectivo-2").style.display = "none";
    document.getElementById("div-payment-transferencia").style.display = "none";
  }else if(forma_pago === "5"){
    document.getElementById("div-payment-efectivo-1").style.display = "none";
    document.getElementById("div-payment-efectivo-2").style.display = "none";
    document.getElementById("div-payment-transferencia").style.display = "none";
  } else {
    document.getElementById("div-payment-efectivo-1").style.display = "none";
    document.getElementById("div-payment-efectivo-2").style.display = "none";
    document.getElementById("div-payment-transferencia").style.display = "";
  }
}
function calcularCambio() {
  var total_pago = $("#total_pagar_venta").val();
  var total_pago = parseFloat(total_pago);

  var total_pagado = $("#total_pagado_venta").val();
  var total_pagado = parseFloat(total_pagado);
  if(isNaN(total_pagado)){
    var cambio = 0;
  }else{
    var cambio = total_pagado - total_pago;
  }

 
  $("#total_cambio_venta").val(parseFloat(cambio).toFixed(2));
}
function confirmacionPago() {
  var forma_pago = $("#forma_pago_venta").val();
  var total_pago = $("#total_pagar_venta").val();
  var total_pagado = $("#total_pagado_venta").val();
  var total_Cambio = $("#total_cambio_venta").val();
  var referencia_venta = $("#referencia_venta").val();
  var codigo_venta = localStorage.getItem("codigo_venta");
  if (forma_pago == "1") {
    /*
    if (total_pagado < total_pago) {
      Swal.fire({
        icon: "error",
        title: "Error de pago",
        text: "El monto pagado no puede ser menor al total de la venta.",
        confirmButtonText: "Aceptar",
      });
    } else {
      
    }
    */
    let datos = new FormData();
    datos.append("codigo_venta", codigo_venta);
    datos.append("forma_pago", forma_pago);
    datos.append("total_pago", total_pago);
    datos.append("total_pagado", total_pagado);
    datos.append("total_cambio", total_Cambio);
    datos.append("referencia_venta", "");
    datos.append("modulo_venta", "generar_pago_venta");

    fetch(urlPathNew + "app/ajax/ventaAjax.php", {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.json())
      .then((respuesta) => {
        return alertas_ajax(respuesta);
      });
  } else {
    let datos = new FormData();
    datos.append("codigo_venta", codigo_venta);
    datos.append("forma_pago", forma_pago);
    datos.append("total_pago", total_pago);
    datos.append("total_pagado", total_pago);
    datos.append("total_cambio", "0.00");
    datos.append("referencia_venta", referencia_venta);
    datos.append("modulo_venta", "generar_pago_venta");

    fetch(urlPathNew + "app/ajax/ventaAjax.php", {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.json())
      .then((respuesta) => {
        return alertas_ajax(respuesta);
      });
  }
}
function eleccionAccionCaja(sesion) {
  var accion = $("#accion_caja").val();

  switch (accion) {
    case 'cierre':
      document.getElementById("div-caja-cierre").style.display = "";
      document.getElementById("div-caja-salida").style.display = "none";
      document.getElementById("div-caja-entrada").style.display = "none";
      obtenerDatosCorteCaja(sesion);
      
    break;
    case 'entrada':
      document.getElementById("div-caja-cierre").style.display = "none";
      document.getElementById("div-caja-salida").style.display = "none";
      document.getElementById("div-caja-entrada").style.display = "";
    break;
    case 'salida':
      document.getElementById("div-caja-cierre").style.display = "none";
      document.getElementById("div-caja-salida").style.display = "";
      document.getElementById("div-caja-entrada").style.display = "none";
    break;
  
  }


}