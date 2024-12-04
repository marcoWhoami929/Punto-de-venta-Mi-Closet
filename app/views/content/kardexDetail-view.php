<div class="container is-fluid mb-6">
    <h1 class="title">Kardex</h1>
    <h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Detalle Movimientos Producto</h2>
</div>

<div class="container is-fluid pb-6">
    <?php

    include "./app/views/inc/btn_back.php";

    $id_producto = $insLogin->limpiarCadena($url[1]);
    $datos = $insLogin->seleccionarDatos("Normal", "movimiento_inventario WHERE (id_producto ='$id_producto')", "*", 0);

    if ($datos->rowCount() > 1) {
        $movimientos = $datos->fetchAll();

    ?>

    <?php

    } else {
        echo  '
				<article class="message is-warning mt-4 mb-4">
		 <div class="message-header">
			<p></p>
		 </div>
		<div class="message-body has-text-centered">
			<i class="fas fa-exclamation-triangle fa-5x"></i><br>
			El produucto no tiene movimientos
		</div>
	</article>';
    }
    ?>
</div>