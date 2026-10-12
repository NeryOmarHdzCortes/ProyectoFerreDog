/**
 * JavaScript para recuperar contraseña
 * - Mostrar/ocultar contraseñas
 * - Validación en tiempo real de requisitos
 */

document.addEventListener('DOMContentLoaded', function () {
    // Mostrar/ocultar contraseña nueva
    const toggleNueva = document.getElementById('toggleNueva');
    const inputNueva = document.getElementById('nueva_contrasena');

    if (toggleNueva && inputNueva) {
        toggleNueva.addEventListener('click', function () {
            const tipo = inputNueva.getAttribute('type');
            if (tipo === 'password') {
                inputNueva.setAttribute('type', 'text');
                toggleNueva.textContent = '🙈';
            } else {
                inputNueva.setAttribute('type', 'password');
                toggleNueva.textContent = '👁️';
            }
        });
    }

    // Mostrar/ocultar contraseña confirmar
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
    if (inputNueva) {
        inputNueva.addEventListener('input', function () {
            const contrasena = this.value;

            // Requisito: longitud mínima 8
            const reqLongitud = document.getElementById('req-longitud');
            if (contrasena.length >= 8) {
                reqLongitud.className = 'requisito-cumplido';
                reqLongitud.innerHTML = '✓ Mínimo 8 caracteres';
            } else {
                reqLongitud.className = 'requisito-no-cumplido';
                reqLongitud.innerHTML = '✗ Mínimo 8 caracteres';
            }

            // Requisito: al menos un número
            const reqNumero = document.getElementById('req-numero');
            if (/[0-9]/.test(contrasena)) {
                reqNumero.className = 'requisito-cumplido';
                reqNumero.innerHTML = '✓ Un número';
            } else {
                reqNumero.className = 'requisito-no-cumplido';
                reqNumero.innerHTML = '✗ Un número';
            }

            // Requisito: mayúsculas y minúsculas
            const reqMayuscula = document.getElementById('req-mayuscula');
            if (/[A-Z]/.test(contrasena) && /[a-z]/.test(contrasena)) {
                reqMayuscula.className = 'requisito-cumplido';
                reqMayuscula.innerHTML = '✓ Mayúsculas y minúsculas';
            } else {
                reqMayuscula.className = 'requisito-no-cumplido';
                reqMayuscula.innerHTML = '✗ Mayúsculas y minúsculas';
            }

            // Requisito: al menos un símbolo
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

    // Validar código: solo números
    const inputCodigo = document.getElementById('codigo');
    if (inputCodigo) {
        inputCodigo.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
