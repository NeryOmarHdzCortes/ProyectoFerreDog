<?php
/**
 * Controlador de autenticación
 * Maneja login, logout y recuperación de contraseña
 */

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Usuario.php';

class AuthController {
    private $pdo;
    private $modeloUsuario;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->modeloUsuario = new Usuario($this->pdo);
    }
    
    /**
     * Mostrar formulario de login
     */
    public function login() {
        // Si ya hay sesión, redirigir al inicio
        if (isset($_SESSION['Id_Usuario'])) {
            header('Location: /ProyectoFerredog/public/index.php?c=inicio&a=index');
            exit;
        }
        
        $error = '';
        $mensaje = '';
        
        // Verificar si viene de sesión expirada
        if (isset($_GET['sesion']) && $_GET['sesion'] === 'expirada') {
            $mensaje = 'Tu sesión expiró por inactividad. Inicia sesión nuevamente.';
        }
        
        // Procesar formulario POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            
            if (empty($usuario) || empty($contrasena)) {
                $error = 'Por favor completa todos los campos.';
            } else {
                $resultado = $this->modeloUsuario->autenticar($usuario, $contrasena);
                
                if ($resultado === false) {
                    // Usuario no existe o contraseña incorrecta
                    $error = 'Usuario o contraseña incorrectos. Al 5.º intento fallido la cuenta se bloquea 15 min.';
                } elseif (isset($resultado['bloqueado'])) {
                    // Cuenta bloqueada
                    $minutosRestantes = ceil($resultado['tiempo_restante'] / 60);
                    $error = "Tu cuenta está bloqueada temporalmente. Intenta nuevamente en {$minutosRestantes} minutos.";
                } elseif (isset($resultado['inactivo'])) {
                    // Cuenta inactiva
                    $error = 'Tu cuenta está inactiva. Contacta al administrador.';
                } else {
                    // Login exitoso
                    session_regenerate_id(true);
                    
                    $_SESSION['Id_Usuario'] = $resultado['Id_Usuario'];
                    $_SESSION['Usuario'] = $resultado['Usuario'];
                    $_SESSION['Nombre'] = $resultado['Nombre'] . ' ' . $resultado['Apellidos'];
                    $_SESSION['Rol'] = $resultado['Rol'];
                    $_SESSION['ultima_actividad'] = time();
                    
                    // Redirigir al inicio
                    header('Location: /ProyectoFerredog/public/index.php?c=inicio&a=index');
                    exit;
                }
            }
        }
        
        // Mostrar vista
        require_once BASE_PATH . '/app/views/login/login.php';
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        // Destruir sesión
        session_start();
        session_unset();
        session_destroy();
        
        // Redirigir al login
        header('Location: /ProyectoFerredog/public/index.php');
        exit;
    }
    
    /**
     * Recuperar contraseña (HU-1, tarea T4)
     * Se implementará en la siguiente tarea
     */
    public function recuperar() {
        require_once BASE_PATH . '/app/views/login/recuperar.php';
    }
}
