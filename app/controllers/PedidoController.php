<?php
/**
 * Controlador de pedidos a proveedores (HU-7 T1)
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Pedido.php';
require_once BASE_PATH . '/app/libraries/Correo.php';

class PedidoController {
    private $pdo;
    private $modeloPedido;

    private const URL_LISTA = '/ProyectoFerredog/public/index.php?c=pedido&a=lista';

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloPedido = new Pedido($this->pdo);

        requerirRol(['Administrador']);
    }

    /**
     * Muestra los pedidos pendientes ordenados del más reciente al más antiguo.
     */
    public function lista() {
        $pedidos = $this->modeloPedido->listarPorRecibir();
        $titulo = 'Pedidos a proveedores - Ferredog';
        $scriptExtra = 'pedidos.js';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/pedidos/lista.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Cancela por POST y notifica después de comprobar que el pedido cambió.
     */
    public function cancelar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idPedido = $this->obtenerEntero($_POST['id_pedido'] ?? null);
        if ($idPedido === null || $idPedido <= 0) {
            $_SESSION['mensaje_error'] = 'Pedido no válido.';
            $this->redirigir(self::URL_LISTA);
        }

        try {
            $datosCorreo = $this->modeloPedido->obtenerDatosCorreoCancelacion($idPedido);
            if (!$this->modeloPedido->cancelar($idPedido)) {
                $_SESSION['mensaje_error'] = 'El pedido ya no está por recibir';
                $this->redirigir(self::URL_LISTA);
            }

            $correoEnviado = true;
            if ($datosCorreo && !empty($datosCorreo['Id_Proveedor'])) {
                try {
                    $servicioCorreo = new Correo();
                    $correoEnviado = $servicioCorreo->enviarCancelacion(
                        (string) ($datosCorreo['Correo'] ?? ''),
                        (string) $datosCorreo['Proveedor'],
                        $idPedido,
                        $datosCorreo['Total']
                    );
                } catch (Throwable $errorCorreo) {
                    error_log('Error al notificar la cancelación del pedido #' . $idPedido . ': '
                        . $errorCorreo->getMessage());
                    $correoEnviado = false;
                }
            }

            if ($correoEnviado) {
                $_SESSION['mensaje_exito'] = 'Pedido #' . $idPedido . ' cancelado.';
            } else {
                $_SESSION['mensaje_error'] =
                    'Pedido cancelado, pero no se pudo enviar el correo al proveedor';
            }
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al cancelar pedido #' . $idPedido . ': ' . $error->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo cancelar el pedido. Intenta de nuevo.';
        }

        $this->redirigir(self::URL_LISTA);
    }

    /**
     * Página temporal hasta que se implemente HU-7 T2.
     */
    public function nuevo() {
        $titulo = 'Nuevo pedido - Ferredog';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/pedidos/nuevo.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
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
