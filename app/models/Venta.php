<?php
/**
 * Modelo de ventas
 */
class Venta {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarMetodosPago() {
        return $this->pdo->query(
            'SELECT Id_Metodo_Pago, Nombre FROM metodo_pago ORDER BY Id_Metodo_Pago'
        )->fetchAll();
    }

    public function obtenerSiguienteFolio() {
        return (int) $this->pdo->query(
            'SELECT COALESCE(MAX(Id_Ventas), 0) + 1 FROM ventas'
        )->fetchColumn();
    }

    public function obtenerMetodoPago($idMetodoPago) {
        $consulta = $this->pdo->prepare(
            'SELECT Id_Metodo_Pago, Nombre FROM metodo_pago WHERE Id_Metodo_Pago = ?'
        );
        $consulta->execute([$idMetodoPago]);

        return $consulta->fetch();
    }

    public function obtenerIdEmpleado($idUsuario) {
        $consulta = $this->pdo->prepare(
            'SELECT Id_Empleado FROM usuario WHERE Id_Usuario = ?'
        );
        $consulta->execute([$idUsuario]);

        return $consulta->fetchColumn();
    }

    /**
     * Consulta y bloquea los productos y existencias que se van a registrar.
     */
    public function obtenerProductosParaVenta(array $idsProducto) {
        if (empty($idsProducto)) {
            return [];
        }

        $marcadores = implode(',', array_fill(0, count($idsProducto), '?'));
        $sql = "SELECT p.Id_Producto, p.Nombre, p.Precio_Venta, p.Estado,
                       COALESCE(i.Existencias, 0) AS Existencias
                FROM productos p
                LEFT JOIN inventario i ON i.Id_Producto = p.Id_Producto
                WHERE p.Id_Producto IN ($marcadores)
                ORDER BY p.Id_Producto
                FOR UPDATE";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute(array_values($idsProducto));

        return $consulta->fetchAll();
    }

    public function insertarVenta($total, $idMetodoPago, $idEmpleado) {
        $consulta = $this->pdo->prepare(
            "INSERT INTO ventas
                (Fecha, Descripcion, Total, Estado, Id_Metodo_Pago, Id_Cliente, Id_Empleado)
             VALUES (CURDATE(), 'Venta de mostrador', ?, 'Completada', ?, NULL, ?)"
        );
        $consulta->execute([$total, $idMetodoPago, $idEmpleado]);

        return (int) $this->pdo->lastInsertId();
    }

    public function insertarDetalleVenta($cantidad, $precioUnitario, $subtotal, $idVenta, $idProducto) {
        $consulta = $this->pdo->prepare(
            'INSERT INTO detalle_ventas
                (Cantidad, Precio_Unitario, Descuento, Subtotal, Id_Ventas, Id_Producto)
             VALUES (?, ?, 0, ?, ?, ?)'
        );
        $consulta->execute([
            $cantidad,
            $precioUnitario,
            $subtotal,
            $idVenta,
            $idProducto
        ]);
    }

    public function obtenerTicket($idVenta, $idEmpleado) {
        $consulta = $this->pdo->prepare(
            'SELECT v.Id_Ventas, v.Fecha, v.Total, v.Estado, mp.Nombre AS Metodo
             FROM ventas v
             INNER JOIN metodo_pago mp ON mp.Id_Metodo_Pago = v.Id_Metodo_Pago
             WHERE v.Id_Ventas = ? AND v.Id_Empleado = ?'
        );
        $consulta->execute([$idVenta, $idEmpleado]);

        return $consulta->fetch();
    }

    public function listarDetallesTicket($idVenta) {
        $consulta = $this->pdo->prepare(
            'SELECT d.Cantidad, d.Precio_Unitario, d.Subtotal,
                    COALESCE(p.Nombre, d.Nombre_Producto) AS Producto
             FROM detalle_ventas d
             LEFT JOIN productos p ON p.Id_Producto = d.Id_Producto
             WHERE d.Id_Ventas = ?
             ORDER BY d.Id_Detalle'
        );
        $consulta->execute([$idVenta]);

        return $consulta->fetchAll();
    }

    public function listarExistenciasActualizadas($idVenta) {
        $consulta = $this->pdo->prepare(
            "SELECT Nombre_Producto, Stock_Antes, Stock_Despues
             FROM movimiento_inventario
             WHERE Tipo = 'Salida' AND Motivo = CONCAT('Venta #', ?)
             ORDER BY Id_Movimiento"
        );
        $consulta->execute([$idVenta]);

        return $consulta->fetchAll();
    }
}
