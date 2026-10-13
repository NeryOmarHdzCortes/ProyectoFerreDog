<?php
/**
 * Controlador de Productos (HU-5)
 * Accesible por Administrador y Almacenista (gestión); Vendedor (solo consulta)
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Catalogo.php';
require_once BASE_PATH . '/app/models/Proveedor.php';

class ProductoController {
    private $pdo;
    private $modeloProducto;
    private $modeloCatalogo;
    private $modeloProveedor;

    private const URL_LISTA = '/ProyectoFerredog/public/index.php?c=producto&a=lista';

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloProducto = new Producto($this->pdo);
        $this->modeloCatalogo = new Catalogo($this->pdo);
        $this->modeloProveedor = new Proveedor($this->pdo);

        // Verificar que el usuario tiene acceso a productos
        requerirRol(['Administrador', 'Almacenista', 'Vendedor']);
    }

    /**
     * Lista de productos con buscador y filtros (W-06)
     */
    public function lista() {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $filtroCategoria = (int) ($_GET['categoria'] ?? 0);
        $filtroProveedor = (int) ($_GET['proveedor'] ?? 0);
        $filtroEstado = $_GET['estado'] ?? 'Activo'; // Activos por defecto

        $productos = $this->modeloProducto->obtenerTodos($busqueda, $filtroCategoria, $filtroProveedor, $filtroEstado);
        
        // Obtener catálogos para los filtros
        $categorias = $this->modeloCatalogo->obtenerCategorias();
        $proveedores = $this->modeloProveedor->obtenerActivos();
        
        // Contar productos en stock mínimo
        $productosStockMinimo = $this->modeloProducto->contarEnStockMinimo();

        $titulo = 'Productos - Ferredog';
        $scriptExtra = 'productos.js';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/productos/lista.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Formulario para crear producto (W-07)
     */
    public function crear() {
        // Vendedor no puede crear productos
        requerirRol(['Administrador', 'Almacenista']);
        
        // Obtener catálogos para los combos
        $categorias = $this->modeloCatalogo->obtenerCategorias();
        $marcas = $this->modeloCatalogo->obtenerMarcas();
        $unidades = $this->modeloCatalogo->obtenerUnidades();
        $proveedores = $this->modeloProveedor->obtenerActivos();
        
        $titulo = 'Agregar producto - Ferredog';
        $scriptExtra = 'productos.js';
        $esEdicion = false;
        $producto = null;

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/productos/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Formulario para editar producto (W-07)
     */
    public function editar() {
        // Vendedor no puede editar productos
        requerirRol(['Administrador', 'Almacenista']);
        
        $idProducto = (int) ($_GET['id'] ?? 0);
        
        if ($idProducto <= 0) {
            $_SESSION['mensaje_error'] = 'Producto no válido.';
            $this->redirigir(self::URL_LISTA);
        }
        
        $producto = $this->modeloProducto->obtenerPorId($idProducto);
        
        if (!$producto) {
            $_SESSION['mensaje_error'] = 'Producto no encontrado.';
            $this->redirigir(self::URL_LISTA);
        }
        
        // Obtener catálogos para los combos
        $categorias = $this->modeloCatalogo->obtenerCategorias();
        $marcas = $this->modeloCatalogo->obtenerMarcas();
        $unidades = $this->modeloCatalogo->obtenerUnidades();
        $proveedores = $this->modeloProveedor->obtenerActivos();
        
        $titulo = 'Editar producto - Ferredog';
        $scriptExtra = 'productos.js';
        $esEdicion = true;

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/productos/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Redirigir a una URL
     */
    private function redirigir($url) {
        header("Location: $url");
        exit;
    }
}
