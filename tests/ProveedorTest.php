<?php

class ProveedorTest extends TestCase
{
    private $modelo;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->modelo = new Proveedor($pdo);
    }

    public function ejecutar(): void
    {
        $this->probar('HU6-01', 'HU-6', 'Crear, editar y validar duplicados excluyendo al propio proveedor', function () {
            $this->enTransaccion(function () {
                $primero = $this->crearProveedor();
                $segundo = $this->crearProveedor();
                $idPrimero = $this->ultimoProveedor($primero['nombre']);
                $idSegundo = $this->ultimoProveedor($segundo['nombre']);

                $this->assertTrue($this->modelo->existeNombre($primero['nombre']));
                $this->assertTrue($this->modelo->existeCorreo($primero['correo']));
                $this->assertFalse($this->modelo->existeNombre($primero['nombre'], $idPrimero));
                $this->assertFalse($this->modelo->existeCorreo($primero['correo'], $idPrimero));
                $this->assertTrue($this->modelo->existeNombre($primero['nombre'], $idSegundo));
                $this->assertTrue($this->modelo->existeCorreo($primero['correo'], $idSegundo));

                $nombreEditado = $primero['nombre'] . '_EDITADO';
                $this->modelo->editar(
                    $idPrimero,
                    $nombreEditado,
                    'Distribuidor',
                    'Contacto Editado',
                    '5550100124',
                    $primero['correo'],
                    'Direccion Editada'
                );
                $editado = $this->modelo->obtenerPorId($idPrimero);
                $this->assertEquals($nombreEditado, $editado['Nombre']);
                $this->assertEquals('Contacto Editado', $editado['Contacto']);
            });
        });

        $this->probar('HU6-02', 'HU-6', 'Desactivar proveedor pone Estado en cero', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idProveedor = $this->ultimoProveedor($proveedor['nombre']);
                $this->modelo->desactivar($idProveedor);

                $fila = $this->pdo->prepare('SELECT Estado FROM proveedor WHERE Id_Proveedor = :id');
                $fila->execute(['id' => $idProveedor]);
                $this->assertEquals(0, (int) $fila->fetchColumn());
                $this->assertEquals('Inactivo', $this->modelo->obtenerPorId($idProveedor)['Estado']);
            });
        });

        $this->probar('HU6-03', 'HU-6', 'Conteo y lista de productos surtidos por proveedor', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idProveedor = $this->ultimoProveedor($proveedor['nombre']);
                $referencias = $this->obtenerReferenciasProducto();
                $modeloProducto = new Producto($this->pdo);
                $nombres = [];

                for ($indice = 0; $indice < 2; $indice++) {
                    $sufijo = $this->sufijoUnico();
                    $codigo = 'TEST_Codigo_' . $sufijo;
                    $nombre = 'TESTProductoProveedor' . $this->sufijoLetrasAleatorias();
                    $modeloProducto->crear(
                        $codigo,
                        $nombre,
                        $referencias['marca'],
                        $referencias['categoria'],
                        $idProveedor,
                        $referencias['unidad'],
                        50.00,
                        1,
                        1
                    );
                    $nombres[] = $nombre;
                }

                $this->assertEquals(2, (int) $this->modelo->contarProductosQueSurte($idProveedor));
                $productos = $this->modelo->obtenerProductosQueSurte($idProveedor, 'TESTProductoProveedor');
                $this->assertEquals(2, count($productos));
                $this->assertTrue(in_array($nombres[0], array_column($productos, 'Nombre'), true));
                $this->assertTrue(in_array($nombres[1], array_column($productos, 'Nombre'), true));
            });
        });

        $this->probar('HU6-04', 'HU-6', 'Eliminar proveedor inactivo sin enlaces y rechazar activos', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idProveedor = $this->ultimoProveedor($proveedor['nombre']);

                $resultadoActivo = $this->modelo->eliminar($idProveedor);
                $this->assertEquals('activo', $resultadoActivo['resultado']);
                $this->assertTrue($this->modelo->obtenerPorId($idProveedor) !== false);

                $this->modelo->desactivar($idProveedor);
                $resultadoInactivo = $this->modelo->eliminar($idProveedor);
                $this->assertEquals('borrado', $resultadoInactivo['resultado']);
                $this->assertFalse($this->modelo->obtenerPorId($idProveedor));
            });
        });

        $this->probar('HU6-05', 'HU-6', 'Eliminar proveedor sin compras y desasignar sus productos', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idProveedor = $this->ultimoProveedor($proveedor['nombre']);
                $referencias = $this->obtenerReferenciasProducto();
                $sufijo = $this->sufijoLetrasAleatorias();
                $modeloProducto = new Producto($this->pdo);
                $modeloProducto->crear(
                    'CodigoAsignado' . $sufijo,
                    'ProductoAsignado' . $sufijo,
                    $referencias['marca'],
                    $referencias['categoria'],
                    $idProveedor,
                    $referencias['unidad'],
                    50.00,
                    1,
                    1
                );

                $this->modelo->desactivar($idProveedor);
                $resultado = $this->modelo->eliminar($idProveedor);
                $this->assertEquals('borrado', $resultado['resultado']);
                $this->assertEquals(1, $resultado['productos_desasignados']);
                $this->assertFalse($this->modelo->obtenerPorId($idProveedor));

                $consultaProveedorProducto = $this->pdo->prepare(
                    'SELECT Id_Proveedor FROM productos WHERE Nombre = :nombre'
                );
                $consultaProveedorProducto->execute(['nombre' => 'ProductoAsignado' . $sufijo]);
                $this->assertNull($consultaProveedorProducto->fetchColumn());

                $productoSinProveedor = $modeloProducto->obtenerTodos(
                    'ProductoAsignado' . $sufijo,
                    0,
                    0,
                    ''
                );
                $this->assertEquals('ProductoAsignado' . $sufijo, $productoSinProveedor[0]['Nombre']);
                $this->assertNull($productoSinProveedor[0]['Proveedor']);
            });
        });

        $this->probar('HU6-06', 'HU-6', 'Eliminar proveedor con compras y conservar sus datos en el historial', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idProveedor = $this->ultimoProveedor($proveedor['nombre']);
                $actualizarRFC = $this->pdo->prepare(
                    'UPDATE proveedor SET RFC = :rfc WHERE Id_Proveedor = :id'
                );
                $actualizarRFC->execute(['rfc' => 'ABC010101A1', 'id' => $idProveedor]);
                $idEmpleado = (int) $this->pdo
                    ->query('SELECT Id_Empleado FROM empleados ORDER BY Id_Empleado LIMIT 1')
                    ->fetchColumn();
                $this->assertTrue($idEmpleado > 0, 'Se requiere un empleado para la compra.');
                $referencias = $this->obtenerReferenciasProducto();
                $sufijo = $this->sufijoLetrasAleatorias();
                $modeloProducto = new Producto($this->pdo);
                $modeloProducto->crear(
                    'CodigoCompra' . $sufijo,
                    'ProductoCompra' . $sufijo,
                    $referencias['marca'],
                    $referencias['categoria'],
                    $idProveedor,
                    $referencias['unidad'],
                    50.00,
                    1,
                    1
                );

                $insertarCompra = $this->pdo->prepare(
                    "INSERT INTO compras (Total, Estado, Id_Proveedor, Id_Empleado)
                     VALUES (1, 'Recibida', :proveedor, :empleado)"
                );
                $insertarCompra->execute([
                    'proveedor' => $idProveedor,
                    'empleado' => $idEmpleado
                ]);

                $this->modelo->desactivar($idProveedor);
                $resultado = $this->modelo->eliminar($idProveedor);
                $this->assertEquals('borrado', $resultado['resultado']);
                $this->assertEquals(1, $resultado['productos_desasignados']);

                $this->assertFalse($this->modelo->obtenerPorId($idProveedor));
                $consultaCompra = $this->pdo->prepare(
                    'SELECT Id_Proveedor, Nombre_Proveedor, RFC_Proveedor
                     FROM compras WHERE Id_Proveedor IS NULL AND Nombre_Proveedor = :nombre'
                );
                $consultaCompra->execute(['nombre' => $proveedor['nombre']]);
                $compra = $consultaCompra->fetch(PDO::FETCH_ASSOC);
                $this->assertNull($compra['Id_Proveedor']);
                $this->assertEquals($proveedor['nombre'], $compra['Nombre_Proveedor']);
                $this->assertEquals('ABC010101A1', $compra['RFC_Proveedor']);
                $productoSinProveedor = $modeloProducto->obtenerTodos(
                    'ProductoCompra' . $sufijo,
                    0,
                    0,
                    ''
                );
                $this->assertNull($productoSinProveedor[0]['Proveedor']);
                $this->assertEquals([], $this->modelo->obtenerTodos($proveedor['nombre'], 'Activo'));
                $this->assertEquals([], $this->modelo->obtenerTodos($proveedor['nombre'], 'Inactivo'));
                $this->assertEquals([], $this->modelo->obtenerTodos($proveedor['nombre'], ''));
                $this->assertFalse(in_array(
                    $idProveedor,
                    array_column($this->modelo->obtenerActivos(), 'Id_Proveedor')
                ));
            });
        });
    }

    private function crearProveedor(): array
    {
        $sufijo = $this->sufijoUnico();
        $proveedor = [
            'nombre' => 'ProveedorPrueba' . $this->sufijoLetrasAleatorias(),
            'correo' => 'test_' . $sufijo . '@gmail.com'
        ];
        $this->assertTrue(Validador::correoValido($proveedor['correo']));

        $this->modelo->crear(
            $proveedor['nombre'],
            'Distribuidor',
            'Contacto Prueba',
            '5550100123',
            $proveedor['correo'],
            'Direccion Prueba'
        );

        return $proveedor;
    }

    private function ultimoProveedor(string $nombre): int
    {
        $consulta = $this->pdo->prepare(
            'SELECT Id_Proveedor FROM proveedor WHERE Nombre = :nombre'
        );
        $consulta->execute(['nombre' => $nombre]);
        $id = (int) $consulta->fetchColumn();
        $this->assertTrue($id > 0, 'El proveedor de prueba debe haberse creado.');
        return $id;
    }

    private function obtenerReferenciasProducto(): array
    {
        $referencias = [
            'marca' => (int) $this->pdo->query(
                'SELECT Id_Marca FROM marcas ORDER BY Id_Marca LIMIT 1'
            )->fetchColumn(),
            'categoria' => (int) $this->pdo->query(
                'SELECT Id_Categoria FROM categoria ORDER BY Id_Categoria LIMIT 1'
            )->fetchColumn(),
            'unidad' => (int) $this->pdo->query(
                'SELECT Id_Unidad FROM unidad_medida ORDER BY Id_Unidad LIMIT 1'
            )->fetchColumn()
        ];

        foreach ($referencias as $nombre => $id) {
            $this->assertTrue($id > 0, 'Se requiere un registro de catálogo para ' . $nombre . '.');
        }

        return $referencias;
    }
}
