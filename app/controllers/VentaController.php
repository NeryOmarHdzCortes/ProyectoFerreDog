<?php
/**
 * Controlador de ventas (HU-2)
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Venta.php';

class VentaController {
    private $pdo;
    private $modeloVenta;

    private const URL_VENTAS = '/ProyectoFerredog/public/index.php?c=venta&a=index';

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloVenta = new Venta($this->pdo);

        requerirRol(['Vendedor']);
    }

    /**
     * Muestra la interfaz inicial para registrar una venta (W-16).
     */
    public function index() {
        $metodosPago = $this->modeloVenta->listarMetodosPago();
        $siguienteFolio = $this->modeloVenta->obtenerSiguienteFolio();

        $folioProvisional = 'V-' . str_pad((string) $siguienteFolio, 4, '0', STR_PAD_LEFT);
        $fechaVenta = date('d/m/Y');
        $titulo = 'Registrar venta - Ferredog';
        $scriptExtra = 'ventas.js?v=' . filemtime(BASE_PATH . '/public/assets/js/ventas.js');
        $mensajeError = $_SESSION['mensaje_error'] ?? '';
        unset($_SESSION['mensaje_error']);

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

    /**
     * Registra la venta y sus detalles en una sola transacción.
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_VENTAS);
        }

        try {
            $productosSolicitados = $this->normalizarProductosEnviados($_POST['productos'] ?? null);
            $idMetodoPago = $this->obtenerEntero($_POST['metodo'] ?? null);
            if ($idMetodoPago === null || $idMetodoPago <= 0) {
                throw new InvalidArgumentException('Selecciona un método de pago válido.');
            }

            $this->pdo->beginTransaction();

            $metodoPago = $this->modeloVenta->obtenerMetodoPago($idMetodoPago);
            if (!$metodoPago) {
                throw new InvalidArgumentException('El método de pago ya no está disponible.');
            }
            if (!in_array($metodoPago['Nombre'], ['Efectivo', 'Tarjeta', 'Transferencia'], true)) {
                throw new InvalidArgumentException('El método de pago no es válido.');
            }

            $idUsuario = $this->obtenerEntero($_SESSION['Id_Usuario'] ?? null);
            if ($idUsuario === null || $idUsuario <= 0) {
                throw new InvalidArgumentException('No se pudo identificar al vendedor de la sesión.');
            }
            $idEmpleado = $this->modeloVenta->obtenerIdEmpleado($idUsuario);
            if ($idEmpleado === false || $idEmpleado === null || (int) $idEmpleado <= 0) {
                throw new InvalidArgumentException('No se encontró el empleado asociado al usuario.');
            }

            $productosCatalogo = $this->modeloVenta->obtenerProductosParaVenta(
                array_keys($productosSolicitados)
            );
            $productosPorId = array_column($productosCatalogo, null, 'Id_Producto');
            if (count($productosPorId) !== count($productosSolicitados)) {
                throw new InvalidArgumentException('Uno o más productos ya no están disponibles.');
            }

            $detallesVenta = [];
            $totalCentavos = 0;
            foreach ($productosSolicitados as $idProducto => $cantidad) {
                $producto = $productosPorId[$idProducto];
                $precioTexto = $producto['Precio_Venta'] ?? null;
                if ((int) $producto['Estado'] !== 1 || $precioTexto === null
                    || !is_numeric($precioTexto) || !is_finite((float) $precioTexto)) {
                    throw new InvalidArgumentException(
                        'El producto ' . $producto['Nombre'] . ' ya no está disponible para venta.'
                    );
                }

                $precioCentavos = $this->convertirACentavos((string) $precioTexto);
                $existencias = filter_var(
                    (string) $producto['Existencias'],
                    FILTER_VALIDATE_INT
                );
                if ($precioCentavos <= 0 || $existencias === false || $existencias < 0) {
                    throw new InvalidArgumentException(
                        'No se pudieron validar el precio o las existencias de ' . $producto['Nombre'] . '.'
                    );
                }
                if ($cantidad > $existencias) {
                    throw new InvalidArgumentException(
                        'Existencia insuficiente de ' . $producto['Nombre']
                        . ': solo hay ' . $existencias . ' piezas.'
                    );
                }
                if ($cantidad > intdiv(self::MAX_CENTAVOS, $precioCentavos)) {
                    throw new InvalidArgumentException('El importe de la venta supera el máximo permitido.');
                }

                $subtotalCentavos = $cantidad * $precioCentavos;
                if ($totalCentavos > self::MAX_CENTAVOS - $subtotalCentavos) {
                    throw new InvalidArgumentException('El importe de la venta supera el máximo permitido.');
                }
                $totalCentavos += $subtotalCentavos;
                $detallesVenta[] = [
                    'id_producto' => $idProducto,
                    'cantidad' => $cantidad,
                    'precio_centavos' => $precioCentavos,
                    'subtotal_centavos' => $subtotalCentavos
                ];
            }

            if ($metodoPago['Nombre'] === 'Efectivo') {
                $pagoCentavos = $this->validarPago($_POST['pago'] ?? null);
                if ($pagoCentavos < $totalCentavos) {
                    $faltante = $this->convertirACentavosTexto($totalCentavos - $pagoCentavos);
                    throw new InvalidArgumentException(
                        'Pago insuficiente: faltan $' . $faltante . '. No se puede registrar la venta.'
                    );
                }
            } else {
                $pagoCentavos = $totalCentavos;
            }

            $idVenta = $this->modeloVenta->insertarVenta(
                $this->convertirACentavosTexto($totalCentavos),
                $idMetodoPago,
                (int) $idEmpleado
            );
            foreach ($detallesVenta as $detalle) {
                $this->modeloVenta->insertarDetalleVenta(
                    $detalle['cantidad'],
                    $this->convertirACentavosTexto($detalle['precio_centavos']),
                    $this->convertirACentavosTexto($detalle['subtotal_centavos']),
                    $idVenta,
                    $detalle['id_producto']
                );
            }

            $this->pdo->commit();
            $_SESSION['venta_pago'][$idVenta] = [
                'pago' => $this->convertirACentavosTexto($pagoCentavos),
                'cambio' => $this->convertirACentavosTexto($pagoCentavos - $totalCentavos)
            ];
            $this->redirigir(
                '/ProyectoFerredog/public/index.php?c=venta&a=ticket&id=' . $idVenta
            );
        } catch (PDOException $error) {
            $this->revertirTransaccion();
            $estadoSql = (string) ($error->errorInfo[0] ?? $error->getCode());
            if (in_array($estadoSql, ['45000', '23000'], true)) {
                $_SESSION['mensaje_error'] = $error->errorInfo[2] ?? $error->getMessage();
            } else {
                error_log('Error al registrar venta: ' . $error->getMessage());
                $_SESSION['mensaje_error'] = 'No se pudo registrar la venta. Intenta de nuevo.';
            }
        } catch (InvalidArgumentException $error) {
            $this->revertirTransaccion();
            $_SESSION['mensaje_error'] = $error->getMessage();
        } catch (Throwable $error) {
            $this->revertirTransaccion();
            error_log('Error al registrar venta: ' . $error->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo registrar la venta. Intenta de nuevo.';
        }

        $this->redirigir(self::URL_VENTAS);
    }

    /**
     * Muestra un ticket únicamente al vendedor que registró la venta.
     */
    public function ticket() {
        $idVenta = $this->obtenerEntero($_GET['id'] ?? null);
        $idUsuario = $this->obtenerEntero($_SESSION['Id_Usuario'] ?? null);
        if ($idVenta === null || $idVenta <= 0 || $idUsuario === null || $idUsuario <= 0) {
            $_SESSION['mensaje_error'] = 'Venta no encontrada.';
            $this->redirigir(self::URL_VENTAS);
        }

        try {
            $idEmpleado = $this->modeloVenta->obtenerIdEmpleado($idUsuario);
            if ($idEmpleado === false || $idEmpleado === null) {
                $_SESSION['mensaje_error'] = 'Venta no encontrada.';
                $this->redirigir(self::URL_VENTAS);
            }
            $venta = $this->modeloVenta->obtenerTicket($idVenta, (int) $idEmpleado);
            if (!$venta) {
                $_SESSION['mensaje_error'] = 'Venta no encontrada.';
                $this->redirigir(self::URL_VENTAS);
            }

            $detalles = $this->modeloVenta->listarDetallesTicket($idVenta);
            $existenciasActualizadas = $this->modeloVenta->listarExistenciasActualizadas($idVenta);
            $datosPago = $_SESSION['venta_pago'][$idVenta] ?? null;
            unset($_SESSION['venta_pago'][$idVenta]);
            $folio = 'V-' . str_pad((string) $idVenta, 4, '0', STR_PAD_LEFT);
            $fechaVenta = (new DateTime($venta['Fecha']))->format('d/m/Y');
            $nombreVendedor = (string) ($_SESSION['Nombre'] ?? '');
            $titulo = 'Venta registrada - Ferredog';
            $claseCuerpo = 'pantalla-ticket';
            $scriptExtra = 'imprimir.js?v=' . filemtime(BASE_PATH . '/public/assets/js/imprimir.js');

            require_once BASE_PATH . '/app/views/layout/header.php';
            require_once BASE_PATH . '/app/views/layout/sidebar.php';
            require_once BASE_PATH . '/app/views/ventas/ticket.php';
            require_once BASE_PATH . '/app/views/layout/footer.php';
        } catch (Throwable $error) {
            error_log('Error al consultar el ticket de venta #' . $idVenta . ': ' . $error->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo mostrar el ticket. Intenta de nuevo.';
            $this->redirigir(self::URL_VENTAS);
        }
    }

    private const MAX_CENTAVOS = 9999999999;

    private function normalizarProductosEnviados($productosEnviados) {
        if (!is_array($productosEnviados) || empty($productosEnviados)) {
            throw new InvalidArgumentException('Agrega al menos un producto a la venta.');
        }

        $productos = [];
        foreach ($productosEnviados as $datosProducto) {
            if (!is_array($datosProducto)) {
                throw new InvalidArgumentException('Los datos de un producto no son válidos.');
            }
            $idProducto = $this->obtenerEntero($datosProducto['id'] ?? null);
            $cantidad = $this->obtenerEntero($datosProducto['cantidad'] ?? null);
            if ($idProducto === null || $idProducto <= 0
                || $cantidad === null || $cantidad <= 0 || $cantidad > 2147483647) {
                throw new InvalidArgumentException('Las cantidades deben ser números enteros mayores a 0.');
            }

            $cantidadAcumulada = ($productos[$idProducto] ?? 0) + $cantidad;
            if ($cantidadAcumulada > 2147483647) {
                throw new InvalidArgumentException('La cantidad solicitada supera el máximo permitido.');
            }
            $productos[$idProducto] = $cantidadAcumulada;
        }

        return $productos;
    }

    private function validarPago($pagoEnviado) {
        if (!is_scalar($pagoEnviado)) {
            throw new InvalidArgumentException('Escribe el pago recibido.');
        }
        $pagoTexto = trim((string) $pagoEnviado);
        if (!preg_match('/^[0-9]+(?:\.[0-9]{1,2})?$/', $pagoTexto)) {
            throw new InvalidArgumentException('El pago recibido debe ser un importe válido.');
        }

        $pagoCentavos = $this->convertirACentavos($pagoTexto);
        if ($pagoCentavos > self::MAX_CENTAVOS) {
            throw new InvalidArgumentException('El pago recibido supera el máximo permitido.');
        }

        return $pagoCentavos;
    }

    private function convertirACentavos($valor) {
        if (!preg_match('/^[0-9]+(?:\.[0-9]{1,2})?$/', $valor)) {
            throw new InvalidArgumentException('El importe no es válido.');
        }
        $partes = explode('.', $valor, 2);
        $unidades = ltrim($partes[0], '0');
        if (strlen($unidades) > 8) {
            throw new InvalidArgumentException('El importe supera el máximo permitido.');
        }

        $centavos = ((int) $unidades * 100) + (int) str_pad($partes[1] ?? '', 2, '0');
        if ($centavos > self::MAX_CENTAVOS) {
            throw new InvalidArgumentException('El importe supera el máximo permitido.');
        }

        return $centavos;
    }

    private function convertirACentavosTexto($centavos) {
        return intdiv($centavos, 100) . '.' . str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT);
    }

    private function obtenerEntero($valor) {
        if (!is_scalar($valor)) {
            return null;
        }
        $texto = trim((string) $valor);
        if (!preg_match('/^[0-9]+$/', $texto)) {
            return null;
        }
        $texto = ltrim($texto, '0');
        if ($texto === '') {
            return 0;
        }
        $entero = filter_var($texto, FILTER_VALIDATE_INT);

        return $entero === false ? null : $entero;
    }

    private function revertirTransaccion() {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    private function redirigir($url) {
        header('Location: ' . $url);
        exit;
    }
}
