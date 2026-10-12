/**
 * JavaScript para gestión de usuarios
 */

// Cambiar estado de usuario (activar/desactivar)
function cambiarEstado(idUsuario, activar) {
    if (activar) {
        // Activar sin confirmación
        document.getElementById('inputIdUsuario').value = idUsuario;
        document.getElementById('inputNuevoEstado').value = 'Activo';
        document.getElementById('formCambiarEstado').submit();
    } else {
        // Desactivar con confirmación
        document.getElementById('inputIdUsuario').value = idUsuario;
        document.getElementById('inputNuevoEstado').value = 'Inactivo';
        document.getElementById('modalDesactivar').style.display = 'flex';
    }
}

function cerrarModal() {
    document.getElementById('modalDesactivar').style.display = 'none';
    // Revertir el switch
    location.reload();
}

// Mostrar/ocultar contraseña
document.addEventListener('DOMContentLoaded', function () {
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

