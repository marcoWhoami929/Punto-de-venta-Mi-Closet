var url = "http://localhost/pos/";
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

    fetch(url + path, {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.json())
      .then((respuesta) => {
        return alertas_ajax(respuesta);
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

/*----------  Buscar codigo  ----------*/
function buscar_codigo() {
  let input_codigo = document.querySelector("#input_codigo").value;

  input_codigo = input_codigo.trim();

  if (input_codigo != "") {
    let datos = new FormData();
    datos.append("buscar_codigo", input_codigo);
    datos.append("modulo_venta", "buscar_codigo");

    fetch(url + "app/ajax/ventaAjax.php", {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.text())
      .then((respuesta) => {
        let tabla_productos = document.querySelector("#tabla_productos");
        tabla_productos.innerHTML = respuesta;
      });
  } else {
    Swal.fire({
      icon: "error",
      title: "Ocurrió un error inesperado",
      text: "Debes de introducir el Nombre, Marca o Modelo del producto",
      confirmButtonText: "Aceptar",
    });
  }
}

/*----------  Agregar codigo  ----------*/
function agregar_codigo($codigo) {
  document.querySelector("#sale-barcode-input").value = $codigo;
  setTimeout("agregar_producto()", 100);
}

/* Actualizar cantidad de producto */
function actualizar_cantidad(id, codigo) {
  let cantidad = document.querySelector(id).value;
  var porcentaje = $("#descuento").val();

  cantidad = cantidad.trim();
  codigo.trim();

  if (cantidad > 0) {
    let datos = new FormData();
    datos.append("codigo", codigo);
    datos.append("producto_cantidad", cantidad);
    datos.append("porc_descuento", porcentaje);
    datos.append("modulo_venta", "actualizar_producto");

    fetch(url + "app/ajax/ventaAjax.php", {
      method: "POST",
      body: datos,
    })
      .then((respuesta) => respuesta.json())
      .then((respuesta) => {
        return alertas_ajax(respuesta);
      });
  } else {
    Swal.fire({
      icon: "error",
      title: "Ocurrió un error inesperado",
      text: "Debes de introducir una cantidad mayor a 0",
      confirmButtonText: "Aceptar",
    });
  }
}

/*----------  Buscar cliente  ----------*/
function buscar_cliente() {
  let input_cliente = document.querySelector("#input_cliente").value;

  input_cliente = input_cliente.trim();

  if (input_cliente != "") {
    let datos = new FormData();
    datos.append("buscar_cliente", input_cliente);
    datos.append("modulo_venta", "buscar_cliente");

    fetch(url + "app/ajax/ventaAjax.php", {
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

  fetch(url + "app/ajax/ventaAjax.php", {
    method: "POST",
    body: datos,
  })
    .then((respuesta) => respuesta.json())
    .then((respuesta) => {
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
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, remover",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      let datos = new FormData();
      datos.append("id_cliente", id);
      datos.append("modulo_venta", "remover_cliente");

      fetch(url + "app/ajax/ventaAjax.php", {
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
function actualizarEstatus(tabla, id, estatus) {
  let datos = new FormData();
  datos.append("tabla", tabla);
  datos.append("id_venta", id);
  datos.append("estatus", estatus);
  datos.append("modulo_venta", "actualizar_estatus");

  fetch(url + "app/ajax/ventaAjax.php", {
    method: "POST",
    body: datos,
  })
    .then((respuesta) => respuesta.json())
    .then((respuesta) => {
      return alertas_ajax(respuesta);
    });
}
