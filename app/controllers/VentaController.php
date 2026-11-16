<?php
/**
 * Controlador de ventas (HU-2)
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';

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

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/ventas/registrar.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }
}
