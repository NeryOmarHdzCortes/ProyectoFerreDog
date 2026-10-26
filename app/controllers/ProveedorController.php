<?php
/**
 * Controlador de Proveedores (HU-6)
 * Solo accesible por Administrador
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Proveedor.php';
require_once BASE_PATH . '/app/libraries/Validador.php';

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
        $filtroEstado = $this->obtenerTexto($_GET['estado'] ?? 'Activo');
        if (!in_array($filtroEstado, ['', 'Activo', 'Inactivo'], true)) {
            $filtroEstado = 'Activo';
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
        $datosFormulario = [
            'nombre' => '',
            'tipo' => '',
            'contacto' => '',
            'telefono' => '',
            'correo' => '',
            'direccion' => ''
        ];
        $datosFormulario = array_merge($datosFormulario, $_SESSION['old'] ?? []);

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

        if ($idProveedor > 0) {
            try {
                $proveedorExistente = $this->modeloProveedor->obtenerPorId($idProveedor);
            } catch (Exception $e) {
                error_log('Error al validar proveedor: ' . $e->getMessage());
                $this->redirigirConErroresProveedor(
                    ['No se pudo validar el proveedor. Intenta de nuevo.'],
                    $idProveedor,
                    [],
                    []
                );
            }

            if (!$proveedorExistente) {
                $_SESSION['mensaje_error'] = 'Proveedor no encontrado.';
                $this->redirigir(self::URL_LISTA);
            }
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
        $erroresPorCampo = [];

        if ($datosProveedor['nombre'] === '') {
            $errores[] = 'El nombre o razón social es obligatorio.';
            $erroresPorCampo['nombre'] = 'El nombre o razón social es obligatorio.';
        }
        if (!in_array($datosProveedor['tipo'], self::TIPOS_VALIDOS, true)) {
            $errores[] = 'Selecciona un tipo de proveedor válido.';
            $erroresPorCampo['tipo'] = 'Selecciona un tipo de proveedor válido.';
        }
        if ($datosProveedor['contacto'] === '') {
            $errores[] = 'La persona de contacto es obligatoria.';
            $erroresPorCampo['contacto'] = 'La persona de contacto es obligatoria.';
        }
        if (!Validador::telefonoValido($datosProveedor['telefono'])) {
            $mensajeTelefono = 'El teléfono debe tener 10 dígitos, solo números.';
            $errores[] = $mensajeTelefono;
            $erroresPorCampo['telefono'] = $mensajeTelefono;
        }
        if ($datosProveedor['correo'] === '') {
            $mensajeCorreo = 'El correo es obligatorio';
            $errores[] = $mensajeCorreo;
            $erroresPorCampo['correo'] = $mensajeCorreo;
        } elseif (!Validador::correoValido($datosProveedor['correo'])) {
            $mensajeCorreo = 'Escribe un correo válido, por ejemplo nombre@gmail.com.';
            $errores[] = $mensajeCorreo;
            $erroresPorCampo['correo'] = $mensajeCorreo;
        }

        if (!empty($errores)) {
            $this->redirigirConErroresProveedor($errores, $idProveedor, $datosProveedor, $erroresPorCampo);
        }

        try {
            if ($this->modeloProveedor->existeNombre($datosProveedor['nombre'], $idProveedor ?: null)) {
                $errores[] = 'Ya existe otro proveedor con ese nombre o razón social.';
                $erroresPorCampo['nombre'] = 'Ya existe otro proveedor con ese nombre o razón social.';
            }
            if ($this->modeloProveedor->existeCorreo($datosProveedor['correo'], $idProveedor ?: null)) {
                $errores[] = 'Ya existe otro proveedor con ese correo electrónico.';
                $erroresPorCampo['correo'] = 'Ya existe otro proveedor con ese correo electrónico.';
            }

            if (!empty($errores)) {
                $this->redirigirConErroresProveedor($errores, $idProveedor, $datosProveedor, $erroresPorCampo);
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

            unset($_SESSION['old'], $_SESSION['error'], $_SESSION['error_campos']);
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al guardar proveedor: ' . $e->getMessage());
            $esTelefonoInvalido = (string) $e->getCode() === '23000'
                && strpos($e->getMessage(), 'chk_prov_telefono') !== false;
            $mensaje = $esTelefonoInvalido
                ? 'El teléfono debe tener 10 dígitos, solo números.'
                : ((string) $e->getCode() === '23000'
                    ? 'No se pudo guardar el proveedor porque el nombre o el correo ya está registrado.'
                    : 'No se pudo guardar el proveedor. Intenta de nuevo.');
            $camposError = $esTelefonoInvalido
                ? ['telefono' => 'El teléfono debe tener 10 dígitos, solo números.']
                : ((string) $e->getCode() === '23000'
                    ? ['nombre' => 'Revisa si el nombre ya está registrado.', 'correo' => 'Revisa si el correo ya está registrado.']
                    : []);
            $this->redirigirConErroresProveedor([$mensaje], $idProveedor, $datosProveedor, $camposError);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al guardar proveedor: ' . $e->getMessage());
            $this->redirigirConErroresProveedor(
                ['No se pudo guardar el proveedor. Intenta de nuevo.'],
                $idProveedor,
                $datosProveedor,
                []
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

    /**
     * Eliminar definitivamente un proveedor inactivo y desasignar sus productos.
     */
    public function eliminar() {
        requerirRol(['Administrador']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idProveedor = $this->obtenerEntero($_POST['id_proveedor'] ?? null);
        if ($idProveedor === null || $idProveedor <= 0) {
            $_SESSION['mensaje_error'] = 'Proveedor no válido.';
            $this->redirigir(self::URL_LISTA);
        }

        try {
            $resultado = $this->modeloProveedor->eliminar($idProveedor);
            if ($resultado['resultado'] === 'activo') {
                $_SESSION['mensaje_error'] = 'Primero desactiva el registro para poder eliminarlo.';
            } elseif ($resultado['resultado'] === 'borrado') {
                $_SESSION['mensaje_exito'] = 'Proveedor eliminado. '
                    . (int) $resultado['productos_desasignados']
                    . ' productos quedaron sin proveedor.';
            } else {
                $_SESSION['mensaje_error'] = 'Proveedor no encontrado.';
            }
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al eliminar proveedor: ' . $error->getMessage());
            $codigoSql = $error instanceof PDOException
                ? (string) ($error->errorInfo[0] ?? $error->getCode())
                : '';
            $_SESSION['mensaje_error'] = $codigoSql === '23000'
                ? 'No se puede eliminar el proveedor porque tiene registros relacionados.'
                : 'No se pudo eliminar el proveedor. Intenta de nuevo.';
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

    private function redirigirConErroresProveedor($errores, $idProveedor, $datosProveedor, $erroresPorCampo = []) {
        $_SESSION['old'] = $this->obtenerDatosFormularioProveedor();
        $_SESSION['error'] = implode(' ', array_unique($errores));
        $_SESSION['error_campos'] = $erroresPorCampo;
        $url = $idProveedor > 0
            ? '/ProyectoFerredog/public/index.php?c=proveedor&a=editar&id=' . $idProveedor
            : self::URL_CREAR;
        $this->redirigir($url);
    }

    private function obtenerDatosFormularioProveedor() {
        $campos = ['nombre', 'tipo', 'contacto', 'telefono', 'correo', 'direccion'];
        $datos = [];

        foreach ($campos as $campo) {
            if (isset($_POST[$campo]) && is_scalar($_POST[$campo])) {
                $datos[$campo] = (string) $_POST[$campo];
            }
        }

        return $datos;
    }

    private function redirigir($url) {
        header('Location: ' . $url);
        exit;
    }
}
