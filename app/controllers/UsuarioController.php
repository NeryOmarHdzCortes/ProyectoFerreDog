<?php
/**
 * Controlador de Usuarios (HU-4)
 * Solo accesible por Administrador
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Usuario.php';

class UsuarioController {
    private $pdo;
    private $modeloUsuario;

    private const URL_LISTA = '/ProyectoFerredog/public/index.php?c=usuario&a=lista';
    private const ROLES_VALIDOS = ['Administrador', 'Almacenista', 'Vendedor'];
    private const ESTADOS_VALIDOS = ['Activo', 'Inactivo'];

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloUsuario = new Usuario($this->pdo);

        // Solo Administrador puede acceder
        requerirRol(['Administrador']);
    }

    /**
     * Lista de usuarios con buscador y filtros (W-04)
     */
    public function lista() {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $filtroRol = $_GET['rol'] ?? '';
        $filtroEstado = $_GET['estado'] ?? '';

        $usuarios = $this->modeloUsuario->obtenerTodos($busqueda, $filtroRol, $filtroEstado);

        $titulo = 'Usuarios - Ferredog';
        $scriptExtra = 'usuarios.js';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/usuarios/lista.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Activar o desactivar un usuario desde la lista
     */
    public function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idUsuario = (int) ($_POST['id_usuario'] ?? 0);
        $nuevoEstado = $_POST['nuevo_estado'] ?? '';

        if ($idUsuario <= 0 || !in_array($nuevoEstado, self::ESTADOS_VALIDOS, true)) {
            $_SESSION['mensaje_error'] = 'Solicitud no válida.';
            $this->redirigir(self::URL_LISTA);
        }

        // No permitir desactivarse a sí mismo
        if ($idUsuario === (int) $_SESSION['Id_Usuario']) {
            $_SESSION['mensaje_error'] = 'No puedes desactivar tu propia cuenta.';
            $this->redirigir(self::URL_LISTA);
        }

        $this->modeloUsuario->cambiarEstado($idUsuario, $nuevoEstado);

        $_SESSION['mensaje_exito'] = 'Estado del usuario actualizado correctamente.';
        $this->redirigir(self::URL_LISTA);
    }

    /**
     * Formulario para crear usuario (W-05)
     */
    public function crear() {
        $titulo = 'Agregar usuario - Ferredog';
        $scriptExtra = 'usuarios.js';
        $accion = 'crear';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/usuarios/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Formulario para editar usuario (W-05 con datos cargados)
     */
    public function editar() {
        $idUsuario = (int) ($_GET['id'] ?? 0);

        $usuario = $this->modeloUsuario->obtenerPorId($idUsuario);

        if (!$usuario) {
            $this->redirigir(self::URL_LISTA);
        }

        $titulo = 'Editar usuario - Ferredog';
        $scriptExtra = 'usuarios.js';
        $accion = 'editar';

        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/usuarios/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Guardar usuario (crear o editar)
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir(self::URL_LISTA);
        }

        $idUsuario = (int) ($_POST['id_usuario'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $rol = $_POST['rol'] ?? '';
        $estado = $_POST['estado'] ?? 'Activo';
        $contrasena = $_POST['contrasena'] ?? '';
        $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

        // Validaciones comunes
        $errores = [];

        if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
        if ($apellidos === '') $errores[] = 'Los apellidos son obligatorios.';
        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }
        if (!in_array($rol, self::ROLES_VALIDOS, true)) {
            $errores[] = 'El rol seleccionado no es válido.';
        }
        if (!in_array($estado, self::ESTADOS_VALIDOS, true)) {
            $errores[] = 'El estado seleccionado no es válido.';
        }

        if ($idUsuario === 0) {
            // Crear: nombre de usuario y contraseña
            if ($nombreUsuario === '') {
                $errores[] = 'El nombre de usuario es obligatorio.';
            } elseif ($this->modeloUsuario->existeUsuario($nombreUsuario)) {
                $errores[] = 'Este nombre de usuario ya existe.';
            }

            if ($contrasena === '') {
                $errores[] = 'La contraseña es obligatoria.';
            } elseif ($contrasena !== $confirmarContrasena) {
                $errores[] = 'Las contraseñas no coinciden.';
            } elseif (!$this->validarContrasenaSegura($contrasena)) {
                $errores[] = 'La contraseña no cumple con los requisitos de seguridad.';
            }

            if ($this->modeloUsuario->existeCorreo($correo)) {
                $errores[] = 'Este correo electrónico ya está registrado.';
            }
        } else {
            // Editar: el usuario debe existir y el correo no puede ser de otro empleado
            $idEmpleado = $this->modeloUsuario->obtenerIdEmpleado($idUsuario);

            if (!$idEmpleado) {
                $this->redirigir(self::URL_LISTA);
            }

            if ($this->modeloUsuario->existeCorreo($correo, $idEmpleado)) {
                $errores[] = 'Este correo electrónico ya está registrado.';
            }

            // El administrador no puede quitarse su rol ni desactivarse a sí mismo
            if ($idUsuario === (int) $_SESSION['Id_Usuario']) {
                if ($rol !== 'Administrador') {
                    $errores[] = 'No puedes quitarte el rol de Administrador.';
                }
                if ($estado !== 'Activo') {
                    $errores[] = 'No puedes desactivar tu propia cuenta.';
                }
            }
        }

        if (!empty($errores)) {
            $_SESSION['mensaje_error'] = implode('<br>', $errores);
            // No se guardan las contraseñas en sesión
            $datos = $_POST;
            unset($datos['contrasena'], $datos['confirmar_contrasena']);
            $_SESSION['datos_formulario'] = $datos;

            if ($idUsuario === 0) {
                $this->redirigir('/ProyectoFerredog/public/index.php?c=usuario&a=crear');
            }
            $this->redirigir('/ProyectoFerredog/public/index.php?c=usuario&a=editar&id=' . $idUsuario);
        }

        // Escribe en empleados y usuario: todo o nada
        try {
            $this->pdo->beginTransaction();

            if ($idUsuario === 0) {
                $this->modeloUsuario->crear($nombre, $apellidos, $correo, $nombreUsuario, $contrasena, $rol, $estado);
                $_SESSION['mensaje_exito'] = 'Usuario creado correctamente.';
            } else {
                $this->modeloUsuario->editar($idUsuario, $nombre, $apellidos, $correo, $rol, $estado);
                $_SESSION['mensaje_exito'] = 'Usuario actualizado correctamente.';
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error al guardar usuario: ' . $e->getMessage());
            $_SESSION['mensaje_error'] = 'No se pudo guardar el usuario. Intenta de nuevo.';
        }

        $this->redirigir(self::URL_LISTA);
    }

    /**
     * Contraseña segura: mínimo 8 caracteres, mayúscula, minúscula, número y símbolo
     */
    private function validarContrasenaSegura($contrasena) {
        return strlen($contrasena) >= 8
            && preg_match('/[A-Z]/', $contrasena)
            && preg_match('/[a-z]/', $contrasena)
            && preg_match('/[0-9]/', $contrasena)
            && preg_match('/[^A-Za-z0-9]/', $contrasena);
    }

    /**
     * Redirigir y terminar la ejecución
     */
    private function redirigir($url) {
        header('Location: ' . $url);
        exit;
    }
}