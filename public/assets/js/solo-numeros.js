/**
 * Restringe campos marcados a enteros o decimales sin depender de type="number".
 */
(function () {
    const camposConfigurados = new WeakSet();

    function limpiarValor(valor, esDecimal) {
        const caracteresPermitidos = valor.replace(esDecimal ? /[^0-9.]/g : /[^0-9]/g, '');
        if (!esDecimal) {
            return caracteresPermitidos;
        }

        const punto = caracteresPermitidos.indexOf('.');
        if (punto === -1) {
            return caracteresPermitidos;
        }

        return caracteresPermitidos.slice(0, punto + 1)
            + caracteresPermitidos.slice(punto + 1).replace(/\./g, '').slice(0, 2);
    }

    function configurarSoloNumeros(campo) {
        if (!(campo instanceof HTMLInputElement)
            || (!campo.hasAttribute('data-solo-enteros') && !campo.hasAttribute('data-solo-decimales'))) {
            return;
        }

        const esDecimal = campo.hasAttribute('data-solo-decimales');
        campo.type = 'text';
        campo.inputMode = esDecimal ? 'decimal' : 'numeric';
        campo.autocomplete = 'off';

        if (camposConfigurados.has(campo)) {
            return;
        }
        camposConfigurados.add(campo);

        function limpiarCampo(valor) {
            const valorLimpio = limpiarValor(valor, esDecimal);
            return campo.maxLength >= 0 ? valorLimpio.slice(0, campo.maxLength) : valorLimpio;
        }

        campo.value = limpiarCampo(campo.value);

        function insertarTexto(texto) {
            const inicio = campo.selectionStart ?? campo.value.length;
            const fin = campo.selectionEnd ?? inicio;
            const textoLimpio = limpiarValor(texto, esDecimal);
            if (textoLimpio === '') {
                return;
            }

            const valorNuevo = limpiarCampo(
                campo.value.slice(0, inicio) + textoLimpio + campo.value.slice(fin)
            );
            campo.value = valorNuevo;
            const posicion = Math.min(
                limpiarCampo(campo.value.slice(0, inicio) + textoLimpio).length,
                valorNuevo.length
            );
            campo.setSelectionRange(posicion, posicion);
            campo.dispatchEvent(new Event('input', { bubbles: true }));
        }

        campo.addEventListener('keydown', function (evento) {
            if (evento.ctrlKey || evento.metaKey) {
                if (['a', 'c', 'v', 'x'].includes(evento.key.toLowerCase())) {
                    return;
                }
                evento.preventDefault();
                return;
            }

            if ([
                'Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp',
                'ArrowDown', 'Home', 'End', 'Enter', 'Escape', 'Shift', 'Control', 'Meta'
            ].includes(evento.key)) {
                return;
            }

            if (evento.key.length !== 1) {
                evento.preventDefault();
                return;
            }

            const inicio = campo.selectionStart ?? campo.value.length;
            const fin = campo.selectionEnd ?? inicio;
            const candidato = campo.value.slice(0, inicio) + evento.key + campo.value.slice(fin);
            const valido = esDecimal
                ? /^\d*(?:\.\d{0,2})?$/.test(candidato)
                : /^\d*$/.test(candidato);
            if (!valido) {
                evento.preventDefault();
            }
        });

        campo.addEventListener('paste', function (evento) {
            evento.preventDefault();
            insertarTexto(evento.clipboardData.getData('text'));
        });

        campo.addEventListener('drop', function (evento) {
            evento.preventDefault();
            insertarTexto(evento.dataTransfer.getData('text'));
        });

        campo.addEventListener('input', function () {
            const inicio = campo.selectionStart ?? campo.value.length;
            const valorLimpio = limpiarCampo(campo.value);
            if (valorLimpio !== campo.value) {
                const posicion = Math.min(limpiarCampo(campo.value.slice(0, inicio)).length, valorLimpio.length);
                campo.value = valorLimpio;
                campo.setSelectionRange(posicion, posicion);
            }
        });
    }

    window.configurarSoloNumeros = configurarSoloNumeros;

    function configurarCamposExistentes() {
        document.querySelectorAll('[data-solo-enteros], [data-solo-decimales]')
            .forEach(configurarSoloNumeros);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', configurarCamposExistentes);
    } else {
        configurarCamposExistentes();
    }
})();
