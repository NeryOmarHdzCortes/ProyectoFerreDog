/**
 * Completa los datos de la ventana de confirmación de cancelación.
 */
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalCancelarPedido');

    if (modal) {
        modal.addEventListener('show.bs.modal', function (evento) {
            const boton = evento.relatedTarget;
            if (!boton) {
                return;
            }

            const idPedido = boton.dataset.idPedido;
            document.getElementById('tituloModalCancelarPedido').textContent =
                '¿Cancelar el pedido #' + idPedido + '?';
            document.getElementById('proveedorPedidoCancelar').textContent = boton.dataset.proveedor;
            document.getElementById('totalPedidoCancelar').textContent = boton.dataset.total;
            document.getElementById('idPedidoCancelar').value = idPedido;
        });
    }

    const formulario = document.getElementById('formularioPedido');
    if (!formulario) {
        return;
    }

    const selectorProveedor = document.getElementById('proveedorPedido');
    const tablaProductos = document.getElementById('productosPedido');
    const errorProductos = document.getElementById('productosPedido-error');
    const indicadorCarga = document.getElementById('productosPedido-cargando');
    const totalPedido = document.getElementById('totalPedido');
    const formatoMoneda = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    });
    let productosIniciales = {};
    let validacionIniciada = false;
    let numeroSolicitud = 0;

    try {
        productosIniciales = JSON.parse(formulario.dataset.productosAnteriores || '{}');
    } catch (error) {
        console.error('No se pudieron recuperar los productos capturados del pedido.', error);
    }

    function crearCelda(texto) {
        const celda = document.createElement('td');
        celda.textContent = texto;
        return celda;
    }

    function marcarError(campo, mensaje) {
        const pista = campo.parentElement.querySelector('.invalid-feedback');
        campo.classList.toggle('is-invalid', mensaje !== '');
        campo.setAttribute('aria-invalid', mensaje === '' ? 'false' : 'true');
        if (pista) {
            pista.textContent = mensaje;
            pista.hidden = mensaje === '';
            pista.classList.toggle('d-block', mensaje !== '');
        }
    }

    function crearCampo(nombre, tipo, valor, min, step, etiqueta) {
        const grupo = document.createElement('div');
        const campo = document.createElement('input');
        const pista = document.createElement('div');

        campo.type = tipo;
        campo.name = nombre;
        campo.value = valor;
        campo.className = 'form-control';
        campo.inputMode = nombre.endsWith('[costo]') ? 'decimal' : 'numeric';
        campo.setAttribute('aria-label', etiqueta);
        if (min !== null) {
            campo.min = min;
        }
        if (step !== null) {
            campo.step = step;
        }
        pista.className = 'invalid-feedback';
        pista.hidden = true;
        grupo.appendChild(campo);
        grupo.appendChild(pista);

        return { grupo: grupo, campo: campo, pista: pista };
    }

    function crearFila(producto, datosAnteriores) {
        const fila = document.createElement('tr');
        const celdaProducto = document.createElement('td');
        celdaProducto.textContent = producto.Nombre || '';
        fila.appendChild(celdaProducto);
        fila.appendChild(crearCelda(producto.Marca || '—'));

        const celdaStock = document.createElement('td');
        const existencias = Number(producto.Existencias) || 0;
        const stockMinimo = Number(producto.Stock_Minimo) || 0;
        if (existencias <= stockMinimo) {
            const insignia = document.createElement('span');
            insignia.className = 'badge badge-rojo';
            insignia.textContent = existencias + ' · bajo';
            celdaStock.appendChild(insignia);
        } else {
            celdaStock.textContent = String(existencias);
        }
        fila.appendChild(celdaStock);

        const idProducto = Number(producto.Id_Producto);
        const costoInicial = datosAnteriores && datosAnteriores.costo !== undefined
            ? String(datosAnteriores.costo)
            : (producto.Precio_Compra === null ? '' : String(producto.Precio_Compra));
        const cantidadInicial = datosAnteriores && datosAnteriores.cantidad !== undefined
            ? String(datosAnteriores.cantidad)
            : '';
        const costo = crearCampo(
            'productos[' + idProducto + '][costo]',
            'text',
            costoInicial,
            '0.01',
            '0.01',
            'Costo de ' + producto.Nombre
        );
        const cantidad = crearCampo(
            'productos[' + idProducto + '][cantidad]',
            'text',
            cantidadInicial,
            '1',
            '1',
            'Cantidad de ' + producto.Nombre
        );
        const celdaCosto = document.createElement('td');
        const celdaCantidad = document.createElement('td');
        const celdaSubtotal = document.createElement('td');
        const salidaSubtotal = document.createElement('span');
        salidaSubtotal.dataset.subtotal = 'true';
        salidaSubtotal.textContent = '—';
        celdaCosto.appendChild(costo.grupo);
        celdaCantidad.appendChild(cantidad.grupo);
        celdaSubtotal.appendChild(salidaSubtotal);
        fila.appendChild(celdaCosto);
        fila.appendChild(celdaCantidad);
        fila.appendChild(celdaSubtotal);

        return fila;
    }

    function mostrarFilaVacia(mensaje) {
        tablaProductos.replaceChildren();
        const fila = document.createElement('tr');
        const celda = document.createElement('td');
        celda.colSpan = 6;
        celda.className = 'text-center';
        celda.textContent = mensaje;
        fila.appendChild(celda);
        tablaProductos.appendChild(fila);
    }

    function validarFormulario() {
        let valido = true;
        let cantidadProductos = 0;
        let total = 0;

        const proveedorValido = selectorProveedor.value !== '';
        marcarError(selectorProveedor, proveedorValido ? '' : 'Elige un proveedor');
        if (!proveedorValido) {
            valido = false;
        }

        tablaProductos.querySelectorAll('tr[data-producto-id]').forEach(function (fila) {
            const campoCantidad = fila.querySelector('[name$="[cantidad]"]');
            const campoCosto = fila.querySelector('[name$="[costo]"]');
            const salidaSubtotal = fila.querySelector('[data-subtotal]');
            const cantidadTexto = campoCantidad.value.trim();
            const costoTexto = campoCosto.value.trim();

            marcarError(campoCantidad, '');
            marcarError(campoCosto, '');
            if (cantidadTexto === '') {
                salidaSubtotal.textContent = '—';
                return;
            }

            cantidadProductos++;
            const cantidadValida = /^[0-9]+$/.test(cantidadTexto) && Number(cantidadTexto) > 0;
            if (!cantidadValida) {
                marcarError(campoCantidad, 'La cantidad debe ser mayor a 0');
                valido = false;
            }

            const costoValido = /^[0-9]+(?:\.[0-9]{1,2})?$/.test(costoTexto)
                && Number(costoTexto) > 0;
            if (!costoValido) {
                marcarError(campoCosto, 'El costo debe ser mayor a 0');
                valido = false;
            }

            if (cantidadValida && costoValido) {
                const subtotal = Number(cantidadTexto) * Number(costoTexto);
                salidaSubtotal.textContent = formatoMoneda.format(subtotal);
                total += subtotal;
            } else {
                salidaSubtotal.textContent = '—';
            }
        });

        const hayProductos = cantidadProductos > 0;
        errorProductos.textContent = hayProductos ? '' : 'Agrega al menos un producto';
        errorProductos.hidden = hayProductos;
        if (!hayProductos) {
            valido = false;
        }

        totalPedido.textContent = formatoMoneda.format(total);
        return valido;
    }

    async function cargarProductos(restaurarCaptura) {
        const idProveedor = selectorProveedor.value;
        const solicitudActual = ++numeroSolicitud;
        errorProductos.hidden = true;
        errorProductos.textContent = '';
        if (validacionIniciada) {
            marcarError(selectorProveedor, idProveedor ? '' : 'Elige un proveedor');
        } else {
            marcarError(selectorProveedor, '');
        }

        if (!idProveedor) {
            indicadorCarga.hidden = true;
            mostrarFilaVacia('Elige un proveedor para ver sus productos');
            totalPedido.textContent = formatoMoneda.format(0);
            return;
        }

        indicadorCarga.hidden = false;
        mostrarFilaVacia('Cargando productos...');
        const url = new URL(formulario.dataset.productosUrl, window.location.origin);
        url.searchParams.set('id', idProveedor);

        try {
            const respuesta = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json' }
            });
            const datos = await respuesta.json();
            if (!respuesta.ok) {
                throw new Error(datos.error || 'No se pudieron cargar los productos del proveedor.');
            }
            if (!Array.isArray(datos)) {
                throw new Error('La respuesta de productos no tiene un formato válido.');
            }
            if (solicitudActual !== numeroSolicitud) {
                return;
            }

            indicadorCarga.hidden = true;
            tablaProductos.replaceChildren();
            if (datos.length === 0) {
                mostrarFilaVacia('Este proveedor no tiene productos activos');
                return;
            }

            datos.forEach(function (producto) {
                const id = String(producto.Id_Producto);
                const valores = restaurarCaptura ? productosIniciales[id] : null;
                const fila = crearFila(producto, valores);
                fila.dataset.productoId = id;
                tablaProductos.appendChild(fila);
            });
            if (validacionIniciada) {
                validarFormulario();
            } else {
                actualizarTotales();
            }
        } catch (error) {
            if (solicitudActual !== numeroSolicitud) {
                return;
            }
            indicadorCarga.hidden = true;
            errorProductos.textContent = error.message;
            errorProductos.hidden = false;
            mostrarFilaVacia('No se pudieron cargar los productos');
            console.error('Error al cargar productos del proveedor:', error);
        }
    }

    function actualizarTotales() {
        let total = 0;
        tablaProductos.querySelectorAll('tr[data-producto-id]').forEach(function (fila) {
            const cantidad = Number(fila.querySelector('[name$="[cantidad]"]').value);
            const costo = Number(fila.querySelector('[name$="[costo]"]').value);
            const salidaSubtotal = fila.querySelector('[data-subtotal]');
            if (cantidad > 0 && costo > 0) {
                const subtotal = cantidad * costo;
                salidaSubtotal.textContent = formatoMoneda.format(subtotal);
                total += subtotal;
            } else {
                salidaSubtotal.textContent = '—';
            }
        });
        totalPedido.textContent = formatoMoneda.format(total);
    }

    selectorProveedor.addEventListener('change', function () {
        cargarProductos(false);
    });

    formulario.addEventListener('input', function (evento) {
        if (evento.target.matches('[name$="[cantidad]"], [name$="[costo]"]')) {
            if (validacionIniciada) {
                validarFormulario();
            } else {
                actualizarTotales();
            }
        }
    });

    formulario.addEventListener('submit', function (evento) {
        validacionIniciada = true;
        if (!validarFormulario()) {
            evento.preventDefault();
            const primerCampoInvalido = formulario.querySelector('.is-invalid');
            if (primerCampoInvalido) {
                primerCampoInvalido.focus();
            }
        }
    });

    cargarProductos(selectorProveedor.value !== ''
        && selectorProveedor.value === formulario.dataset.proveedorAnterior);
});
