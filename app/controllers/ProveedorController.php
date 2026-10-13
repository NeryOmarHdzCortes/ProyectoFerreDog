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
    private const URL_CREAR = '/ProyectoFerredog/public/index.php?c=proveedor&a=crear';
    private const TIPOS_VALIDOS = ['Distribuidor', 'Mayorista', 'Fabricante'];

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

    /**
     * Registrar o actualizar un proveedor
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idProveedor = 0;
        if (isset($_POST['id_proveedor'])) {
            $idValidado = $this->obtenerEntero($_POST['id_proveedor']);
            if ($idValidado === null || $idValidado <= 0) {
                $_SESSION['mensaje_error'] = 'Proveedor no válido.';
                $this->redirigir(self::URL_LISTA);
            }
            $idProveedor = $idValidado;
        }

        if ($idProveedor > 0 && !$this->modeloProveedor->obtenerPorId($idProveedor)) {
            $_SESSION['mensaje_error'] = 'Proveedor no encontrado.';
            $this->redirigir(self::URL_LISTA);
        }

        $datosProveedor = [
            'nombre' => $this->obtenerTexto($_POST['nombre'] ?? ''),
            'tipo' => $this->obtenerTexto($_POST['tipo'] ?? ''),
            'contacto' => $this->obtenerTexto($_POST['contacto'] ?? ''),
            'telefono' => $this->obtenerTexto($_POST['telefono'] ?? ''),
            'correo' => $this->obtenerTexto($_POST['correo'] ?? ''),
            'direccion' => $this->obtenerTexto($_POST['direccion'] ?? '')
        ];
        $errores = [];

        if ($datosProveedor['nombre'] === '') {
            $errores[] = 'El nombre o razón social es obligatorio.';
        }
        if (!in_array($datosProveedor['tipo'], self::TIPOS_VALIDOS, true)) {
            $errores[] = 'Selecciona un tipo de proveedor válido.';
        }
        if ($datosProveedor['contacto'] === '') {
            $errores[] = 'La persona de contacto es obligatoria.';
        }
        if ($datosProveedor['telefono'] === '') {
            $errores[] = 'El teléfono es obligatorio.';
        }
        if ($datosProveedor['correo'] === '') {
            $errores[] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($datosProveedor['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }

        if (!empty($errores)) {
            $this->redirigirConErroresProveedor($errores, $idProveedor, $datosProveedor);
        }

        try {
            if ($this->modeloProveedor->existeNombre($datosProveedor['nombre'], $idProveedor ?: null)) {
                $errores[] = 'Ya existe otro proveedor con ese nombre o razón social.';
            }
            if ($this->modeloProveedor->existeCorreo($datosProveedor['correo'], $idProveedor ?: null)) {
                $errores[] = 'Ya existe otro proveedor con ese correo electrónico.';
            }

            if (!empty($errores)) {
                $this->redirigirConErroresProveedor($errores, $idProveedor, $datosProveedor);
            }

            if ($idProveedor === 0) {
                $this->modeloProveedor->crear(
                    $datosProveedor['nombre'],
                    $datosProveedor['tipo'],
                    $datosProveedor['contacto'],
                    $datosProveedor['telefono'],
                    $datosProveedor['correo'],
                    $datosProveedor['direccion']
                );
                $_SESSION['mensaje_exito'] = 'Proveedor creado correctamente.';
            } else {
                $this->modeloProveedor->editar(
                    $idProveedor,
                    $datosProveedor['nombre'],
                    $datosProveedor['tipo'],
                    $datosProveedor['contacto'],
                    $datosProveedor['telefono'],
                    $datosProveedor['correo'],
                    $datosProveedor['direccion']
                );
                $_SESSION['mensaje_exito'] = 'Proveedor actualizado correctamente.';
            }

            unset($_SESSION['datos_proveedor'], $_SESSION['errores']);
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al guardar proveedor: ' . $e->getMessage());
            $mensaje = (string) $e->getCode() === '23000'
                ? 'No se pudo guardar el proveedor porque el nombre o el correo ya está registrado.'
                : 'No se pudo guardar el proveedor. Intenta de nuevo.';
            $this->redirigirConErroresProveedor([$mensaje], $idProveedor, $datosProveedor);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al guardar proveedor: ' . $e->getMessage());
            $this->redirigirConErroresProveedor(
                ['No se pudo guardar el proveedor. Intenta de nuevo.'],
                $idProveedor,
                $datosProveedor
            );
        }

        $this->redirigir(self::URL_LISTA);
    }

    /**
     * Desactivar un proveedor sin borrar su registro
     */
    public function desactivar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idProveedor = $this->obtenerEntero($_POST['id_proveedor'] ?? null);
        if ($idProveedor === null || $idProveedor <= 0) {
            $_SESSION['mensaje_error'] = 'Proveedor no válido.';
            $this->redirigir(self::URL_LISTA);
        }

        if (!$this->modeloProveedor->obtenerPorId($idProveedor)) {
            $_SESSION['mensaje_error'] = 'Proveedor no encontrado.';
            $this->redirigir(self::URL_LISTA);
        }

        try {
            $this->modeloProveedor->desactivar($idProveedor);
            $_SESSION['mensaje_exito'] = 'Proveedor desactivado correctamente.';
        } catch (PDOException $e) {
            error_log('Error al desactivar proveedor: ' . $e->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo desactivar el proveedor. Intenta de nuevo.';
        }

        $this->redirigir(self::URL_LISTA);
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

    private function redirigirConErroresProveedor($errores, $idProveedor, $datosProveedor) {
        $_SESSION['datos_proveedor'] = $datosProveedor;
        $_SESSION['errores'] = $errores;
        $url = $idProveedor > 0
            ? '/ProyectoFerredog/public/index.php?c=proveedor&a=editar&id=' . $idProveedor
            : self::URL_CREAR;
        $this->redirigir($url);
    }

    private function redirigir($url) {
        header('Location: ' . $url);
        exit;
    }
}
