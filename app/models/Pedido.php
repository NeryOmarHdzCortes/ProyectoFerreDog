<?php
/**
 * Modelo de pedidos a proveedores
 */
class Pedido {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista pedidos pendientes conservando el nombre del proveedor eliminado.
     */
    public function listarPorRecibir() {
        $sql = "SELECT c.Id_Compras, c.Fecha_Compra, c.Total, c.Estado,
                       COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       (SELECT COUNT(*) FROM detalle_compras d
                        WHERE d.Id_Compras = c.Id_Compras) AS Productos
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                WHERE c.Estado = 'Pendiente'
                ORDER BY c.Fecha_Compra DESC, c.Id_Compras DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Lista pedidos pendientes del más antiguo al más reciente.
     */
    public function listarPorRecibirPaginados($limite, $desplazamiento) {
        $sql = "SELECT c.Id_Compras, c.Fecha_Compra, c.Total,
                       COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       (SELECT COUNT(*) FROM detalle_compras d
                        WHERE d.Id_Compras = c.Id_Compras) AS Productos
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                WHERE c.Estado = 'Pendiente'
                ORDER BY c.Fecha_Compra ASC, c.Id_Compras ASC
                LIMIT ? OFFSET ?";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindValue(1, (int) $limite, PDO::PARAM_INT);
        $consulta->bindValue(2, (int) $desplazamiento, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    /**
     * Cuenta pedidos pendientes para la paginación de Entradas.
     */
    public function contarPorRecibir() {
        $consulta = $this->pdo->query(
            "SELECT COUNT(*) FROM compras WHERE Estado = 'Pendiente'"
        );

        return (int) $consulta->fetchColumn();
    }

    /**
     * Busca pedidos pendientes por proveedor o por un producto de sus detalles.
     */
    public function buscarPendientesPorTexto($busqueda, $limite, $desplazamiento) {
        $sql = "SELECT c.Id_Compras, c.Fecha_Compra, c.Total,
                       COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       (SELECT COUNT(*) FROM detalle_compras dc
                        WHERE dc.Id_Compras = c.Id_Compras) AS Productos
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                WHERE c.Estado = 'Pendiente'
                  AND (
                      COALESCE(pr.Nombre, c.Nombre_Proveedor) LIKE ?
                      OR EXISTS (
                          SELECT 1
                          FROM detalle_compras d
                          LEFT JOIN productos p ON p.Id_Producto = d.Id_Producto
                          WHERE d.Id_Compras = c.Id_Compras
                            AND COALESCE(p.Nombre, d.Nombre_Producto) LIKE ?
                      )
                  )
                ORDER BY c.Fecha_Compra ASC, c.Id_Compras ASC
                LIMIT ? OFFSET ?";
        $consulta = $this->pdo->prepare($sql);
        $parametroBusqueda = '%' . $busqueda . '%';
        $consulta->bindValue(1, $parametroBusqueda, PDO::PARAM_STR);
        $consulta->bindValue(2, $parametroBusqueda, PDO::PARAM_STR);
        $consulta->bindValue(3, (int) $limite, PDO::PARAM_INT);
        $consulta->bindValue(4, (int) $desplazamiento, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    /**
     * Cuenta pedidos pendientes que coinciden con proveedor o producto.
     */
    public function contarPendientesPorTexto($busqueda) {
        $sql = "SELECT COUNT(*)
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                WHERE c.Estado = 'Pendiente'
                  AND (
                      COALESCE(pr.Nombre, c.Nombre_Proveedor) LIKE ?
                      OR EXISTS (
                          SELECT 1
                          FROM detalle_compras d
                          LEFT JOIN productos p ON p.Id_Producto = d.Id_Producto
                          WHERE d.Id_Compras = c.Id_Compras
                            AND COALESCE(p.Nombre, d.Nombre_Producto) LIKE ?
                      )
                  )";
        $consulta = $this->pdo->prepare($sql);
        $parametroBusqueda = '%' . $busqueda . '%';
        $consulta->execute([$parametroBusqueda, $parametroBusqueda]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Lista pedidos por texto y estado, con paginación de la vista administrativa.
     */
    public function listarPedidos($busqueda, $estado, $limite, $desplazamiento) {
        list($condiciones, $parametros) = $this->construirFiltrosPedidos($busqueda, $estado);
        $sql = "SELECT c.Id_Compras, c.Fecha_Compra, c.Total, c.Estado,
                       COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       (SELECT COUNT(*) FROM detalle_compras d
                        WHERE d.Id_Compras = c.Id_Compras) AS Productos
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor"
            . $condiciones
            . " ORDER BY c.Fecha_Compra DESC, c.Id_Compras DESC
                LIMIT ? OFFSET ?";
        $consulta = $this->pdo->prepare($sql);
        $indice = 1;
        foreach ($parametros as $parametro) {
            $consulta->bindValue($indice++, $parametro, is_int($parametro) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $consulta->bindValue($indice++, (int) $limite, PDO::PARAM_INT);
        $consulta->bindValue($indice, (int) $desplazamiento, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    /**
     * Cuenta pedidos con los mismos filtros de la lista paginada.
     */
    public function contarPedidos($busqueda, $estado) {
        list($condiciones, $parametros) = $this->construirFiltrosPedidos($busqueda, $estado);
        $consulta = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM compras c
             LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor"
            . $condiciones
        );
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Obtiene encabezado y empleados del pedido conservando el proveedor histórico.
     */
    public function obtenerDetallePedido($idPedido) {
        $sql = "SELECT c.Id_Compras, c.Fecha_Compra, c.Total, c.Estado,
                       c.Fecha_Recepcion,
                       COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       CONCAT(e.Nombre, ' ', e.Apellidos) AS EmpleadoRegistro,
                       CONCAT(er.Nombre, ' ', er.Apellidos) AS EmpleadoRecepcion
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                LEFT JOIN empleados e ON e.Id_Empleado = c.Id_Empleado
                LEFT JOIN empleados er ON er.Id_Empleado = c.Id_Empleado_Recibe
                WHERE c.Id_Compras = ?";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->fetch();
    }

    /**
     * Lista detalles de historial aun si el producto fue eliminado.
     */
    public function listarDetallesHistorialPedido($idPedido) {
        $sql = "SELECT d.Id_Detalle, d.Cantidad, d.Cantidad_Recibida,
                       d.Incidencia, d.Motivo_Incidencia,
                       d.Precio_Unitario, d.Total AS Subtotal,
                       COALESCE(p.Nombre, d.Nombre_Producto) AS Producto
                FROM detalle_compras d
                LEFT JOIN productos p ON p.Id_Producto = d.Id_Producto
                WHERE d.Id_Compras = ?
                ORDER BY d.Id_Detalle";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->fetchAll();
    }

    private function construirFiltrosPedidos($busqueda, $estado) {
        $condiciones = [];
        $parametros = [];

        if ($estado !== 'Todos') {
            $condiciones[] = 'c.Estado = ?';
            $parametros[] = $estado;
        }

        if ($busqueda !== '') {
            if (preg_match('/^#?[0-9]+$/', $busqueda) === 1) {
                $condiciones[] = 'c.Id_Compras = ?';
                $parametros[] = (int) ltrim($busqueda, '#');
            } else {
                $condiciones[] = 'COALESCE(pr.Nombre, c.Nombre_Proveedor) LIKE ?';
                $parametros[] = '%' . $busqueda . '%';
            }
        }

        return [
            empty($condiciones) ? '' : ' WHERE ' . implode(' AND ', $condiciones),
            $parametros
        ];
    }

    /**
     * Actualiza únicamente pedidos que siguen pendientes.
     */
    public function cancelar($idPedido) {
        $sql = "UPDATE compras
                SET Estado = 'Cancelada'
                WHERE Id_Compras = ? AND Estado = 'Pendiente'";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->rowCount() > 0;
    }

    /**
     * Obtiene los datos de correo sin depender del nombre actual del proveedor.
     */
    public function obtenerDatosCorreoCancelacion($idPedido) {
        $sql = "SELECT pr.Id_Proveedor, COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       pr.Correo, c.Total
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                WHERE c.Id_Compras = ? AND c.Estado = 'Pendiente'";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->fetch();
    }

    /**
     * Lista proveedores activos para el formulario de pedidos.
     */
    public function listarProveedoresActivos() {
        $sql = "SELECT Id_Proveedor, Nombre, Correo
                FROM proveedor
                WHERE Estado = 1
                ORDER BY Nombre";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Consulta los productos activos que surte un proveedor.
     */
    public function listarProductosActivosPorProveedor($idProveedor) {
        $sql = "SELECT p.Id_Producto, p.Nombre, m.Nombre AS Marca,
                       COALESCE(i.Existencias, 0) AS Existencias,
                       p.Stock_Minimo, p.Stock_Maximo, p.Precio_Compra
                FROM productos p
                LEFT JOIN inventario i ON i.Id_Producto = p.Id_Producto
                LEFT JOIN marcas m ON m.Id_Marca = p.Id_Marca
                WHERE p.Id_Proveedor = ? AND p.Estado = 1
                ORDER BY p.Nombre";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idProveedor]);

        return $consulta->fetchAll();
    }

    /**
     * Devuelve el proveedor activo solo si está habilitado para nuevos pedidos.
     */
    public function obtenerProveedorActivo($idProveedor) {
        $sql = "SELECT Id_Proveedor, Nombre, Correo
                FROM proveedor
                WHERE Id_Proveedor = ? AND Estado = 1";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idProveedor]);

        return $consulta->fetch();
    }

    /**
     * Valida las cantidades y obtiene el costo exclusivamente del catálogo.
     */
    public function prepararProductosPedido(array $productosEnviados, array $productosCatalogo) {
        $productosValidos = [];
        $errores = [];

        foreach ($productosEnviados as $idProductoEnviado => $datosProducto) {
            if (!is_array($datosProducto)) {
                $errores[] = 'Los datos de un producto no son válidos.';
                continue;
            }

            $cantidadTexto = is_scalar($datosProducto['cantidad'] ?? null)
                ? trim((string) $datosProducto['cantidad'])
                : '';
            if ($cantidadTexto === '') {
                continue;
            }

            $idProducto = filter_var((string) $idProductoEnviado, FILTER_VALIDATE_INT);
            if ($idProducto === false || $idProducto <= 0 || !isset($productosCatalogo[$idProducto])) {
                $errores[] = 'El producto ya no pertenece a este proveedor o está inactivo.';
                continue;
            }

            $productoCatalogo = $productosCatalogo[$idProducto];
            $precioCompra = $productoCatalogo['Precio_Compra'] ?? null;
            if ($precioCompra === null || !is_numeric($precioCompra)
                || !is_finite((float) $precioCompra) || (float) $precioCompra <= 0) {
                $errores[] = 'El producto ' . $productoCatalogo['Nombre']
                    . ' no tiene precio de compra; asígnalo en Productos';
                continue;
            }

            $costoCatalogo = number_format((float) $precioCompra, 2, '.', '');
            $validacionDetalle = $this->validarValoresDetalle($cantidadTexto, $costoCatalogo);
            $errores = array_merge($errores, $validacionDetalle['errores']);
            if ($validacionDetalle['costo_centavos'] <= 0) {
                $errores[] = 'El producto ' . $productoCatalogo['Nombre']
                    . ' no tiene precio de compra; asígnalo en Productos';
                continue;
            }
            if (!empty($validacionDetalle['errores'])) {
                continue;
            }

            $productosValidos[$idProducto] = [
                'cantidad' => $validacionDetalle['cantidad'],
                'costo_centavos' => $validacionDetalle['costo_centavos']
            ];
        }

        return [
            'productos' => $productosValidos,
            'errores' => $errores
        ];
    }

    /**
     * Valida la cantidad y el costo leído del catálogo para un renglón.
     */
    public function validarValoresDetalle($cantidadTexto, $costoCatalogo) {
        $errores = [];
        $cantidad = filter_var($cantidadTexto, FILTER_VALIDATE_INT);
        if ($cantidad === false || $cantidad <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0';
        }

        $costoCentavos = $this->convertirACentavos((string) $costoCatalogo);

        return [
            'cantidad' => $cantidad,
            'costo_centavos' => $costoCentavos,
            'errores' => $errores
        ];
    }

    /**
     * Devuelve el error de negocio cuando un pedido no tiene renglones válidos.
     */
    public function validarProductosPedido(array $productos) {
        return empty($productos) ? ['Agrega al menos un producto'] : [];
    }

    /**
     * Busca un pedido pendiente por número.
     */
    public function buscarPorNumero($idPedido) {
        $sql = "SELECT c.Id_Compras, c.Fecha_Compra, c.Total,
                       COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       (SELECT COUNT(*) FROM detalle_compras d
                        WHERE d.Id_Compras = c.Id_Compras) AS Productos
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                WHERE c.Id_Compras = ? AND c.Estado = 'Pendiente'";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->fetch();
    }

    /**
     * Busca pedidos pendientes por el nombre actual o histórico del proveedor.
     */
    public function buscarPorProveedor($nombreProveedor) {
        $sql = "SELECT c.Id_Compras, c.Fecha_Compra, c.Total,
                       COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
                       (SELECT COUNT(*) FROM detalle_compras d
                        WHERE d.Id_Compras = c.Id_Compras) AS Productos
                FROM compras c
                LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
                WHERE c.Estado = 'Pendiente'
                  AND COALESCE(pr.Nombre, c.Nombre_Proveedor) LIKE ?
                ORDER BY c.Fecha_Compra DESC, c.Id_Compras DESC";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute(['%' . $nombreProveedor . '%']);

        return $consulta->fetchAll();
    }

    /**
     * Obtiene los detalles de un pedido, incluso si el producto fue eliminado.
     */
    public function listarDetallesPedido($idPedido) {
        $sql = "SELECT d.Id_Detalle, d.Cantidad,
                       COALESCE(p.Nombre, d.Nombre_Producto) AS Producto,
                       m.Nombre AS Marca, d.Id_Producto
                FROM detalle_compras d
                LEFT JOIN productos p ON p.Id_Producto = d.Id_Producto
                LEFT JOIN marcas m ON m.Id_Marca = p.Id_Marca
                WHERE d.Id_Compras = ?
                ORDER BY d.Id_Detalle";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->fetchAll();
    }

    /**
     * Bloquea el pedido mientras se valida y registra su recepción.
     */
    public function bloquearPedidoPendiente($idPedido) {
        $sql = "SELECT Id_Compras
                FROM compras
                WHERE Id_Compras = ? AND Estado = 'Pendiente'
                FOR UPDATE";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->fetchColumn();
    }

    /**
     * Bloquea y obtiene los renglones asociados al pedido.
     */
    public function listarDetallesParaRecepcion($idPedido) {
        $sql = "SELECT Id_Detalle, Cantidad, Id_Producto
                FROM detalle_compras
                WHERE Id_Compras = ?
                ORDER BY Id_Detalle
                FOR UPDATE";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idPedido]);

        return $consulta->fetchAll();
    }

    /**
     * Guarda la cantidad e incidencia reportadas para un renglón del pedido.
     */
    public function actualizarRecepcionDetalle($idPedido, $idDetalle, $cantidadRecibida, $incidencia, $motivo) {
        $sql = "UPDATE detalle_compras
                SET Cantidad_Recibida = ?, Incidencia = ?, Motivo_Incidencia = ?
                WHERE Id_Detalle = ? AND Id_Compras = ?";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([
            $cantidadRecibida,
            $incidencia,
            $motivo,
            $idDetalle,
            $idPedido
        ]);

        return $consulta->rowCount();
    }

    /**
     * Cambia el pedido a Recibida; el trigger actualiza inventario y movimientos.
     */
    public function marcarPedidoRecibido($idPedido, $idEmpleado) {
        $sql = "UPDATE compras
                SET Estado = 'Recibida',
                    Fecha_Recepcion = NOW(),
                    Id_Empleado_Recibe = ?
                WHERE Id_Compras = ? AND Estado = 'Pendiente'";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idEmpleado, $idPedido]);

        return $consulta->rowCount();
    }

    private function convertirACentavos($valor) {
        $partes = explode('.', $valor, 2);
        $centavos = str_pad($partes[1] ?? '', 2, '0');

        return ((int) $partes[0] * 100) + (int) $centavos;
    }
}
