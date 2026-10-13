<?php
/**
 * Modelo Producto
 * Maneja las tablas productos e inventario
 */

class Producto {
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
    
    private function construirFiltros($busqueda, $idCategoria, $idProveedor, $estado) {
        $condiciones = " WHERE 1=1";
        $parametros = [];

        if ($busqueda !== '') {
            $condiciones .= " AND (p.Nombre LIKE :busqueda1 OR p.Codigo_Barras LIKE :busqueda2)";
            $parametros['busqueda1'] = "%{$busqueda}%";
            $parametros['busqueda2'] = "%{$busqueda}%";
        }

        if ($idCategoria > 0) {
            $condiciones .= " AND p.Id_Categoria = :categoria";
            $parametros['categoria'] = $idCategoria;
        }

        if ($idProveedor > 0) {
            $condiciones .= " AND p.Id_Proveedor = :proveedor";
            $parametros['proveedor'] = $idProveedor;
        }

        if ($estado === 'Activo' || $estado === 'Inactivo') {
            $condiciones .= " AND p.Estado = :estado";
            $parametros['estado'] = $this->estadoANumero($estado);
        }

        return [$condiciones, $parametros];
    }

    /**
     * Obtener una página de productos con filtros de búsqueda
     * @param string $busqueda Término de búsqueda (nombre o código)
     * @param int $idCategoria Filtro de categoría
     * @param int $idProveedor Filtro de proveedor
     * @param string $estado Filtro de estado
     * @param int $pagina Número de página
     * @param int $limite Productos por página
     * @return array Lista de productos con datos del JOIN
     */
    public function obtenerTodos($busqueda = '', $idCategoria = 0, $idProveedor = 0, $estado = '', $pagina = 1, $limite = 10) {
        $sql = "SELECT p.Id_Producto, p.Codigo_Barras, p.Nombre, p.Precio_Venta,
                       p.Stock_Minimo, p.Stock_Maximo,
                       m.Nombre AS Marca,
                       c.Nombre AS Categoria,
                       prov.Nombre AS Proveedor,
                       COALESCE(i.Existencias, 0) AS Existencias,
                       CASE p.Estado WHEN 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado
                FROM productos p
                LEFT JOIN marcas m ON p.Id_Marca = m.Id_Marca
                LEFT JOIN categoria c ON p.Id_Categoria = c.Id_Categoria
                LEFT JOIN proveedor prov ON p.Id_Proveedor = prov.Id_Proveedor
                LEFT JOIN inventario i ON p.Id_Producto = i.Id_Producto";
        list($condiciones, $parametros) = $this->construirFiltros($busqueda, $idCategoria, $idProveedor, $estado);
        $pagina = max(1, (int) $pagina);
        $limite = max(1, (int) $limite);
        $sql .= $condiciones . " ORDER BY p.Nombre, p.Id_Producto LIMIT :limite OFFSET :desplazamiento";

        $stmt = $this->pdo->prepare($sql);
        foreach ($parametros as $nombre => $valor) {
            $tipo = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $nombre, $valor, $tipo);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':desplazamiento', ($pagina - 1) * $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Contar productos que coinciden con los filtros de la lista
     */
    public function contarFiltrados($busqueda = '', $idCategoria = 0, $idProveedor = 0, $estado = '') {
        list($condiciones, $parametros) = $this->construirFiltros($busqueda, $idCategoria, $idProveedor, $estado);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos p" . $condiciones);

        foreach ($parametros as $nombre => $valor) {
            $tipo = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $nombre, $valor, $tipo);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Obtener un producto por ID con sus datos completos
     * @param int $idProducto
     * @return array|false Datos del producto o false si no existe
     */
    public function obtenerPorId($idProducto) {
        $sql = "SELECT p.Id_Producto, p.Codigo_Barras, p.Nombre, p.Precio_Venta,
                       p.Stock_Minimo, p.Stock_Maximo, p.Id_Categoria, p.Id_Unidad,
                       p.Id_Proveedor, p.Id_Marca,
                       COALESCE(i.Existencias, 0) AS Existencias,
                       CASE p.Estado WHEN 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado
                FROM productos p
                LEFT JOIN inventario i ON p.Id_Producto = i.Id_Producto
                WHERE p.Id_Producto = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idProducto]);
        
        return $stmt->fetch();
    }
    
    /**
     * Verificar si existe un código de barras (excluyendo opcionalmente un producto)
     */
    public function existeCodigo($codigoBarras, $idProductoExcluir = null) {
        $sql = "SELECT COUNT(*) FROM productos WHERE Codigo_Barras = :codigo";
        $params = ['codigo' => $codigoBarras];
        
        if ($idProductoExcluir) {
            $sql .= " AND Id_Producto != :id";
            $params['id'] = $idProductoExcluir;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Crear producto (transacción: productos + inventario)
     */
    public function crear($codigoBarras, $nombre, $idMarca, $idCategoria, $idProveedor, 
                         $idUnidad, $precioVenta, $stockInicial, $stockMinimo) {
        // 1. Insertar en productos
        $sql = "INSERT INTO productos (Codigo_Barras, Nombre, Precio_Venta, Stock_Minimo, 
                                       Fecha_Registro, Estado, Id_Categoria, 
                                       Id_Unidad, Id_Proveedor, Id_Marca)
                VALUES (:codigo, :nombre, :precio, :stock_min, NOW(), 1, 
                        :categoria, :unidad, :proveedor, :marca)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoBarras,
            'nombre' => $nombre,
            'precio' => $precioVenta, // NULL si no se proporciona
            'stock_min' => $stockMinimo,
            'categoria' => $idCategoria,
            'unidad' => $idUnidad,
            'proveedor' => $idProveedor,
            'marca' => $idMarca
        ]);
        
        $idProducto = $this->pdo->lastInsertId();
        
        // 2. Insertar en inventario
        $sql = "INSERT INTO inventario (Existencias, Fecha_Entrada, Id_Producto)
                VALUES (:existencias, NOW(), :id_producto)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'existencias' => $stockInicial,
            'id_producto' => $idProducto
        ]);
    }
    
    /**
     * Editar producto (actualiza productos y stock mínimo)
     */
    public function editar($idProducto, $codigoBarras, $nombre, $idMarca, $idCategoria, 
                          $idProveedor, $idUnidad, $precioVenta, $stockMinimo) {
        $sql = "UPDATE productos 
                SET Codigo_Barras = :codigo,
                    Nombre = :nombre,
                    Precio_Venta = :precio,
                    Stock_Minimo = :stock_min,
                    Id_Marca = :marca,
                    Id_Categoria = :categoria,
                    Id_Proveedor = :proveedor,
                    Id_Unidad = :unidad
                WHERE Id_Producto = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoBarras,
            'nombre' => $nombre,
            'precio' => $precioVenta,
            'stock_min' => $stockMinimo,
            'marca' => $idMarca,
            'categoria' => $idCategoria,
            'proveedor' => $idProveedor,
            'unidad' => $idUnidad,
            'id' => $idProducto
        ]);
    }
    
    /**
     * Cambiar estado de un producto
     */
    public function cambiarEstado($idProducto, $estado) {
        $estadoNumerico = $this->estadoANumero($estado);
        
        $sql = "UPDATE productos SET Estado = :estado WHERE Id_Producto = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'estado' => $estadoNumerico,
            'id' => $idProducto
        ]);
    }
    
    /**
     * Asignar precio de venta a un producto
     */
    public function asignarPrecio($idProducto, $precio) {
        $sql = "UPDATE productos SET Precio_Venta = :precio WHERE Id_Producto = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'precio' => $precio,
            'id' => $idProducto
        ]);
    }
    
    /**
     * Contar productos en stock mínimo
     */
    public function contarEnStockMinimo() {
        $sql = "SELECT COUNT(*) 
                FROM productos p
                LEFT JOIN inventario i ON p.Id_Producto = i.Id_Producto
                WHERE COALESCE(i.Existencias, 0) <= p.Stock_Minimo AND p.Estado = 1";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
}
