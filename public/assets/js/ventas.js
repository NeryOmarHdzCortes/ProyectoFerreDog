/**
 * Búsqueda de productos y construcción del ticket en memoria.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formularioBusqueda = document.getElementById('formularioBusquedaVenta');
    const campoBusqueda = document.getElementById('busquedaProductoVenta');
    const resultados = document.getElementById('resultadosProductosVenta');
    const errorBusqueda = document.getElementById('busquedaProductoVenta-error');
    const alertaVenta = document.getElementById('alertaVenta');
    const renglonesTicket = document.getElementById('renglonesTicketVenta');
    const salidaTotal = document.getElementById('totalVenta');
    const selectorMetodoPago = document.getElementById('metodoPagoVenta');
    const campoPago = document.getElementById('pagoRecibidoVenta');
    const salidaCambio = document.getElementById('cambioVenta');
    const botonRegistrar = document.querySelector('.btn-venta-registrar');
    const botonCancelar = document.querySelector('.btn-venta-cancelar');
    const botonConfirmarCancelar = document.getElementById('confirmarCancelarVenta');
    const modalCancelar = new bootstrap.Modal(document.getElementById('modalCancelarVenta'));
    const productosEncontrados = new Map();
    const ticket = [];
    const formatoMoneda = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    });

    function crearCelda(texto) {
        const celda = document.createElement('td');
        celda.textContent = texto;
        return celda;
    }

    function mostrarErrorBusqueda(mensaje) {
        errorBusqueda.textContent = mensaje;
        errorBusqueda.hidden = mensaje === '';
        campoBusqueda.classList.toggle('is-invalid', mensaje !== '');
        campoBusqueda.setAttribute('aria-invalid', mensaje === '' ? 'false' : 'true');
    }

    function mostrarAlertaVenta(mensaje) {
        alertaVenta.textContent = mensaje;
        alertaVenta.hidden = mensaje === '';
    }

    function mostrarResultadosVacios(mensaje) {
        resultados.replaceChildren();
        const fila = document.createElement('tr');
        const celda = crearCelda(mensaje);
        celda.colSpan = 6;
        celda.className = 'text-center';
        fila.appendChild(celda);
        resultados.appendChild(fila);
    }

    function actualizarTotal() {
        const total = window.Reglas.total(ticket.map(function (renglon) {
            return { cantidad: renglon.cantidad, costo: renglon.precio };
        }));
        salidaTotal.textContent = formatoMoneda.format(total);
        return total;
    }

    function actualizarPago() {
        const importeTotal = actualizarTotal();
        const metodo = selectorMetodoPago.selectedOptions[0]
            ? selectorMetodoPago.selectedOptions[0].textContent.trim().toLocaleLowerCase()
            : '';
        const esEfectivo = metodo === 'efectivo';
        campoPago.readOnly = !esEfectivo;
        if (!esEfectivo) {
            campoPago.value = importeTotal.toFixed(2);
        }

        if (ticket.length === 0) {
            botonRegistrar.disabled = true;
            salidaCambio.textContent = formatoMoneda.format(0);
            mostrarAlertaVenta('');
            return;
        }

        const pagoTexto = campoPago.value.trim();
        if (!/^[0-9]+(?:\.[0-9]{1,2})?$/.test(pagoTexto)) {
            botonRegistrar.disabled = true;
            salidaCambio.textContent = '—';
            mostrarAlertaVenta('Escribe el pago recibido.');
            return;
        }

        const pago = Number(pagoTexto);
        if (!Number.isFinite(pago) || pago < importeTotal) {
            const faltante = window.Reglas.faltaPorPagar(pago, importeTotal);
            botonRegistrar.disabled = true;
            salidaCambio.textContent = '—';
            mostrarAlertaVenta(
                '⚠ Pago insuficiente: faltan ' + formatoMoneda.format(faltante)
                    + '. No se puede registrar la venta.'
            );
            return;
        }

        botonRegistrar.disabled = false;
        salidaCambio.textContent = formatoMoneda.format(
            window.Reglas.cambio(pago, importeTotal)
        );
        mostrarAlertaVenta('');
    }

    function mostrarTicket() {
        renglonesTicket.replaceChildren();
        if (ticket.length === 0) {
            const filaVacia = document.createElement('tr');
            const celdaVacia = crearCelda('Agrega productos para iniciar la venta');
            celdaVacia.colSpan = 5;
            celdaVacia.className = 'text-center';
            filaVacia.appendChild(celdaVacia);
            renglonesTicket.appendChild(filaVacia);
            actualizarPago();
            return;
        }

        ticket.forEach(function (renglon) {
            const fila = document.createElement('tr');
            fila.appendChild(crearCelda(renglon.nombre));

            const celdaCantidad = document.createElement('td');
            const campoCantidad = document.createElement('input');
            campoCantidad.type = 'text';
            campoCantidad.className = 'form-control';
            campoCantidad.value = String(renglon.cantidad);
            campoCantidad.inputMode = 'numeric';
            campoCantidad.setAttribute('data-solo-enteros', '');
            campoCantidad.setAttribute('data-cantidad-ticket', String(renglon.idProducto));
            campoCantidad.setAttribute('aria-label', 'Cantidad de ' + renglon.nombre);
            window.configurarSoloNumeros(campoCantidad);
            celdaCantidad.appendChild(campoCantidad);
            fila.appendChild(celdaCantidad);

            fila.appendChild(crearCelda(formatoMoneda.format(renglon.precio)));
            fila.appendChild(crearCelda(
                formatoMoneda.format(window.Reglas.subtotal(renglon.cantidad, renglon.precio))
            ));

            const celdaAccion = document.createElement('td');
            const botonQuitar = document.createElement('button');
            botonQuitar.type = 'button';
            botonQuitar.className = 'btn btn-sm btn-outline-secondary';
            botonQuitar.textContent = '×';
            botonQuitar.setAttribute('aria-label', 'Quitar ' + renglon.nombre + ' del ticket');
            botonQuitar.setAttribute('data-quitar-ticket', String(renglon.idProducto));
            celdaAccion.appendChild(botonQuitar);
            fila.appendChild(celdaAccion);
            renglonesTicket.appendChild(fila);
        });

        actualizarPago();
    }

    function mostrarResultados(productos) {
        productosEncontrados.clear();
        resultados.replaceChildren();
        if (productos.length === 0) {
            mostrarResultadosVacios('No se encontraron productos con ese nombre o código.');
            return;
        }

        productos.forEach(function (producto) {
            const idProducto = String(producto.Id_Producto);
            productosEncontrados.set(idProducto, producto);
            const fila = document.createElement('tr');
            fila.appendChild(crearCelda(String(producto.Codigo_Barras || '—')));
            fila.appendChild(crearCelda(String(producto.Nombre || '')));
            fila.appendChild(crearCelda(formatoMoneda.format(Number(producto.Precio_Venta))));
            fila.appendChild(crearCelda(String(Number(producto.Existencias) || 0)));

            const celdaCantidad = document.createElement('td');
            const campoCantidad = document.createElement('input');
            campoCantidad.type = 'text';
            campoCantidad.className = 'form-control';
            campoCantidad.value = '1';
            campoCantidad.inputMode = 'numeric';
            campoCantidad.setAttribute('data-solo-enteros', '');
            campoCantidad.setAttribute('data-cantidad-producto', idProducto);
            campoCantidad.setAttribute('aria-label', 'Cantidad de ' + producto.Nombre);
            window.configurarSoloNumeros(campoCantidad);
            celdaCantidad.appendChild(campoCantidad);
            fila.appendChild(celdaCantidad);

            const celdaAccion = document.createElement('td');
            const botonAgregar = document.createElement('button');
            botonAgregar.type = 'button';
            botonAgregar.className = 'btn btn-primary btn-sm';
            botonAgregar.textContent = 'Agregar';
            botonAgregar.setAttribute('data-agregar-producto', idProducto);
            celdaAccion.appendChild(botonAgregar);
            fila.appendChild(celdaAccion);
            resultados.appendChild(fila);
        });
    }

    formularioBusqueda.addEventListener('submit', async function (evento) {
        evento.preventDefault();
        mostrarErrorBusqueda('');
        const busqueda = campoBusqueda.value.trim();
        if (busqueda === '') {
            mostrarErrorBusqueda('Escribe un nombre o código');
            campoBusqueda.focus();
            return;
        }
        if (Array.from(busqueda).length > 100) {
            mostrarErrorBusqueda('La búsqueda no puede superar 100 caracteres.');
            campoBusqueda.focus();
            return;
        }

        const url = new URL(formularioBusqueda.dataset.buscarUrl, window.location.origin);
        url.searchParams.set('q', busqueda);
        mostrarResultadosVacios('Buscando productos...');

        try {
            const respuesta = await fetch(url.toString(), {
                headers: { Accept: 'application/json' }
            });
            const datos = await respuesta.json();
            if (!respuesta.ok) {
                throw new Error(datos.error || 'No se pudieron buscar productos.');
            }
            if (!Array.isArray(datos.productos)) {
                throw new Error('La respuesta de productos no tiene un formato válido.');
            }
            mostrarResultados(datos.productos);
        } catch (error) {
            mostrarResultadosVacios('No se pudieron cargar los productos');
            mostrarErrorBusqueda(error.message);
            console.error('Error al buscar productos para la venta:', error);
        }
    });

    resultados.addEventListener('click', function (evento) {
        const boton = evento.target.closest('[data-agregar-producto]');
        if (!boton) {
            return;
        }

        const idProducto = boton.dataset.agregarProducto;
        const producto = productosEncontrados.get(idProducto);
        const campoCantidad = resultados.querySelector(
            '[data-cantidad-producto="' + idProducto + '"]'
        );
        const textoCantidad = campoCantidad ? campoCantidad.value.trim() : '';
        if (!producto || !/^[0-9]+$/.test(textoCantidad)
            || !Number.isSafeInteger(Number(textoCantidad)) || Number(textoCantidad) <= 0) {
            mostrarAlertaVenta('La cantidad debe ser mayor a 0');
            if (campoCantidad) {
                campoCantidad.focus();
            }
            return;
        }

        const renglonExistente = ticket.find(function (renglon) {
            return String(renglon.idProducto) === idProducto;
        });
        const cantidadEnTicket = renglonExistente ? renglonExistente.cantidad : 0;
        const existencias = Number(producto.Existencias) || 0;
        if (!window.Reglas.puedeAgregar(Number(textoCantidad), cantidadEnTicket, existencias)) {
            mostrarAlertaVenta(
                '⚠ Existencia insuficiente: solo hay ' + existencias
                    + ' piezas. El producto no se agregó.'
            );
            campoCantidad.focus();
            return;
        }

        if (renglonExistente) {
            renglonExistente.cantidad += Number(textoCantidad);
        } else {
            ticket.push({
                idProducto: Number(producto.Id_Producto),
                codigo: String(producto.Codigo_Barras || ''),
                nombre: String(producto.Nombre || ''),
                precio: Number(producto.Precio_Venta),
                existencias: existencias,
                cantidad: Number(textoCantidad)
            });
        }
        mostrarErrorBusqueda('');
        mostrarAlertaVenta('');
        mostrarTicket();
    });

    renglonesTicket.addEventListener('click', function (evento) {
        const boton = evento.target.closest('[data-quitar-ticket]');
        if (!boton) {
            return;
        }

        const idProducto = Number(boton.dataset.quitarTicket);
        const indice = ticket.findIndex(function (renglon) {
            return renglon.idProducto === idProducto;
        });
        if (indice !== -1) {
            ticket.splice(indice, 1);
            mostrarAlertaVenta('');
            mostrarTicket();
        }
    });

    renglonesTicket.addEventListener('change', function (evento) {
        const campo = evento.target.closest('[data-cantidad-ticket]');
        if (!campo) {
            return;
        }

        const idProducto = Number(campo.dataset.cantidadTicket);
        const renglon = ticket.find(function (producto) {
            return producto.idProducto === idProducto;
        });
        const cantidadTexto = campo.value.trim();
        if (!renglon || !/^[0-9]+$/.test(cantidadTexto)
            || !Number.isSafeInteger(Number(cantidadTexto)) || Number(cantidadTexto) <= 0) {
            mostrarAlertaVenta('La cantidad debe ser mayor a 0');
            mostrarTicket();
            return;
        }
        if (!window.Reglas.puedeAgregar(Number(cantidadTexto), 0, renglon.existencias)) {
            mostrarAlertaVenta(
                '⚠ Existencia insuficiente: solo hay ' + renglon.existencias
                    + ' piezas. El producto no se agregó.'
            );
            mostrarTicket();
            return;
        }
        renglon.cantidad = Number(cantidadTexto);
        mostrarAlertaVenta('');
        mostrarTicket();
    });

    campoPago.addEventListener('input', actualizarPago);
    selectorMetodoPago.addEventListener('change', actualizarPago);

    botonCancelar.addEventListener('click', function () {
        modalCancelar.show();
    });

    botonConfirmarCancelar.addEventListener('click', function () {
        ticket.splice(0, ticket.length);
        campoPago.value = '0.00';
        selectorMetodoPago.selectedIndex = 0;
        mostrarErrorBusqueda('');
        mostrarAlertaVenta('');
        mostrarResultadosVacios('Busca un producto para ver resultados');
        productosEncontrados.clear();
        modalCancelar.hide();
        actualizarPago();
        mostrarTicket();
    });

    mostrarTicket();
});
