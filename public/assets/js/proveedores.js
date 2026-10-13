/**
 * Interacciones de búsqueda y confirmación del módulo de proveedores.
 */
document.addEventListener('DOMContentLoaded', function () {
    const campoCorreo = document.getElementById('correoProveedor');
    const errorCorreo = document.getElementById('correoProveedor-error');

    if (campoCorreo && errorCorreo && campoCorreo.form) {
        const correoValido = function (correo) {
            const normalizado = correo.trim().toLowerCase();
            const formatoValido = /^[a-z0-9._%+-]+@[a-z0-9-]+(\.[a-z0-9-]+)*\.[a-z]{2,}$/.test(normalizado);

            if (!formatoValido) {
                return false;
            }

            const dominio = normalizado.split('@')[1];
            const extensionesPermitidas = [
                '.com', '.net', '.org', '.edu', '.info', '.mx',
                '.com.mx', '.edu.mx', '.gob.mx', '.org.mx', '.net.mx'
            ];

            if (!extensionesPermitidas.some(function (extension) {
                return dominio.endsWith(extension);
            })) {
                return false;
            }

            if (/^(gmail|hotmail|outlook|yahoo|live|icloud)/.test(dominio)) {
                return [
                    'gmail.com', 'hotmail.com', 'outlook.com', 'outlook.es',
                    'yahoo.com', 'yahoo.com.mx', 'live.com', 'live.com.mx', 'icloud.com'
                ].includes(dominio);
            }

            return true;
        };

        const mostrarErrorCorreo = function () {
            const valido = correoValido(campoCorreo.value);
            errorCorreo.hidden = valido;
            errorCorreo.classList.toggle('d-block', !valido);
            campoCorreo.classList.toggle('is-invalid', !valido);
            campoCorreo.setAttribute('aria-invalid', valido ? 'false' : 'true');
            return valido;
        };

        campoCorreo.form.addEventListener('submit', function (evento) {
            if (!mostrarErrorCorreo()) {
                evento.preventDefault();
                evento.stopImmediatePropagation();
                campoCorreo.focus();
            }
        });
        campoCorreo.addEventListener('invalid', function (evento) {
            if (!correoValido(campoCorreo.value)) {
                evento.preventDefault();
                errorCorreo.hidden = false;
                errorCorreo.classList.add('d-block');
                campoCorreo.classList.add('is-invalid');
                campoCorreo.setAttribute('aria-invalid', 'true');
            }
        });
        campoCorreo.addEventListener('input', mostrarErrorCorreo);
    }

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
