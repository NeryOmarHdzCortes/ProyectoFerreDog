<main class="contenido-principal pantalla-venta">
    <h1 class="titulo-pagina">Registrar venta</h1>
    <p class="subtitulo-pagina">Busca productos, agrégalos con su cantidad y cobra.</p>

    <div class="venta-paneles">
        <section class="seccion-card venta-panel">
            <h2 class="venta-panel-titulo">Buscar producto</h2>
            <div class="input-group venta-buscador">
                <label for="busquedaProductoVenta" class="visually-hidden">Buscar producto por nombre o código de barras</label>
                <input
                    type="search"
                    id="busquedaProductoVenta"
                    class="form-control"
                    placeholder="Nombre o código de barras"
                    autocomplete="off"
                >
                <button type="button" class="btn btn-primary btn-venta-buscar">Buscar producto</button>
            </div>

            <div class="table-responsive venta-resultados">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">CÓDIGO</th>
                            <th scope="col">PRODUCTO</th>
                            <th scope="col">PRECIO</th>
                            <th scope="col">EXIST.</th>
                            <th scope="col">CANT.</th>
                            <th scope="col"><span class="visually-hidden">Acción</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center">Busca un producto para ver resultados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="venta-nota">No se muestran productos inactivos ni sin precio.</p>
        </section>

        <section class="seccion-card venta-panel">
            <div class="venta-ticket-encabezado">
                <h2 class="venta-panel-titulo">Ticket en construcción</h2>
                <span>Folio: <?php echo htmlspecialchars($folioProvisional, ENT_QUOTES, 'UTF-8'); ?>
                    · <?php echo htmlspecialchars($fechaVenta, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="table-responsive venta-ticket-tabla">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">PRODUCTO</th>
                            <th scope="col">CANT.</th>
                            <th scope="col">P. UNIT.</th>
                            <th scope="col">SUBTOTAL</th>
                            <th scope="col"><span class="visually-hidden">Quitar</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">Agrega productos para iniciar la venta</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="venta-total">
                <strong>Total</strong>
                <strong>$0.00</strong>
            </div>

            <div class="venta-pago">
                <div>
                    <label for="metodoPagoVenta" class="form-label">Método de pago</label>
                    <select id="metodoPagoVenta" class="form-control">
                        <?php foreach ($metodosPago as $metodoPago): ?>
                            <option value="<?php echo (int) $metodoPago['Id_Metodo_Pago']; ?>">
                                <?php echo htmlspecialchars($metodoPago['Nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="pagoRecibidoVenta" class="form-label">Pago recibido</label>
                    <input
                        type="text"
                        id="pagoRecibidoVenta"
                        class="form-control"
                        value="0.00"
                        inputmode="decimal"
                        data-solo-decimales
                    >
                </div>
                <div>
                    <label for="cambioVenta" class="form-label">Cambio</label>
                    <output id="cambioVenta" class="form-control venta-cambio">$0.00</output>
                </div>
            </div>

            <div class="venta-acciones">
                <button type="button" class="btn btn-success btn-venta-registrar" disabled>
                    Registrar venta
                </button>
                <button type="button" class="btn btn-danger btn-venta-cancelar">
                    Cancelar venta
                </button>
                <button type="button" class="btn btn-secondary btn-venta-imprimir" disabled>
                    Imprimir ticket
                </button>
            </div>
        </section>
    </div>
</main>
