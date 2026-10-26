/**
 * JavaScript para gestión de usuarios
 */

// Mostrar/ocultar contraseña
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-eliminar-usuario').forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            if (!window.confirm(formulario.dataset.confirmacion)) {
                evento.preventDefault();
            }
        });
    });

    const campoCorreo = document.getElementById('correo');
    const errorCorreo = document.getElementById('correo-error');

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

    const toggleContrasena = document.getElementById('toggleContrasena');
    const inputContrasena = document.getElementById('contrasena');

    if (toggleContrasena && inputContrasena) {
        toggleContrasena.addEventListener('click', function () {
            const tipo = inputContrasena.getAttribute('type');
            if (tipo === 'password') {
                inputContrasena.setAttribute('type', 'text');
                toggleContrasena.textContent = '🙈';
            } else {
                inputContrasena.setAttribute('type', 'password');
                toggleContrasena.textContent = '👁️';
            }
        });
    }

    const toggleConfirmar = document.getElementById('toggleConfirmar');
    const inputConfirmar = document.getElementById('confirmar_contrasena');

    if (toggleConfirmar && inputConfirmar) {
        toggleConfirmar.addEventListener('click', function () {
            const tipo = inputConfirmar.getAttribute('type');
            if (tipo === 'password') {
                inputConfirmar.setAttribute('type', 'text');
                toggleConfirmar.textContent = '🙈';
            } else {
                inputConfirmar.setAttribute('type', 'password');
                toggleConfirmar.textContent = '👁️';
            }
        });
    }

    // Validación en tiempo real de requisitos
    if (inputContrasena) {
        inputContrasena.addEventListener('input', function () {
            const contrasena = this.value;

            const reqLongitud = document.getElementById('req-longitud');
            if (contrasena.length >= 8) {
                reqLongitud.className = 'requisito-cumplido';
                reqLongitud.innerHTML = '✓ Mínimo 8 caracteres';
            } else {
                reqLongitud.className = 'requisito-no-cumplido';
                reqLongitud.innerHTML = '✗ Mínimo 8 caracteres';
            }

            const reqNumero = document.getElementById('req-numero');
            if (/[0-9]/.test(contrasena)) {
                reqNumero.className = 'requisito-cumplido';
                reqNumero.innerHTML = '✓ Un número';
            } else {
                reqNumero.className = 'requisito-no-cumplido';
                reqNumero.innerHTML = '✗ Un número';
            }

            const reqMayuscula = document.getElementById('req-mayuscula');
            if (/[A-Z]/.test(contrasena) && /[a-z]/.test(contrasena)) {
                reqMayuscula.className = 'requisito-cumplido';
                reqMayuscula.innerHTML = '✓ Mayúsculas y minúsculas';
            } else {
                reqMayuscula.className = 'requisito-no-cumplido';
                reqMayuscula.innerHTML = '✗ Mayúsculas y minúsculas';
            }

            const reqSimbolo = document.getElementById('req-simbolo');
            if (/[^A-Za-z0-9]/.test(contrasena)) {
                reqSimbolo.className = 'requisito-cumplido';
                reqSimbolo.innerHTML = '✓ Un símbolo (!@#$%)';
            } else {
                reqSimbolo.className = 'requisito-no-cumplido';
                reqSimbolo.innerHTML = '✗ Un símbolo (!@#$%)';
            }
        });
    }
});
