<main class="contenido-principal">
    <div class="encabezado-pagina">
        <div>
            <h1 class="titulo-pagina">Pedidos a proveedores</h1>
            <p class="subtitulo-pagina">Pedidos Por recibir; cancela uno si el proveedor no puede surtirlo</p>
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
        <?php if (empty($pedidos)): ?>
            <p class="mb-0">No hay pedidos por recibir</p>
        <?php else: ?>
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
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?php echo (int) $pedido['Id_Compras']; ?></td>
                                <td><?php echo htmlspecialchars($pedido['Proveedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php
                                    $fecha = new DateTime($pedido['Fecha_Compra']);
                                    echo htmlspecialchars($fecha->format('d/m/Y'), ENT_QUOTES, 'UTF-8');
                                    ?>
                                </td>
                                <td><?php echo (int) $pedido['Productos']; ?></td>
                                <td>$<?php echo number_format((float) $pedido['Total'], 2, '.', ','); ?></td>
                                <td><span class="badge badge-amarillo">Por recibir</span></td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-cancelar-pedido"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCancelarPedido"
                                        data-id-pedido="<?php echo (int) $pedido['Id_Compras']; ?>"
                                        data-proveedor="<?php echo htmlspecialchars($pedido['Proveedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        data-total="<?php echo number_format((float) $pedido['Total'], 2, '.', ','); ?>"
                                    >Cancelar pedido</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="tabla-pie">
            Mostrando <?php echo count($pedidos); ?> pedidos Por recibir
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
