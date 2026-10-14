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

        $this->probar('HU4-03', 'HU-4', 'Activar/desactivar y traducir estado', function () {
            $this->enTransaccion(function () {
                $usuario = $this->crearUsuario();

                $this->assertEquals('Activo', $this->modelo->obtenerPorId($usuario['id'])['Estado']);
                $this->modelo->cambiarEstado($usuario['id'], 'Inactivo');
                $this->assertEquals('Inactivo', $this->modelo->obtenerPorId($usuario['id'])['Estado']);
                $this->modelo->cambiarEstado($usuario['id'], 'Activo');
                $this->assertEquals('Activo', $this->modelo->obtenerPorId($usuario['id'])['Estado']);
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
