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
    private const PRODUCTOS_POR_PAGINA = 10;
    private const STOCK_MAXIMO_PREDETERMINADO = 100;

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
        $busqueda = $this->obtenerTexto($_GET['busqueda'] ?? '');
        $filtroCategoria = max(0, $this->obtenerEntero($_GET['categoria'] ?? 0) ?? 0);
        $filtroProveedor = max(0, $this->obtenerEntero($_GET['proveedor'] ?? 0) ?? 0);
        $verEliminados = ($_SESSION['Rol'] ?? '') === 'Administrador'
            && $this->obtenerTexto($_GET['eliminados'] ?? '') === '1';
        $filtroEstado = $verEliminados ? 'Inactivo' : 'Activo';

        $totalProductos = $this->modeloProducto->contarFiltrados(
            $busqueda,
            $filtroCategoria,
            $filtroProveedor,
            $filtroEstado
        );
        $totalPaginas = max(1, (int) ceil($totalProductos / self::PRODUCTOS_POR_PAGINA));
        $pagina = max(1, $this->obtenerEntero($_GET['pagina'] ?? 1) ?? 1);
        $pagina = min($pagina, $totalPaginas);
        $productos = $this->modeloProducto->obtenerTodos(
            $busqueda,
            $filtroCategoria,
            $filtroProveedor,
            $filtroEstado,
            $pagina,
            self::PRODUCTOS_POR_PAGINA
        );
        
        // Obtener catálogos para los filtros
        $categorias = $this->modeloCatalogo->obtenerCategorias();
        $proveedores = $this->modeloProveedor->obtenerActivos();
        
        // Contar productos en stock mínimo
        $productosStockMinimo = $this->modeloProducto->contarEnStockMinimo();
        $parametrosPaginacion = [
            'c' => 'producto',
            'a' => 'lista',
            'busqueda' => $busqueda,
            'categoria' => $filtroCategoria,
            'proveedor' => $filtroProveedor
        ];
        if ($verEliminados) {
            $parametrosPaginacion['eliminados'] = 1;
        }

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
        
        if (!$producto || $producto['Estado'] !== 'Activo') {
            $_SESSION['mensaje_error'] = 'El producto no existe o fue eliminado.';
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
     * Registrar o editar un producto
     */
    public function guardar() {
        requerirRol(['Administrador', 'Almacenista']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idProducto = 0;
        if (isset($_POST['id_producto'])) {
            $idProductoValidado = $this->obtenerEntero($_POST['id_producto']);
            if ($idProductoValidado === null || $idProductoValidado <= 0) {
                $_SESSION['mensaje_error'] = 'Producto no válido.';
                $this->redirigir(self::URL_LISTA);
            }
            $idProducto = $idProductoValidado;
        }

        $productoActual = null;
        if ($idProducto > 0) {
            $productoActual = $this->modeloProducto->obtenerPorId($idProducto);
            if (!$productoActual || $productoActual['Estado'] !== 'Activo') {
                $_SESSION['mensaje_error'] = 'El producto no existe o fue eliminado.';
                $this->redirigir(self::URL_LISTA);
            }
        }

        $codigoBarras = $this->obtenerTexto($_POST['codigo'] ?? '');
        $nombre = $this->obtenerTexto($_POST['nombre'] ?? '');
        $idMarca = $this->obtenerEntero($_POST['marca'] ?? null);
        $idCategoria = $this->obtenerEntero($_POST['categoria'] ?? null);
        $idProveedor = $this->obtenerEntero($_POST['proveedor'] ?? null);
        $idUnidad = $this->obtenerEntero($_POST['unidad'] ?? null);
        $stockMinimo = $this->obtenerEntero($_POST['stock_minimo'] ?? null);
        $stockInicial = $idProducto === 0
            ? $this->obtenerEntero($_POST['stock_inicial'] ?? null)
            : null;
        $errores = [];

        if ($codigoBarras === '') {
            $errores[] = 'El código es obligatorio.';
        } elseif ($this->modeloProducto->existeCodigo($codigoBarras, $idProducto ?: null)) {
            $errores[] = 'Este código ya existe.';
        }

        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio.';
        }

        $marcasValidas = array_map('intval', array_column($this->modeloCatalogo->obtenerMarcas(), 'Id_Marca'));
        $categoriasValidas = array_map('intval', array_column($this->modeloCatalogo->obtenerCategorias(), 'Id_Categoria'));
        $proveedoresValidos = array_map('intval', array_column($this->modeloProveedor->obtenerActivos(), 'Id_Proveedor'));
        $unidadesValidas = array_map('intval', array_column($this->modeloCatalogo->obtenerUnidades(), 'Id_Unidad'));

        if ($idMarca === null || !in_array($idMarca, $marcasValidas, true)) {
            $errores[] = 'La marca seleccionada no es válida.';
        }
        if ($idCategoria === null || !in_array($idCategoria, $categoriasValidas, true)) {
            $errores[] = 'La categoría seleccionada no es válida.';
        }
        if ($idProveedor === null || !in_array($idProveedor, $proveedoresValidos, true)) {
            $errores[] = 'El proveedor seleccionado no es válido o está inactivo.';
        }
        if ($idUnidad === null || !in_array($idUnidad, $unidadesValidas, true)) {
            $errores[] = 'La unidad seleccionada no es válida.';
        }

        if ($stockMinimo === null || $stockMinimo < 1) {
            $errores[] = 'El stock mínimo debe ser un número entero mayor a 0.';
        } else {
            $stockMaximo = $productoActual
                ? (int) $productoActual['Stock_Maximo']
                : self::STOCK_MAXIMO_PREDETERMINADO;
            if ($stockMinimo > $stockMaximo) {
                $errores[] = 'El stock mínimo no puede ser mayor al stock máximo (' . $stockMaximo . ').';
            }
        }

        if ($idProducto === 0 && ($stockInicial === null || $stockInicial < 0)) {
            $errores[] = 'El stock inicial debe ser un número entero igual o mayor a 0.';
        }

        $precioVenta = null;
        if (in_array($_SESSION['Rol'], ['Administrador', 'Almacenista'], true)) {
            $precioCapturado = $this->obtenerTexto($_POST['precio_venta'] ?? '');
            if ($precioCapturado !== '') {
                if (!is_numeric($precioCapturado) || !is_finite((float) $precioCapturado) || (float) $precioCapturado <= 0) {
                    $errores[] = 'El precio de venta debe ser mayor a 0.';
                } else {
                    $precioVenta = (float) $precioCapturado;
                }
            } elseif ($productoActual && $_SESSION['Rol'] === 'Almacenista') {
                $precioVenta = $productoActual['Precio_Venta'];
            }
        } elseif ($productoActual) {
            $precioVenta = $productoActual['Precio_Venta'];
        }

        if (!empty($errores)) {
            $this->redirigirConErrores($errores, $idProducto);
        }

        try {
            if ($idProducto === 0) {
                $this->pdo->beginTransaction();
                $this->modeloProducto->crear(
                    $codigoBarras,
                    $nombre,
                    $idMarca,
                    $idCategoria,
                    $idProveedor,
                    $idUnidad,
                    $precioVenta,
                    $stockInicial,
                    $stockMinimo
                );
                $this->pdo->commit();
                $_SESSION['mensaje_exito'] = 'Producto creado correctamente.';
            } else {
                $this->modeloProducto->editar(
                    $idProducto,
                    $codigoBarras,
                    $nombre,
                    $idMarca,
                    $idCategoria,
                    $idProveedor,
                    $idUnidad,
                    $precioVenta,
                    $stockMinimo
                );
                $_SESSION['mensaje_exito'] = 'Producto actualizado correctamente.';
            }

            unset($_SESSION['datos_producto'], $_SESSION['errores']);
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al guardar producto: ' . $e->getMessage());
            $esRechazoStockMinimo = (string) $e->getCode() === '23000'
                && stripos($e->getMessage(), 'chk_prod_stock') !== false;
            if ($esRechazoStockMinimo) {
                $mensaje = 'El stock mínimo debe ser un número entero mayor a 0.';
            } else {
                $mensaje = (string) $e->getCode() === '23000'
                    ? 'No se pudo guardar el producto porque el código ya existe o algún dato seleccionado dejó de estar disponible.'
                    : 'No se pudo guardar el producto. Intenta de nuevo.';
            }
            $this->redirigirConErrores([$mensaje], $idProducto);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al guardar producto: ' . $e->getMessage());
            $this->redirigirConErrores(['No se pudo guardar el producto. Intenta de nuevo.'], $idProducto);
        }

        $this->redirigir(self::URL_LISTA);
    }

    /**
     * Asignar o actualizar el precio de venta desde la lista
     */
    public function ponerPrecio() {
        requerirRol(['Administrador', 'Almacenista']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idProducto = $this->obtenerEntero($_POST['id_producto'] ?? null);

        if ($idProducto === null || $idProducto <= 0) {
            $_SESSION['mensaje_error'] = 'Producto no válido.';
            $this->redirigir(self::URL_LISTA);
        }

        $producto = $this->modeloProducto->obtenerPorId($idProducto);
        if (!$producto || $producto['Estado'] !== 'Activo') {
            $_SESSION['mensaje_error'] = 'El producto no existe o fue eliminado.';
            $this->redirigir(self::URL_LISTA);
        }

        $precioRecibido = $_POST['precio'] ?? null;
        $precioTexto = is_string($precioRecibido) ? trim($precioRecibido) : '';
        $precioVenta = $precioTexto === '' ? false : filter_var($precioTexto, FILTER_VALIDATE_FLOAT);

        if ($precioVenta === false || !is_finite($precioVenta) || $precioVenta <= 0) {
            $_SESSION['mensaje_error'] = 'El precio de venta debe ser mayor a 0.';
            $this->redirigir(self::URL_LISTA);
        }

        try {
            $this->modeloProducto->asignarPrecio($idProducto, $precioVenta);
            $_SESSION['mensaje_exito'] = 'Precio del producto actualizado correctamente.';
        } catch (PDOException $e) {
            error_log('Error al asignar precio al producto: ' . $e->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo actualizar el precio. Intenta de nuevo.';
        }

        $this->redirigir(self::URL_LISTA);
    }

    /**
     * Eliminar lógicamente un producto.
     */
    public function desactivar() {
        requerirRol(['Administrador']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idProducto = $this->obtenerEntero($_POST['id_producto'] ?? null);
        if ($idProducto === null || $idProducto <= 0) {
            $_SESSION['mensaje_error'] = 'Producto no válido.';
            $this->redirigir(self::URL_LISTA);
        }

        $producto = $this->modeloProducto->obtenerPorId($idProducto);
        if (!$producto || $producto['Estado'] !== 'Activo') {
            $_SESSION['mensaje_error'] = 'El producto no existe o fue eliminado.';
            $this->redirigir(self::URL_LISTA);
        }

        try {
            $this->modeloProducto->desactivar($idProducto);
            $_SESSION['mensaje_exito'] = 'Producto eliminado.';
        } catch (PDOException $e) {
            error_log('Error al eliminar producto: ' . $e->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo eliminar el producto. Intenta de nuevo.';
        }

        $this->redirigir(self::URL_LISTA);
    }

    /**
     * Restaurar un producto eliminado, solo para Administrador.
     */
    public function restaurar() {
        requerirRol(['Administrador']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA . '&eliminados=1');
        }

        $idProducto = $this->obtenerEntero($_POST['id_producto'] ?? null);
        if ($idProducto === null || $idProducto <= 0) {
            $_SESSION['mensaje_error'] = 'Producto no válido.';
            $this->redirigir(self::URL_LISTA . '&eliminados=1');
        }

        $producto = $this->modeloProducto->obtenerPorId($idProducto);
        if (!$producto || $producto['Estado'] !== 'Inactivo') {
            $_SESSION['mensaje_error'] = 'No se encontró un producto eliminado para restaurar.';
            $this->redirigir(self::URL_LISTA . '&eliminados=1');
        }

        try {
            $this->modeloProducto->restaurar($idProducto);
            $_SESSION['mensaje_exito'] = 'Producto restaurado.';
        } catch (PDOException $e) {
            error_log('Error al restaurar producto: ' . $e->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo restaurar el producto. Intenta de nuevo.';
        }

        $this->redirigir(self::URL_LISTA . '&eliminados=1');
    }

    private function obtenerEntero($valor) {
        if (!is_scalar($valor)) {
            return null;
        }

        $entero = filter_var((string) $valor, FILTER_VALIDATE_INT);
        return $entero === false ? null : $entero;
    }

    private function obtenerTexto($valor) {
        return is_scalar($valor) ? trim((string) $valor) : '';
    }

    private function redirigirConErrores($errores, $idProducto) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_producto'] = [];
        foreach (['codigo', 'nombre', 'marca', 'categoria', 'proveedor', 'unidad', 'precio_venta', 'stock_inicial', 'stock_minimo'] as $campo) {
            if (isset($_POST[$campo]) && is_scalar($_POST[$campo])) {
                $_SESSION['datos_producto'][$campo] = (string) $_POST[$campo];
            }
        }
        $url = $idProducto > 0
            ? '/ProyectoFerredog/public/index.php?c=producto&a=editar&id=' . $idProducto
            : '/ProyectoFerredog/public/index.php?c=producto&a=crear';
        $this->redirigir($url);
    }

    /**
     * Redirigir a una URL
     */
    private function redirigir($url) {
        header("Location: $url");
        exit;
    }
}
