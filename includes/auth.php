<?php
/**
 * Verificación de autenticación
 * - Verifica que exista sesión activa
 * - Verifica el rol del usuario
 * - Controla inactividad de 30 minutos
 * - Envía headers para evitar cache
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['Id_Usuario'])) {
    // No hay sesión, redirigir al login
    header('Location: /ProyectoFerredog/public/index.php');
    exit;
}

// Verificar inactividad de 30 minutos
$tiempoInactividad = 30 * 60; // 30 minutos en segundos

if (isset($_SESSION['ultima_actividad'])) {
    $tiempoTranscurrido = time() - $_SESSION['ultima_actividad'];
    
    if ($tiempoTranscurrido > $tiempoInactividad) {
        // Sesión expirada por inactividad
        session_unset();
        session_destroy();
        header('Location: /ProyectoFerredog/public/index.php?sesion=expirada');
        exit;
    }
}

// Actualizar última actividad
$_SESSION['ultima_actividad'] = time();

// Enviar headers para evitar cache (evita "atrás" después de cerrar sesión)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

/**
 * Verificar si el usuario tiene uno de los roles permitidos
 * @param array $rolesPermitidos Array con los roles que pueden acceder
 * @return bool True si tiene permiso, False si no
 */
function verificarRol($rolesPermitidos) {
    if (!isset($_SESSION['Rol'])) {
        return false;
    }
    
    return in_array($_SESSION['Rol'], $rolesPermitidos);
}

/**
 * Redirigir si el usuario no tiene el rol requerido
 * @param array $rolesPermitidos Array con los roles que pueden acceder
 */
function requerirRol($rolesPermitidos) {
    if (!verificarRol($rolesPermitidos)) {
        // No tiene permiso, redirigir al inicio
        header('Location: /ProyectoFerredog/public/index.php?c=inicio&a=index');
        exit;
    }
}
