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
}
