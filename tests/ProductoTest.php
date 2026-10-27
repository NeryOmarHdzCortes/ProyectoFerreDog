<?php

class ProductoTest extends TestCase
{
    private $modelo;
    private $referencias;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->modelo = new Producto($pdo);
        $this->referencias = $this->obtenerReferencias();
    }

    public function ejecutar(): void
    {
        $this->probar('HU5-01', 'HU-5', 'Crear y editar producto con inventario', function () {
            $this->enTransaccion(function () {
                $producto = $this->crearProducto();
                $antes = $this->modelo->obtenerPorId($producto['id']);
                $this->assertEquals($producto['nombre'], $antes['Nombre']);
                $this->assertEquals(7, (int) $antes['Existencias']);

                $nuevoNombre = $producto['nombre'] . '_EDITADO';
                $nuevoCodigo = $producto['codigo'] . '_E';
                $this->modelo->editar(
                    $producto['id'],
                    $nuevoCodigo,
                    $nuevoNombre,
                    $this->referencias['marca'],
                    $this->referencias['categoria'],
                    $this->referencias['proveedor'],
                    $this->referencias['unidad'],
                    125.50,
                    3
                );

                $despues = $this->modelo->obtenerPorId($producto['id']);
                $this->assertEquals($nuevoNombre, $despues['Nombre']);
                $this->assertEquals($nuevoCodigo, $despues['Codigo_Barras']);
                $this->assertFalse($this->modelo->existeCodigo($nuevoCodigo, $producto['id']));
            });
        });

        $this->probar('HU5-02', 'HU-5', 'Código duplicado y búsqueda por nombre/código', function () {
            $this->enTransaccion(function () {
                $producto = $this->crearProducto();

                $this->assertTrue($this->modelo->existeCodigo($producto['codigo']));
                $this->assertFalse($this->modelo->existeCodigo($producto['codigo'] . '_OTRO'));
                $porNombre = $this->modelo->obtenerTodos($producto['nombre']);
                $porCodigo = $this->modelo->obtenerTodos($producto['codigo']);
                $this->assertTrue(count($porNombre) > 0);
                $this->assertTrue(count($porCodigo) > 0);
                $this->assertEquals($producto['id'], (int) $porNombre[0]['Id_Producto']);
                $this->assertEquals($producto['id'], (int) $porCodigo[0]['Id_Producto']);
            });
        });

        $this->probar('HU5-03', 'HU-5', 'Paginación limitada a un máximo de 10 productos', function () {
            $this->enTransaccion(function () {
                $prefijo = 'TESTPaginacion' . $this->sufijoLetrasAleatorias();
                for ($indice = 0; $indice < 12; $indice++) {
                    $this->crearProducto($prefijo . '_' . $indice, $prefijo . '_COD_' . $indice);
                }

                $pagina = $this->modelo->obtenerTodos($prefijo, 0, 0, '', 1, 10);
                $this->assertEquals(10, count($pagina));
            });
        });

        $this->probar('HU5-04', 'HU-5', 'Producto inexistente, precio inválido y desactivación', function () {
            $this->enTransaccion(function () {
                $this->assertFalse($this->modelo->obtenerPorId(-1));
                $producto = $this->crearProducto();

                foreach ([0, -1] as $precio) {
                    $error = $this->expectException(function () use ($producto, $precio) {
                        $this->modelo->asignarPrecio($producto['id'], $precio);
                    }, PDOException::class);
                    $this->assertEquals('23000', (string) $error->getCode());
                }

                $this->modelo->desactivar($producto['id']);
                $fila = $this->pdo->prepare('SELECT Estado FROM productos WHERE Id_Producto = :id');
                $fila->execute(['id' => $producto['id']]);
                $this->assertEquals(0, (int) $fila->fetchColumn());
            });
        });

        $this->probar('HU5-05', 'HU-5', 'Producto sin inventario muestra existencias cero', function () {
            $this->enTransaccion(function () {
                $producto = $this->crearProducto();
                $eliminarInventario = $this->pdo->prepare(
                    'DELETE FROM inventario WHERE Id_Producto = :id'
                );
                $eliminarInventario->execute(['id' => $producto['id']]);

                $porId = $this->modelo->obtenerPorId($producto['id']);
                $lista = $this->modelo->obtenerTodos($producto['nombre']);
                $this->assertEquals(0, (int) $porId['Existencias']);
                $this->assertEquals(0, (int) $lista[0]['Existencias']);
            });
        });

        $this->probar('HU5-06', 'HU-5', 'Stock mínimo cero es rechazado', function () {
            $this->enTransaccion(function () {
                $error = $this->expectException(function () {
                    $this->crearProducto(null, null, 0);
                }, PDOException::class);
                $this->assertEquals('23000', (string) $error->getCode());
            });
        });

        $this->probar('HU5-07', 'HU-5', 'Stock mínimo uno es aceptado', function () {
            $this->enTransaccion(function () {
                $producto = $this->crearProducto(null, null, 1);
                $consulta = $this->pdo->prepare(
                    'SELECT Stock_Minimo FROM productos WHERE Id_Producto = :id'
                );
                $consulta->execute(['id' => $producto['id']]);
                $this->assertEquals(1, (int) $consulta->fetchColumn());
            });
        });

        $this->probar('HU5-08', 'HU-5', 'Eliminar producto inactivo sin enlaces y rechazar activos', function () {
            $this->enTransaccion(function () {
                $sufijo = $this->sufijoLetrasAleatorias();
                $producto = $this->crearProducto('ProductoPrueba' . $sufijo, 'CodigoProducto' . $sufijo);

                $resultadoActivo = $this->modelo->eliminar($producto['id']);
                $this->assertEquals('activo', $resultadoActivo['resultado']);
                $this->assertNotNull($this->modelo->obtenerPorId($producto['id']));

                $this->modelo->desactivar($producto['id']);
                $resultadoInactivo = $this->modelo->eliminar($producto['id']);
                $this->assertEquals('borrado', $resultadoInactivo['resultado']);
                $this->assertFalse($this->modelo->obtenerPorId($producto['id']));
                $consultaInventario = $this->pdo->prepare(
                    'SELECT COUNT(*) FROM inventario WHERE Id_Producto = :id'
                );
                $consultaInventario->execute(['id' => $producto['id']]);
                $this->assertEquals(0, (int) $consultaInventario->fetchColumn());
            });
        });

        $this->probar('HU5-09', 'HU-5', 'Eliminar producto con historial y conservar los datos de venta', function () {
            $this->enTransaccion(function () {
                $sufijo = $this->sufijoLetrasAleatorias();
                $producto = $this->crearProducto('ProductoVenta' . $sufijo, 'CodigoVenta' . $sufijo);
                $idEmpleado = (int) $this->pdo
                    ->query('SELECT Id_Empleado FROM empleados ORDER BY Id_Empleado LIMIT 1')
                    ->fetchColumn();
                $idMetodoPago = (int) $this->pdo
                    ->query('SELECT Id_Metodo_Pago FROM metodo_pago ORDER BY Id_Metodo_Pago LIMIT 1')
                    ->fetchColumn();
                $this->assertTrue($idEmpleado > 0, 'Se requiere un empleado para la venta.');
                $this->assertTrue($idMetodoPago > 0, 'Se requiere un método de pago para la venta.');

                $insertarVenta = $this->pdo->prepare(
                    "INSERT INTO ventas (Total, Estado, Id_Metodo_Pago, Id_Empleado)
                     VALUES (1, 'Completada', :metodo_pago, :empleado)"
                );
                $insertarVenta->execute([
                    'metodo_pago' => $idMetodoPago,
                    'empleado' => $idEmpleado
                ]);
                $idVenta = (int) $this->pdo->lastInsertId();
                $insertarDetalle = $this->pdo->prepare(
                    'INSERT INTO detalle_ventas
                     (Cantidad, Precio_Unitario, Descuento, Subtotal, Id_Ventas, Id_Producto)
                     VALUES (1, 1, 0, 1, :venta, :producto)'
                );
                $insertarDetalle->execute([
                    'venta' => $idVenta,
                    'producto' => $producto['id']
                ]);

                $this->modelo->desactivar($producto['id']);
                $resultado = $this->modelo->eliminar($producto['id']);
                $this->assertEquals('borrado', $resultado['resultado']);
                $this->assertFalse($this->modelo->obtenerPorId($producto['id']));

                foreach (['Activo', 'Inactivo', ''] as $estado) {
                    $this->assertEquals(
                        0,
                        $this->modelo->contarFiltrados($producto['nombre'], 0, 0, $estado)
                    );
                    $this->assertEquals(
                        [],
                        $this->modelo->obtenerTodos($producto['nombre'], 0, 0, $estado)
                    );
                }

                $consultaDetalle = $this->pdo->prepare(
                    'SELECT Id_Producto, Nombre_Producto
                     FROM detalle_ventas WHERE Id_Ventas = :venta'
                );
                $consultaDetalle->execute(['venta' => $idVenta]);
                $detalle = $consultaDetalle->fetch(PDO::FETCH_ASSOC);
                $this->assertNull($detalle['Id_Producto']);
                $this->assertEquals($producto['nombre'], $detalle['Nombre_Producto']);
            });
        });

        $this->probar('HU5-10', 'HU-5', 'Guardar y editar el precio de compra del producto', function () {
            $this->enTransaccion(function () {
                $producto = $this->crearProducto(null, null, 2, 12.35);
                $guardado = $this->modelo->obtenerPorId($producto['id']);
                $this->assertEquals(12.35, (float) $guardado['Precio_Compra']);

                $this->modelo->editar(
                    $producto['id'],
                    $producto['codigo'] . '_E',
                    $producto['nombre'] . '_EDITADO',
                    $this->referencias['marca'],
                    $this->referencias['categoria'],
                    $this->referencias['proveedor'],
                    $this->referencias['unidad'],
                    125.50,
                    3,
                    18.75
                );
                $editado = $this->modelo->obtenerPorId($producto['id']);
                $this->assertEquals(18.75, (float) $editado['Precio_Compra']);
            });
        });

        $this->probar('HU5-11', 'HU-5', 'El Almacenista no puede cambiar el precio de compra', function () {
            $this->enTransaccion(function () {
                $producto = $this->crearProducto(null, null, 2, 12.50);
                $actual = $this->modelo->obtenerPorId($producto['id']);
                $resultado = Producto::resolverPrecioCompra(
                    'Almacenista',
                    '999.99',
                    $actual
                );
                $this->assertEquals(12.50, (float) $resultado['precio']);
                $this->assertNull($resultado['error']);

                $this->modelo->editar(
                    $producto['id'],
                    $producto['codigo'],
                    $producto['nombre'],
                    $this->referencias['marca'],
                    $this->referencias['categoria'],
                    $this->referencias['proveedor'],
                    $this->referencias['unidad'],
                    100.00,
                    2,
                    $resultado['precio']
                );
                $sinCambios = $this->modelo->obtenerPorId($producto['id']);
                $this->assertEquals(12.50, (float) $sinCambios['Precio_Compra']);

                $resultadoNegativo = Producto::resolverPrecioCompra(
                    'Administrador',
                    '-1',
                    null
                );
                $this->assertEquals(
                    'El precio de compra no puede ser negativo',
                    $resultadoNegativo['error']
                );
            });
        });
    }

    private function crearProducto(
        ?string $nombre = null,
        ?string $codigo = null,
        int $stockMinimo = 2,
        ?float $precioCompra = null
    ): array
    {
        $sufijo = $this->sufijoUnico();
        $producto = [
            'nombre' => $nombre ?? 'TESTProducto' . $this->sufijoLetrasAleatorias(),
            'codigo' => $codigo ?? 'TEST_Codigo_' . $sufijo
        ];

        $this->modelo->crear(
            $producto['codigo'],
            $producto['nombre'],
            $this->referencias['marca'],
            $this->referencias['categoria'],
            $this->referencias['proveedor'],
            $this->referencias['unidad'],
            100.00,
            7,
            $stockMinimo,
            $precioCompra
        );

        $consulta = $this->pdo->prepare(
            'SELECT Id_Producto FROM productos WHERE Codigo_Barras = :codigo'
        );
        $consulta->execute(['codigo' => $producto['codigo']]);
        $producto['id'] = (int) $consulta->fetchColumn();
        $this->assertTrue($producto['id'] > 0, 'El producto de prueba debe haberse creado.');

        return $producto;
    }

    private function obtenerReferencias(): array
    {
        $tablas = [
            'marca' => ['marcas', 'Id_Marca'],
            'categoria' => ['categoria', 'Id_Categoria'],
            'proveedor' => ['proveedor', 'Id_Proveedor'],
            'unidad' => ['unidad_medida', 'Id_Unidad']
        ];
        $referencias = [];

        foreach ($tablas as $nombre => $referencia) {
            list($tabla, $columna) = $referencia;
            $referencias[$nombre] = (int) $this->pdo
                ->query("SELECT {$columna} FROM {$tabla} ORDER BY {$columna} LIMIT 1")
                ->fetchColumn();
            $this->assertTrue($referencias[$nombre] > 0, 'Se requiere al menos una fila en ' . $tabla . '.');
        }

        return $referencias;
    }
}
