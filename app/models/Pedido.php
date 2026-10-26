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
}
