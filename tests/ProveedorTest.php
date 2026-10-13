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
                    'TEST_Contacto_Editado',
                    'TEST_5550101',
                    $primero['correo'],
                    'TEST_Direccion_Editada'
                );
                $editado = $this->modelo->obtenerPorId($idPrimero);
                $this->assertEquals($nombreEditado, $editado['Nombre']);
                $this->assertEquals('TEST_Contacto_Editado', $editado['Contacto']);
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
                    $nombre = 'TEST_ProductoProveedor_' . $sufijo;
                    $modeloProducto->crear(
                        $codigo,
                        $nombre,
                        $referencias['marca'],
                        $referencias['categoria'],
                        $idProveedor,
                        $referencias['unidad'],
                        50.00,
                        1,
                        0
                    );
                    $nombres[] = $nombre;
                }

                $this->assertEquals(2, (int) $this->modelo->contarProductosQueSurte($idProveedor));
                $productos = $this->modelo->obtenerProductosQueSurte($idProveedor, 'TEST_ProductoProveedor_');
                $this->assertEquals(2, count($productos));
                $this->assertTrue(in_array($nombres[0], array_column($productos, 'Nombre'), true));
                $this->assertTrue(in_array($nombres[1], array_column($productos, 'Nombre'), true));
            });
        });
    }

    private function crearProveedor(): array
    {
        $sufijo = $this->sufijoUnico();
        $proveedor = [
            'nombre' => 'TEST_Proveedor_' . $sufijo,
            'correo' => 'TEST_' . $sufijo . '@example.invalid'
        ];

        $this->modelo->crear(
            $proveedor['nombre'],
            'Distribuidor',
            'TEST_Contacto_' . $sufijo,
            'TEST_5550100',
            $proveedor['correo'],
            'TEST_Direccion_' . $sufijo
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

