/**
 * JavaScript para el formulario de login
 * - Mostrar/ocultar contraseña
 */

document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('togglePassword');
    const inputContrasena = document.getElementById('contrasena');

    if (togglePassword && inputContrasena) {
        togglePassword.addEventListener('click', function () {
            // Alternar tipo de input
            const tipo = inputContrasena.getAttribute('type');

            if (tipo === 'password') {
                inputContrasena.setAttribute('type', 'text');
                togglePassword.textContent = '🙈'; // Ojo cerrado
            } else {
                inputContrasena.setAttribute('type', 'password');
                togglePassword.textContent = '👁️'; // Ojo abierto
            }
        });
    }
});
