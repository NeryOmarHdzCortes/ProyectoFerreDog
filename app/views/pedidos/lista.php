<?php
$clasesEstadoPedido = [
    'Pendiente' => 'badge-amarillo',
    'Recibida' => 'badge-verde',
    'Cancelada' => 'badge-gris'
];
$textosEstadoPedido = [
    'Pendiente' => 'Por recibir',
    'Recibida' => 'Recibido',
    'Cancelada' => 'Cancelado'
];
?>
<main class="contenido-principal">
    <div class="encabezado-pagina">
        <div>
            <h1 class="titulo-pagina">Pedidos a proveedores</h1>
            <p class="subtitulo-pagina">Consulta pedidos y cancela los que el proveedor no pueda surtir</p>
        </div>
        <a href="/ProyectoFerredog/public/index.php?c=pedido&amp;a=nuevo" class="btn btn-primary btn-agregar-usuario">
            + Nuevo pedido
        </a>
    </div>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alerta-exito" role="status">
            <?php echo htmlspecialchars($_SESSION['mensaje_exito'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['mensaje_exito']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alerta-error" role="alert">
            <?php echo htmlspecialchars($_SESSION['mensaje_error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['mensaje_error']); ?>
        </div>
    <?php endif; ?>

    <section class="seccion-card">
        <form method="GET" action="/ProyectoFerredog/public/index.php" class="filtros-form mb-4">
            <input type="hidden" name="c" value="pedido">
            <input type="hidden" name="a" value="lista">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-6">
                    <label for="busquedaPedido" class="form-label">Buscar por número o proveedor</label>
                    <div class="input-group">
                        <span class="input-group-text" aria-hidden="true">⌕</span>
                        <input
                            type="search"
                            id="busquedaPedido"
                            name="busqueda"
                            class="form-control"
                            placeholder="Buscar por número o proveedor"
                            value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>
                </div>
                <div class="col-12 col-md-5 col-lg-3">
                    <label for="filtroEstadoPedido" class="form-label">Estado</label>
                    <select id="filtroEstadoPedido" name="estado" class="form-control">
                        <option value="Pendiente" <?php echo $filtroEstado === 'Pendiente' ? 'selected' : ''; ?>>Por recibir</option>
                        <option value="Recibida" <?php echo $filtroEstado === 'Recibida' ? 'selected' : ''; ?>>Recibido</option>
                        <option value="Cancelada" <?php echo $filtroEstado === 'Cancelada' ? 'selected' : ''; ?>>Cancelado</option>
                        <option value="Todos" <?php echo $filtroEstado === 'Todos' ? 'selected' : ''; ?>>Todos</option>
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">N.º</th>
                        <th scope="col">Proveedor</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Productos</th>
                        <th scope="col">Total</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron pedidos</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <?php
                            $idPedido = (int) $pedido['Id_Compras'];
                            $estadoPedido = $pedido['Estado'];
                            $textoEstado = $textosEstadoPedido[$estadoPedido] ?? $estadoPedido;
                            $claseEstado = $clasesEstadoPedido[$estadoPedido] ?? 'badge-gris';
                            $fecha = new DateTime($pedido['Fecha_Compra']);
                            ?>
                            <tr>
                                <td>#<?php echo $idPedido; ?></td>
                                <td><?php echo htmlspecialchars($pedido['Proveedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($fecha->format('d/m/Y'), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int) $pedido['Productos']; ?></td>
                                <td>$<?php echo number_format((float) $pedido['Total'], 2, '.', ','); ?></td>
                                <td>
                                    <span class="badge <?php echo $claseEstado; ?>">
                                        <?php echo htmlspecialchars($textoEstado, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a
                                            href="/ProyectoFerredog/public/index.php?c=pedido&amp;a=ver&amp;id=<?php echo $idPedido; ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >Ver</a>
                                        <?php if ($estadoPedido === 'Pendiente'): ?>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-cancelar-pedido"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCancelarPedido"
                                                data-id-pedido="<?php echo $idPedido; ?>"
                                                data-proveedor="<?php echo htmlspecialchars($pedido['Proveedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-total="<?php echo number_format((float) $pedido['Total'], 2, '.', ','); ?>"
                                            >Cancelar pedido</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="tabla-pie d-flex justify-content-between align-items-center flex-wrap gap-3">
            <span>Mostrando <?php echo count($pedidos); ?> pedidos <?php echo htmlspecialchars($estadoTexto, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Paginación de pedidos">
                    <ul class="pagination mb-0">
                        <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                            <?php if ($pagina <= 1): ?>
                                <span class="page-link" aria-hidden="true">&lsaquo;</span>
                            <?php else: ?>
                                <a
                                    class="page-link"
                                    href="<?php echo htmlspecialchars('/ProyectoFerredog/public/index.php?' . http_build_query(array_merge($parametrosPaginacion, ['pagina' => $pagina - 1])), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="Página anterior"
                                >&lsaquo;</a>
                            <?php endif; ?>
                        </li>
                        <?php for ($numeroPagina = 1; $numeroPagina <= $totalPaginas; $numeroPagina++): ?>
                            <li class="page-item <?php echo $numeroPagina === $pagina ? 'active' : ''; ?>">
                                <?php if ($numeroPagina === $pagina): ?>
                                    <span class="page-link" aria-current="page"><?php echo $numeroPagina; ?></span>
                                <?php else: ?>
                                    <a
                                        class="page-link"
                                        href="<?php echo htmlspecialchars('/ProyectoFerredog/public/index.php?' . http_build_query(array_merge($parametrosPaginacion, ['pagina' => $numeroPagina])), ENT_QUOTES, 'UTF-8'); ?>"
                                    ><?php echo $numeroPagina; ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
                            <?php if ($pagina >= $totalPaginas): ?>
                                <span class="page-link" aria-hidden="true">&rsaquo;</span>
                            <?php else: ?>
                                <a
                                    class="page-link"
                                    href="<?php echo htmlspecialchars('/ProyectoFerredog/public/index.php?' . http_build_query(array_merge($parametrosPaginacion, ['pagina' => $pagina + 1])), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="Página siguiente"
                                >&rsaquo;</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>
</main>

<div class="modal fade" id="modalCancelarPedido" tabindex="-1" aria-labelledby="tituloModalCancelarPedido" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="modal-title fs-5" id="tituloModalCancelarPedido">¿Cancelar el pedido?</h2>
                    <p class="mb-0 text-secondary">
                        <span id="proveedorPedidoCancelar"></span> ·
                        $<span id="totalPedidoCancelar"></span> · Por recibir
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    El pedido pasará a Cancelado y se avisará al proveedor por correo.
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No, mantenerlo</button>
                <form method="POST" action="/ProyectoFerredog/public/index.php?c=pedido&amp;a=cancelar" id="formCancelarPedido">
                    <input type="hidden" name="id_pedido" id="idPedidoCancelar">
                    <button type="submit" class="btn btn-danger">Sí, cancelar pedido</button>
                </form>
            </div>
        </div>
    </div>
</div>
