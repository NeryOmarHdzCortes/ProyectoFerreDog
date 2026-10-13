<?php
/**
 * Modelo Proveedor
 * Maneja la tabla proveedor
 */

class Proveedor {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Convertir estado numérico a texto
     */
    private function estadoATexto($estado) {
        return $estado == 1 ? 'Activo' : 'Inactivo';
    }
    
    /**
     * Convertir estado texto a numérico
     */
    private function estadoANumero($estado) {
        return $estado === 'Activo' ? 1 : 0;
    }
    
    /**
     * Obtener todos los proveedores activos (para combos)
     */
    public function obtenerActivos() {
        $sql = "SELECT Id_Proveedor, Nombre 
                FROM proveedor 
                WHERE Estado = 1 
                ORDER BY Nombre";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todos los proveedores con filtros
     */
    public function obtenerTodos($busqueda = '', $estado = '') {
        $sql = "SELECT Id_Proveedor, Nombre, Tipo, Contacto, Telefono, Correo,
                       CASE Estado WHEN 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado
                FROM proveedor
                WHERE 1=1";
        
        $params = [];
        
        if ($busqueda !== '') {
            $sql .= " AND (Nombre LIKE :busqueda1 OR Contacto LIKE :busqueda2)";
            $params['busqueda1'] = "%{$busqueda}%";
            $params['busqueda2'] = "%{$busqueda}%";
        }
        
        if ($estado !== '') {
            $estadoNumerico = $this->estadoANumero($estado);
            $sql .= " AND Estado = :estado";
            $params['estado'] = $estadoNumerico;
        }
        
        $sql .= " ORDER BY Nombre";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener un proveedor por ID
     */
    public function obtenerPorId($idProveedor) {
        $sql = "SELECT Id_Proveedor, Nombre, Tipo, Contacto, Telefono, Correo, Direccion,
                       CASE Estado WHEN 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado
                FROM proveedor
                WHERE Id_Proveedor = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idProveedor]);
        
        return $stmt->fetch();
    }

    /**
     * Verificar si el nombre ya pertenece a otro proveedor
     */
    public function existeNombre($nombre, $idProveedorExcluir = null) {
        $sql = "SELECT COUNT(*) FROM proveedor WHERE Nombre = :nombre";
        $parametros = ['nombre' => $nombre];

        if ($idProveedorExcluir !== null) {
            $sql .= " AND Id_Proveedor != :id";
            $parametros['id'] = $idProveedorExcluir;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si el correo ya pertenece a otro proveedor
     */
    public function existeCorreo($correo, $idProveedorExcluir = null) {
        $sql = "SELECT COUNT(*) FROM proveedor WHERE Correo = :correo";
        $parametros = ['correo' => $correo];

        if ($idProveedorExcluir !== null) {
            $sql .= " AND Id_Proveedor != :id";
            $parametros['id'] = $idProveedorExcluir;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Registrar un proveedor activo
     */
    public function crear($nombre, $tipo, $contacto, $telefono, $correo, $direccion) {
        $sql = "INSERT INTO proveedor (Nombre, Tipo, Contacto, Telefono, Correo, Direccion, Estado)
                VALUES (:nombre, :tipo, :contacto, :telefono, :correo, :direccion, 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'tipo' => $tipo,
            'contacto' => $contacto,
            'telefono' => $telefono,
            'correo' => $correo,
            'direccion' => $direccion
        ]);
    }

    /**
     * Actualizar los datos de un proveedor sin modificar su estado
     */
    public function editar($idProveedor, $nombre, $tipo, $contacto, $telefono, $correo, $direccion) {
        $sql = "UPDATE proveedor
                SET Nombre = :nombre,
                    Tipo = :tipo,
                    Contacto = :contacto,
                    Telefono = :telefono,
                    Correo = :correo,
                    Direccion = :direccion
                WHERE Id_Proveedor = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'tipo' => $tipo,
            'contacto' => $contacto,
            'telefono' => $telefono,
            'correo' => $correo,
            'direccion' => $direccion,
            'id' => $idProveedor
        ]);
    }

    /**
     * Desactivar un proveedor conservando su registro
     */
    public function desactivar($idProveedor) {
        $sql = "UPDATE proveedor SET Estado = 0 WHERE Id_Proveedor = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idProveedor]);
    }
    
    /**
     * Obtener productos que surte un proveedor
     */
    public function obtenerProductosQueSurte($idProveedor, $busqueda = '') {
        $sql = "SELECT p.Codigo_Barras, p.Nombre, COALESCE(i.Existencias, 0) AS Existencias, p.Stock_Minimo
                FROM productos p
                LEFT JOIN inventario i ON p.Id_Producto = i.Id_Producto
                WHERE p.Id_Proveedor = :proveedor AND p.Estado = 1";
        
        $params = ['proveedor' => $idProveedor];
        
        if ($busqueda !== '') {
            $sql .= " AND (p.Nombre LIKE :busqueda1 OR p.Codigo_Barras LIKE :busqueda2)";
            $params['busqueda1'] = "%{$busqueda}%";
            $params['busqueda2'] = "%{$busqueda}%";
        }
        
        $sql .= " ORDER BY p.Nombre LIMIT 7";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Contar productos que surte un proveedor
     */
    public function contarProductosQueSurte($idProveedor) {
        $sql = "SELECT COUNT(*) FROM productos WHERE Id_Proveedor = :proveedor AND Estado = 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['proveedor' => $idProveedor]);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Contar proveedores activos
     */
    public function contarActivos() {
        $sql = "SELECT COUNT(*) FROM proveedor WHERE Estado = 1";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
}
