<?php

require_once __DIR__ . '/bootstrap.php';

$tipo = $argv[1] ?? '';
$idSesion = $argv[2] ?? '';
session_save_path(sys_get_temp_dir());
session_id($idSesion);
session_start();
$_SESSION['Id_Usuario'] = 1;
$_SESSION['Rol'] = 'Administrador';
$_SESSION['ultima_actividad'] = time();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = json_decode(stream_get_contents(STDIN), true);

register_shutdown_function(function () {
    echo "\nSESSION_STATE:" . base64_encode(json_encode($_SESSION)) . "\n";
});

if ($tipo === 'usuario') {
    require_once BASE_PATH . '/app/controllers/UsuarioController.php';
    (new UsuarioController())->guardar();
} elseif ($tipo === 'proveedor') {
    require_once BASE_PATH . '/app/controllers/ProveedorController.php';
    (new ProveedorController())->guardar();
}

