<?php

class ControladorFormularioTest extends TestCase
{
    public function ejecutar(): void
    {
        $this->probar('FORM-01', 'Formulario', 'Usuario conserva campos sin guardar contraseñas', function () {
            $sufijo = $this->sufijoUnico();
            $datos = [
                'id_usuario' => '0',
                'nombre' => 'TEST_Nombre_' . $sufijo,
                'apellidos' => 'TEST_Apellido_' . $sufijo,
                'nombre_usuario' => 'TEST_Usuario_' . $sufijo,
                'correo' => 'x@gmail.com.xd',
                'rol' => 'Vendedor',
                'estado' => 'Activo',
                'contrasena' => 'NoGuardar#2026',
                'confirmar_contrasena' => 'NoGuardar#2026'
            ];

            $sesion = $this->enviarControlador('usuario', $datos);
            $this->assertEquals($datos['nombre'], $sesion['old']['nombre']);
            $this->assertEquals($datos['correo'], $sesion['old']['correo']);
            $this->assertTrue(isset($sesion['error']));
            $this->assertFalse(isset($sesion['old']['contrasena']));
            $this->assertFalse(isset($sesion['old']['confirmar_contrasena']));
        });

        $this->probar('FORM-02', 'Formulario', 'Proveedor conserva campos y muestra error de correo', function () {
            $sufijo = $this->sufijoUnico();
            $datos = [
                'nombre' => 'TEST_Proveedor_' . $sufijo,
                'tipo' => 'Distribuidor',
                'contacto' => 'TEST_Contacto_' . $sufijo,
                'telefono' => 'TEST_Telefono_' . $sufijo,
                'correo' => 'x@dominio.xd',
                'direccion' => 'TEST_Direccion_' . $sufijo
            ];

            $sesion = $this->enviarControlador('proveedor', $datos);
            $this->assertEquals($datos['nombre'], $sesion['old']['nombre']);
            $this->assertEquals($datos['tipo'], $sesion['old']['tipo']);
            $this->assertEquals($datos['correo'], $sesion['old']['correo']);
            $this->assertTrue(isset($sesion['error']));
        });
    }

    private function enviarControlador(string $tipo, array $datos): array
    {
        if (!function_exists('proc_open')) {
            throw new RuntimeException('proc_open no está disponible para probar redirects en un proceso aislado.');
        }

        $idSesion = bin2hex(random_bytes(16));
        $archivoSesion = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sess_' . $idSesion;
        $comando = [
            PHP_BINARY,
            __DIR__ . '/controller_validation_worker.php',
            $tipo,
            $idSesion
        ];
        $descriptores = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $proceso = proc_open($comando, $descriptores, $tuberias, BASE_PATH);

        if (!is_resource($proceso)) {
            throw new RuntimeException('No se pudo iniciar el proceso de prueba del controlador.');
        }

        try {
            fwrite($tuberias[0], json_encode($datos));
            fclose($tuberias[0]);
            $salida = stream_get_contents($tuberias[1]);
            fclose($tuberias[1]);
            $error = stream_get_contents($tuberias[2]);
            fclose($tuberias[2]);
            $codigoSalida = proc_close($proceso);
            $proceso = null;

            if ($codigoSalida !== 0) {
                throw new RuntimeException('El proceso controlador terminó con error: ' . $error . $salida);
            }

            if (preg_match('/SESSION_STATE:([A-Za-z0-9+\/=]+)/', $salida, $coincidencias) !== 1) {
                throw new RuntimeException('El proceso controlador no devolvió el estado de sesión: ' . $error . $salida);
            }

            $sesion = json_decode(base64_decode($coincidencias[1]), true);
            if (!is_array($sesion)) {
                throw new RuntimeException('El estado de sesión del controlador no es JSON válido.');
            }

            return $sesion;
        } finally {
            if (is_resource($proceso)) {
                foreach ($tuberias as $tuberia) {
                    if (is_resource($tuberia)) {
                        fclose($tuberia);
                    }
                }
                proc_terminate($proceso);
                proc_close($proceso);
            }
            if (is_file($archivoSesion)) {
                unlink($archivoSesion);
            }
        }
    }
}

