<?php

class UsuarioGestionTest extends TestCase
{
    private $modelo;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->modelo = new Usuario($pdo);
    }

    public function ejecutar(): void
    {
        $this->probar('HU4-01', 'HU-4', 'Crear, leer y detectar nombre/correo duplicados', function () {
            $this->enTransaccion(function () {
                $primero = $this->crearUsuario();
                $segundo = $this->crearUsuario();

                $leido = $this->modelo->obtenerPorId($primero['id']);
                $this->assertEquals($primero['usuario'], $leido['Usuario']);
                $this->assertTrue($this->modelo->existeUsuario($primero['usuario']), 'Detectar el usuario existente.');
                $this->assertTrue($this->modelo->existeUsuario($segundo['usuario']), 'Detectar el segundo usuario existente.');
                $this->assertTrue($this->modelo->existeCorreo($primero['correo']), 'Detectar el correo existente.');
                $this->assertTrue($this->modelo->existeCorreo($segundo['correo']), 'Detectar el segundo correo existente.');

                $idEmpleado = $this->modelo->obtenerIdEmpleado($primero['id']);
                $this->assertFalse(
                    $this->modelo->existeUsuario($primero['usuario'], $primero['id']),
                    'La exclusión del usuario editado debe permitir conservar su nombre.'
                );
                $this->assertFalse(
                    $this->modelo->existeCorreo($primero['correo'], $idEmpleado),
                    'La exclusión del empleado editado debe permitir conservar su correo.'
                );
            });
        });

        $this->probar('HU4-02', 'HU-4', 'La búsqueda devuelve coincidencias', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario();
                $coincidencias = $this->modelo->obtenerTodos($usuario['usuario']);

                $this->assertTrue(count($coincidencias) > 0);
                $this->assertEquals($usuario['usuario'], $coincidencias[0]['Usuario']);
            });
        });

        $this->probar('HU4-03', 'HU-4', 'Eliminar usuario sin enlaces borra sus datos', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario();
                $idEmpleado = $this->modelo->obtenerIdEmpleado($usuario['id']);

                $resultado = $this->modelo->eliminar($usuario['id']);

                $this->assertEquals('borrado', $resultado['accion']);
                $consultaUsuario = $this->pdo->prepare('SELECT COUNT(*) FROM usuario WHERE Id_Usuario = :id');
                $consultaUsuario->execute(['id' => $usuario['id']]);
                $this->assertEquals(0, (int) $consultaUsuario->fetchColumn());
                $consultaEmpleado = $this->pdo->prepare('SELECT COUNT(*) FROM empleados WHERE Id_Empleado = :id');
                $consultaEmpleado->execute(['id' => $idEmpleado]);
                $this->assertEquals(0, (int) $consultaEmpleado->fetchColumn());
            });
        });

        $this->probar('HU4-05', 'HU-4', 'Empleado con venta se anonimiza y conserva historial', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario();
                $idEmpleado = $this->modelo->obtenerIdEmpleado($usuario['id']);
                $idMetodoPago = (int) $this->pdo
                    ->query('SELECT Id_Metodo_Pago FROM metodo_pago ORDER BY Id_Metodo_Pago LIMIT 1')
                    ->fetchColumn();
                $this->assertTrue($idMetodoPago > 0, 'Se requiere al menos un método de pago.');

                $venta = $this->pdo->prepare(
                    'INSERT INTO ventas (Total, Id_Metodo_Pago, Id_Empleado)
                     VALUES (:total, :metodo_pago, :empleado)'
                );
                $venta->execute([
                    'total' => 1.00,
                    'metodo_pago' => $idMetodoPago,
                    'empleado' => $idEmpleado
                ]);

                $resultado = $this->modelo->eliminar($usuario['id']);

                $this->assertEquals('anonimizado', $resultado['accion']);
                $consultaUsuario = $this->pdo->prepare('SELECT COUNT(*) FROM usuario WHERE Id_Usuario = :id');
                $consultaUsuario->execute(['id' => $usuario['id']]);
                $this->assertEquals(0, (int) $consultaUsuario->fetchColumn());
                $consultaEmpleado = $this->pdo->prepare(
                    'SELECT Nombre, Apellidos, Correo, Telefono, Puesto, Estado
                     FROM empleados WHERE Id_Empleado = :id'
                );
                $consultaEmpleado->execute(['id' => $idEmpleado]);
                $empleado = $consultaEmpleado->fetch(PDO::FETCH_ASSOC);
                $this->assertEquals('Empleado', $empleado['Nombre']);
                $this->assertEquals('Eliminado', $empleado['Apellidos']);
                $this->assertNull($empleado['Correo']);
                $this->assertNull($empleado['Telefono']);
                $this->assertNull($empleado['Puesto']);
                $this->assertEquals(0, (int) $empleado['Estado']);
            });
        });

        $this->probar('HU4-06', 'HU-4', 'No se puede eliminar al usuario en sesión', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario();
                $idSesionPrueba = bin2hex(random_bytes(16));
                $comando = [
                    PHP_BINARY,
                    __DIR__ . DIRECTORY_SEPARATOR . 'controller_validation_worker.php',
                    'usuario-eliminar',
                    $idSesionPrueba,
                    (string) $usuario['id']
                ];
                $descriptores = [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w']
                ];
                $proceso = proc_open($comando, $descriptores, $tuberias);
                $this->assertTrue(is_resource($proceso), 'Debe iniciarse el proceso para probar la protección de sesión.');

                fwrite($tuberias[0], json_encode(['id_usuario' => $usuario['id']]));
                fclose($tuberias[0]);
                $salida = stream_get_contents($tuberias[1]);
                $error = stream_get_contents($tuberias[2]);
                fclose($tuberias[1]);
                fclose($tuberias[2]);
                $codigoSalida = proc_close($proceso);

                $marcador = strrpos($salida, 'SESSION_STATE:');
                $this->assertTrue($codigoSalida === 0, 'El worker debe finalizar correctamente: ' . $error);
                $this->assertTrue($marcador !== false, 'El worker debe devolver el estado de sesión.');
                $estadoSesion = json_decode(
                    base64_decode(substr($salida, $marcador + strlen('SESSION_STATE:'))),
                    true
                );
                $this->assertEquals('No puedes eliminar tu propia cuenta', $estadoSesion['mensaje_error']);
                $this->assertTrue($this->modelo->obtenerPorId($usuario['id']) !== false);
            });
        });

        $this->probar('HU4-04', 'HU-4', 'La base rechaza nombres con símbolos', function () {
            $this->enTransaccion(function () {
                $sufijo = $this->sufijoUnico();
                $error = $this->expectException(function () use ($sufijo) {
                    $this->modelo->crear(
                        'Sanchez(#',
                        'Automatica ' . strtolower($this->sufijoLetrasAleatorias()),
                        'test_' . $sufijo . '@gmail.com',
                        'test_' . $sufijo,
                        'Prueba#2026',
                        'Vendedor',
                        'Activo'
                    );
                }, PDOException::class);

                $this->assertEquals('23000', (string) $error->getCode());
            });
        });
    }

    private function crearUsuario(): array
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
            'Prueba#2026',
            'Vendedor',
            'Activo'
        );

        $consulta = $this->pdo->prepare('SELECT Id_Usuario FROM usuario WHERE Usuario = :usuario');
        $consulta->execute(['usuario' => $datos['usuario']]);
        $datos['id'] = (int) $consulta->fetchColumn();
        $this->assertTrue($datos['id'] > 0, 'El usuario de prueba debe haberse creado.');

        return $datos;
    }
}
