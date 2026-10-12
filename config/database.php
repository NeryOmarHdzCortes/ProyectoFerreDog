<?php
/**
 * Configuración de la base de datos
 * Lee las variables del archivo .env y establece la conexión PDO
 */

// Leer el archivo .env
function cargarEnv($archivo) {
    if (!file_exists($archivo)) {
        die("Error: El archivo .env no existe. Copia .env.example a .env y configúralo.");
    }
    
    $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        // Ignorar comentarios
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }
        
        // Separar clave=valor
        if (strpos($linea, '=') !== false) {
            list($clave, $valor) = explode('=', $linea, 2);
            $clave = trim($clave);
            $valor = trim($valor);
            
            // Guardar en $_ENV si no está ya definida
            if (!array_key_exists($clave, $_ENV)) {
                $_ENV[$clave] = $valor;
            }
        }
    }
}

// Cargar variables del .env
$rutaEnv = __DIR__ . '/../.env';
cargarEnv($rutaEnv);

// Obtener credenciales
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'ferredog';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    // Crear conexión PDO
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $opciones);
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
