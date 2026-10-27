<?php

class UsuarioTest extends TestCase
{
    private $modelo;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->modelo = new Usuario($pdo);
    }

    public function ejecutar(): void
    {
        $this->probar('HU1-01', 'HU-1', 'Usuario activo por usuario/correo e inactivo rechazado', function () {
            $this->enTransaccion(function () {
                $activo = $this->crearUsuario('Activo', 'Ferredog#2026');
                $inactivo = $this->crearUsuario('Inactivo', 'Ferredog#2026');

                $this->assertNotNull($this->modelo->buscarPorCorreo($activo['correo']));
                $this->assertEquals($activo['usuario'], $this->modelo->autenticar(
                    $activo['usuario'],
                    'Ferredog#2026'
                )['Usuario']);
                $this->assertFalse($this->modelo->buscarPorCorreo($inactivo['correo']));
                $resultadoInactivo = $this->modelo->autenticar($inactivo['usuario'], 'Ferredog#2026');
                $this->assertTrue(isset($resultadoInactivo['inactivo']));
            });
        });

        $this->probar('HU1-02', 'HU-1', 'Hash correcto e intento con contraseña incorrecta', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario('Activo', 'Ferredog#2026');
                $consulta = $this->pdo->prepare('SELECT Password FROM usuario WHERE Usuario = :usuario');
                $consulta->execute(['usuario' => $usuario['usuario']]);
                $hash = $consulta->fetchColumn();

                $this->assertTrue(password_verify('Ferredog#2026', $hash));
                $this->assertFalse(password_verify('ContraseñaIncorrecta#1', $hash));
                $this->assertFalse($this->modelo->autenticar(
                    $usuario['usuario'],
                    'ContraseñaIncorrecta#1'
                ));
            });
        });

        $this->probar('HU1-03', 'HU-1', 'Cinco intentos fallidos bloquean el acceso', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario('Activo', 'Ferredog#2026');

                for ($intento = 0; $intento < 5; $intento++) {
                    $this->assertFalse($this->modelo->autenticar(
                        $usuario['usuario'],
                        'Incorrecta#123'
                    ));
                }

                $consulta = $this->pdo->prepare(
                    'SELECT Intentos_Fallidos, Fecha_Bloqueo FROM usuario WHERE Usuario = :usuario'
                );
                $consulta->execute(['usuario' => $usuario['usuario']]);
                $fila = $consulta->fetch();

                $this->assertEquals(5, (int) $fila['Intentos_Fallidos']);
                $this->assertNotNull($fila['Fecha_Bloqueo']);
                $segundosDesdeBloqueo = time() - strtotime($fila['Fecha_Bloqueo']);
                $resultado = $this->modelo->autenticar($usuario['usuario'], 'Ferredog#2026');
                $this->assertTrue(
                    isset($resultado['bloqueado']),
                    'El quinto intento debe bloquear. Fecha_Bloqueo=' . $fila['Fecha_Bloqueo']
                    . ', PHP=' . date('Y-m-d H:i:s') . ' (' . date_default_timezone_get() . ')'
                    . ', tiempo calculado desde bloqueo=' . $segundosDesdeBloqueo . ' segundos.'
                );
            });
        });

        $this->probar('HU1-04', 'HU-1', 'Código vigente, expirado y usado', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario('Activo', 'Ferredog#2026');
                $codigo = (string) random_int(100000, 999999);

                $this->modelo->guardarCodigoRecuperacion($usuario['id'], $codigo);
                $this->assertTrue($this->modelo->verificarCodigoRecuperacion($usuario['id'], $codigo));
                $this->assertFalse($this->modelo->verificarCodigoRecuperacion(
                    $usuario['id'],
                    (string) random_int(100000, 999999)
                ));

                $vencer = $this->pdo->prepare(
                    'UPDATE usuario SET Codigo_Expiracion = DATE_SUB(NOW(), INTERVAL 1 SECOND) '
                    . 'WHERE Id_Usuario = :id'
                );
                $vencer->execute(['id' => $usuario['id']]);
                $this->assertFalse($this->modelo->verificarCodigoRecuperacion($usuario['id'], $codigo));

                $this->modelo->guardarCodigoRecuperacion($usuario['id'], $codigo);
                $this->modelo->cambiarContrasena($usuario['id'], 'NuevaClave#2026');
                $this->assertFalse($this->modelo->verificarCodigoRecuperacion($usuario['id'], $codigo));
            });
        });

        $this->probar('HU1-05', 'HU-1', 'Correo inactivo no habilita recuperación y el correo activo sigue funcionando', function () {
            $this->enTransaccion(function () {
                $activo = $this->crearUsuario('Activo', 'Ferredog#2026');
                $inactivo = $this->crearUsuario('Inactivo', 'Ferredog#2026');

                $this->assertNotNull($this->modelo->buscarPorCorreo($activo['correo']));
                $this->assertFalse($this->modelo->buscarPorCorreo($inactivo['correo']));

                $rutaLog = BASE_PATH . '/storage/logs/correos.log';
                $existia = file_exists($rutaLog);
                $contenidoOriginal = $existia ? file_get_contents($rutaLog) : '';

                if ($contenidoOriginal === false) {
                    throw new RuntimeException('No se pudo leer el log antes de la prueba.');
                }

                $modoExistia = array_key_exists('MAIL_MODO', $_ENV);
                $modoOriginal = $_ENV['MAIL_MODO'] ?? null;
                $correo = 'TEST_' . $this->sufijoUnico() . '@example.invalid';
                $codigo = (string) random_int(100000, 999999);
                $_ENV['MAIL_MODO'] = 'prueba';

                try {
                    $resultado = (new Correo())->enviarCodigo($correo, 'TEST_Usuario', $codigo);
                    $contenido = file_get_contents($rutaLog);
                    $this->assertTrue($resultado['exito'] === true);
                    $this->assertTrue(strpos($contenido, $correo) !== false);
                    $this->assertTrue(strpos($contenido, $codigo) !== false);
                } finally {
                    if ($modoExistia) {
                        $_ENV['MAIL_MODO'] = $modoOriginal;
                    } else {
                        unset($_ENV['MAIL_MODO']);
                    }

                    if ($existia) {
                        if (file_put_contents($rutaLog, $contenidoOriginal, LOCK_EX) === false) {
                            throw new RuntimeException('No se pudo restaurar el log de correo.');
                        }
                    } elseif (file_exists($rutaLog) && !unlink($rutaLog)) {
                        throw new RuntimeException('No se pudo retirar el log temporal de la prueba.');
                    }
                }
            });
        });
    }

    private function crearUsuario(string $estado, string $contrasena): array
    {
        $sufijo = $this->sufijoUnico();
        $datos = [
            'nombre' => 'Prueba',
            'apellidos' => 'Automatica ' . strtolower($this->sufijoLetrasAleatorias()),
            'correo' => 'test_' . $sufijo . '@gmail.com',
            'usuario' => 'test_' . $sufijo
        ];
        $this->assertTrue(Validador::correoValido($datos['correo']));

        $this->modelo->crear(
            $datos['nombre'],
            $datos['apellidos'],
            $datos['correo'],
            $datos['usuario'],
            $contrasena,
            'Vendedor',
            $estado
        );

        $consulta = $this->pdo->prepare('SELECT Id_Usuario FROM usuario WHERE Usuario = :usuario');
        $consulta->execute(['usuario' => $datos['usuario']]);
        $datos['id'] = (int) $consulta->fetchColumn();

        $this->assertTrue($datos['id'] > 0, 'El usuario de prueba debe haberse creado.');
        return $datos;
    }
}
