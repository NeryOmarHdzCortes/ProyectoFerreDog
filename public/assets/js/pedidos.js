/**
 * Completa los datos de la ventana de confirmación de cancelación.
 */
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalCancelarPedido');

    if (!modal) {
        return;
    }

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
});
