<?php

define('BASE_PATH', dirname(__DIR__));

ob_start();
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Usuario.php';
require_once BASE_PATH . '/app/models/Producto.php';
require_once BASE_PATH . '/app/models/Proveedor.php';
require_once BASE_PATH . '/app/models/Catalogo.php';
require_once BASE_PATH . '/app/libraries/Correo.php';
$salidaDeCarga = ob_get_clean();
$salidaDeCarga = preg_replace('/^(?:\xEF\xBB\xBF)+/', '', $salidaDeCarga);

if ($salidaDeCarga !== '') {
    echo $salidaDeCarga;
}
