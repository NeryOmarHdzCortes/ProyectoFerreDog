/**
 * JavaScript para el módulo de productos (HU-5)
 */

// Variables globales para los modales
let idProductoActual = 0;

/**
 * Abrir modal para poner precio (solo Administrador)
 */
function abrirModalPrecio(idProducto, nombreProducto, precioActual) {
    idProductoActual = idProducto;

    document.getElementById('modalPrecioNombre').textContent = nombreProducto;
    document.getElementById('inputPrecioVenta').value = precioActual > 0 ? precioActual : '';

    document.getElementById('modalPrecio').style.display = 'flex';
}

/**
 * Cerrar modal de precio
 */
function cerrarModalPrecio() {
    document.getElementById('modalPrecio').style.display = 'none';
    idProductoActual = 0;
}

/**
 * Guardar precio (solo Administrador)
 */
function guardarPrecio() {
    const precio = document.getElementById('inputPrecioVenta').value.trim();
    const precioNumerico = Number(precio);

    if (precio === '' || !Number.isFinite(precioNumerico) || precioNumerico <= 0) {
        alert('Por favor ingresa un precio válido mayor a 0.');
        return;
    }

    // Crear formulario y enviarlo
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/ProyectoFerredog/public/index.php?c=producto&a=ponerPrecio';

    const inputId = document.createElement('input');
    inputId.type = 'hidden';
    inputId.name = 'id_producto';
    inputId.value = idProductoActual;

    const inputPrecio = document.createElement('input');
    inputPrecio.type = 'hidden';
    inputPrecio.name = 'precio';
    inputPrecio.value = precio;

    form.appendChild(inputId);
    form.appendChild(inputPrecio);
    document.body.appendChild(form);
    form.submit();
}

/**
 * Confirmar desactivación de producto (solo Administrador)
 */
function confirmarDesactivar(idProducto, nombreProducto) {
    idProductoActual = idProducto;

    document.getElementById('modalDesactivarMensaje').textContent =
        `¿Desactivar "${nombreProducto}"? Ya no aparecerá en la lista de productos activos.`;

    document.getElementById('inputIdProducto').value = idProducto;
    document.getElementById('modalDesactivar').style.display = 'flex';
}

/**
 * Cerrar modal de desactivar
 */
function cerrarModalDesactivar() {
    document.getElementById('modalDesactivar').style.display = 'none';
    idProductoActual = 0;
}

// Cerrar modales al hacer clic fuera de ellos
window.onclick = function (event) {
    const modalPrecio = document.getElementById('modalPrecio');
    const modalDesactivar = document.getElementById('modalDesactivar');

    if (event.target === modalPrecio) {
        cerrarModalPrecio();
    }

    if (event.target === modalDesactivar) {
        cerrarModalDesactivar();
    }
};

const formularioProducto = document.querySelector('.formulario-producto');
const campoStockMinimo = document.getElementById('stock_minimo');
const mensajeStockMinimo = document.getElementById('stock_minimo_error');
const campoPrecioVenta = document.getElementById('precio_venta');
const campoPrecioCompra = document.getElementById('precio_compra');
const mensajePrecioCompra = document.getElementById('precio_compra_error');
const avisoPrecioMenorCosto = document.getElementById('avisoPrecioMenorCosto');

function actualizarAvisoPrecioMenorCosto() {
    if (!campoPrecioVenta || !campoPrecioCompra || !avisoPrecioMenorCosto) {
        return;
    }

    const precioVenta = campoPrecioVenta.value.trim();
    const precioCompra = campoPrecioCompra.value.trim();
    const mostrarAviso = precioVenta !== ''
        && precioCompra !== ''
        && Number.isFinite(Number(precioVenta))
        && Number.isFinite(Number(precioCompra))
        && Number(precioVenta) < Number(precioCompra);
    avisoPrecioMenorCosto.hidden = !mostrarAviso;
}

if (campoPrecioCompra && mensajePrecioCompra) {
    const validarPrecioCompra = () => {
        const valor = campoPrecioCompra.value.trim();
        let mensaje = '';
        if (valor !== '' && Number(valor) < 0) {
            mensaje = 'El precio de compra no puede ser negativo';
        } else if (valor !== '' && !/^\d+(?:\.\d{1,2})?$/.test(valor)) {
            mensaje = 'El precio de compra debe ser un número válido con máximo dos decimales.';
        }

        mensajePrecioCompra.textContent = mensaje;
        mensajePrecioCompra.hidden = mensaje === '';
        campoPrecioCompra.setAttribute('aria-invalid', mensaje === '' ? 'false' : 'true');
        actualizarAvisoPrecioMenorCosto();

        return mensaje === '';
    };

    campoPrecioCompra.addEventListener('input', validarPrecioCompra);
    campoPrecioCompra.addEventListener('invalid', function (evento) {
        evento.preventDefault();
        validarPrecioCompra();
    });

    if (formularioProducto) {
        formularioProducto.addEventListener('submit', function (evento) {
            if (!validarPrecioCompra()) {
                evento.preventDefault();
                campoPrecioCompra.focus();
            }
        });
    }
}

if (campoPrecioVenta) {
    campoPrecioVenta.addEventListener('input', actualizarAvisoPrecioMenorCosto);
}
actualizarAvisoPrecioMenorCosto();

if (formularioProducto && campoStockMinimo && mensajeStockMinimo) {
    const textoErrorStockMinimo = 'El stock mínimo debe ser un número entero mayor a 0.';

    const validarStockMinimo = () => {
        const valor = campoStockMinimo.value.trim();
        const invalido = valor === '' || !Number.isInteger(Number(valor)) || Number(valor) < 1;

        mensajeStockMinimo.textContent = invalido ? textoErrorStockMinimo : '';
        mensajeStockMinimo.hidden = !invalido;
        campoStockMinimo.setAttribute('aria-invalid', invalido ? 'true' : 'false');

        return !invalido;
    };

    campoStockMinimo.addEventListener('invalid', (event) => {
        event.preventDefault();
        validarStockMinimo();
    });

    campoStockMinimo.addEventListener('input', validarStockMinimo);

    formularioProducto.addEventListener('submit', (event) => {
        if (!validarStockMinimo()) {
            event.preventDefault();
            campoStockMinimo.focus();
        }
    });
}

document.querySelectorAll('form[data-confirmar-envio]').forEach(function (formulario) {
    formulario.addEventListener('submit', function (evento) {
        if (!window.confirm(formulario.dataset.mensajeConfirmacion)) {
            evento.preventDefault();
        }
    });
});
