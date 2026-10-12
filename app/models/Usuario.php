<?php
/**
 * Modelo Usuario
 * Maneja las tablas usuario y empleados
 */

class Usuario {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Autenticar usuario por nombre de usuario y contraseña
     * @param string $nombreUsuario
     * @param string $contrasena
     * @return array|false Datos del usuario si es válido, false si no
     */
    public function autenticar($nombreUsuario, $contrasena) {
        $sql = "SELECT u.Id_Usuario, u.Usuario, u.Password, u.Rol, u.Estado, u.Intentos_Fallidos, 
                       u.Fecha_Bloqueo, e.Nombre, e.Apellidos
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE u.Usuario = :usuario";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario' => $nombreUsuario]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            return false; // Usuario no existe
        }
        
        // Verificar si está bloqueado temporalmente
        if ($usuario['Fecha_Bloqueo'] !== null) {
            $tiempoBloqueo = strtotime($usuario['Fecha_Bloqueo']);
            $tiempoActual = time();
            $tiempoTranscurrido = $tiempoActual - $tiempoBloqueo;
            
            // Bloqueado por 15 minutos = 900 segundos
            if ($tiempoTranscurrido < 900) {
                return ['bloqueado' => true, 'tiempo_restante' => 900 - $tiempoTranscurrido];
            } else {
                // Ya pasó el tiempo de bloqueo, reiniciar
                $this->reiniciarBloqueo($usuario['Id_Usuario']);
                $usuario['Intentos_Fallidos'] = 0;
                $usuario['Fecha_Bloqueo'] = null;
            }
        }
        
        // Verificar si está inactivo
        if ($usuario['Estado'] === 'Inactivo') {
            return ['inactivo' => true];
        }
        
        // Verificar contraseña
        if (!password_verify($contrasena, $usuario['Password'])) {
            // Contraseña incorrecta, sumar intento fallido
            $this->registrarIntentoFallido($usuario['Id_Usuario'], $usuario['Intentos_Fallidos']);
            return false;
        }
        
        // Autenticación exitosa, reiniciar intentos
        $this->reiniciarIntentos($usuario['Id_Usuario']);
        
        return $usuario;
    }
    
    /**
     * Registrar un intento fallido de login
     * Si llega a 5, bloquear la cuenta
     */
    private function registrarIntentoFallido($idUsuario, $intentosActuales) {
        $nuevosIntentos = $intentosActuales + 1;
        
        if ($nuevosIntentos >= 5) {
            // Bloquear cuenta
            $sql = "UPDATE usuario 
                    SET Intentos_Fallidos = :intentos, Fecha_Bloqueo = NOW() 
                    WHERE Id_Usuario = :id";
        } else {
            // Solo incrementar intentos
            $sql = "UPDATE usuario 
                    SET Intentos_Fallidos = :intentos 
                    WHERE Id_Usuario = :id";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'intentos' => $nuevosIntentos,
            'id' => $idUsuario
        ]);
    }
    
    /**
     * Reiniciar intentos fallidos a 0 (login exitoso)
     */
    private function reiniciarIntentos($idUsuario) {
        $sql = "UPDATE usuario 
                SET Intentos_Fallidos = 0, Fecha_Bloqueo = NULL 
                WHERE Id_Usuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);
    }
    
    /**
     * Reiniciar bloqueo cuando ya pasó el tiempo
     */
    private function reiniciarBloqueo($idUsuario) {
        $sql = "UPDATE usuario 
                SET Intentos_Fallidos = 0, Fecha_Bloqueo = NULL 
                WHERE Id_Usuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);
    }
    
    /**
     * Obtener todos los usuarios con sus datos de empleado
     * @param string $busqueda Término de búsqueda opcional
     * @param string $rol Filtro de rol opcional
     * @param string $estado Filtro de estado opcional
     * @return array Lista de usuarios
     */
    public function obtenerTodos($busqueda = '', $rol = '', $estado = '') {
        $sql = "SELECT u.Id_Usuario, u.Usuario, u.Rol, u.Estado, 
                       e.Nombre, e.Apellidos, e.Correo
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE 1=1";
        
        $params = [];
        
        if ($busqueda) {
            $sql .= " AND (e.Nombre LIKE :busqueda OR e.Apellidos LIKE :busqueda OR u.Usuario LIKE :busqueda)";
            $params['busqueda'] = "%{$busqueda}%";
        }
        
        if ($rol) {
            $sql .= " AND u.Rol = :rol";
            $params['rol'] = $rol;
        }
        
        if ($estado) {
            $sql .= " AND u.Estado = :estado";
            $params['estado'] = $estado;
        }
        
        $sql .= " ORDER BY e.Nombre, e.Apellidos";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
}

    
    /**
     * Buscar usuario por correo (solo activos)
     * @param string $correo
     * @return array|false Datos del usuario si existe y está activo, false si no
     */
    public function buscarPorCorreo($correo) {
        $sql = "SELECT u.Id_Usuario, u.Usuario, u.Estado, e.Nombre, e.Apellidos, e.Correo
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE e.Correo = :correo AND u.Estado = 'Activo'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        
        return $stmt->fetch();
    }
    
    /**
     * Guardar código de recuperación con expiración de 10 minutos
     * @param int $idUsuario
     * @param string $codigo Código en texto plano (se guarda con hash)
     */
    public function guardarCodigoRecuperacion($idUsuario, $codigo) {
        $codigoHash = password_hash($codigo, PASSWORD_DEFAULT);
        $expiracion = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        $sql = "UPDATE usuario 
                SET Codigo_Recuperacion = :codigo, Codigo_Expiracion = :expiracion 
                WHERE Id_Usuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoHash,
            'expiracion' => $expiracion,
            'id' => $idUsuario
        ]);
    }
    
    /**
     * Verificar código de recuperación
     * @param int $idUsuario
     * @param string $codigo Código en texto plano
     * @return bool True si el código es válido y vigente, false si no
     */
    public function verificarCodigoRecuperacion($idUsuario, $codigo) {
        $sql = "SELECT Codigo_Recuperacion, Codigo_Expiracion 
                FROM usuario 
                WHERE Id_Usuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);
        $usuario = $stmt->fetch();
        
        if (!$usuario || !$usuario['Codigo_Recuperacion'] || !$usuario['Codigo_Expiracion']) {
            return false;
        }
        
        // Verificar si expiró
        $ahora = time();
        $expiracion = strtotime($usuario['Codigo_Expiracion']);
        
        if ($ahora > $expiracion) {
            return false; // Código expirado
        }
        
        // Verificar el código
        return password_verify($codigo, $usuario['Codigo_Recuperacion']);
    }
    
    /**
     * Cambiar contraseña y limpiar datos de recuperación
     * @param int $idUsuario
     * @param string $nuevaContrasena Contraseña en texto plano (se guarda con hash)
     */
    public function cambiarContrasena($idUsuario, $nuevaContrasena) {
        $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        
        // Cambiar contraseña y reiniciar intentos, bloqueo y código de recuperación
        $sql = "UPDATE usuario 
                SET Password = :password, 
                    Intentos_Fallidos = 0, 
                    Fecha_Bloqueo = NULL,
                    Codigo_Recuperacion = NULL,
                    Codigo_Expiracion = NULL
                WHERE Id_Usuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'password' => $contrasenaHash,
            'id' => $idUsuario
        ]);
    }
