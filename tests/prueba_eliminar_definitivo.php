<?php

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Proveedor.php';

$modeloProducto = new Producto($pdo);
$modeloProveedor = new Proveedor($pdo);
$resultados = [];
$proveedorNombre = 'Proveedor Prueba Borrado ' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
$codigoProductoA = 'CODIGOPRUEBAA' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
$codigoProductoB = 'CODIGOPRUEBAB' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
$codigoProductoC = 'CODIGOPRUEBAC' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
$nombreProductoA = 'Producto Prueba Borrado A ' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
$nombreProductoB = 'Producto Prueba Borrado B ' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
$nombreProductoC = 'Producto Prueba Borrado C ' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
$idProveedor = null;
$idProductoA = null;
$idProductoB = null;
$idProductoC = null;
$idCompra = null;
$idVenta = null;
$idEmpleado = null;
$idMetodoPago = null;
$preparacionCompleta = false;

$registrarResultado = function (int $numero, string $descripcion, callable $prueba) use (&$resultados): void {
    try {
        $detalle = $prueba();
        $resultados[] = [
            'numero' => $numero,
            'descripcion' => $descripcion,
            'resultado' => 'OK',
            'detalle' => (string) $detalle
        ];
    } catch (Throwable $error) {
        $resultados[] = [
            'numero' => $numero,
            'descripcion' => $descripcion,
            'resultado' => 'FALLA',
            'detalle' => $error->getMessage()
        ];
    }
};

$asegurar = function (bool $condicion, string $detalle): void {
    if (!$condicion) {
        throw new RuntimeException($detalle);
    }
};

$crearProducto = function (
    string $codigo,
    string $nombre,
    int $idProveedor,
    array $referencias
) use ($modeloProducto): int {
    $modeloProducto->crear(
        $codigo,
        $nombre,
        $referencias['marca'],
        $referencias['categoria'],
        $idProveedor,
        $referencias['unidad'],
        50.00,
        20,
        1
    );

    $consulta = $GLOBALS['pdo']->prepare(
        'SELECT Id_Producto FROM productos WHERE Codigo_Barras = :codigo'
    );
    $consulta->execute(['codigo' => $codigo]);
    $id = (int) $consulta->fetchColumn();
    if ($id <= 0) {
        throw new RuntimeException('No se pudo recuperar el producto recién creado: ' . $nombre);
    }
    return $id;
};

try {
    $registrarResultado(1, 'Crear proveedor, productos A y B, e inventario de 20 unidades', function () use (
        $pdo,
        $modeloProveedor,
        $proveedorNombre,
        $codigoProductoA,
        $codigoProductoB,
        $nombreProductoA,
        $nombreProductoB,
        &$idProveedor,
        &$idProductoA,
        &$idProductoB,
        $crearProducto,
        $asegurar
    ): string {
        $modeloProveedor->crear(
            $proveedorNombre,
            'Distribuidor',
            'Contacto Prueba',
            '5550100123',
            'prueba' . strtolower(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8)) . '@example.com',
            'Direccion Prueba'
        );

        $consultaProveedor = $pdo->prepare(
            'SELECT Id_Proveedor FROM proveedor WHERE Nombre = :nombre'
        );
        $consultaProveedor->execute(['nombre' => $proveedorNombre]);
        $idProveedor = (int) $consultaProveedor->fetchColumn();
        $asegurar($idProveedor > 0, 'No se encontró el proveedor de prueba creado.');

        $referencias = [
            'marca' => (int) $pdo->query('SELECT Id_Marca FROM marcas ORDER BY Id_Marca LIMIT 1')->fetchColumn(),
            'categoria' => (int) $pdo->query('SELECT Id_Categoria FROM categoria ORDER BY Id_Categoria LIMIT 1')->fetchColumn(),
            'unidad' => (int) $pdo->query('SELECT Id_Unidad FROM unidad_medida ORDER BY Id_Unidad LIMIT 1')->fetchColumn()
        ];
        $asegurar(
            $referencias['marca'] > 0 && $referencias['categoria'] > 0 && $referencias['unidad'] > 0,
            'Se requiere una marca, categoría y unidad de medida existentes.'
        );

        $idProductoA = $crearProducto($codigoProductoA, $nombreProductoA, $idProveedor, $referencias);
        $idProductoB = $crearProducto($codigoProductoB, $nombreProductoB, $idProveedor, $referencias);

        $consultaInventario = $pdo->prepare(
            'SELECT Existencias FROM inventario WHERE Id_Producto = :id'
        );
        foreach ([$idProductoA, $idProductoB] as $idProducto) {
            $consultaInventario->execute(['id' => $idProducto]);
            $asegurar((int) $consultaInventario->fetchColumn() === 20, 'El inventario inicial debe ser 20.');
        }

        return 'Proveedor y dos productos de prueba creados; ambos tienen 20 unidades.';
    });

    $registrarResultado(2, 'Crear compra y venta con detalle del producto B', function () use (
        $pdo,
        $idProveedor,
        $idProductoB,
        &$idCompra,
        &$idVenta,
        &$idEmpleado,
        &$idMetodoPago,
        $asegurar
    ): string {
        $asegurar($idProveedor !== null && $idProductoB !== null, 'No se completó la preparación de proveedor y producto B.');

        $idEmpleado = (int) $pdo->query(
            'SELECT Id_Empleado FROM empleados ORDER BY Id_Empleado LIMIT 1'
        )->fetchColumn();
        $idMetodoPago = (int) $pdo->query(
            'SELECT Id_Metodo_Pago FROM metodo_pago ORDER BY Id_Metodo_Pago LIMIT 1'
        )->fetchColumn();
        $asegurar($idEmpleado > 0, 'No existe un empleado para asociar a la prueba.');
        $asegurar($idMetodoPago > 0, 'No existe un método de pago para asociar a la venta.');

        $insertarCompra = $pdo->prepare(
            "INSERT INTO compras (Total, Estado, Id_Proveedor, Id_Empleado)
             VALUES (10.00, 'Recibida', :proveedor, :empleado)"
        );
        $insertarCompra->execute([
            'proveedor' => $idProveedor,
            'empleado' => $idEmpleado
        ]);
        $idCompra = (int) $pdo->lastInsertId();

        $insertarDetalleCompra = $pdo->prepare(
            'INSERT INTO detalle_compras (Cantidad, Precio_Unitario, Total, Id_Compras, Id_Producto)
             VALUES (1, 10.00, 10.00, :compra, :producto)'
        );
        $insertarDetalleCompra->execute([
            'compra' => $idCompra,
            'producto' => $idProductoB
        ]);

        $insertarVenta = $pdo->prepare(
            "INSERT INTO ventas (Total, Estado, Id_Metodo_Pago, Id_Empleado)
             VALUES (100.00, 'Completada', :metodo_pago, :empleado)"
        );
        $insertarVenta->execute([
            'metodo_pago' => $idMetodoPago,
            'empleado' => $idEmpleado
        ]);
        $idVenta = (int) $pdo->lastInsertId();

        $insertarDetalleVenta = $pdo->prepare(
            'INSERT INTO detalle_ventas
             (Cantidad, Precio_Unitario, Descuento, Subtotal, Id_Ventas, Id_Producto)
             VALUES (2, 50.00, 0, 100.00, :venta, :producto)'
        );
        $insertarDetalleVenta->execute([
            'venta' => $idVenta,
            'producto' => $idProductoB
        ]);

        return 'Compra ' . $idCompra . ' y venta ' . $idVenta . ' creadas con detalle del producto B.';
    });

    $registrarResultado(3, 'Comprobar snapshots llenados por los triggers', function () use (
        $pdo,
        $idCompra,
        $idVenta,
        $nombreProductoB,
        $proveedorNombre,
        $asegurar
    ): string {
        $asegurar($idCompra !== null && $idVenta !== null, 'No se crearon la compra y la venta de prueba.');

        $consultaVenta = $pdo->prepare(
            'SELECT Nombre_Producto FROM detalle_ventas WHERE Id_Ventas = :id_venta'
        );
        $consultaVenta->execute(['id_venta' => $idVenta]);
        $nombreEnVenta = $consultaVenta->fetchColumn();
        $asegurar($nombreEnVenta === $nombreProductoB, 'El trigger no llenó detalle_ventas.Nombre_Producto.');

        $consultaCompraDetalle = $pdo->prepare(
            'SELECT Nombre_Producto FROM detalle_compras WHERE Id_Compras = :id_compra'
        );
        $consultaCompraDetalle->execute(['id_compra' => $idCompra]);
        $nombreEnCompra = $consultaCompraDetalle->fetchColumn();
        $asegurar($nombreEnCompra === $nombreProductoB, 'El trigger no llenó detalle_compras.Nombre_Producto.');

        $consultaProveedor = $pdo->prepare(
            'SELECT Nombre_Proveedor FROM compras WHERE Id_Compras = :id_compra'
        );
        $consultaProveedor->execute(['id_compra' => $idCompra]);
        $nombreProveedorEnCompra = $consultaProveedor->fetchColumn();
        $asegurar($nombreProveedorEnCompra === $proveedorNombre, 'El trigger no llenó compras.Nombre_Proveedor.');

        return 'Los dos detalles guardaron el nombre del producto y la compra guardó el del proveedor.';
    });

    $registrarResultado(4, 'Desactivar y eliminar A sin historial', function () use (
        $pdo,
        $modeloProducto,
        $idProductoA,
        $asegurar
    ): string {
        $asegurar($idProductoA !== null, 'No se creó el producto A.');
        $modeloProducto->desactivar($idProductoA);
        $resultado = $modeloProducto->eliminar($idProductoA);
        $asegurar($resultado['resultado'] === 'borrado', 'El modelo no confirmó el borrado del producto A.');

        $consultaProducto = $pdo->prepare('SELECT COUNT(*) FROM productos WHERE Id_Producto = :id');
        $consultaProducto->execute(['id' => $idProductoA]);
        $asegurar((int) $consultaProducto->fetchColumn() === 0, 'El producto A todavía existe.');

        $consultaInventario = $pdo->prepare('SELECT COUNT(*) FROM inventario WHERE Id_Producto = :id');
        $consultaInventario->execute(['id' => $idProductoA]);
        $asegurar((int) $consultaInventario->fetchColumn() === 0, 'El inventario del producto A todavía existe.');

        return 'El producto A y su inventario ya no existen.';
    });

    $registrarResultado(5, 'Desactivar y eliminar B conservando detalles y movimientos', function () use (
        $pdo,
        $modeloProducto,
        $idProductoB,
        $nombreProductoB,
        $asegurar
    ): string {
        $asegurar($idProductoB !== null, 'No se creó el producto B.');
        $modeloProducto->desactivar($idProductoB);
        $resultado = $modeloProducto->eliminar($idProductoB);
        $asegurar($resultado['resultado'] === 'borrado', 'El modelo no confirmó el borrado del producto B.');

        foreach (['productos' => 'Id_Producto', 'inventario' => 'Id_Producto'] as $tabla => $columna) {
            $consulta = $pdo->prepare("SELECT COUNT(*) FROM {$tabla} WHERE {$columna} = :id");
            $consulta->execute(['id' => $idProductoB]);
            $asegurar((int) $consulta->fetchColumn() === 0, $tabla . ' todavía contiene el producto B.');
        }

        foreach (['detalle_ventas', 'detalle_compras'] as $tabla) {
            $consulta = $pdo->prepare(
                "SELECT Id_Producto, Nombre_Producto FROM {$tabla} WHERE Nombre_Producto = :nombre"
            );
            $consulta->execute(['nombre' => $nombreProductoB]);
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            $asegurar(is_array($fila), 'No se conservó la fila en ' . $tabla . '.');
            $asegurar($fila['Id_Producto'] === null, 'La referencia del producto no quedó NULL en ' . $tabla . '.');
            $asegurar($fila['Nombre_Producto'] === $nombreProductoB, 'El nombre histórico no se conservó en ' . $tabla . '.');
        }

        $consultaMovimientos = $pdo->prepare(
            'SELECT Id_Producto, Nombre_Producto
             FROM movimiento_inventario
             WHERE Nombre_Producto = :nombre'
        );
        $consultaMovimientos->execute(['nombre' => $nombreProductoB]);
        $movimientos = $consultaMovimientos->fetchAll(PDO::FETCH_ASSOC);
        $asegurar(count($movimientos) > 0, 'No se encontraron movimientos generados para el producto B.');
        foreach ($movimientos as $movimiento) {
            $asegurar($movimiento['Id_Producto'] === null, 'Un movimiento todavía referencia al producto B.');
            $asegurar(
                is_string($movimiento['Nombre_Producto']) && $movimiento['Nombre_Producto'] !== '',
                'Un movimiento no conserva el nombre del producto B.'
            );
        }

        return 'B e inventario fueron borrados; detalles y ' . count($movimientos) . ' movimientos conservan el nombre.';
    });

    $registrarResultado(6, 'Eliminar el proveedor y conservar la compra', function () use (
        $pdo,
        $modeloProveedor,
        $modeloProducto,
        $proveedorNombre,
        $codigoProductoC,
        $nombreProductoC,
        &$idProductoC,
        $idProveedor,
        $idEmpleado,
        $idCompra,
        $asegurar,
        $crearProducto
    ): string {
        $asegurar($idProveedor !== null && $idCompra !== null, 'No se creó el proveedor o la compra.');
        $referencias = [
            'marca' => (int) $pdo->query('SELECT Id_Marca FROM marcas ORDER BY Id_Marca LIMIT 1')->fetchColumn(),
            'categoria' => (int) $pdo->query('SELECT Id_Categoria FROM categoria ORDER BY Id_Categoria LIMIT 1')->fetchColumn(),
            'unidad' => (int) $pdo->query('SELECT Id_Unidad FROM unidad_medida ORDER BY Id_Unidad LIMIT 1')->fetchColumn()
        ];
        $idProductoC = $crearProducto($codigoProductoC, $nombreProductoC, $idProveedor, $referencias);

        $consultaProductoC = $pdo->prepare(
            'SELECT Estado, Id_Proveedor FROM productos WHERE Id_Producto = :id'
        );
        $consultaProductoC->execute(['id' => $idProductoC]);
        $productoC = $consultaProductoC->fetch(PDO::FETCH_ASSOC);
        $asegurar((int) $productoC['Estado'] === 1, 'El producto C debe estar activo para comprobar el rechazo.');

        $resultadoActivo = $modeloProducto->eliminar($idProductoC);
        $asegurar($resultadoActivo['resultado'] === 'activo', 'El modelo permitió eliminar el producto C activo.');

        $modeloProveedor->desactivar($idProveedor);
        $resultado = $modeloProveedor->eliminar($idProveedor);
        $asegurar($resultado['resultado'] === 'borrado', 'El modelo no confirmó el borrado del proveedor.');

        $consultaProveedor = $pdo->prepare('SELECT COUNT(*) FROM proveedor WHERE Id_Proveedor = :id');
        $consultaProveedor->execute(['id' => $idProveedor]);
        $asegurar((int) $consultaProveedor->fetchColumn() === 0, 'El proveedor todavía existe.');

        $consultaProductoC->execute(['id' => $idProductoC]);
        $productoC = $consultaProductoC->fetch(PDO::FETCH_ASSOC);
        $asegurar($productoC !== false && $productoC['Id_Proveedor'] === null, 'El producto C no quedó sin proveedor.');

        $consultaCompra = $pdo->prepare(
            'SELECT Id_Proveedor, Nombre_Proveedor FROM compras WHERE Id_Compras = :id'
        );
        $consultaCompra->execute(['id' => $idCompra]);
        $compra = $consultaCompra->fetch(PDO::FETCH_ASSOC);
        $asegurar($compra !== false, 'La compra de prueba ya no existe.');
        $asegurar($compra['Id_Proveedor'] === null, 'La compra no quedó con Id_Proveedor NULL.');
        $asegurar($compra['Nombre_Proveedor'] === $proveedorNombre, 'La compra no conserva Nombre_Proveedor.');

        return 'Proveedor borrado; producto C y compra conservan sus datos con Id_Proveedor NULL.';
    });

    $registrarResultado(7, 'Consultar historial con LEFT JOIN y COALESCE (equivalente; no hay método de modelo)', function () use (
        $pdo,
        $idCompra,
        $idVenta,
        $nombreProductoB,
        $proveedorNombre,
        $asegurar
    ): string {
        $asegurar($idCompra !== null && $idVenta !== null, 'No existen las cabeceras de prueba.');

        $consultaVenta = $pdo->prepare(
            'SELECT COALESCE(p.Nombre, dv.Nombre_Producto) AS Nombre_Producto,
                    p.Codigo_Barras
             FROM detalle_ventas dv
             LEFT JOIN productos p ON p.Id_Producto = dv.Id_Producto
             WHERE dv.Id_Ventas = :id_venta'
        );
        $consultaVenta->execute(['id_venta' => $idVenta]);
        $venta = $consultaVenta->fetch(PDO::FETCH_ASSOC);
        $asegurar($venta !== false && $venta['Nombre_Producto'] === $nombreProductoB, 'La consulta equivalente del historial de ventas no devolvió el nombre.');

        $consultaCompra = $pdo->prepare(
            'SELECT COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Nombre_Proveedor,
                    COALESCE(p.Nombre, dc.Nombre_Producto) AS Nombre_Producto,
                    p.Codigo_Barras
             FROM compras c
             LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
             LEFT JOIN detalle_compras dc ON dc.Id_Compras = c.Id_Compras
             LEFT JOIN productos p ON p.Id_Producto = dc.Id_Producto
             WHERE c.Id_Compras = :id_compra'
        );
        $consultaCompra->execute(['id_compra' => $idCompra]);
        $compra = $consultaCompra->fetch(PDO::FETCH_ASSOC);
        $asegurar($compra !== false && $compra['Nombre_Proveedor'] === $proveedorNombre, 'La consulta equivalente del historial no devolvió el nombre del proveedor.');
        $asegurar($compra['Nombre_Producto'] === $nombreProductoB, 'La consulta equivalente del historial de compras no devolvió el nombre del producto.');

        return 'La aplicación no tiene métodos de historial; consultas SQL equivalentes devolvieron los nombres capturados y el código directamente desde productos.';
    });

    $registrarResultado(8, 'Comprobar que no haya productos ni proveedores con Estado 2', function () use (
        $pdo,
        $asegurar
    ): string {
        $cantidadProductos = (int) $pdo->query('SELECT COUNT(*) FROM productos WHERE Estado = 2')->fetchColumn();
        $cantidadProveedores = (int) $pdo->query('SELECT COUNT(*) FROM proveedor WHERE Estado = 2')->fetchColumn();
        $asegurar(
            $cantidadProductos === 0 && $cantidadProveedores === 0,
            'Se encontraron productos Estado 2: ' . $cantidadProductos . '; proveedores Estado 2: ' . $cantidadProveedores . '.'
        );
        return 'No hay productos ni proveedores con Estado 2.';
    });

    $registrarResultado(9, 'Rechazar la eliminación de un producto activo', function () use (
        $pdo,
        $modeloProducto,
        $idProductoC,
        $asegurar
    ): string {
        $asegurar($idProductoC !== null, 'No se creó el producto C.');
        $resultado = $modeloProducto->eliminar($idProductoC);
        $asegurar($resultado['resultado'] === 'activo', 'El modelo no rechazó el producto activo.');

        $consulta = $pdo->prepare('SELECT COUNT(*) FROM productos WHERE Id_Producto = :id AND Estado = 1');
        $consulta->execute(['id' => $idProductoC]);
        $asegurar((int) $consulta->fetchColumn() === 1, 'El producto C activo dejó de existir o cambió de estado.');
        return 'El modelo devolvió activo y conservó el producto C.';
    });

    $preparacionCompleta = true;
} finally {
    $erroresLimpieza = [];
    $intentarLimpieza = function (string $descripcion, callable $accion) use (&$erroresLimpieza): void {
        try {
            $accion();
        } catch (Throwable $error) {
            $erroresLimpieza[] = $descripcion . ': ' . $error->getMessage();
        }
    };

    $obtenerIdsProducto = function () use (
        $pdo,
        $codigoProductoA,
        $codigoProductoB,
        $codigoProductoC,
        $idProductoA,
        $idProductoB,
        $idProductoC
    ): array {
        $ids = array_filter([$idProductoA, $idProductoB, $idProductoC], function ($id): bool {
            return $id !== null;
        });
        $consulta = $pdo->prepare(
            'SELECT Id_Producto FROM productos WHERE Codigo_Barras IN (:codigo_a, :codigo_b, :codigo_c)'
        );
        $consulta->execute([
            'codigo_a' => $codigoProductoA,
            'codigo_b' => $codigoProductoB,
            'codigo_c' => $codigoProductoC
        ]);
        return array_values(array_unique(array_merge($ids, array_map('intval', $consulta->fetchAll(PDO::FETCH_COLUMN)))));
    };

    $intentarLimpieza('Eliminar movimientos de prueba', function () use (
        $pdo,
        $nombreProductoA,
        $nombreProductoB,
        $nombreProductoC,
        $obtenerIdsProducto,
        &$erroresLimpieza
    ): void {
        $ids = $obtenerIdsProducto();
        $consulta = $pdo->prepare(
            'DELETE FROM movimiento_inventario
             WHERE Nombre_Producto IN (:nombre_a, :nombre_b, :nombre_c)'
        );
        $consulta->execute([
            'nombre_a' => $nombreProductoA,
            'nombre_b' => $nombreProductoB,
            'nombre_c' => $nombreProductoC
        ]);
        foreach ($ids as $id) {
            $borrarPorId = $pdo->prepare('DELETE FROM movimiento_inventario WHERE Id_Producto = :id');
            $borrarPorId->execute(['id' => $id]);
        }
    });

    if ($idVenta !== null) {
        $intentarLimpieza('Eliminar detalles de venta de prueba', function () use ($pdo, $idVenta): void {
            $consulta = $pdo->prepare('DELETE FROM detalle_ventas WHERE Id_Ventas = :id');
            $consulta->execute(['id' => $idVenta]);
        });
        $intentarLimpieza('Eliminar venta de prueba', function () use ($pdo, $idVenta): void {
            $consulta = $pdo->prepare('DELETE FROM ventas WHERE Id_Ventas = :id');
            $consulta->execute(['id' => $idVenta]);
        });
    }

    if ($idCompra !== null) {
        $intentarLimpieza('Eliminar detalles de compra de prueba', function () use ($pdo, $idCompra): void {
            $consulta = $pdo->prepare('DELETE FROM detalle_compras WHERE Id_Compras = :id');
            $consulta->execute(['id' => $idCompra]);
        });
        $intentarLimpieza('Eliminar compra de prueba', function () use ($pdo, $idCompra): void {
            $consulta = $pdo->prepare('DELETE FROM compras WHERE Id_Compras = :id');
            $consulta->execute(['id' => $idCompra]);
        });
    }

    $intentarLimpieza('Eliminar inventario y productos de prueba', function () use ($pdo, $obtenerIdsProducto): void {
        $ids = $obtenerIdsProducto();
        foreach ($ids as $id) {
            $inventario = $pdo->prepare('DELETE FROM inventario WHERE Id_Producto = :id');
            $inventario->execute(['id' => $id]);
            $producto = $pdo->prepare('DELETE FROM productos WHERE Id_Producto = :id');
            $producto->execute(['id' => $id]);
        }
    });

    $intentarLimpieza('Eliminar proveedor de prueba', function () use ($pdo, $proveedorNombre, $idProveedor): void {
        $consulta = $pdo->prepare(
            'DELETE FROM proveedor WHERE Nombre = :nombre OR Id_Proveedor = :id'
        );
        $consulta->execute([
            'nombre' => $proveedorNombre,
            'id' => $idProveedor
        ]);
    });

    $intentarLimpieza('Verificar limpieza completa de los datos de prueba', function () use (
        $pdo,
        $proveedorNombre,
        $codigoProductoA,
        $codigoProductoB,
        $codigoProductoC,
        $nombreProductoA,
        $nombreProductoB,
        $nombreProductoC,
        $idProductoA,
        $idProductoB,
        $idProductoC,
        $idCompra,
        $idVenta,
        $asegurar
    ): void {
        $consultaProductos = $pdo->prepare(
            'SELECT COUNT(*) FROM productos
             WHERE Codigo_Barras IN (:codigo_a, :codigo_b, :codigo_c)
                OR Nombre IN (:nombre_a, :nombre_b, :nombre_c)'
        );
        $consultaProductos->execute([
            'codigo_a' => $codigoProductoA,
            'codigo_b' => $codigoProductoB,
            'codigo_c' => $codigoProductoC,
            'nombre_a' => $nombreProductoA,
            'nombre_b' => $nombreProductoB,
            'nombre_c' => $nombreProductoC
        ]);
        $asegurar((int) $consultaProductos->fetchColumn() === 0, 'Quedó algún producto de prueba.');

        $consultaInventario = $pdo->prepare('SELECT COUNT(*) FROM inventario WHERE Id_Producto = :id');
        foreach ([$idProductoA, $idProductoB, $idProductoC] as $idProducto) {
            if ($idProducto !== null) {
                $consultaInventario->execute(['id' => $idProducto]);
                $asegurar((int) $consultaInventario->fetchColumn() === 0, 'Quedó inventario de prueba.');
            }
        }

        $consultaProveedor = $pdo->prepare('SELECT COUNT(*) FROM proveedor WHERE Nombre = :nombre');
        $consultaProveedor->execute(['nombre' => $proveedorNombre]);
        $asegurar((int) $consultaProveedor->fetchColumn() === 0, 'Quedó el proveedor de prueba.');

        $consultaMovimientos = $pdo->prepare(
            'SELECT COUNT(*) FROM movimiento_inventario
             WHERE Nombre_Producto IN (:nombre_a, :nombre_b, :nombre_c)'
        );
        $consultaMovimientos->execute([
            'nombre_a' => $nombreProductoA,
            'nombre_b' => $nombreProductoB,
            'nombre_c' => $nombreProductoC
        ]);
        $asegurar((int) $consultaMovimientos->fetchColumn() === 0, 'Quedó algún movimiento de prueba.');

        if ($idVenta !== null) {
            $consultaVenta = $pdo->prepare('SELECT COUNT(*) FROM ventas WHERE Id_Ventas = :id');
            $consultaVenta->execute(['id' => $idVenta]);
            $asegurar((int) $consultaVenta->fetchColumn() === 0, 'Quedó la venta de prueba.');
            $consultaDetalleVenta = $pdo->prepare('SELECT COUNT(*) FROM detalle_ventas WHERE Id_Ventas = :id');
            $consultaDetalleVenta->execute(['id' => $idVenta]);
            $asegurar((int) $consultaDetalleVenta->fetchColumn() === 0, 'Quedó el detalle de venta de prueba.');
        }

        if ($idCompra !== null) {
            $consultaCompra = $pdo->prepare('SELECT COUNT(*) FROM compras WHERE Id_Compras = :id');
            $consultaCompra->execute(['id' => $idCompra]);
            $asegurar((int) $consultaCompra->fetchColumn() === 0, 'Quedó la compra de prueba.');
            $consultaDetalleCompra = $pdo->prepare('SELECT COUNT(*) FROM detalle_compras WHERE Id_Compras = :id');
            $consultaDetalleCompra->execute(['id' => $idCompra]);
            $asegurar((int) $consultaDetalleCompra->fetchColumn() === 0, 'Quedó el detalle de compra de prueba.');
        }
    });

    $resultados[] = [
        'numero' => 10,
        'descripcion' => 'Limpiar todos los datos creados por el script',
        'resultado' => $erroresLimpieza === [] ? 'OK' : 'FALLA',
        'detalle' => $erroresLimpieza === []
            ? 'Se eliminaron los movimientos, detalles, cabeceras, inventarios, productos y proveedor de prueba.'
            : implode(' | ', $erroresLimpieza)
    ];
}

printf("%-4s | %-74s | %-6s | %s\n", '#', 'Descripción', 'Estado', 'Detalle');
printf("%s\n", str_repeat('-', 150));
foreach ($resultados as $resultado) {
    printf(
        "%-4d | %-74s | %-6s | %s\n",
        $resultado['numero'],
        $resultado['descripcion'],
        $resultado['resultado'],
        $resultado['detalle']
    );
}

$cantidadOk = count(array_filter($resultados, function (array $resultado): bool {
    return $resultado['resultado'] === 'OK';
}));
$cantidadTotal = count($resultados);
printf("\n%d de %d pruebas OK\n", $cantidadOk, $cantidadTotal);
if (!$preparacionCompleta) {
    echo "Nota: la preparación no concluyó por completo; revisa las pruebas FALLA y el detalle de limpieza.\n";
}

exit($cantidadOk === $cantidadTotal ? 0 : 1);
