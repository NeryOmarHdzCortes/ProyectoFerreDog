<?php
/**
 * Punto de entrada y enrutador del sistema
 * Enrutamiento: ?c=controlador&a=accion
 * Sin parámetros: si hay sesión va a inicio, si no al login
 */

// Iniciar sesión
session_start();

// Obtener parámetros de la URL
$controlador = $_GET['c'] ?? null;
$accion = $_GET['a'] ?? null;

// Ruta base del proyecto
define('BASE_PATH', dirname(__DIR__));

// Sin parámetros: decidir según sesión
if (!$controlador) {
    if (isset($_SESSION['Id_Usuario'])) {
        // Hay sesión, ir al inicio
        $controlador = 'inicio';
        $accion = 'index';
    } else {
        // No hay sesión, ir al login
        $controlador = 'auth';
        $accion = 'login';
    }
}

// Acción por defecto
if (!$accion) {
    $accion = 'index';
}

// Construir nombre del controlador
$nombreControlador = ucfirst($controlador) . 'Controller';
$rutaControlador = BASE_PATH . '/app/controllers/' . $nombreControlador . '.php';

// Verificar que el archivo existe
if (!file_exists($rutaControlador)) {
    die("Error: Controlador no encontrado - {$nombreControlador}");
}

// Incluir el controlador
require_once $rutaControlador;

// Verificar que la clase existe
if (!class_exists($nombreControlador)) {
    die("Error: Clase del controlador no encontrada - {$nombreControlador}");
}

// Instanciar el controlador
$instanciaControlador = new $nombreControlador();

// Verificar que el método existe
if (!method_exists($instanciaControlador, $accion)) {
    die("Error: Método no encontrado - {$nombreControlador}::{$accion}");
}

// Ejecutar la acción
$instanciaControlador->$accion();
