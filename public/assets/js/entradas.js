/**
 * Gestiona en el navegador los reportes provisionales de faltantes o daños.
 */
document.addEventListener('DOMContentLoaded', function () {
    const modalElemento = document.getElementById('modalReporteIncidencia');
    if (!modalElemento) {
        return;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalElemento);
    const formularioReporte = document.getElementById('formularioReporteIncidencia');
    const detalleReporte = document.getElementById('detalleReporteIncidencia');
    const tipoIncidencia = document.getElementById('tipoIncidencia');
    const cantidadBuenEstado = document.getElementById('cantidadBuenEstado');
    const motivoIncidencia = document.getElementById('motivoIncidencia');
    const avisoInventario = document.getElementById('avisoCantidadInventario');
    const botonQuitarReporte = document.getElementById('quitarReporteIncidencia');
    const contenedorReportes = document.getElementById('reportesEntradaOcultos');
    const formularioEntrada = document.getElementById('formularioEntradaPedido');
    const idPedidoEntrada = formularioEntrada.querySelector('[name="id_compra"]').value;
    const contadorProductosRevisados = document.getElementById('contadorProductosRevisados');
    const mensajeProductosPendientes = document.getElementById('mensajeProductosPendientes');
    const botonRegistrarEntrada = document.getElementById('botonRegistrarEntrada');
    const filasProductos = Array.from(formularioEntrada.querySelectorAll('tbody tr'));
    let filaActiva = null;
    let mostrarPendientes = false;

    function pintarRenglon(fila) {
        const confirmacion = fila.querySelector('[data-confirmacion-producto]');
        const botonReporte = fila.querySelector('[data-abrir-reporte]');
        const estado = fila.dataset.estado;

        confirmacion.checked = estado === 'confirmado';
        confirmacion.disabled = estado === 'reportado' || !fila.dataset.idDetalle;

        if (botonReporte) {
            const tieneReporte = estado === 'reportado';
            botonReporte.textContent = tieneReporte ? 'Editar reporte' : 'Reportar';
            botonReporte.disabled = estado === 'confirmado';
            botonReporte.classList.toggle('btn-secondary', estado === 'confirmado');
            botonReporte.classList.toggle('btn-cancelar-pedido', estado !== 'confirmado' && !tieneReporte);
            botonReporte.classList.toggle('btn-outline-secondary', tieneReporte);
        }

        fila.classList.remove('table-primary', 'table-danger', 'table-warning');
        if (estado === 'confirmado') {
            fila.classList.add('table-primary');
        } else if (estado === 'reportado') {
            fila.classList.add('table-danger');
        } else if (mostrarPendientes) {
            fila.classList.add('table-warning');
        }
    }

    function actualizarResumen() {
        let productosRevisados = 0;

        filasProductos.forEach(function (fila) {
            if (fila.dataset.estado !== 'pendiente') {
                productosRevisados++;
            }
        });

        const todosRevisados = filasProductos.length > 0
            && productosRevisados === filasProductos.length;
        contadorProductosRevisados.textContent =
            productosRevisados + ' de ' + filasProductos.length + ' productos revisados'
            + (todosRevisados ? ' ✓' : '');
        contadorProductosRevisados.classList.toggle('text-success', todosRevisados);
        contadorProductosRevisados.classList.toggle('text-secondary', !todosRevisados);
        contadorProductosRevisados.classList.toggle('fw-semibold', todosRevisados);
        const hayPendientes = filasProductos.some(function (fila) {
            return fila.dataset.estado === 'pendiente';
        });
        botonRegistrarEntrada.disabled = hayPendientes;
        botonRegistrarEntrada.classList.toggle('btn-success', todosRevisados);
        botonRegistrarEntrada.classList.toggle('btn-secondary', hayPendientes);
        mensajeProductosPendientes.hidden = !mostrarPendientes || !hayPendientes;
    }

    filasProductos.forEach(function (fila) {
        fila.dataset.estado = fila.dataset.idDetalle ? 'pendiente' : 'confirmado';
        pintarRenglon(fila);

        const confirmacion = fila.querySelector('[data-confirmacion-producto]');
        confirmacion.addEventListener('change', function () {
            if (fila.dataset.estado === 'reportado') {
                pintarRenglon(fila);
                actualizarResumen();
                return;
            }
            fila.dataset.estado = confirmacion.checked ? 'confirmado' : 'pendiente';
            pintarRenglon(fila);
            actualizarResumen();
        });

        const botonReporte = fila.querySelector('[data-abrir-reporte]');
        if (botonReporte) {
            botonReporte.addEventListener('click', function (evento) {
                if (fila.dataset.estado === 'confirmado') {
                    evento.preventDefault();
                    evento.stopImmediatePropagation();
                    pintarRenglon(fila);
                    actualizarResumen();
                    return;
                }
                pintarRenglon(fila);
                actualizarResumen();
            });
        }
    });

    actualizarResumen();

    formularioEntrada.addEventListener('submit', function (evento) {
        const hayPendientes = filasProductos.some(function (fila) {
            return fila.dataset.estado === 'pendiente';
        });
        if (hayPendientes) {
            evento.preventDefault();
            mostrarPendientes = true;
            filasProductos.forEach(pintarRenglon);
            actualizarResumen();
            return;
        }

        const confirmar = window.confirm(
            '¿Registrar la entrada del pedido #' + idPedidoEntrada
            + '? Las existencias se actualizarán con las cantidades recibidas.'
        );
        if (!confirmar) {
            evento.preventDefault();
        }
    });

    function establecerError(campo, mensaje) {
        const mensajeError = document.getElementById(campo.id + '-error');
        const invalido = mensaje !== '';
        campo.classList.toggle('is-invalid', invalido);
        campo.setAttribute('aria-invalid', invalido ? 'true' : 'false');
        if (mensajeError) {
            mensajeError.textContent = mensaje;
            mensajeError.hidden = !invalido;
            mensajeError.classList.toggle('d-block', invalido);
        }
    }

    function limpiarErrores() {
        establecerError(tipoIncidencia, '');
        establecerError(cantidadBuenEstado, '');
        establecerError(motivoIncidencia, '');
    }

    function actualizarAviso() {
        const cantidad = cantidadBuenEstado.value.trim();
        const cantidadNumerica = /^[0-9]+$/.test(cantidad) ? Number(cantidad) : 0;
        avisoInventario.textContent =
            'Se sumará ' + cantidadNumerica
            + ' pieza(s) al inventario; el motivo queda en el historial de movimientos.';
    }

    function validarReporte() {
        let valido = true;
        const tipoValido = tipoIncidencia.value === 'Faltante' || tipoIncidencia.value === 'Dañado';
        if (!tipoValido) {
            establecerError(tipoIncidencia, 'Selecciona el tipo de incidencia');
            valido = false;
        } else {
            establecerError(tipoIncidencia, '');
        }

        const textoCantidad = cantidadBuenEstado.value.trim();
        const cantidad = Number(textoCantidad);
        const cantidadPedida = Number(filaActiva.dataset.cantidadPedida);
        if (textoCantidad === '') {
            establecerError(cantidadBuenEstado, 'La cantidad es obligatoria');
            valido = false;
        } else {
            const cantidadValida = /^[0-9]+$/.test(textoCantidad)
                && Number.isInteger(cantidad)
                && cantidad >= 0
                && cantidad < cantidadPedida;
            if (!cantidadValida) {
                establecerError(
                    cantidadBuenEstado,
                    'La cantidad debe ser un entero entre 0 y ' + (cantidadPedida - 1)
                );
                valido = false;
            } else {
                establecerError(cantidadBuenEstado, '');
            }
        }

        const motivo = motivoIncidencia.value.trim();
        if (motivo === '' || motivo.length > 200) {
            establecerError(motivoIncidencia, 'Escribe un motivo de hasta 200 caracteres');
            valido = false;
        } else {
            establecerError(motivoIncidencia, '');
        }

        return valido;
    }

    function actualizarCamposOcultos(fila, reporte) {
        const idDetalle = fila.dataset.idDetalle;
        let grupo = contenedorReportes.querySelector('[data-reporte-id="' + idDetalle + '"]');
        if (!grupo) {
            grupo = document.createElement('div');
            grupo.dataset.reporteId = idDetalle;
            contenedorReportes.appendChild(grupo);
        }
        grupo.replaceChildren();

        [
            ['cantidad_recibida', reporte.cantidad],
            ['incidencia', reporte.tipo],
            ['motivo', reporte.motivo]
        ].forEach(function (dato) {
            const campo = document.createElement('input');
            campo.type = 'hidden';
            campo.name = 'reportes[' + idDetalle + '][' + dato[0] + ']';
            campo.value = dato[1];
            grupo.appendChild(campo);
        });
    }

    function guardarReporte() {
        const cantidadPedida = Number(filaActiva.dataset.cantidadPedida);
        const cantidadRecibida = Number(cantidadBuenEstado.value);
        const faltantes = cantidadPedida - cantidadRecibida;
        const tipo = tipoIncidencia.value;
        const motivo = motivoIncidencia.value.trim();
        const reporte = {
            cantidad: String(cantidadRecibida),
            tipo: tipo,
            motivo: motivo
        };

        filaActiva.dataset.reporteCantidad = reporte.cantidad;
        filaActiva.dataset.reporteTipo = reporte.tipo;
        filaActiva.dataset.reporteMotivo = reporte.motivo;
        filaActiva.dataset.estado = 'reportado';
        filaActiva.querySelector('[data-cantidad-recibida]').textContent = reporte.cantidad;

        const observacion = filaActiva.querySelector('[data-observacion]');
        const insignia = document.createElement('span');
        insignia.className = 'badge badge-rojo';
        insignia.textContent = tipo + ': ' + faltantes;
        observacion.replaceChildren(insignia);

        actualizarCamposOcultos(filaActiva, reporte);
        pintarRenglon(filaActiva);
        actualizarResumen();
        modal.hide();
    }

    function quitarReporte() {
        if (!filaActiva) {
            return;
        }

        const idDetalle = filaActiva.dataset.idDetalle;
        const grupo = contenedorReportes.querySelector('[data-reporte-id="' + idDetalle + '"]');
        if (grupo) {
            grupo.remove();
        }

        delete filaActiva.dataset.reporteCantidad;
        delete filaActiva.dataset.reporteTipo;
        delete filaActiva.dataset.reporteMotivo;
        filaActiva.dataset.estado = 'pendiente';
        filaActiva.querySelector('[data-cantidad-recibida]').textContent =
            filaActiva.dataset.cantidadPedida;
        filaActiva.querySelector('[data-observacion]').textContent = '—';

        pintarRenglon(filaActiva);
        actualizarResumen();
        modal.hide();
    }

    document.querySelectorAll('[data-abrir-reporte]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            filaActiva = boton.closest('tr');
            const cantidadPedida = Number(filaActiva.dataset.cantidadPedida);
            detalleReporte.textContent =
                filaActiva.dataset.producto + ' · ' + filaActiva.dataset.marca
                + ' · Cantidad pedida: ' + cantidadPedida;
            tipoIncidencia.value = filaActiva.dataset.reporteTipo || '';
            cantidadBuenEstado.value = filaActiva.dataset.reporteCantidad || '';
            cantidadBuenEstado.min = '0';
            cantidadBuenEstado.max = String(cantidadPedida - 1);
            motivoIncidencia.value = filaActiva.dataset.reporteMotivo || '';
            botonQuitarReporte.hidden = !filaActiva.dataset.reporteTipo;
            limpiarErrores();
            actualizarAviso();
            modal.show();
        });
    });

    cantidadBuenEstado.addEventListener('input', actualizarAviso);
    tipoIncidencia.addEventListener('change', function () {
        establecerError(tipoIncidencia, '');
    });
    motivoIncidencia.addEventListener('input', function () {
        if (motivoIncidencia.value.length <= 200 && motivoIncidencia.value.trim() !== '') {
            establecerError(motivoIncidencia, '');
        }
    });

    formularioReporte.addEventListener('submit', function (evento) {
        evento.preventDefault();
        if (!filaActiva || !validarReporte()) {
            return;
        }
        guardarReporte();
    });

    botonQuitarReporte.addEventListener('click', quitarReporte);

    modalElemento.addEventListener('hidden.bs.modal', function () {
        filaActiva = null;
        formularioReporte.reset();
        limpiarErrores();
    });
});
