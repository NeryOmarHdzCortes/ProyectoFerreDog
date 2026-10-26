<?php
/**
 * Controlador de pedidos a proveedores (HU-7)
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Pedido.php';
require_once BASE_PATH . '/app/libraries/Correo.php';

class PedidoController {
    private $pdo;
    private $modeloPedido;

    private const URL_LISTA = '/ProyectoFerredog/public/index.php?c=pedido&a=lista';
    private const URL_NUEVO = '/ProyectoFerredog/public/index.php?c=pedido&a=nuevo';

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
     * Formulario para registrar un pedido nuevo (W-12).
     */
    public function nuevo() {
        $proveedores = $this->modeloPedido->listarProveedoresActivos();
        $datosAnteriores = is_array($_SESSION['old'] ?? null) ? $_SESSION['old'] : [];
        $productosAnteriores = $datosAnteriores['productos'] ?? [];
        $error = $_SESSION['error'] ?? $_SESSION['mensaje_error'] ?? '';
        unset($_SESSION['old'], $_SESSION['error'], $_SESSION['error_campos'], $_SESSION['mensaje_error']);

        $titulo = 'Nuevo pedido - Ferredog';
        $scriptExtra = 'pedidos.js';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/pedidos/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Devuelve como JSON los productos activos que surte el proveedor solicitado.
     */
    public function productosProveedor() {
        header('Content-Type: application/json; charset=UTF-8');

        try {
            $idProveedor = $this->obtenerEntero($_GET['id'] ?? null);
            if ($idProveedor === null || $idProveedor <= 0
                || !$this->modeloPedido->obtenerProveedorActivo($idProveedor)) {
                http_response_code(400);
                echo json_encode(['error' => 'Selecciona un proveedor activo válido.']);
                return;
            }

            echo json_encode(
                $this->modeloPedido->listarProductosActivosPorProveedor($idProveedor),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (Throwable $error) {
            error_log('Error al consultar productos del proveedor #' . $idProveedor . ': ' . $error->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'No se pudieron cargar los productos del proveedor.']);
        }
    }

    /**
     * Valida y registra un pedido completo dentro de una transacción.
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_NUEVO);
        }

        $idProveedor = $this->obtenerEntero($_POST['proveedor_id'] ?? null);
        $productosEnviados = is_array($_POST['productos'] ?? null) ? $_POST['productos'] : [];
        $proveedor = null;
        $productosValidos = [];
        $errores = [];

        if ($idProveedor === null || $idProveedor <= 0
            || !($proveedor = $this->modeloPedido->obtenerProveedorActivo($idProveedor))) {
            $errores[] = 'Elige un proveedor';
        }

        foreach ($productosEnviados as $idProductoEnviado => $datosProducto) {
            if (!is_array($datosProducto)) {
                $errores[] = 'Los datos de un producto no son válidos.';
                continue;
            }

            $cantidadTexto = $this->obtenerTexto($datosProducto['cantidad'] ?? '');
            if ($cantidadTexto === '') {
                continue;
            }

            $idProducto = $this->obtenerEntero($idProductoEnviado);
            $cantidad = filter_var($cantidadTexto, FILTER_VALIDATE_INT);
            $costoTexto = $this->obtenerTexto($datosProducto['costo'] ?? '');
            $costoValido = preg_match('/^[0-9]+(?:\\.[0-9]{1,2})?$/', $costoTexto) === 1;
            $costoCentavos = $costoValido ? $this->convertirACentavos($costoTexto) : 0;

            if ($cantidad === false || $cantidad <= 0) {
                $errores[] = 'La cantidad debe ser mayor a 0';
            }
            if (!$costoValido || $costoCentavos <= 0) {
                $errores[] = 'El costo debe ser mayor a 0';
            }
            if ($idProducto === null || $idProducto <= 0) {
                $errores[] = 'El producto ya no pertenece a este proveedor o está inactivo.';
                continue;
            }

            $productosValidos[$idProducto] = [
                'cantidad' => $cantidad,
                'costo_centavos' => $costoCentavos
            ];
        }

        if (empty($productosValidos)) {
            $errores[] = 'Agrega al menos un producto';
        }

        if ($proveedor) {
            $productosDelProveedor = $this->modeloPedido->listarProductosActivosPorProveedor($idProveedor);
            $productosDelProveedor = array_column($productosDelProveedor, null, 'Id_Producto');
            foreach ($productosValidos as $idProducto => $productoPedido) {
                if (!isset($productosDelProveedor[$idProducto])) {
                    $errores[] = 'El producto ya no pertenece a este proveedor o está inactivo.';
                    unset($productosValidos[$idProducto]);
                }
            }
        }

        if (empty($productosValidos) && !in_array('Agrega al menos un producto', $errores, true)) {
            $errores[] = 'Agrega al menos un producto';
        }

        if (!empty($errores)) {
            $this->redirigirConErroresPedido($idProveedor, $productosEnviados, $errores);
        }

        $totalCentavos = 0;
        $renglonesCorreo = [];
        foreach ($productosValidos as $idProducto => &$productoPedido) {
            $productoCatalogo = $productosDelProveedor[$idProducto];
            $subtotalCentavos = $productoPedido['cantidad'] * $productoPedido['costo_centavos'];
            $totalCentavos += $subtotalCentavos;
            $productoPedido['subtotal_centavos'] = $subtotalCentavos;
            $productoPedido['nombre'] = $productoCatalogo['Nombre'];
            $renglonesCorreo[] = [
                'producto' => $productoCatalogo['Nombre'],
                'cantidad' => $productoPedido['cantidad'],
                'costo' => $productoPedido['costo_centavos'] / 100,
                'subtotal' => $subtotalCentavos / 100
            ];
        }
        unset($productoPedido);

        $idEmpleadoSesion = $this->obtenerEntero($_SESSION['Id_Usuario'] ?? null);
        if ($idEmpleadoSesion === null || $idEmpleadoSesion <= 0) {
            $_SESSION['old'] = $this->obtenerDatosFormularioPedido($idProveedor, $productosEnviados);
            $_SESSION['error'] = 'No se pudo identificar al empleado de la sesión.';
            $this->redirigir(self::URL_NUEVO);
        }

        try {
            $this->pdo->beginTransaction();

            $consultaEmpleado = $this->pdo->prepare(
                'SELECT Id_Empleado FROM usuario WHERE Id_Usuario = ?'
            );
            $consultaEmpleado->execute([$idEmpleadoSesion]);
            $idEmpleado = $consultaEmpleado->fetchColumn();
            if ($idEmpleado === false || $idEmpleado === null) {
                throw new RuntimeException('No se encontró el empleado asociado al usuario.');
            }

            $consultaCompra = $this->pdo->prepare(
                "INSERT INTO compras (Fecha_Compra, Total, Estado, Id_Proveedor, Id_Empleado)
                 VALUES (CURDATE(), ?, 'Pendiente', ?, ?)"
            );
            $consultaCompra->execute([$totalCentavos / 100, $idProveedor, $idEmpleado]);
            $idPedido = (int) $this->pdo->lastInsertId();

            $consultaDetalle = $this->pdo->prepare(
                'INSERT INTO detalle_compras (Cantidad, Precio_Unitario, Total, Id_Compras, Id_Producto)
                 VALUES (?, ?, ?, ?, ?)'
            );
            foreach ($productosValidos as $idProducto => $productoPedido) {
                $consultaDetalle->execute([
                    $productoPedido['cantidad'],
                    $productoPedido['costo_centavos'] / 100,
                    $productoPedido['subtotal_centavos'] / 100,
                    $idPedido,
                    $idProducto
                ]);
            }

            $this->pdo->commit();
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $codigoSql = $error instanceof PDOException
                ? (string) ($error->errorInfo[0] ?? $error->getCode())
                : '';
            if ($codigoSql === '45000') {
                $mensajeError = $error->errorInfo[2] ?? $error->getMessage();
            } else {
                error_log('Error al registrar pedido: ' . $error->getMessage());
                $mensajeError = 'No se pudo guardar el pedido. Intenta de nuevo.';
            }
            $this->redirigirConErroresPedido($idProveedor, $productosEnviados, [$mensajeError]);
        }

        $correoEnviado = false;
        try {
            $correo = new Correo();
            $correoEnviado = $correo->enviarPedido(
                (string) $proveedor['Correo'],
                (string) $proveedor['Nombre'],
                $idPedido,
                date('d/m/Y'),
                $renglonesCorreo,
                $totalCentavos / 100
            );
        } catch (Throwable $errorCorreo) {
            error_log('Error al notificar el pedido #' . $idPedido . ': ' . $errorCorreo->getMessage());
        }

        if ($correoEnviado) {
            $_SESSION['mensaje_exito'] = 'Pedido #' . $idPedido . ' registrado y enviado a '
                . $proveedor['Nombre'] . '.';
        } else {
            $_SESSION['mensaje_error'] = 'Pedido guardado, pero no se pudo enviar el correo al proveedor';
        }

        $this->redirigir(self::URL_LISTA);
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

    private function convertirACentavos($valor) {
        $partes = explode('.', $valor, 2);
        $centavos = str_pad($partes[1] ?? '', 2, '0');

        return ((int) $partes[0] * 100) + (int) $centavos;
    }

    private function obtenerDatosFormularioPedido($idProveedor, $productos) {
        return [
            'proveedor_id' => $idProveedor ?? '',
            'productos' => $productos
        ];
    }

    private function redirigirConErroresPedido($idProveedor, $productos, $errores) {
        $_SESSION['old'] = $this->obtenerDatosFormularioPedido($idProveedor, $productos);
        $_SESSION['error'] = implode(' ', array_unique($errores));
        $this->redirigir(self::URL_NUEVO);
    }

    private function redirigir($url) {
        header('Location: ' . $url);
        exit;
    }
}
