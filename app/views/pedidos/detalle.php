<?php
$textosEstadoDetalle = [
    'Pendiente' => 'Por recibir',
    'Recibida' => 'Recibido',
    'Cancelada' => 'Cancelado'
];
$clasesEstadoDetalle = [
    'Pendiente' => 'badge-amarillo',
    'Recibida' => 'badge-verde',
    'Cancelada' => 'badge-gris'
];
$estadoPedido = $pedido['Estado'];
$fechaPedido = new DateTime($pedido['Fecha_Compra']);
?>
<main class="contenido-principal">
    <div class="encabezado-pagina">
        <div>
            <h1 class="titulo-pagina">Detalle del pedido #<?php echo (int) $pedido['Id_Compras']; ?></h1>
            <p class="subtitulo-pagina">Información y productos del pedido a proveedor</p>
        </div>
        <a href="/ProyectoFerredog/public/index.php?c=pedido&amp;a=lista" class="btn btn-outline-secondary">
            ← Volver a pedidos
        </a>
    </div>

    <section class="seccion-card mb-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <strong>N.º:</strong> #<?php echo (int) $pedido['Id_Compras']; ?>
            </div>
            <div class="col-12 col-md-6">
                <strong>Proveedor:</strong>
                <?php echo htmlspecialchars($pedido['Proveedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="col-12 col-md-6">
                <strong>Fecha:</strong>
                <?php echo htmlspecialchars($fechaPedido->format('d/m/Y'), ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="col-12 col-md-6">
                <strong>Estado:</strong>
                <span class="badge <?php echo $clasesEstadoDetalle[$estadoPedido] ?? 'badge-gris'; ?>">
                    <?php echo htmlspecialchars($textosEstadoDetalle[$estadoPedido] ?? $estadoPedido, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
            <div class="col-12 col-md-6">
                <strong>Total:</strong> $<?php echo number_format((float) $pedido['Total'], 2, '.', ','); ?>
            </div>
            <div class="col-12 col-md-6">
                <strong>Registrado por:</strong>
                <?php echo htmlspecialchars($pedido['EmpleadoRegistro'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php if ($estadoPedido === 'Recibida'): ?>
                <div class="col-12 col-md-6">
                    <strong>Fecha de recepción:</strong>
                    <?php
                    $fechaRecepcion = $pedido['Fecha_Recepcion']
                        ? new DateTime($pedido['Fecha_Recepcion'])
                        : null;
                    echo $fechaRecepcion
                        ? htmlspecialchars($fechaRecepcion->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8')
                        : '—';
                    ?>
                </div>
                <div class="col-12 col-md-6">
                    <strong>Recibido por:</strong>
                    <?php echo htmlspecialchars($pedido['EmpleadoRecepcion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="seccion-card">
        <h2 class="subtitulo-seccion">Productos del pedido</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Cant. pedida</th>
                        <th scope="col">Cant. recibida</th>
                        <th scope="col">Costo</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col">Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($detalles)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Este pedido no tiene productos</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($detalles as $detalle): ?>
                            <?php
                            $cantidadRecibida = $detalle['Cantidad_Recibida'];
                            $observacion = '—';
                            if (in_array($detalle['Incidencia'], ['Dañado', 'Faltante'], true)
                                && $cantidadRecibida !== null) {
                                $cantidadIncidencia = (int) $detalle['Cantidad'] - (int) $cantidadRecibida;
                                $observacion = $detalle['Incidencia'] . ': ' . $cantidadIncidencia
                                    . ' - ' . (string) $detalle['Motivo_Incidencia'];
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detalle['Producto'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int) $detalle['Cantidad']; ?></td>
                                <td><?php echo $cantidadRecibida === null ? '—' : (int) $cantidadRecibida; ?></td>
                                <td>$<?php echo number_format((float) $detalle['Precio_Unitario'], 2, '.', ','); ?></td>
                                <td>$<?php echo number_format((float) $detalle['Subtotal'], 2, '.', ','); ?></td>
                                <td><?php echo htmlspecialchars($observacion, ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
