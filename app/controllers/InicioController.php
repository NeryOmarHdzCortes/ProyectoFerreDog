<?php
/**
 * Controlador de la pantalla de inicio (W-03)
 * Muestra tarjetas con conteos y accesos rápidos según el rol
 *
 * Ajustado a la estructura real de la base `ferredog`:
 * - productos.Estado y proveedor.Estado son numéricos (1 = activo, 0 = inactivo)
 * - Stock_Minimo está en la tabla productos (no en inventario)
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';

class InicioController {
    private $pdo;

    private const SQL_USUARIOS_ACTIVOS = "SELECT COUNT(*) FROM usuario WHERE Estado = 'Activo'";

    private const SQL_PRODUCTOS_CATALOGO = "SELECT COUNT(*) FROM productos WHERE Estado = 1";

    private const SQL_PROVEEDORES_ACTIVOS = "SELECT COUNT(*) FROM proveedor WHERE Estado = 1";

    private const SQL_PRODUCTOS_STOCK_MINIMO = "SELECT COUNT(*)
                                               FROM inventario i
                                               INNER JOIN productos p ON p.Id_Producto = i.Id_Producto
                                               WHERE i.Existencias <= p.Stock_Minimo
                                                 AND p.Estado = 1";

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

        // Conteos que ve cada rol (design.md, W-03)
        $conteos = [];

        if ($rol === 'Administrador') {
            $conteos['usuarios_activos'] = $this->contar(self::SQL_USUARIOS_ACTIVOS);
            $conteos['productos_catalogo'] = $this->contar(self::SQL_PRODUCTOS_CATALOGO);
            $conteos['proveedores_activos'] = $this->contar(self::SQL_PROVEEDORES_ACTIVOS);
            $conteos['productos_stock_minimo'] = $this->contar(self::SQL_PRODUCTOS_STOCK_MINIMO);
        } elseif ($rol === 'Almacenista') {
            $conteos['productos_catalogo'] = $this->contar(self::SQL_PRODUCTOS_CATALOGO);
            $conteos['productos_stock_minimo'] = $this->contar(self::SQL_PRODUCTOS_STOCK_MINIMO);
        } elseif ($rol === 'Vendedor') {
            $conteos['productos_catalogo'] = $this->contar(self::SQL_PRODUCTOS_CATALOGO);
        }

        $titulo = 'Inicio - Ferredog';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/dashboard/inicio.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Ejecuta un conteo. Si la consulta falla, registra el error y devuelve 0
     * para que una tarjeta no tumbe toda la pantalla de inicio.
     */
    private function contar($sql) {
        try {
            return (int) $this->pdo->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log('InicioController: ' . $e->getMessage());
            return 0;
        }
    }
}