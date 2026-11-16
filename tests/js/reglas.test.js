// Pruebas unitarias de las reglas de los formularios (se ejecutan con Node, sin navegador).
// Ejecutar:  node --test tests/js/reglas.test.js
const test = require('node:test');
const assert = require('node:assert/strict');
const reglas = require('../../public/assets/js/reglas.js');

test('limpiarEntero: solo deja dígitos', () => {
    assert.equal(reglas.limpiarEntero('12'), '12');
    assert.equal(reglas.limpiarEntero('1a2-b'), '12');
    assert.equal(reglas.limpiarEntero('i'), '');
    assert.equal(reglas.limpiarEntero('5e3'), '53');
});

test('limpiarDecimal: un punto y máximo 2 decimales', () => {
    assert.equal(reglas.limpiarDecimal('12.505'), '12.50');
    assert.equal(reglas.limpiarDecimal('1.2.3'), '1.23');
    assert.equal(reglas.limpiarDecimal('$80'), '80');
});

test('telefonoValido: exactamente 10 dígitos (proveedores)', () => {
    assert.equal(reglas.telefonoValido('9511234567'), true);
    assert.equal(reglas.telefonoValido('951123456'), false);   // 9
    assert.equal(reglas.telefonoValido('95112345678'), false); // 11
    assert.equal(reglas.telefonoValido('95112345ab'), false);
});

test('correoValido: rechaza extensiones falsas como .xd', () => {
    assert.equal(reglas.correoValido('ventas@ferredog.com'), true);
    assert.equal(reglas.correoValido('ana@gmail.com'), true);
    assert.equal(reglas.correoValido('ana@gmail.xd'), false);
    assert.equal(reglas.correoValido('ana@gmail.co'), false);
    assert.equal(reglas.correoValido('ana@gmail.mx'), false);   // gmail solo .com
    assert.equal(reglas.correoValido('sin-arroba.com'), false);
});

test('puedePedirHasta: stock máximo menos existencias (nunca negativo)', () => {
    assert.equal(reglas.puedePedirHasta(100, 80), 20);
    assert.equal(reglas.puedePedirHasta(60, 6), 54);
    assert.equal(reglas.puedePedirHasta(24, 262), 0);
});

test('validarCantidad: vacío = no se pide; 0 y excesos son error', () => {
    assert.equal(reglas.validarCantidad('', 20), '');            // no se pide, sin error
    assert.equal(reglas.validarCantidad('5', 20), '');
    assert.equal(reglas.validarCantidad('0', 20), 'La cantidad debe ser mayor a 0');
    assert.equal(reglas.validarCantidad('25', 20), 'Supera el stock máximo (puedes pedir hasta 20)');
});

test('subtotal y total del pedido', () => {
    assert.equal(reglas.subtotal(3, 12.5), 37.5);
    assert.equal(reglas.total([{ cantidad: 2, costo: 10 }, { cantidad: 1, costo: 31 }]), 51);
    assert.equal(reglas.total([]), 0);
});
