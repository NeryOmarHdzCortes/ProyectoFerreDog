<?php
$formatearImporte = static function ($importe) {
    return '$' . number_format((float) $importe, 2, '.', ',');
};
?>
<main class="contenido-principal pantalla-ticket">
    <h1 class="titulo-pagina">Venta registrada</h1>
    <div class="ticket-layout">
        <section class="ticket-resumen">
            <div class="alerta-exito" role="status">
                La venta se registró correctamente.
                Total: <strong><?php echo $formatearImporte($venta['Total']); ?></strong>
                · Método: <strong><?php echo htmlspecialchars($venta['Metodo'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>

            <section class="seccion-card">
                <h2 class="h5">Existencias actualizadas</h2>
                <?php if (empty($existenciasActualizadas)): ?>
                    <p class="mb-0">No se reportaron cambios de existencias para esta venta.</p>
                <?php else: ?>
                    <ul class="mb-0">
                        <?php foreach ($existenciasActualizadas as $movimiento): ?>
                            <li>
                                <?php echo htmlspecialchars($movimiento['Nombre_Producto'], ENT_QUOTES, 'UTF-8'); ?>:
                                <?php echo (int) $movimiento['Stock_Antes']; ?>
                                → <?php echo (int) $movimiento['Stock_Despues']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <div class="ticket-acciones">
                <button type="button" class="btn btn-primary" id="imprimirTicket">Imprimir ticket</button>
                <a class="btn btn-secondary" href="/ProyectoFerredog/public/index.php?c=venta&a=index">Nueva venta</a>
            </div>
        </section>

        <article class="ticket-impresion" aria-label="Ticket de venta">
            <h2>FERREDOG</h2>
            <p class="ticket-subtitulo">Ticket de venta</p>
            <hr class="ticket-separador">
            <div class="ticket-dato">
                <span>Folio</span>
                <span><?php echo htmlspecialchars($folio, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="ticket-dato">
                <span>Fecha</span>
                <span><?php echo htmlspecialchars($fechaVenta, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="ticket-dato">
                <span>Vendedor</span>
                <span><?php echo htmlspecialchars($nombreVendedor, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <hr class="ticket-separador">

            <?php foreach ($detalles as $detalle): ?>
                <div class="ticket-renglon">
                    <span class="ticket-renglon-nombre">
                        <?php echo (int) $detalle['Cantidad']; ?> ×
                        <?php echo htmlspecialchars($detalle['Producto'] ?? 'Producto eliminado', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <span><?php echo $formatearImporte($detalle['Subtotal']); ?></span>
                </div>
            <?php endforeach; ?>

            <hr class="ticket-separador">
            <div class="ticket-total">
                <span>TOTAL</span>
                <span><?php echo $formatearImporte($venta['Total']); ?></span>
            </div>
            <div class="ticket-pago">
                <span>Método de pago</span>
                <span><?php echo htmlspecialchars($venta['Metodo'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <?php if ($datosPago !== null): ?>
                <div class="ticket-pago">
                    <span>Pago recibido</span>
                    <span><?php echo $formatearImporte($datosPago['pago']); ?></span>
                </div>
                <div class="ticket-pago">
                    <span>Cambio</span>
                    <span><?php echo $formatearImporte($datosPago['cambio']); ?></span>
                </div>
            <?php endif; ?>
            <p class="ticket-agradecimiento">¡Gracias por su compra!</p>
        </article>
    </div>
</main>
