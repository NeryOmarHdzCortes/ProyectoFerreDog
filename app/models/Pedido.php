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
     * Valida los valores de cantidad y costo enviados para un renglón de pedido.
     */
    public function validarValoresDetalle($cantidadTexto, $costoTexto) {
        $errores = [];
        $cantidad = filter_var($cantidadTexto, FILTER_VALIDATE_INT);
        if ($cantidad === false || $cantidad <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0';
        }

        $costoValido = preg_match('/^[0-9]+(?:\.[0-9]{1,2})?$/', (string) $costoTexto) === 1;
        $costoCentavos = $costoValido ? $this->convertirACentavos((string) $costoTexto) : 0;
        if (!$costoValido || $costoCentavos <= 0) {
            $errores[] = 'El costo debe ser mayor a 0';
        }

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
