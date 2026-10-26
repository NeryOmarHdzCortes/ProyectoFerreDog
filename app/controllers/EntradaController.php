<?php
/**
 * Controlador de búsqueda de pedidos para registrar entradas (HU-3 T1).
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Pedido.php';

class EntradaController {
    private $modeloPedido;
    private $pdo;

    private const URL_BUSCADOR = '/ProyectoFerredog/public/index.php?c=entrada&a=index';

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloPedido = new Pedido($pdo);

        requerirRol(['Almacenista']);
    }

    /**
     * Busca pedidos pendientes por número, proveedor o producto (W-13 y W-14).
     */
    public function index() {
        $mensajeError = $_SESSION['mensaje_error'] ?? null;
        $mensajeExito = $_SESSION['mensaje_exito'] ?? null;
        unset($_SESSION['mensaje_error'], $_SESSION['mensaje_exito']);

        $busqueda = $this->obtenerTexto($_GET['busqueda'] ?? '');
        $busquedaRealizada = $busqueda !== '';
        $idSeleccionado = $this->obtenerEntero($_GET['id'] ?? null);
        $paginaSolicitada = $this->obtenerEntero($_GET['pagina'] ?? null);
        $pagina = $paginaSolicitada !== null && $paginaSolicitada > 0 ? $paginaSolicitada : 1;
        $limite = 10;
        $pedidoNoEncontrado = false;
        $pedido = null;
        $detalles = [];
        $mostrarResultadosBusqueda = false;

        if ($idSeleccionado !== null && $idSeleccionado > 0) {
            $pedido = $this->modeloPedido->buscarPorNumero($idSeleccionado);
            if ($pedido) {
                $detalles = $this->modeloPedido->listarDetallesPedido($idSeleccionado);
            } else {
                $pedidoNoEncontrado = true;
            }
        } elseif ($busquedaRealizada) {
            if (preg_match('/^#?([0-9]+)$/', $busqueda, $coincidencias) === 1) {
                $pedido = $this->modeloPedido->buscarPorNumero((int) $coincidencias[1]);
                if ($pedido) {
                    $detalles = $this->modeloPedido->listarDetallesPedido((int) $pedido['Id_Compras']);
                } else {
                    $pedidoNoEncontrado = true;
                }
            } else {
                $totalCoincidencias = $this->modeloPedido->contarPendientesPorTexto($busqueda);
                if ($totalCoincidencias === 1) {
                    $coincidencias = $this->modeloPedido->buscarPendientesPorTexto($busqueda, 1, 0);
                    $pedido = $coincidencias[0];
                    $detalles = $this->modeloPedido->listarDetallesPedido((int) $pedido['Id_Compras']);
                } elseif ($totalCoincidencias > 1) {
                    $mostrarResultadosBusqueda = true;
                } else {
                    $pedidoNoEncontrado = true;
                }
            }
        }

        $desplazamiento = ($pagina - 1) * $limite;
        if ($mostrarResultadosBusqueda) {
            $totalPedidos = $this->modeloPedido->contarPendientesPorTexto($busqueda);
            $pedidos = $this->modeloPedido->buscarPendientesPorTexto(
                $busqueda,
                $limite,
                $desplazamiento
            );
        } else {
            $totalPedidos = $this->modeloPedido->contarPorRecibir();
            $pedidos = $this->modeloPedido->listarPorRecibirPaginados(
                $limite,
                $desplazamiento
            );
        }
        $totalPaginas = (int) ceil($totalPedidos / $limite);
        if ($totalPaginas > 0 && $pagina > $totalPaginas) {
            $pagina = $totalPaginas;
            $desplazamiento = ($pagina - 1) * $limite;
            $pedidos = $mostrarResultadosBusqueda
                ? $this->modeloPedido->buscarPendientesPorTexto($busqueda, $limite, $desplazamiento)
                : $this->modeloPedido->listarPorRecibirPaginados($limite, $desplazamiento);
        }
        $parametrosPaginacion = [
            'c' => 'entrada',
            'a' => 'index'
        ];
        if ($mostrarResultadosBusqueda) {
            $parametrosPaginacion['busqueda'] = $busqueda;
        }

        $titulo = 'Registrar entrada de mercancía - Ferredog';
        $scriptExtra = 'entradas.js';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/entradas/registrar.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Valida los reportes del navegador y registra la recepción en una transacción.
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_BUSCADOR);
        }

        $idPedido = $this->obtenerEntero($_POST['id_compra'] ?? null);
        if ($idPedido === null || $idPedido <= 0) {
            $_SESSION['mensaje_error'] = 'Pedido no válido.';
            $this->redirigir(self::URL_BUSCADOR);
        }

        $idUsuario = $this->obtenerEntero($_SESSION['Id_Usuario'] ?? null);
        if ($idUsuario === null || $idUsuario <= 0) {
            $_SESSION['mensaje_error'] = 'No se pudo identificar al usuario de la sesión.';
            $this->redirigir($this->crearUrlPedido($idPedido));
        }

        try {
            $this->pdo->beginTransaction();

            if (!$this->modeloPedido->bloquearPedidoPendiente($idPedido)) {
                $this->pdo->rollBack();
                $_SESSION['mensaje_error'] = 'El pedido ya no está por recibir';
                $this->redirigir($this->crearUrlPedido($idPedido));
            }

            $detalles = $this->modeloPedido->listarDetallesParaRecepcion($idPedido);
            if (empty($detalles)) {
                throw new InvalidArgumentException('El pedido no tiene productos para recibir.');
            }

            $reportes = $this->obtenerReportesEnviados();
            $detallesPorId = [];
            foreach ($detalles as $detalle) {
                $idDetalle = (int) $detalle['Id_Detalle'];
                $detallesPorId[$idDetalle] = $detalle;
            }

            foreach ($reportes as $idDetalle => $reporte) {
                if (!isset($detallesPorId[$idDetalle])) {
                    throw new InvalidArgumentException('Un reporte no pertenece a este pedido.');
                }
                if ($detallesPorId[$idDetalle]['Id_Producto'] === null) {
                    throw new InvalidArgumentException('No se puede reportar un producto eliminado.');
                }
            }

            $recepciones = [];
            foreach ($detallesPorId as $idDetalle => $detalle) {
                $cantidadPedida = (int) $detalle['Cantidad'];
                if (!isset($reportes[$idDetalle])) {
                    $recepciones[$idDetalle] = [
                        'cantidad' => $cantidadPedida,
                        'incidencia' => 'Ninguna',
                        'motivo' => null
                    ];
                    continue;
                }

                $recepciones[$idDetalle] = $this->validarReporte(
                    $reportes[$idDetalle],
                    $cantidadPedida
                );
            }

            $consultaEmpleado = $this->pdo->prepare(
                'SELECT Id_Empleado FROM usuario WHERE Id_Usuario = ?'
            );
            $consultaEmpleado->execute([$idUsuario]);
            $idEmpleado = $consultaEmpleado->fetchColumn();
            if ($idEmpleado === false || $idEmpleado === null) {
                throw new InvalidArgumentException('No se encontró el empleado asociado al usuario.');
            }

            foreach ($recepciones as $idDetalle => $recepcion) {
                $this->modeloPedido->actualizarRecepcionDetalle(
                    $idPedido,
                    $idDetalle,
                    $recepcion['cantidad'],
                    $recepcion['incidencia'],
                    $recepcion['motivo']
                );
            }

            if ($this->modeloPedido->marcarPedidoRecibido($idPedido, $idEmpleado) === 0) {
                $this->pdo->rollBack();
                $_SESSION['mensaje_error'] = 'El pedido ya no está por recibir';
                $this->redirigir($this->crearUrlPedido($idPedido));
            }

            $this->pdo->commit();
            $_SESSION['mensaje_exito'] =
                'Entrada registrada. El pedido #' . $idPedido . ' pasó a Recibido.';
            $this->redirigir(self::URL_BUSCADOR);
        } catch (PDOException $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $estadoSql = (string) ($error->errorInfo[0] ?? $error->getCode());
            if ($estadoSql === '45000') {
                $_SESSION['mensaje_error'] = $error->errorInfo[2] ?? $error->getMessage();
            } else {
                error_log('Error SQL al registrar entrada del pedido #' . $idPedido . ': ' . $error->getMessage());
                $_SESSION['mensaje_error'] = 'No se pudo registrar la entrada. Intenta de nuevo.';
            }
        } catch (InvalidArgumentException $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $_SESSION['mensaje_error'] = $error->getMessage();
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al registrar entrada del pedido #' . $idPedido . ': ' . $error->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo registrar la entrada. Intenta de nuevo.';
        }

        $this->redirigir($this->crearUrlPedido($idPedido));
    }

    private function obtenerReportesEnviados() {
        $reportes = $_POST['reportes'] ?? [];
        if (!is_array($reportes)) {
            throw new InvalidArgumentException('Los reportes de entrada no son válidos.');
        }

        $resultado = [];
        foreach ($reportes as $idDetalleEnviado => $reporte) {
            $idDetalle = $this->obtenerEntero($idDetalleEnviado);
            if ($idDetalle === null || $idDetalle <= 0 || !is_array($reporte)) {
                throw new InvalidArgumentException('Los reportes de entrada no son válidos.');
            }
            $resultado[$idDetalle] = $reporte;
        }

        return $resultado;
    }

    private function validarReporte($reporte, $cantidadPedida) {
        $cantidadTexto = $reporte['cantidad_recibida'] ?? null;
        $incidencia = $reporte['incidencia'] ?? null;
        $motivoEnviado = $reporte['motivo'] ?? '';

        if (!is_scalar($cantidadTexto) || preg_match('/^[0-9]+$/', (string) $cantidadTexto) !== 1) {
            throw new InvalidArgumentException('La cantidad recibida debe ser un número entero.');
        }

        $cantidadRecibida = (int) $cantidadTexto;
        if ($cantidadRecibida < 0 || $cantidadRecibida > $cantidadPedida) {
            throw new InvalidArgumentException('La cantidad recibida está fuera del rango permitido.');
        }
        if (!is_string($incidencia) || !in_array($incidencia, ['Ninguna', 'Faltante', 'Dañado'], true)) {
            throw new InvalidArgumentException('La incidencia del producto no es válida.');
        }

        if ($incidencia === 'Ninguna') {
            if ($cantidadRecibida !== $cantidadPedida) {
                throw new InvalidArgumentException('Sin incidencia, la cantidad recibida debe ser igual a la pedida.');
            }

            return [
                'cantidad' => $cantidadRecibida,
                'incidencia' => 'Ninguna',
                'motivo' => null
            ];
        }

        $motivo = is_scalar($motivoEnviado) ? trim((string) $motivoEnviado) : '';
        if ($cantidadRecibida >= $cantidadPedida) {
            throw new InvalidArgumentException('Con una incidencia, la cantidad recibida debe ser menor a la pedida.');
        }
        if ($motivo === '' || mb_strlen($motivo, 'UTF-8') > 200) {
            throw new InvalidArgumentException('El motivo es obligatorio y debe tener máximo 200 caracteres.');
        }

        return [
            'cantidad' => $cantidadRecibida,
            'incidencia' => $incidencia,
            'motivo' => $motivo
        ];
    }

    private function crearUrlPedido($idPedido) {
        return self::URL_BUSCADOR . '&id=' . (int) $idPedido;
    }

    private function redirigir($url) {
        header('Location: ' . $url);
        exit;
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
}
