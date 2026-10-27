<?php
$idProveedorSeleccionado = (int) ($datosAnteriores['proveedor_id'] ?? 0);
$productosAnteriores = is_array($productosAnteriores ?? null) ? $productosAnteriores : [];
$jsonProductosAnteriores = json_encode(
    $productosAnteriores,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);
?>
<main class="contenido-principal">
    <div class="ruta-navegacion">
        <a href="/ProyectoFerredog/public/index.php?c=pedido&amp;a=lista">Pedidos</a>
        <span> / </span>
        <span>Nuevo pedido</span>
    </div>

    <h1 class="titulo-pagina">Nuevo pedido</h1>
    <p class="subtitulo-pagina">Elige un proveedor activo: solo se muestran los productos que surte</p>

    <?php if ($error !== ''): ?>
        <div class="alerta-error" role="alert">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form
        id="formularioPedido"
        method="POST"
        action="/ProyectoFerredog/public/index.php?c=pedido&amp;a=guardar"
        data-productos-url="/ProyectoFerredog/public/index.php?c=pedido&amp;a=productosProveedor"
        data-productos-anteriores="<?php echo htmlspecialchars($jsonProductosAnteriores ?: '{}', ENT_QUOTES, 'UTF-8'); ?>"
        data-proveedor-anterior="<?php echo $idProveedorSeleccionado; ?>"
        novalidate
    >
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-5">
                <label for="proveedorPedido" class="form-label">Proveedor *</label>
                <select id="proveedorPedido" name="proveedor_id" class="form-control" aria-describedby="proveedorPedido-error">
                    <option value="">Elige un proveedor</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option
                            value="<?php echo (int) $proveedor['Id_Proveedor']; ?>"
                            <?php echo $idProveedorSeleccionado === (int) $proveedor['Id_Proveedor'] ? 'selected' : ''; ?>
                        ><?php echo htmlspecialchars($proveedor['Nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="proveedorPedido-error" class="invalid-feedback" hidden>Elige un proveedor</div>
            </div>
            <div class="col-12 col-md-3">
                <label for="fechaPedido" class="form-label">Fecha</label>
                <div class="input-group">
                    <input
                        type="text"
                        id="fechaPedido"
                        class="form-control"
                        value="<?php echo htmlspecialchars(date('d/m/Y'), ENT_QUOTES, 'UTF-8'); ?>"
                        readonly
                        aria-label="Fecha de hoy, no editable"
                    >
                    <span class="input-group-text" aria-hidden="true">🔒</span>
                </div>
            </div>
        </div>

        <div id="productosPedido-error" class="alerta-error" role="alert" hidden></div>
        <div id="productosPedido-cargando" class="alerta-info" role="status" hidden>Cargando productos...</div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Stock actual</th>
                        <th scope="col">Máx.</th>
                        <th scope="col">Costo</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="productosPedido">
                    <tr>
                        <td colspan="7" class="text-center">Elige un proveedor para ver sus productos</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <section class="seccion-card d-flex align-items-center justify-content-between gap-4 py-3 px-4" aria-live="polite">
                <strong>Total del pedido</strong>
                <strong id="totalPedido" class="fs-3">$0.00</strong>
            </section>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-3">
            <a href="/ProyectoFerredog/public/index.php?c=pedido&amp;a=lista" class="btn btn-outline-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary btn-agregar-usuario">Guardar pedido</button>
        </div>
    </form>
</main>
