<?php

require_once BASE_PATH . '/app/models/Pedido.php';

class PedidoTest extends TestCase
{
    private $modelo;
    private $modeloProveedor;
    private $modeloProducto;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->modelo = new Pedido($pdo);
        $this->modeloProveedor = new Proveedor($pdo);
        $this->modeloProducto = new Producto($pdo);
    }

    public function ejecutar(): void
    {
        $this->probar('TEC-PED-01', 'Técnico', 'La lista trae solo pedidos Pendiente', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idPendiente = $this->crearPedido($proveedor['id'], 'Pendiente');
                $idRecibido = $this->crearPedido($proveedor['id'], 'Recibida');

                $pedidos = $this->modelo->listarPorRecibir();
                $ids = array_map('intval', array_column($pedidos, 'Id_Compras'));

                $this->assertTrue(in_array($idPendiente, $ids, true));
                $this->assertFalse(in_array($idRecibido, $ids, true));
                foreach ($pedidos as $pedido) {
                    $this->assertEquals('Pendiente', $pedido['Estado']);
                }
            });
        });

        $this->probar('TEC-PED-02', 'Técnico', 'Cancelar un Pendiente sí y un Recibido no', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idPendiente = $this->crearPedido($proveedor['id'], 'Pendiente');
                $idRecibido = $this->crearPedido($proveedor['id'], 'Recibida');

                $this->assertTrue($this->modelo->cancelar($idPendiente));
                $this->assertFalse($this->modelo->cancelar($idRecibido));
                $this->assertEquals('Cancelada', $this->obtenerEstadoPedido($idPendiente));
                $this->assertEquals('Recibida', $this->obtenerEstadoPedido($idRecibido));
            });
        });

        $this->probar('TEC-PED-03', 'Técnico', 'Guardar un pedido válido; los triggers llenan las copias históricas', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $producto = $this->crearProducto($proveedor['id']);
                $idPedido = $this->crearPedido($proveedor['id'], 'Pendiente', 25.00);
                $this->agregarDetalle($idPedido, $producto['id'], 2, 12.50);

                $consulta = $this->pdo->prepare(
                    'SELECT c.Nombre_Proveedor, d.Nombre_Producto
                     FROM compras c
                     INNER JOIN detalle_compras d ON d.Id_Compras = c.Id_Compras
                     WHERE c.Id_Compras = ?'
                );
                $consulta->execute([$idPedido]);
                $copias = $consulta->fetch();

                $this->assertEquals($proveedor['nombre'], $copias['Nombre_Proveedor']);
                $this->assertEquals($producto['nombre'], $copias['Nombre_Producto']);
            });
        });

        $this->probar('TEC-PED-04', 'Técnico', 'Rechazar cantidad cero, costo cero y pedido sin productos', function () {
            $this->enTransaccion(function () {
                $validacionCantidad = $this->modelo->validarValoresDetalle('0', '5.00');
                $this->assertTrue(in_array('La cantidad debe ser mayor a 0', $validacionCantidad['errores'], true));

                $validacionCosto = $this->modelo->validarValoresDetalle('1', '0');
                $this->assertTrue(in_array('El costo debe ser mayor a 0', $validacionCosto['errores'], true));

                $this->assertEquals(
                    ['Agrega al menos un producto'],
                    $this->modelo->validarProductosPedido([])
                );
                $this->assertEquals([], $this->modelo->validarProductosPedido([1]));
            });
        });

        $this->probar('TEC-PED-05', 'Técnico', 'Recibir completo aumenta el stock por la cantidad pedida', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $producto = $this->crearProducto($proveedor['id'], 4);
                $idPedido = $this->crearPedido($proveedor['id'], 'Pendiente', 30.00);
                $idDetalle = $this->agregarDetalle($idPedido, $producto['id'], 3, 10.00);
                $stockInicial = $this->obtenerExistencias($producto['id']);
                $idEmpleado = $this->obtenerIdEmpleado();

                $this->modelo->actualizarRecepcionDetalle(
                    $idPedido,
                    $idDetalle,
                    3,
                    'Ninguna',
                    null
                );
                $this->assertEquals(1, $this->modelo->marcarPedidoRecibido($idPedido, $idEmpleado));

                $this->assertEquals($stockInicial + 3, $this->obtenerExistencias($producto['id']));
                $this->assertEquals('Recibida', $this->obtenerEstadoPedido($idPedido));
            });
        });

        $this->probar('TEC-PED-06', 'Técnico', 'Recibir con daño aumenta solo la cantidad recibida y guarda el motivo', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $producto = $this->crearProducto($proveedor['id'], 2);
                $idPedido = $this->crearPedido($proveedor['id'], 'Pendiente', 50.00);
                $idDetalle = $this->agregarDetalle($idPedido, $producto['id'], 5, 10.00);
                $stockInicial = $this->obtenerExistencias($producto['id']);
                $idEmpleado = $this->obtenerIdEmpleado();
                $motivo = 'Llegó con una pieza dañada';

                $this->modelo->actualizarRecepcionDetalle(
                    $idPedido,
                    $idDetalle,
                    3,
                    'Dañado',
                    $motivo
                );
                $this->assertEquals(1, $this->modelo->marcarPedidoRecibido($idPedido, $idEmpleado));

                $this->assertEquals($stockInicial + 3, $this->obtenerExistencias($producto['id']));
                $consultaMovimiento = $this->pdo->prepare(
                    "SELECT Motivo
                     FROM movimiento_inventario
                     WHERE Id_Producto = ? AND Id_Empleado = ?
                       AND Motivo LIKE ? AND Tipo = 'Entrada'
                     ORDER BY Id_Movimiento DESC
                     LIMIT 1"
                );
                $consultaMovimiento->execute([
                    $producto['id'],
                    $idEmpleado,
                    '%Pedido #' . $idPedido . '%'
                ]);
                $motivoMovimiento = $consultaMovimiento->fetchColumn();

                $this->assertTrue(
                    is_string($motivoMovimiento) && strpos($motivoMovimiento, $motivo) !== false,
                    'El movimiento debe conservar el motivo reportado.'
                );
            });
        });

        $this->probar('TEC-PED-07', 'Técnico', 'Un pedido recibido ya no se encuentra en la búsqueda', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idPedido = $this->crearPedido($proveedor['id'], 'Recibida');

                $this->assertFalse($this->modelo->buscarPorNumero($idPedido));
                $this->assertEquals([], $this->modelo->buscarPorProveedor($proveedor['nombre']));
            });
        });

        $this->probar('TEC-PED-08', 'Técnico', 'El correo de pedido en modo prueba escribe en el log y lo restaura', function () {
            $this->enTransaccion(function () {
                $rutaLog = BASE_PATH . '/storage/logs/correos.log';
                $existia = file_exists($rutaLog);
                $contenidoOriginal = $existia ? file_get_contents($rutaLog) : '';
                if ($contenidoOriginal === false) {
                    throw new RuntimeException('No se pudo leer el log antes de la prueba.');
                }

                $modoExistia = array_key_exists('MAIL_MODO', $_ENV);
                $modoOriginal = $_ENV['MAIL_MODO'] ?? null;
                $_ENV['MAIL_MODO'] = 'prueba';

                try {
                    $correo = 'test' . $this->sufijoLetrasAleatorias() . '@example.com';
                    $proveedor = 'Proveedor Prueba ' . $this->sufijoLetrasAleatorias();
                    $resultado = (new Correo())->enviarPedido(
                        $correo,
                        $proveedor,
                        987654,
                        date('d/m/Y'),
                        [[
                            'producto' => 'Producto Prueba',
                            'cantidad' => 2,
                            'costo' => 10.00,
                            'subtotal' => 20.00
                        ]],
                        20.00
                    );
                    $contenido = file_get_contents($rutaLog);

                    $this->assertTrue($resultado);
                    $this->assertTrue(is_string($contenido) && strpos($contenido, $correo) !== false);
                    $this->assertTrue(strpos($contenido, 'Pedido #987654') !== false);
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

        $this->probar('TEC-PED-09', 'Técnico', 'Buscar pedidos por número con y sin prefijo #', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idPendiente = $this->crearPedido($proveedor['id'], 'Pendiente');
                $this->crearPedido($proveedor['id'], 'Recibida');

                $conPrefijo = $this->modelo->listarPedidos('#' . $idPendiente, 'Todos', 10, 0);
                $sinPrefijo = $this->modelo->listarPedidos((string) $idPendiente, 'Todos', 10, 0);

                $this->assertEquals(1, count($conPrefijo));
                $this->assertEquals($idPendiente, (int) $conPrefijo[0]['Id_Compras']);
                $this->assertEquals($idPendiente, (int) $sinPrefijo[0]['Id_Compras']);
            });
        });

        $this->probar('TEC-PED-10', 'Técnico', 'Buscar pedidos por nombre de proveedor', function () {
            $this->enTransaccion(function () {
                $proveedorCoincidente = $this->crearProveedor();
                $proveedorDiferente = $this->crearProveedor();
                $idCoincidente = $this->crearPedido($proveedorCoincidente['id'], 'Pendiente');
                $this->crearPedido($proveedorDiferente['id'], 'Pendiente');

                $pedidos = $this->modelo->listarPedidos(
                    substr($proveedorCoincidente['nombre'], -12),
                    'Pendiente',
                    10,
                    0
                );

                $this->assertEquals(1, count($pedidos));
                $this->assertEquals($idCoincidente, (int) $pedidos[0]['Id_Compras']);
                $this->assertEquals($proveedorCoincidente['nombre'], $pedidos[0]['Proveedor']);
            });
        });

        $this->probar('TEC-PED-11', 'Técnico', 'Filtrar pedidos por estado y consultar todos los estados', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idPendiente = $this->crearPedido($proveedor['id'], 'Pendiente');
                $idRecibido = $this->crearPedido($proveedor['id'], 'Recibida');
                $idCancelado = $this->crearPedido($proveedor['id'], 'Cancelada');

                $pendientes = $this->modelo->listarPedidos('', 'Pendiente', 10, 0);
                $recibidos = $this->modelo->listarPedidos('', 'Recibida', 10, 0);
                $cancelados = $this->modelo->listarPedidos('', 'Cancelada', 10, 0);
                $todos = $this->modelo->listarPedidos('', 'Todos', 10, 0);

                $this->assertTrue(in_array($idPendiente, array_map('intval', array_column($pendientes, 'Id_Compras')), true));
                $this->assertTrue(in_array($idRecibido, array_map('intval', array_column($recibidos, 'Id_Compras')), true));
                $this->assertTrue(in_array($idCancelado, array_map('intval', array_column($cancelados, 'Id_Compras')), true));
                $this->assertEquals(3, $this->modelo->contarPedidos($proveedor['nombre'], 'Todos'));
                $this->assertEquals(1, $this->modelo->contarPedidos($proveedor['nombre'], 'Pendiente'));
            });
        });

        $this->probar('TEC-PED-12', 'Técnico', 'Paginar la lista de pedidos en grupos de diez', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                for ($indice = 0; $indice < 11; $indice++) {
                    $this->crearPedido($proveedor['id'], 'Pendiente');
                }

                $busqueda = substr($proveedor['nombre'], -12);
                $primeraPagina = $this->modelo->listarPedidos($busqueda, 'Pendiente', 10, 0);
                $segundaPagina = $this->modelo->listarPedidos($busqueda, 'Pendiente', 10, 10);

                $this->assertEquals(11, $this->modelo->contarPedidos($busqueda, 'Pendiente'));
                $this->assertEquals(10, count($primeraPagina));
                $this->assertEquals(1, count($segundaPagina));
            });
        });

        $this->probar('TEC-PED-13', 'Técnico', 'Consultar el encabezado y renglones históricos del pedido', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $producto = $this->crearProducto($proveedor['id']);
                $idPedido = $this->crearPedido($proveedor['id'], 'Pendiente', 24.00);
                $this->agregarDetalle($idPedido, $producto['id'], 2, 12.00);

                $encabezado = $this->modelo->obtenerDetallePedido($idPedido);
                $detalles = $this->modelo->listarDetallesHistorialPedido($idPedido);

                $this->assertEquals($proveedor['nombre'], $encabezado['Proveedor']);
                $this->assertNotNull($encabezado['EmpleadoRegistro']);
                $this->assertEquals(1, count($detalles));
                $this->assertEquals($producto['nombre'], $detalles[0]['Producto']);
                $this->assertEquals(12.00, (float) $detalles[0]['Precio_Unitario']);
                $this->assertEquals(24.00, (float) $detalles[0]['Subtotal']);
            });
        });

        $this->probar('TEC-PED-14', 'Técnico', 'Buscar entradas por número, proveedor y producto', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $producto = $this->crearProducto($proveedor['id']);
                $idPendiente = $this->crearPedido($proveedor['id'], 'Pendiente', 18.00);
                $idRecibido = $this->crearPedido($proveedor['id'], 'Recibida', 18.00);
                $this->agregarDetalle($idPendiente, $producto['id'], 1, 18.00);
                $this->agregarDetalle($idRecibido, $producto['id'], 1, 18.00);

                $coincidenciasProveedor = $this->modelo->buscarPendientesPorTexto(
                    $proveedor['nombre'],
                    10,
                    0
                );
                $coincidenciasProducto = $this->modelo->buscarPendientesPorTexto(
                    $producto['nombre'],
                    10,
                    0
                );

                $this->assertEquals($idPendiente, (int) $this->modelo->buscarPorNumero($idPendiente)['Id_Compras']);
                $this->assertFalse($this->modelo->buscarPorNumero($idRecibido));
                $this->assertEquals(1, $this->modelo->contarPendientesPorTexto($proveedor['nombre']));
                $this->assertEquals($idPendiente, (int) $coincidenciasProveedor[0]['Id_Compras']);
                $this->assertEquals(1, $this->modelo->contarPendientesPorTexto($producto['nombre']));
                $this->assertEquals($idPendiente, (int) $coincidenciasProducto[0]['Id_Compras']);
            });
        });

        $this->probar('TEC-PED-15', 'Técnico', 'Listar y paginar únicamente pedidos pendientes de Entradas', function () {
            $this->enTransaccion(function () {
                $proveedor = $this->crearProveedor();
                $idsPendientes = [];
                for ($indice = 0; $indice < 11; $indice++) {
                    $idsPendientes[] = $this->crearPedido($proveedor['id'], 'Pendiente');
                }
                $idRecibido = $this->crearPedido($proveedor['id'], 'Recibida');

                $primeraPagina = $this->modelo->buscarPendientesPorTexto(
                    $proveedor['nombre'],
                    10,
                    0
                );
                $segundaPagina = $this->modelo->buscarPendientesPorTexto(
                    $proveedor['nombre'],
                    10,
                    10
                );
                $idsListaPendientes = array_map(
                    'intval',
                    array_column($this->modelo->listarPorRecibirPaginados(100, 0), 'Id_Compras')
                );

                $this->assertEquals(11, $this->modelo->contarPendientesPorTexto($proveedor['nombre']));
                $this->assertEquals(10, count($primeraPagina));
                $this->assertEquals(1, count($segundaPagina));
                $this->assertEquals($idsPendientes[0], (int) $primeraPagina[0]['Id_Compras']);
                $this->assertEquals($idsPendientes[10], (int) $segundaPagina[0]['Id_Compras']);
                $this->assertTrue(in_array($idsPendientes[0], $idsListaPendientes, true));
                $this->assertFalse(in_array($idRecibido, $idsListaPendientes, true));
                $this->assertTrue($this->modelo->contarPorRecibir() >= 11);
            });
        });
    }

    private function crearProveedor(): array
    {
        $sufijo = $this->sufijoLetrasAleatorias();
        $proveedor = [
            'nombre' => 'Proveedor Prueba ' . $sufijo,
            'correo' => 'test' . strtolower($sufijo) . '@example.com'
        ];

        $this->modeloProveedor->crear(
            $proveedor['nombre'],
            'Distribuidor',
            'Contacto Prueba',
            (string) random_int(1000000000, 9999999999),
            $proveedor['correo'],
            'Direccion Prueba'
        );

        $consulta = $this->pdo->prepare('SELECT Id_Proveedor FROM proveedor WHERE Nombre = ?');
        $consulta->execute([$proveedor['nombre']]);
        $proveedor['id'] = (int) $consulta->fetchColumn();
        $this->assertTrue($proveedor['id'] > 0, 'El proveedor de prueba debe crearse.');

        return $proveedor;
    }

    private function crearProducto(int $idProveedor, int $existencias = 0): array
    {
        $referencias = $this->obtenerReferenciasCatalogo();
        $sufijo = $this->sufijoLetrasAleatorias();
        $producto = [
            'nombre' => 'Producto Prueba ' . $sufijo,
            'codigo' => 'TEST' . $this->sufijoUnico()
        ];

        $this->modeloProducto->crear(
            $producto['codigo'],
            $producto['nombre'],
            $referencias['marca'],
            $referencias['categoria'],
            $idProveedor,
            $referencias['unidad'],
            20.00,
            $existencias,
            1
        );

        $consulta = $this->pdo->prepare('SELECT Id_Producto FROM productos WHERE Codigo_Barras = ?');
        $consulta->execute([$producto['codigo']]);
        $producto['id'] = (int) $consulta->fetchColumn();
        $this->assertTrue($producto['id'] > 0, 'El producto de prueba debe crearse.');

        return $producto;
    }

    private function crearPedido(int $idProveedor, string $estado, float $total = 0.00): int
    {
        $consulta = $this->pdo->prepare(
            'INSERT INTO compras (Total, Estado, Id_Proveedor, Id_Empleado)
             VALUES (?, ?, ?, ?)'
        );
        $consulta->execute([$total, $estado, $idProveedor, $this->obtenerIdEmpleado()]);

        return (int) $this->pdo->lastInsertId();
    }

    private function agregarDetalle(int $idPedido, int $idProducto, int $cantidad, float $costo): int
    {
        $consulta = $this->pdo->prepare(
            'INSERT INTO detalle_compras (Cantidad, Precio_Unitario, Total, Id_Compras, Id_Producto)
             VALUES (?, ?, ?, ?, ?)'
        );
        $consulta->execute([$cantidad, $costo, $cantidad * $costo, $idPedido, $idProducto]);

        return (int) $this->pdo->lastInsertId();
    }

    private function obtenerEstadoPedido(int $idPedido): string
    {
        $consulta = $this->pdo->prepare('SELECT Estado FROM compras WHERE Id_Compras = ?');
        $consulta->execute([$idPedido]);

        return (string) $consulta->fetchColumn();
    }

    private function obtenerExistencias(int $idProducto): int
    {
        $consulta = $this->pdo->prepare(
            'SELECT Existencias FROM inventario WHERE Id_Producto = ?'
        );
        $consulta->execute([$idProducto]);

        return (int) $consulta->fetchColumn();
    }

    private function obtenerIdEmpleado(): int
    {
        $idEmpleado = (int) $this->pdo
            ->query('SELECT Id_Empleado FROM empleados ORDER BY Id_Empleado LIMIT 1')
            ->fetchColumn();
        $this->assertTrue($idEmpleado > 0, 'Se requiere un empleado de referencia para las llaves foráneas.');

        return $idEmpleado;
    }

    private function obtenerReferenciasCatalogo(): array
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
