<?php
/**
 * Controlador de ventas (HU-2)
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Producto.php';

class VentaController {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;

        requerirRol(['Vendedor']);
    }

    /**
     * Muestra la interfaz inicial para registrar una venta (W-16).
     */
    public function index() {
        $metodosPago = $this->pdo->query(
            'SELECT Id_Metodo_Pago, Nombre FROM metodo_pago ORDER BY Id_Metodo_Pago'
        )->fetchAll();
        $siguienteFolio = (int) $this->pdo->query(
            'SELECT COALESCE(MAX(Id_Ventas), 0) + 1 FROM ventas'
        )->fetchColumn();

        $folioProvisional = 'V-' . str_pad((string) $siguienteFolio, 4, '0', STR_PAD_LEFT);
        $fechaVenta = date('d/m/Y');
        $titulo = 'Registrar venta - Ferredog';
        $scriptExtra = 'ventas.js?v=' . filemtime(BASE_PATH . '/public/assets/js/ventas.js');

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/ventas/registrar.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Busca productos activos con precio para agregarlos a una venta.
     */
    public function buscar() {
        header('Content-Type: application/json; charset=UTF-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método de solicitud no permitido.']);
            return;
        }

        $busqueda = is_scalar($_GET['q'] ?? null) ? trim((string) $_GET['q']) : '';
        $caracteres = [];
        $longitud = preg_match_all('/./us', $busqueda, $caracteres);
        if ($busqueda === '' || $longitud === false || $longitud > 100) {
            http_response_code(400);
            echo json_encode(['error' => $busqueda === ''
                ? 'Escribe un nombre o código.'
                : 'La búsqueda no puede superar 100 caracteres.']);
            return;
        }

        try {
            $modeloProducto = new Producto($this->pdo);
            echo json_encode(
                ['productos' => $modeloProducto->buscarParaVenta($busqueda)],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (Throwable $error) {
            error_log('Error al buscar productos para venta: ' . $error->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'No se pudieron buscar productos. Intenta de nuevo.']);
        }
    }
}
