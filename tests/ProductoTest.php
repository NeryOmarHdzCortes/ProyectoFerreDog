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
                $prefijo = 'TEST_Paginacion_' . $this->sufijoUnico();
                for ($indice = 0; $indice < 12; $indice++) {
                    $this->crearProducto($prefijo . '_' . $indice, $prefijo . '_COD_' . $indice);
                }

                $pagina = $this->modelo->obtenerTodos($prefijo, 0, 0, '', 1, 10);
                $this->assertEquals(10, count($pagina));
            });
        });

        $this->probar('HU5-04', 'HU-5', 'Producto inexistente, precio inválido, eliminación y restauración', function () {
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
                $this->assertEquals([], $this->modelo->obtenerTodos($producto['nombre']));

                $this->modelo->restaurar($producto['id']);
                $restaurado = $this->modelo->obtenerTodos($producto['nombre']);
                $this->assertEquals($producto['id'], (int) $restaurado[0]['Id_Producto']);
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
    }

    private function crearProducto(
        ?string $nombre = null,
        ?string $codigo = null,
        int $stockMinimo = 2
    ): array
    {
        $sufijo = $this->sufijoUnico();
        $producto = [
            'nombre' => $nombre ?? 'TEST_Producto_' . $sufijo,
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
            $stockMinimo
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
