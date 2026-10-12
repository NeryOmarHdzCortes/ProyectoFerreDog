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
        
        // Verificar si viene de recuperación exitosa
        if (isset($_SESSION['mensaje_login'])) {
            $mensaje = $_SESSION['mensaje_login'];
            unset($_SESSION['mensaje_login']);
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
     * Recuperar contraseña (HU-1, requisito 3)
     * Una sola página con dos pasos
     */
    public function recuperar() {
        // Si ya hay sesión, redirigir al inicio
        if (isset($_SESSION['Id_Usuario'])) {
            header('Location: /ProyectoFerredog/public/index.php?c=inicio&a=index');
            exit;
        }
        
        $error = '';
        $exito = '';
        $paso = 1;
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            
            // === PASO 1: Solicitar código ===
            if ($accion === 'enviar_codigo' || $accion === 'reenviar_codigo') {
                $correo = trim($_POST['correo'] ?? '');
                
                if (empty($correo)) {
                    $error = 'Por favor ingresa tu correo electrónico.';
                } else {
                    // Buscar usuario por correo (solo activos)
                    $usuario = $this->modeloUsuario->buscarPorCorreo($correo);
                    
                    // Siempre mostrar el mismo mensaje (seguridad)
                    $exito = 'Si el correo está registrado, recibirás un código.';
                    
                    if ($usuario) {
                        // Generar código de 6 dígitos
                        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                        
                        // Guardar código hasheado en BD
                        $this->modeloUsuario->guardarCodigoRecuperacion($usuario['Id_Usuario'], $codigo);
                        
                        // Enviar por correo
                        require_once BASE_PATH . '/app/libraries/Correo.php';
                        $servicioCorreo = new Correo();
                        $nombre = $usuario['Nombre'] . ' ' . $usuario['Apellidos'];
                        $resultado = $servicioCorreo->enviarCodigo($correo, $nombre, $codigo);
                        
                        if ($resultado['exito']) {
                            $exito = $resultado['mensaje'];
                            // Guardar ID en sesión para el paso 2
                            $_SESSION['recuperacion_id'] = $usuario['Id_Usuario'];
                            $_SESSION['recuperacion_correo'] = $correo;
                            $paso = 2;
                        } else {
                            $error = $resultado['mensaje'];
                        }
                    }
                }
            }
            
            // === PASO 2: Verificar código y cambiar contraseña ===
            elseif ($accion === 'cambiar_contrasena') {
                $codigoIngresado = trim($_POST['codigo'] ?? '');
                $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
                $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
                
                // Validar que esté en proceso de recuperación
                if (!isset($_SESSION['recuperacion_id'])) {
                    $error = 'Sesión de recuperación expirada. Solicita un nuevo código.';
                    $paso = 1;
                } else {
                    $idUsuario = $_SESSION['recuperacion_id'];
                    
                    // Validar campos
                    if (empty($codigoIngresado) || empty($nuevaContrasena) || empty($confirmarContrasena)) {
                        $error = 'Por favor completa todos los campos.';
                        $paso = 2;
                    } elseif ($nuevaContrasena !== $confirmarContrasena) {
                        $error = 'Las contraseñas no coinciden.';
                        $paso = 2;
                    } elseif (!$this->validarContrasenaSegura($nuevaContrasena)) {
                        $error = 'La contraseña no cumple con los requisitos de seguridad.';
                        $paso = 2;
                    } else {
                        // Verificar código
                        if ($this->modeloUsuario->verificarCodigoRecuperacion($idUsuario, $codigoIngresado)) {
                            // Código válido, cambiar contraseña
                            $this->modeloUsuario->cambiarContrasena($idUsuario, $nuevaContrasena);
                            
                            // Limpiar sesión de recuperación
                            unset($_SESSION['recuperacion_id']);
                            unset($_SESSION['recuperacion_correo']);
                            
                            // Redirigir al login con mensaje de éxito
                            $_SESSION['mensaje_login'] = 'Contraseña cambiada exitosamente. Ya puedes iniciar sesión.';
                            header('Location: /ProyectoFerredog/public/index.php?c=auth&a=login');
                            exit;
                        } else {
                            $error = 'El código es incorrecto o ya expiró. Solicita uno nuevo.';
                            $paso = 1;
                            unset($_SESSION['recuperacion_id']);
                            unset($_SESSION['recuperacion_correo']);
                        }
                    }
                }
            }
        }
        
        // Si hay sesión de recuperación activa, ir al paso 2
        if (isset($_SESSION['recuperacion_id']) && $paso === 1 && empty($_POST)) {
            $paso = 2;
        }
        
        // Mostrar vista
        require_once BASE_PATH . '/app/views/login/recuperar.php';
    }
    
    /**
     * Validar que la contraseña sea segura
     * Mínimo 8 caracteres, con al menos una mayúscula, una minúscula, un número y un símbolo
     */
    private function validarContrasenaSegura($contrasena) {
        if (strlen($contrasena) < 8) {
            return false;
        }
        
        $tieneMayuscula = preg_match('/[A-Z]/', $contrasena);
        $tieneMinuscula = preg_match('/[a-z]/', $contrasena);
        $tieneNumero = preg_match('/[0-9]/', $contrasena);
        $tieneSimbolo = preg_match('/[^A-Za-z0-9]/', $contrasena);
        
        return $tieneMayuscula && $tieneMinuscula && $tieneNumero && $tieneSimbolo;
    }
}
