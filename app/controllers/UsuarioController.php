<?php
/**
 * Controlador de Usuarios
 * Solo accesible por Administrador
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Usuario.php';

class UsuarioController {
    private $pdo;
    private $modeloUsuario;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloUsuario = new Usuario($this->pdo);
        
        // Solo Administrador puede acceder
        requerirRol(['Administrador']);
    }
    
    /**
     * Lista de usuarios con buscador y filtros
     */
    public function lista() {
        $busqueda = $_GET['busqueda'] ?? '';
        $filtroRol = $_GET['rol'] ?? '';
        $filtroEstado = $_GET['estado'] ?? '';
        
        // Obtener usuarios
        $usuarios = $this->modeloUsuario->obtenerTodos($busqueda, $filtroRol, $filtroEstado);
        
        $titulo = 'Usuarios - Ferredog';
        $scriptExtra = 'usuarios.js';
        
        // Cargar vista
        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/usuarios/lista.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }
    
    /**
     * Cambiar estado de un usuario (activar/desactivar)
     */
    public function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=lista');
            exit;
        }
        
        $idUsuario = $_POST['id_usuario'] ?? 0;
        $nuevoEstado = $_POST['nuevo_estado'] ?? '';
        
        // No permitir desactivarse a sí mismo
        if ($idUsuario == $_SESSION['Id_Usuario']) {
            $_SESSION['mensaje_error'] = 'No puedes desactivar tu propia cuenta.';
            header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=lista');
            exit;
        }
        
        // Cambiar estado
        $sql = "UPDATE usuario SET Estado = :estado WHERE Id_Usuario = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'estado' => $nuevoEstado,
            'id' => $idUsuario
        ]);
        
        $_SESSION['mensaje_exito'] = 'Estado del usuario actualizado correctamente.';
        header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=lista');
        exit;
    }
    
    /**
     * Formulario para crear usuario
     */
    public function crear() {
        $titulo = 'Agregar usuario - Ferredog';
        $scriptExtra = 'usuarios.js';
        $accion = 'crear';
        
        // Cargar vista
        require_once BASE_PATH . '/app/views/layout/header.php';
        require_once BASE_PATH . '/app/views/layout/sidebar.php';
        require_once BASE_PATH . '/app/views/usuarios/formulario.php';
        require_once BASE_PATH . '/app/views/layout/footer.php';
    }
    
    /**
     * Formulario para editar usuario
     */
    public function editar() {
        $idUsuario = $_GET['id'] ?? 0;
        
        // Obtener datos del usuario
        $sql = "SELECT u.Id_Usuario, u.Usuario, u.Rol, u.Estado, u.Id_Empleado,
                       e.Nombre, e.Apellidos, e.Correo
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE u.Id_Usuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=lista');
            exit;
        }
        
        $titulo = 'Editar usuario - Ferredog';
        $scriptExtra = 'usuarios.js';
        $accion = 'editar';
        
        // Cargar vista
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
            header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=lista');
            exit;
        }
        
        $idUsuario = $_POST['id_usuario'] ?? 0;
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $rol = $_POST['rol'] ?? '';
        $estado = $_POST['estado'] ?? 'Activo';
        
        // Validaciones
        $errores = [];
        
        if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
        if (empty($apellidos)) $errores[] = 'Los apellidos son obligatorios.';
        if (empty($nombreUsuario)) $errores[] = 'El nombre de usuario es obligatorio.';
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }
        if (!in_array($rol, ['Administrador', 'Almacenista', 'Vendedor'])) {
            $errores[] = 'El rol seleccionado no es válido.';
        }
        
        if ($idUsuario == 0) {
            // Crear: validar contraseña
            $contrasena = $_POST['contrasena'] ?? '';
            $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
            
            if (empty($contrasena)) {
                $errores[] = 'La contraseña es obligatoria.';
            } elseif ($contrasena !== $confirmarContrasena) {
                $errores[] = 'Las contraseñas no coinciden.';
            } elseif (!$this->validarContrasenaSegura($contrasena)) {
                $errores[] = 'La contraseña no cumple con los requisitos de seguridad.';
            }
            
            // Verificar duplicados
            if ($this->modeloUsuario->existeUsuario($nombreUsuario)) {
                $errores[] = 'Este nombre de usuario ya existe.';
            }
            if ($this->modeloUsuario->existeCorreo($correo)) {
                $errores[] = 'Este correo electrónico ya está registrado.';
            }
        } else {
            // Editar: verificar duplicados excluyendo al mismo usuario
            $sql = "SELECT Id_Empleado FROM usuario WHERE Id_Usuario = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $idUsuario]);
            $idEmpleado = $stmt->fetchColumn();
            
            if ($this->modeloUsuario->existeCorreo($correo, $idEmpleado)) {
                $errores[] = 'Este correo electrónico ya está registrado.';
            }
            
            // No permitir quitarse el rol de Administrador a sí mismo
            if ($idUsuario == $_SESSION['Id_Usuario'] && $rol !== 'Administrador') {
                $errores[] = 'No puedes quitarte el rol de Administrador.';
            }
        }
        
        if (!empty($errores)) {
            $_SESSION['mensaje_error'] = implode('<br>', $errores);
            $_SESSION['datos_formulario'] = $_POST;
            
            if ($idUsuario == 0) {
                header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=crear');
            } else {
                header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=editar&id=' . $idUsuario);
            }
            exit;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            if ($idUsuario == 0) {
                // Crear usuario
                $this->modeloUsuario->crear($nombre, $apellidos, $correo, $nombreUsuario, $contrasena, $rol, $estado);
                $_SESSION['mensaje_exito'] = 'Usuario creado correctamente.';
            } else {
                // Editar usuario
                $this->modeloUsuario->editar($idUsuario, $nombre, $apellidos, $correo, $rol, $estado);
                $_SESSION['mensaje_exito'] = 'Usuario actualizado correctamente.';
            }
            
            $this->pdo->commit();
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['mensaje_error'] = 'Error al guardar el usuario: ' . $e->getMessage();
        }
        
        header('Location: /ProyectoFerredog/public/index.php?c=usuario&a=lista');
        exit;
    }
    
    /**
     * Validar contraseña segura
     */
    private function validarContrasenaSegura($contrasena) {
        if (strlen($contrasena) < 8) return false;
        if (!preg_match('/[A-Z]/', $contrasena)) return false;
        if (!preg_match('/[a-z]/', $contrasena)) return false;
        if (!preg_match('/[0-9]/', $contrasena)) return false;
        if (!preg_match('/[^A-Za-z0-9]/', $contrasena)) return false;
        return true;
    }
}

