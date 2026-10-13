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
    const precio = parseFloat(document.getElementById('inputPrecioVenta').value);

    if (!precio || precio <= 0) {
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
