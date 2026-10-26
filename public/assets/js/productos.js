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

// Cerrar modales al hacer clic fuera de ellos
window.onclick = function (event) {
    const modalPrecio = document.getElementById('modalPrecio');

    if (event.target === modalPrecio) {
        cerrarModalPrecio();
    }
};

document.querySelectorAll('.form-eliminar-producto').forEach((formulario) => {
    formulario.addEventListener('submit', (event) => {
        if (!window.confirm(formulario.dataset.confirmacion)) {
            event.preventDefault();
        }
    });
});

const formularioProducto = document.querySelector('.formulario-producto');
const campoStockMinimo = document.getElementById('stock_minimo');
const mensajeStockMinimo = document.getElementById('stock_minimo_error');

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
