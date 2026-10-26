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
     * Convertir estado texto a numérico
     */
    private function estadoANumero($estado) {
        return $estado === 'Activo' ? 1 : 0;
    }

    /* =========================================================
     * HU-1: Login y bloqueo
     * ========================================================= */

    /**
     * Autenticar usuario por nombre de usuario y contraseña
     * @return array|false Datos del usuario si es válido, false si no
     */
    public function autenticar($nombreUsuario, $contrasena) {
        $sql = "SELECT u.Id_Usuario, u.Usuario, u.Password, u.Rol, u.Intentos_Fallidos,
                       u.Fecha_Bloqueo, e.Nombre, e.Apellidos,
                       CASE WHEN u.Estado = 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE u.Usuario = :usuario";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario' => $nombreUsuario]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            return false; // Usuario no existe
        }

        // Verificar si está bloqueado temporalmente (15 minutos = 900 segundos)
        if ($usuario['Fecha_Bloqueo'] !== null) {
            $tiempoTranscurrido = time() - strtotime($usuario['Fecha_Bloqueo']);

            if ($tiempoTranscurrido < 900) {
                return ['bloqueado' => true, 'tiempo_restante' => 900 - $tiempoTranscurrido];
            }

            // Ya pasó el tiempo de bloqueo, reiniciar
            $this->reiniciarIntentos($usuario['Id_Usuario']);
            $usuario['Intentos_Fallidos'] = 0;
            $usuario['Fecha_Bloqueo'] = null;
        }

        // Verificar si está inactivo
        if ($usuario['Estado'] === 'Inactivo') {
            return ['inactivo' => true];
        }

        // Verificar contraseña
        if (!password_verify($contrasena, $usuario['Password'])) {
            $this->registrarIntentoFallido($usuario['Id_Usuario'], $usuario['Intentos_Fallidos']);
            return false;
        }

        // Autenticación exitosa, reiniciar intentos
        $this->reiniciarIntentos($usuario['Id_Usuario']);

        return $usuario;
    }

    /**
     * Registrar un intento fallido; al llegar a 5 se bloquea la cuenta
     */
    private function registrarIntentoFallido($idUsuario, $intentosActuales) {
        $nuevosIntentos = $intentosActuales + 1;

        if ($nuevosIntentos >= 5) {
            $sql = "UPDATE usuario
                    SET Intentos_Fallidos = :intentos, Fecha_Bloqueo = NOW()
                    WHERE Id_Usuario = :id";
        } else {
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
     * Reiniciar intentos fallidos y bloqueo (login exitoso o bloqueo vencido)
     */
    private function reiniciarIntentos($idUsuario) {
        $sql = "UPDATE usuario
                SET Intentos_Fallidos = 0, Fecha_Bloqueo = NULL
                WHERE Id_Usuario = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);
    }

    /* =========================================================
     * HU-1: Recuperar contraseña
     * ========================================================= */

    /**
     * Buscar usuario por correo (solo activos)
     * @return array|false
     */
    public function buscarPorCorreo($correo) {
        $sql = "SELECT u.Id_Usuario, u.Usuario, e.Nombre, e.Apellidos, e.Correo
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE e.Correo = :correo AND u.Estado = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);

        return $stmt->fetch();
    }

    /**
     * Guardar código de recuperación (con hash) y expiración de 10 minutos
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
     * Verificar que el código sea correcto y siga vigente
     * @return bool
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

        if (time() > strtotime($usuario['Codigo_Expiracion'])) {
            return false; // Código expirado
        }

        return password_verify($codigo, $usuario['Codigo_Recuperacion']);
    }

    /**
     * Cambiar contraseña y limpiar intentos, bloqueo y código de recuperación
     */
    public function cambiarContrasena($idUsuario, $nuevaContrasena) {
        $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

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

    /* =========================================================
     * HU-4: Gestionar usuarios
     * ========================================================= */

    /**
     * Lista de usuarios con buscador y filtros
     * Nota: cada parámetro tiene nombre distinto porque PDO (con EMULATE_PREPARES = false)
     * no permite repetir el mismo nombre de parámetro en una consulta.
     */
    public function obtenerTodos($busqueda = '', $rol = '', $estado = '') {
        $sql = "SELECT u.Id_Usuario, u.Usuario, u.Rol, CASE WHEN u.Estado = 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado,
                       e.Nombre, e.Apellidos, e.Correo
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE 1=1";

        $params = [];

        if ($busqueda !== '') {
            $sql .= " AND (e.Nombre LIKE :busqueda1 OR e.Apellidos LIKE :busqueda2 OR u.Usuario LIKE :busqueda3)";
            $params['busqueda1'] = "%{$busqueda}%";
            $params['busqueda2'] = "%{$busqueda}%";
            $params['busqueda3'] = "%{$busqueda}%";
        }

        if ($rol !== '') {
            $sql .= " AND u.Rol = :rol";
            $params['rol'] = $rol;
        }

        if ($estado === 'Activo' || $estado === 'Inactivo') {
            $sql .= " AND u.Estado = :estado";
            $params['estado'] = $this->estadoANumero($estado);
        }

        $sql .= " ORDER BY e.Nombre, e.Apellidos";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Obtener un usuario con sus datos de empleado (para el formulario de editar)
     * @return array|false
     */
    public function obtenerPorId($idUsuario) {
        $sql = "SELECT u.Id_Usuario, u.Usuario, u.Rol, CASE WHEN u.Estado = 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado, u.Id_Empleado,
                       e.Nombre, e.Apellidos, e.Correo
                FROM usuario u
                INNER JOIN empleados e ON u.Id_Empleado = e.Id_Empleado
                WHERE u.Id_Usuario = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);

        return $stmt->fetch();
    }

    /**
     * Obtener el Id_Empleado de un usuario
     * @return int|false
     */
    public function obtenerIdEmpleado($idUsuario) {
        $sql = "SELECT Id_Empleado FROM usuario WHERE Id_Usuario = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);

        return $stmt->fetchColumn();
    }

    /**
     * Verificar si existe un nombre de usuario (opcionalmente excluyendo uno)
     */
    public function existeUsuario($nombreUsuario, $idUsuarioExcluir = null) {
        $sql = "SELECT COUNT(*) FROM usuario WHERE Usuario = :usuario";
        $params = ['usuario' => $nombreUsuario];

        if ($idUsuarioExcluir) {
            $sql .= " AND Id_Usuario != :id";
            $params['id'] = $idUsuarioExcluir;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si existe un correo en empleados (opcionalmente excluyendo uno)
     */
    public function existeCorreo($correo, $idEmpleadoExcluir = null) {
        $sql = "SELECT COUNT(*) FROM empleados WHERE Correo = :correo";
        $params = ['correo' => $correo];

        if ($idEmpleadoExcluir) {
            $sql .= " AND Id_Empleado != :id";
            $params['id'] = $idEmpleadoExcluir;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear usuario: inserta en empleados y luego en usuario.
     * La transacción la abre el controlador (UsuarioController::guardar).
     */
    public function crear($nombre, $apellidos, $correo, $nombreUsuario, $contrasena, $rol, $estado) {
        // 1. Insertar en empleados
        $sql = "INSERT INTO empleados (Nombre, Apellidos, Correo, Puesto, Estado, Fecha_Alta)
                VALUES (:nombre, :apellidos, :correo, :puesto, :estado, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'correo' => $correo,
            'puesto' => $rol,
            'estado' => $this->estadoANumero($estado)
        ]);

        $idEmpleado = $this->pdo->lastInsertId();

        // 2. Insertar en usuario
        $sql = "INSERT INTO usuario (Usuario, Password, Rol, Estado, Id_Empleado, Fecha_Alta, Intentos_Fallidos)
                VALUES (:usuario, :password, :rol, :estado, :id_empleado, NOW(), 0)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'usuario' => $nombreUsuario,
            'password' => password_hash($contrasena, PASSWORD_DEFAULT),
            'rol' => $rol,
            'estado' => $this->estadoANumero($estado),
            'id_empleado' => $idEmpleado
        ]);
    }

    /**
     * Editar usuario: actualiza empleados y usuario.
     * La transacción la abre el controlador (UsuarioController::guardar).
     */
    public function editar($idUsuario, $nombre, $apellidos, $correo, $rol, $estado) {
        $idEmpleado = $this->obtenerIdEmpleado($idUsuario);

        // 1. Actualizar empleados
        $sql = "UPDATE empleados
                SET Nombre = :nombre, Apellidos = :apellidos, Correo = :correo, Puesto = :puesto, Estado = :estado
                WHERE Id_Empleado = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'correo' => $correo,
            'puesto' => $rol,
            'estado' => $this->estadoANumero($estado),
            'id' => $idEmpleado
        ]);

        // 2. Actualizar usuario
        $sql = "UPDATE usuario
                SET Rol = :rol, Estado = :estado
                WHERE Id_Usuario = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'rol' => $rol,
            'estado' => $this->estadoANumero($estado),
            'id' => $idUsuario
        ]);
    }

    /**
     * Activar o desactivar un usuario desde la lista
     */
    public function cambiarEstado($idUsuario, $estado) {
        $sql = "UPDATE usuario SET Estado = :estado WHERE Id_Usuario = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'estado' => $this->estadoANumero($estado),
            'id' => $idUsuario
        ]);
    }
}