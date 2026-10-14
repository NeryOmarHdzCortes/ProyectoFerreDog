<?php

class ValidadorTest extends TestCase
{
    public function ejecutar(): void
    {
        $validos = [
            'ana@gmail.com',
            'juan@itvalleoaxaca.edu.mx',
            'ventas@ferreteria.com.mx'
        ];

        foreach ($validos as $indice => $correo) {
            $this->probar('VAL-' . str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT), 'Correo', 'Acepta ' . $correo, function () use ($correo) {
                $this->assertTrue(Validador::correoValido($correo));
            });
        }

        $invalidos = [
            'x@gmail.com.xd',
            'x@gmail.co',
            'x@gmail',
            'x@hotmail.con',
            'x@@gmail.com',
            'x@dominio.xd'
        ];

        foreach ($invalidos as $indice => $correo) {
            $this->probar('VAL-' . str_pad((string) ($indice + 4), 2, '0', STR_PAD_LEFT), 'Correo', 'Rechaza ' . $correo, function () use ($correo) {
                $this->assertFalse(Validador::correoValido($correo));
            });
        }

        $this->probar('VAL-10', 'Correo', 'Normaliza espacios y mayúsculas', function () {
            $this->assertTrue(Validador::correoValido('  ANA@GMAIL.COM  '));
        });

        $this->probar('VAL-11', 'Teléfono', 'Acepta teléfono de proveedor de 10 dígitos', function () {
            $this->assertTrue(Validador::telefonoValido('9511234567'));
        });

        $telefonosInvalidos = [
            '951123456',
            '95112345678',
            '951-123-4567',
            '95112345ab',
            ''
        ];

        foreach ($telefonosInvalidos as $indice => $telefono) {
            $this->probar('VAL-' . str_pad((string) ($indice + 12), 2, '0', STR_PAD_LEFT), 'Teléfono', 'Rechaza teléfono inválido', function () use ($telefono) {
                $this->assertFalse(Validador::telefonoValido($telefono));
            });
        }
    }
}
