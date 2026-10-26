<main class="contenido-principal">
    <h1 class="titulo-pagina">Registrar entrada de mercancía</h1>
    <p class="subtitulo-pagina">Busca el pedido Por recibir que llegó y revisa sus productos</p>

    <form method="GET" action="/ProyectoFerredog/public/index.php" class="mb-4">
        <input type="hidden" name="c" value="entrada">
        <input type="hidden" name="a" value="index">
        <div class="row g-2">
            <div class="col-12 col-md-6">
                <label for="busquedaPedidoEntrada" class="visually-hidden">Buscar pedido por número o proveedor</label>
                <input
                    type="search"
                    id="busquedaPedidoEntrada"
                    name="busqueda"
                    class="form-control"
                    placeholder="Buscar pedido por número o proveedor"
                    value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-agregar-usuario">Buscar</button>
            </div>
        </div>
    </form>

    <?php if ($mensajeError): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <div class="alert alert-success" role="status">
            <?php echo htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($pedidoNoEncontrado): ?>
        <div class="alert border-start border-4 border-danger bg-danger-subtle text-danger-emphasis" role="alert">
            <strong>Pedido no encontrado</strong>
            <div>El pedido no existe o ya fue recibido. No se puede registrar la entrada.</div>
        </div>
    <?php elseif ($pedido): ?>
        <section class="seccion-card d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="subtitulo-seccion mb-1">
                    Pedido #<?php echo (int) $pedido['Id_Compras']; ?> ·
                    <?php echo htmlspecialchars($pedido['Proveedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </h2>
                <p class="mb-0">
                    Fecha del pedido:
                    <?php
                    $fechaPedido = new DateTime($pedido['Fecha_Compra']);
                    echo htmlspecialchars($fechaPedido->format('d/m/Y'), ENT_QUOTES, 'UTF-8');
                    ?>
                    · <?php echo (int) $pedido['Productos']; ?> productos
                </p>
            </div>
            <span class="badge badge-amarillo">Por recibir</span>
        </section>

        <form id="formularioEntradaPedido" method="POST" action="/ProyectoFerredog/public/index.php?c=entrada&amp;a=registrar">
            <input type="hidden" name="id_compra" value="<?php echo (int) $pedido['Id_Compras']; ?>">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Producto</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Cant. pedida</th>
                            <th scope="col">Cant. recibida</th>
                            <th scope="col">Observación</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                            <?php $productoEliminado = $detalle['Id_Producto'] === null; ?>
                            <tr
                                <?php if (!$productoEliminado): ?>
                                    data-id-detalle="<?php echo (int) $detalle['Id_Detalle']; ?>"
                                    data-producto="<?php echo htmlspecialchars($detalle['Producto'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    data-marca="<?php echo htmlspecialchars($detalle['Marca'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>"
                                    data-cantidad-pedida="<?php echo (int) $detalle['Cantidad']; ?>"
                                <?php endif; ?>
                            >
                                <td>
                                    <?php echo htmlspecialchars($detalle['Producto'] ?? 'Producto', ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if ($productoEliminado): ?>
                                        <span>(eliminado)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($detalle['Marca'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int) $detalle['Cantidad']; ?></td>
                                <td data-cantidad-recibida><?php echo (int) $detalle['Cantidad']; ?></td>
                                <td data-observacion>—</td>
                                <td>
                                    <?php if (!$productoEliminado): ?>
                                        <button type="button" class="btn btn-sm btn-cancelar-pedido" data-abrir-reporte>
                                            Reportar
                                        </button>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="reportesEntradaOcultos"></div>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-success">Registrar entrada de mercancía</button>
            </div>
        </form>

        <div class="modal fade" id="modalReporteIncidencia" tabindex="-1" aria-labelledby="tituloReporteIncidencia" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="formularioReporteIncidencia" novalidate>
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h2 class="modal-title fs-5" id="tituloReporteIncidencia">Reportar faltante o daño</h2>
                                <p class="mb-0 text-secondary" id="detalleReporteIncidencia"></p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="tipoIncidencia" class="form-label">Tipo *</label>
                                <select id="tipoIncidencia" class="form-control" required aria-describedby="tipoIncidencia-error">
                                    <option value="">Elige el tipo</option>
                                    <option value="Faltante">Faltante</option>
                                    <option value="Dañado">Dañado</option>
                                </select>
                                <div id="tipoIncidencia-error" class="invalid-feedback">Selecciona el tipo de incidencia</div>
                            </div>
                            <div class="mb-3">
                                <label for="cantidadBuenEstado" class="form-label">Cantidad recibida en buen estado *</label>
                                <input
                                    type="number"
                                    id="cantidadBuenEstado"
                                    class="form-control"
                                    min="0"
                                    step="1"
                                    required
                                    aria-describedby="cantidadBuenEstado-error"
                                >
                                <div id="cantidadBuenEstado-error" class="invalid-feedback">La cantidad debe ser un entero entre 0 y la cantidad pedida menos 1</div>
                            </div>
                            <div class="mb-3">
                                <label for="motivoIncidencia" class="form-label">Motivo *</label>
                                <input
                                    type="text"
                                    id="motivoIncidencia"
                                    class="form-control"
                                    maxlength="200"
                                    required
                                    aria-describedby="motivoIncidencia-error"
                                >
                                <div id="motivoIncidencia-error" class="invalid-feedback">Escribe un motivo de hasta 200 caracteres</div>
                            </div>
                            <p class="small mb-0" id="avisoCantidadInventario" aria-live="polite">
                                Se sumará 0 pieza(s) al inventario; el motivo queda en el historial de movimientos.
                            </p>
                        </div>
                        <div class="modal-footer border-0 justify-content-between">
                            <button type="button" id="quitarReporteIncidencia" class="btn btn-outline-secondary" hidden>
                                Quitar reporte
                            </button>
                            <div class="d-flex gap-2 ms-auto">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Guardar reporte</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php elseif (count($pedidos) > 1): ?>
        <section class="seccion-card">
            <h2 class="seccion-titulo">Pedidos Por recibir encontrados</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">N.º</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Total</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedidoEncontrado): ?>
                            <tr>
                                <td>#<?php echo (int) $pedidoEncontrado['Id_Compras']; ?></td>
                                <td>
                                    <?php
                                    $fechaPedido = new DateTime($pedidoEncontrado['Fecha_Compra']);
                                    echo htmlspecialchars($fechaPedido->format('d/m/Y'), ENT_QUOTES, 'UTF-8');
                                    ?>
                                </td>
                                <td>$<?php echo number_format((float) $pedidoEncontrado['Total'], 2, '.', ','); ?></td>
                                <td>
                                    <form method="GET" action="/ProyectoFerredog/public/index.php">
                                        <input type="hidden" name="c" value="entrada">
                                        <input type="hidden" name="a" value="index">
                                        <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="id" value="<?php echo (int) $pedidoEncontrado['Id_Compras']; ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">Elegir pedido</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</main>
