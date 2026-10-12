<?php
/**
 * Controlador de la pantalla de inicio
 * Muestra tarjetas con conteos y accesos rápidos según el rol
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';

class InicioController {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Pantalla de inicio con tarjetas según el rol
     */
    public function index() {
        $rol = $_SESSION['Rol'];
        $nombre = explode(' ', $_SESSION['Nombre'])[0]; // Solo el primer nombre
        
        // Obtener conteos según el rol
        $conteos = [];
        
        if ($rol === 'Administrador') {
            // Usuarios activos
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM usuario WHERE Estado = 'Activo'");
            $conteos['usuarios_activos'] = $stmt->fetchColumn();
            
            // Productos en catálogo
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM productos WHERE Estado = 'Activo'");
            $conteos['productos_catalogo'] = $stmt->fetchColumn();
            
            // Proveedores activos
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM proveedor WHERE Estado = 'Activo'");
            $conteos['proveedores_activos'] = $stmt->fetchColumn();
            
            // Productos en stock mínimo
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM inventario i 
                                      INNER JOIN productos p ON i.Id_Producto = p.Id_Producto 
                                      WHERE i.Existencias <= i.Stock_Minimo AND p.Estado = 'Activo'");
            $conteos['productos_stock_minimo'] = $stmt->fetchColumn();
            
        } elseif ($rol === 'Almacenista') {
            // Productos en catálogo
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM productos WHERE Estado = 'Activo'");
            $conteos['productos_catalogo'] = $stmt->fetchColumn();
            
            // Productos en stock mínimo
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM inventario i 
                                      INNER JOIN productos p ON i.Id_Producto = p.Id_Producto 
                                      WHERE i.Existencias <= i.Stock_Minimo AND p.Estado = 'Activo'");
            $conteos['productos_stock_minimo'] = $stmt->fetchColumn();
            
        } elseif ($rol === 'Vendedor') {
            // Productos en catálogo
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM productos WHERE Estado = 'Activo'");
            $conteos['productos_catalogo'] = $stmt->fetchColumn();
        }
        
        $titulo = 'Inicio - Ferredog';
        
        // Cargar vista
        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/dashboard/inicio.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }
}
