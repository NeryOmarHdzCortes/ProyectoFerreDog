<?php
/**
 * Controlador de Proveedores (HU-6)
 * Solo accesible por Administrador
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Proveedor.php';

class ProveedorController {
    private $pdo;
    private $modeloProveedor;

    private const URL_LISTA = '/ProyectoFerredog/public/index.php?c=proveedor&a=lista';

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloProveedor = new Proveedor($this->pdo);

        requerirRol(['Administrador']);
    }

    /**
     * Lista de proveedores con búsqueda y filtro por estado (W-08)
     */
    public function lista() {
        $busqueda = $this->obtenerTexto($_GET['busqueda'] ?? '');
        $filtroEstado = $this->obtenerTexto($_GET['estado'] ?? '');
        if (!in_array($filtroEstado, ['', 'Activo', 'Inactivo'], true)) {
            $filtroEstado = '';
        }

        $proveedores = $this->modeloProveedor->obtenerTodos($busqueda, $filtroEstado);
        $totalProveedores = count($proveedores);
        $proveedoresActivos = count(array_filter(
            $proveedores,
            function ($proveedor) {
                return $proveedor['Estado'] === 'Activo';
            }
        ));

        $titulo = 'Proveedores - Ferredog';
        $scriptExtra = 'proveedores.js';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/proveedores/lista.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Formulario para agregar proveedor (W-09)
     */
    public function crear() {
        $titulo = 'Agregar proveedor - Ferredog';
        $scriptExtra = 'proveedores.js';
        $modoFormulario = 'agregar';
        $proveedor = null;

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/proveedores/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Consulta de proveedor en modo de solo lectura (W-09)
     */
    public function ver() {
        $this->mostrarFormularioProveedor('ver');
    }

    /**
     * Formulario para editar proveedor (W-09)
     */
    public function editar() {
        $this->mostrarFormularioProveedor('editar');
    }

    private function mostrarFormularioProveedor($modoFormulario) {
        $idProveedor = $this->obtenerEntero($_GET['id'] ?? null);
        if ($idProveedor === null || $idProveedor <= 0) {
            $_SESSION['mensaje_error'] = 'Proveedor no válido.';
            $this->redirigir(self::URL_LISTA);
        }

        $proveedor = $this->modeloProveedor->obtenerPorId($idProveedor);
        if (!$proveedor) {
            $_SESSION['mensaje_error'] = 'Proveedor no encontrado.';
            $this->redirigir(self::URL_LISTA);
        }

        $busquedaProductos = $this->obtenerTexto($_GET['busqueda_productos'] ?? '');
        $productosQueSurte = $this->modeloProveedor->obtenerProductosQueSurte(
            $idProveedor,
            $busquedaProductos
        );
        $totalProductosQueSurte = (int) $this->modeloProveedor->contarProductosQueSurte($idProveedor);

        $titulo = ($modoFormulario === 'ver' ? 'Ver proveedor - ' : 'Editar proveedor - ')
            . $proveedor['Nombre'] . ' - Ferredog';
        $scriptExtra = 'proveedores.js';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/proveedores/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    private function obtenerTexto($valor) {
        return is_scalar($valor) ? trim((string) $valor) : '';
    }

    private function obtenerEntero($valor) {
        if (!is_scalar($valor)) {
            return null;
        }

        $entero = filter_var((string) $valor, FILTER_VALIDATE_INT);
        return $entero === false ? null : $entero;
    }

    private function redirigir($url) {
        header('Location: ' . $url);
        exit;
    }
}
