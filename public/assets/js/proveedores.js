/**
 * Interacciones de búsqueda y confirmación del módulo de proveedores.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-enviar-al-cambiar]').forEach(function (campo) {
        campo.addEventListener('change', function () {
            campo.form.submit();
        });
    });

    document.querySelectorAll('form[data-confirmar-envio]').forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            const mensaje = formulario.dataset.mensajeConfirmacion;
            if (!window.confirm(mensaje)) {
                evento.preventDefault();
            }
        });
    });
});
