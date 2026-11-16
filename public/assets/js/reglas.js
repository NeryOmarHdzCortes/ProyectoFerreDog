/**
 * Reglas de validación y cálculo de los formularios de Ferredog.
 * Funciones puras (sin DOM): se usan en el navegador (window.Reglas)
 * y se prueban con Node (require). Ejecutar pruebas: node --test tests/js/reglas.test.js
 */
(function (global) {
    'use strict';

    function limpiarEntero(valor) {
        return String(valor).replace(/[^0-9]/g, '');
    }

    function limpiarDecimal(valor) {
        const permitidos = String(valor).replace(/[^0-9.]/g, '');
        const punto = permitidos.indexOf('.');
        if (punto === -1) {
            return permitidos;
        }
        return permitidos.slice(0, punto + 1)
            + permitidos.slice(punto + 1).replace(/\./g, '').slice(0, 2);
    }

    function telefonoValido(telefono) {
        return /^[0-9]{10}$/.test(String(telefono).trim());
    }

    const EXTENSIONES = [
        '.com', '.net', '.org', '.edu', '.info', '.mx',
        '.com.mx', '.edu.mx', '.gob.mx', '.org.mx', '.net.mx'
    ];
    const DOMINIOS_CONOCIDOS = [
        'gmail.com', 'hotmail.com', 'outlook.com', 'outlook.es',
        'yahoo.com', 'yahoo.com.mx', 'live.com', 'live.com.mx', 'icloud.com'
    ];

    // Misma regla que proveedores.js y Validador::correoValido() en PHP
    function correoValido(correo) {
        const normalizado = String(correo).trim().toLowerCase();
        if (!/^[a-z0-9._%+-]+@[a-z0-9-]+(\.[a-z0-9-]+)*\.[a-z]{2,}$/.test(normalizado)) {
            return false;
        }
        const dominio = normalizado.split('@')[1];
        if (!EXTENSIONES.some(function (extension) { return dominio.endsWith(extension); })) {
            return false;
        }
        if (/^(gmail|hotmail|outlook|yahoo|live|icloud)/.test(dominio)) {
            return DOMINIOS_CONOCIDOS.includes(dominio);
        }
        return true;
    }

    function puedePedirHasta(stockMaximo, existencias) {
        return Math.max(0, Number(stockMaximo) - Number(existencias));
    }

    function validarCantidad(texto, maximoPermitido) {
        const valor = String(texto).trim();
        if (valor === '') {
            return '';
        }
        const cantidad = Number(valor);
        if (!Number.isInteger(cantidad) || cantidad <= 0) {
            return 'La cantidad debe ser mayor a 0';
        }
        if (cantidad > maximoPermitido) {
            return 'Supera el stock máximo (puedes pedir hasta ' + maximoPermitido + ')';
        }
        return '';
    }

    function subtotal(cantidad, costo) {
        return Math.round(Number(cantidad) * Number(costo) * 100) / 100;
    }

    function total(renglones) {
        return renglones.reduce(function (suma, r) {
            return Math.round((suma + subtotal(r.cantidad, r.costo)) * 100) / 100;
        }, 0);
    }

    function puedeAgregar(cantidadNueva, cantidadEnTicket, existencias) {
        const nueva = Number(cantidadNueva);
        const actual = Number(cantidadEnTicket);
        const disponible = Number(existencias);

        return Number.isSafeInteger(nueva)
            && nueva > 0
            && Number.isSafeInteger(actual)
            && actual >= 0
            && Number.isSafeInteger(disponible)
            && disponible >= 0
            && nueva + actual <= disponible;
    }

    function cambio(pago, importeTotal) {
        return Math.round((Number(pago) - Number(importeTotal)) * 100) / 100;
    }

    function faltaPorPagar(pago, importeTotal) {
        return Math.max(0, Math.round((Number(importeTotal) - Number(pago)) * 100) / 100);
    }

    const Reglas = {
        limpiarEntero, limpiarDecimal, telefonoValido, correoValido,
        puedePedirHasta, validarCantidad, subtotal, total,
        puedeAgregar, cambio, faltaPorPagar
    };

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = Reglas;      // Node (pruebas)
    } else {
        global.Reglas = Reglas;       // Navegador
    }
})(typeof window !== 'undefined' ? window : globalThis);
