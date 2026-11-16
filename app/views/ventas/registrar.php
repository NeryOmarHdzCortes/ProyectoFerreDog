<main class="contenido-principal pantalla-venta">
    <h1 class="titulo-pagina">Registrar venta</h1>
    <p class="subtitulo-pagina">Busca productos, agrégalos con su cantidad y cobra.</p>

    <?php if ($mensajeError !== ''): ?>
        <div class="alerta-error" role="alert">
            <?php echo htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="venta-paneles">
        <section class="seccion-card venta-panel">
            <h2 class="venta-panel-titulo">Buscar producto</h2>
            <form
                id="formularioBusquedaVenta"
                data-buscar-url="/ProyectoFerredog/public/index.php?c=venta&amp;a=buscar"
                novalidate
            >
                <div class="input-group venta-buscador">
                    <label for="busquedaProductoVenta" class="visually-hidden">Buscar producto por nombre o código de barras</label>
                    <input
                        type="search"
                        id="busquedaProductoVenta"
                        class="form-control"
                        placeholder="Nombre o código de barras"
                        maxlength="100"
                        autocomplete="off"
                    >
                    <button type="submit" class="btn btn-primary btn-venta-buscar">Buscar producto</button>
                </div>
            </form>

            <div id="busquedaProductoVenta-error" class="alerta-error" role="alert" hidden></div>

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
                    <tbody id="resultadosProductosVenta">
                        <tr>
                            <td colspan="6" class="text-center">Busca un producto para ver resultados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="alertaVenta" class="alerta-error venta-alerta" role="alert" hidden></div>
            <p class="venta-nota">No se muestran productos inactivos ni sin precio.</p>
        </section>

        <form
            id="formularioRegistroVenta"
            class="seccion-card venta-panel"
            method="POST"
            action="/ProyectoFerredog/public/index.php?c=venta&amp;a=registrar"
            novalidate
        >
            <div class="venta-ticket-encabezado">
                <h2 class="venta-panel-titulo">Ticket de venta</h2>
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
                    <tbody id="renglonesTicketVenta">
                        <tr>
                            <td colspan="5" class="text-center">Agrega productos para iniciar la venta</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="venta-total">
                <strong>Total</strong>
                <strong id="totalVenta">$0.00</strong>
            </div>

            <div class="venta-pago">
                <div>
                    <label for="metodoPagoVenta" class="form-label">Método de pago</label>
                    <select id="metodoPagoVenta" name="metodo" class="form-control">
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
                        name="pago"
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
                <button type="submit" class="btn btn-success btn-venta-registrar" disabled>
                    Registrar venta
                </button>
                <button type="button" class="btn btn-danger btn-venta-cancelar">
                    Cancelar venta
                </button>
            </div>
            <div id="productosRegistroVenta" hidden></div>
        </form>
    </div>

    <div class="modal fade" id="modalConfirmarVenta" tabindex="-1" aria-labelledby="tituloModalConfirmarVenta" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="tituloModalConfirmarVenta">Confirmar venta</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ¿Registrar la venta por <strong id="totalConfirmacionVenta">$0.00</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Revisar ticket</button>
                    <button type="button" class="btn btn-success" id="confirmarRegistroVenta">Confirmar venta</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCancelarVenta" tabindex="-1" aria-labelledby="tituloModalCancelarVenta" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="tituloModalCancelarVenta">¿Cancelar la venta?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">Se descartarán los productos del ticket.</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Seguir vendiendo</button>
                    <button type="button" class="btn btn-danger" id="confirmarCancelarVenta">Cancelar venta</button>
                </div>
            </div>
        </div>
    </div>
</main>
<script
    src="/ProyectoFerredog/public/assets/js/reglas.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/reglas.js'); ?>"
    defer
></script>
