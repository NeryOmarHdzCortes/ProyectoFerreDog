<?php

const URL_APLICACION = 'http://localhost/ProyectoFerredog/public/index.php';
const RUTA_LOG_CORREOS = __DIR__ . '/../storage/logs/correos.log';
const RUTA_REPORTE_INTEGRAL = __DIR__ . '/../docs/sprint2/pruebas_integrales.md';
const CONTRASENA_PRUEBA = 'PruebaIntegral2026!';

$definicionesPruebas = [
    'P-01' => 'El menú muestra Pedidos y no muestra Entradas',
    'P-02' => 'La lista inicial muestra solo pedidos Por recibir y el pie "Mostrando…"',
    'P-04' => 'Buscar por número con # encuentra el pedido',
    'P-05' => 'Buscar por número sin # encuentra el pedido',
    'P-06' => 'Buscar por nombre de proveedor',
    'P-07' => 'Filtro Recibido: solo recibidos y sin botón Cancelar pedido',
    'P-08' => 'Filtro Cancelado',
    'P-09' => 'Filtro Todos',
    'P-10' => 'Número inexistente: "No se encontraron pedidos"',
    'P-11' => 'Ver detalle de un pedido recibido muestra recepción y observación del daño',
    'P-12' => 'Nuevo pedido: el combo solo trae proveedores activos',
    'P-13' => 'productosProveedor devuelve JSON válido (sin BOM) solo con productos activos del proveedor',
    'P-16' => 'Guardar sin proveedor → "Elige un proveedor" y no se crea nada',
    'P-17' => 'Sin productos → "Agrega al menos un producto"',
    'P-18' => 'Cantidad 0 → "La cantidad debe ser mayor a 0"',
    'P-19' => 'El servidor ignora un costo falso y usa Precio_Compra del catálogo',
    'P-20' => 'Un producto sin costo no se puede pedir',
    'P-21' => 'Cantidad que supera Stock_Maximo → mensaje de la base y no se crea nada',
    'P-22' => 'Pedido válido: mensaje "Pedido #N registrado…", Estado Pendiente, total recalculado por el servidor (manda un total falso y comprueba que se ignora) y Nombre_Producto/Nombre_Proveedor llenos por los triggers',
    'P-24' => 'correos.log tiene el correo del pedido con productos y total',
    'P-27' => 'Cancelar un pedido Por recibir → Cancelada y mensaje',
    'P-25' => 'correos.log tiene el correo de cancelación',
    'P-28' => 'Cancelar un pedido Recibido → "El pedido ya no está por recibir" y sigue Recibida',
    'E-01' => 'El menú muestra Entradas',
    'E-02' => 'La lista inicial "Pedidos por recibir" no tiene botón Recibir',
    'E-03' => 'Buscar por número abre el pedido y NO muestra la lista de abajo',
    'E-05' => 'Buscar por proveedor muestra la lista filtrada',
    'E-06' => 'Buscar por producto encuentra el pedido que lo trae',
    'E-07' => 'Buscar un pedido Recibido → "Pedido no encontrado"',
    'E-08' => 'Buscar un pedido Cancelado → "Pedido no encontrado"',
    'E-11' => 'Registrar con Dañado y sin motivo → rechazado; el pedido sigue Pendiente y el stock no cambia',
    'E-12' => 'Cantidad recibida mayor a la pedida → rechazado',
    'E-18' => 'Registrar entrada (producto 1 completo; producto 2 Dañado 1 de 2 con motivo; producto 3 Faltante 0 de 3 con motivo) → mensaje "Entrada registrada…" y Estado Recibida',
    'E-19' => 'Volver a buscar ese pedido → "Pedido no encontrado"',
    'E-21' => 'Stock: el producto 1 sube lo pedido, el producto 2 sube 1, el producto 3 no sube; los movimientos tienen "Pedido #N" y "Pedido #N - Dañado: …"',
    'E-22' => 'detalle_compras guardó Cantidad_Recibida, Incidencia y Motivo_Incidencia',
    'E-23' => 'compras guardó Fecha_Recepcion e Id_Empleado_Recibe del almacenista',
    'E-20' => 'El Administrador ve en "Ver" el daño y quién recibió',
    'R-01' => 'Almacenista en ?c=pedido&a=lista → acceso negado',
    'R-02' => 'Vendedor en ?c=pedido&a=lista → acceso negado',
    'R-03' => 'Vendedor en ?c=entrada&a=index → acceso negado',
    'R-04' => 'Administrador en ?c=entrada&a=index → acceso negado',
    'R-05' => 'Sin sesión, ?c=pedido&a=lista → manda al login',
    'A-01' => 'Agregar proveedor sin correo → "El correo es obligatorio"',
    'A-02' => 'Editar proveedor borrando el correo → mismo mensaje',
    'T-01' => 'Ejecuta C:\\xampp\\php\\php.exe tests\\run.php e incluye el total de OK/FALLA'
];

$pruebasManualesPendientes = [
    'P-03', 'P-14', 'P-15', 'P-23', 'P-26',
    'E-04', 'E-09', 'E-10', 'E-13', 'E-14', 'E-15', 'E-16', 'E-17'
];
$resultadosPruebas = [];
$erroresLimpieza = [];
$pdo = null;
$idProveedor = null;
$idEmpleadoAdmin = null;
$idEmpleadoAlmacenista = null;
$idEmpleadoVendedor = null;
$idUsuarioAdmin = null;
$idUsuarioAlmacenista = null;
$idUsuarioVendedor = null;
$idsProductos = [];
$idsPedidos = [];
$archivosCookies = [];
$existeLogOriginal = false;
$contenidoLogOriginal = '';
$tamanoLogOriginal = 0;
$contenidoEnvOriginal = null;
$envModificadoTemporalmente = false;
$modoCorreoOriginal = null;
$modoCorreoExistia = false;
$nombreProveedor = '';
$nombresProductos = [];
$pedidoPrincipal = null;
$pedidoCancelado = null;
$pedidoExtra = null;
$idsDetalles = [];
$existenciasIniciales = [];
$sufijo = '';

// Comprueba primero el servidor web; si no responde no se hacen cambios en la base.
if (!extension_loaded('curl')) {
    fwrite(STDERR, "ERROR: la extensión curl de PHP no está disponible; no se ejecutaron las pruebas.\n");
    exit(1);
}

$curlApache = curl_init(URL_APLICACION);
curl_setopt_array($curlApache, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 10
]);
$respuestaApache = curl_exec($curlApache);
$errorApache = curl_error($curlApache);
$codigoApache = (int) curl_getinfo($curlApache, CURLINFO_RESPONSE_CODE);
curl_close($curlApache);
if ($respuestaApache === false || $codigoApache < 200 || $codigoApache >= 400) {
    fwrite(
        STDERR,
        "ERROR: Apache no responde correctamente en " . URL_APLICACION
        . ($codigoApache > 0 ? " (HTTP {$codigoApache})" : '')
        . ($errorApache !== '' ? ": {$errorApache}" : '')
        . ". Inicia Apache y vuelve a ejecutar la prueba.\n"
    );
    exit(1);
}

$prueba = function ($id, callable $ejecucion) use (&$resultadosPruebas) {
    try {
        $detalle = $ejecucion();
        $resultadosPruebas[$id] = [
            'resultado' => 'OK',
            'detalle' => (string) $detalle
        ];
    } catch (Throwable $error) {
        $resultadosPruebas[$id] = [
            'resultado' => 'FALLA',
            'detalle' => $error->getMessage()
        ];
    }
};

$exigir = function ($condicion, $mensaje) {
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
};

$consultarEntero = function ($sql, array $parametros = []) use (&$pdo) {
    $consulta = $pdo->prepare($sql);
    $consulta->execute($parametros);

    return (int) $consulta->fetchColumn();
};

$hacerPeticion = function ($cookie, $ruta, $metodo = 'GET', array $datos = []) {
    $url = URL_APLICACION . $ruta;
    $opciones = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_USERAGENT => 'Ferredog-Prueba-Integral-Sprint2/1.0'
    ];
    if ($metodo === 'POST') {
        $opciones[CURLOPT_POST] = true;
        $opciones[CURLOPT_POSTFIELDS] = http_build_query($datos, '', '&');
        $opciones[CURLOPT_HTTPHEADER] = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
    }

    $curl = curl_init($url);
    curl_setopt_array($curl, $opciones);
    $cuerpo = curl_exec($curl);
    $error = curl_error($curl);
    $codigo = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $urlFinal = (string) curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    curl_close($curl);
    if ($cuerpo === false) {
        throw new RuntimeException('Falló la petición HTTP: ' . $error);
    }

    return [
        'cuerpo' => (string) $cuerpo,
        'codigo' => $codigo,
        'url' => $urlFinal
    ];
};

$iniciarSesionHttp = function ($rol) use (&$archivosCookies, $hacerPeticion, $exigir) {
    $respuesta = $hacerPeticion(
        $archivosCookies[$rol],
        '',
        'POST',
        [
            'usuario' => $archivosCookies['usuarios'][$rol],
            'contrasena' => CONTRASENA_PRUEBA
        ]
    );
    $exigir(
        strpos($respuesta['url'], 'c=inicio') !== false
            && strpos($respuesta['cuerpo'], 'Cerrar sesión') !== false,
        "No inició sesión correctamente el rol {$rol}."
    );
};

$contarPedidosProveedor = function () use (&$pdo, &$idProveedor) {
    $consulta = $pdo->prepare('SELECT COUNT(*) FROM compras WHERE Id_Proveedor = ?');
    $consulta->execute([$idProveedor]);

    return (int) $consulta->fetchColumn();
};

$crearPedidoHttp = function (array $productos) use (
    &$pdo,
    &$idProveedor,
    &$idEmpleadoAdmin,
    &$idsPedidos,
    &$archivosCookies,
    $hacerPeticion
) {
    $consulta = $pdo->prepare(
        'SELECT COALESCE(MAX(Id_Compras), 0) FROM compras WHERE Id_Proveedor = ? AND Id_Empleado = ?'
    );
    $consulta->execute([$idProveedor, $idEmpleadoAdmin]);
    $idAnterior = (int) $consulta->fetchColumn();
    $respuesta = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=guardar',
        'POST',
        [
            'proveedor_id' => $idProveedor,
            'productos' => $productos,
            'total' => '99999999.99'
        ]
    );
    $consulta->execute([$idProveedor, $idEmpleadoAdmin]);
    $idNuevo = (int) $consulta->fetchColumn();
    if ($idNuevo > $idAnterior) {
        $idsPedidos[$idNuevo] = $idNuevo;
    } else {
        $idNuevo = null;
    }

    return [
        'respuesta' => $respuesta,
        'id' => $idNuevo
    ];
};

try {
    $rutaEnv = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($rutaEnv)) {
        throw new RuntimeException('No existe .env; no se puede configurar MAIL_MODO para las pruebas.');
    }
    $modoCorreoProceso = getenv('MAIL_MODO');
    if ($modoCorreoProceso !== false && $modoCorreoProceso !== 'prueba') {
        throw new RuntimeException(
            'El entorno de Apache/PHP define MAIL_MODO fuera de prueba; se detiene para evitar correo real.'
        );
    }
    $contenidoEnvOriginal = file_get_contents($rutaEnv);
    if ($contenidoEnvOriginal === false) {
        throw new RuntimeException('No se pudo leer .env para preparar el modo de correo.');
    }
    if (preg_match('/^[ \t]*MAIL_MODO[ \t]*=[ \t]*prueba[ \t]*$/mi', $contenidoEnvOriginal) !== 1) {
        if (preg_match('/^[ \t]*MAIL_MODO[ \t]*=.*$/mi', $contenidoEnvOriginal) === 1) {
            $contenidoEnvTemporal = preg_replace(
                '/^([ \t]*MAIL_MODO[ \t]*=[ \t]*).*$/mi',
                '$1prueba',
                $contenidoEnvOriginal,
                1
            );
            if (!is_string($contenidoEnvTemporal)) {
                throw new RuntimeException('No se pudo preparar .env para el modo de correo de prueba.');
            }
        } else {
            $separador = substr($contenidoEnvOriginal, -1) === "\n" ? '' : PHP_EOL;
            $contenidoEnvTemporal = $contenidoEnvOriginal . $separador . 'MAIL_MODO=prueba' . PHP_EOL;
        }
        $envModificadoTemporalmente = true;
        if (file_put_contents($rutaEnv, $contenidoEnvTemporal, LOCK_EX) === false) {
            throw new RuntimeException('No se pudo activar temporalmente MAIL_MODO=prueba.');
        }
    }

    require_once __DIR__ . '/../config/database.php';
    $modoCorreoExistia = array_key_exists('MAIL_MODO', $_ENV);
    $modoCorreoOriginal = $_ENV['MAIL_MODO'] ?? null;
    $_ENV['MAIL_MODO'] = 'prueba';

    $existeLogOriginal = file_exists(RUTA_LOG_CORREOS);
    if ($existeLogOriginal) {
        $contenidoLogOriginal = file_get_contents(RUTA_LOG_CORREOS);
        if ($contenidoLogOriginal === false) {
            throw new RuntimeException('No se pudo leer correos.log antes de iniciar.');
        }
        $tamanoLogOriginal = strlen($contenidoLogOriginal);
    }

    $colision = $pdo->prepare(
        "SELECT
            (SELECT COUNT(*) FROM proveedor WHERE Nombre LIKE 'Prueba Integral %')
          + (SELECT COUNT(*) FROM productos WHERE Nombre LIKE 'Prueba Integral %')
          + (SELECT COUNT(*) FROM empleados WHERE Nombre LIKE 'Prueba Integral %')
          + (SELECT COUNT(*) FROM compras WHERE Nombre_Proveedor LIKE 'Prueba Integral %')
          + (SELECT COUNT(*) FROM detalle_compras WHERE Nombre_Producto LIKE 'Prueba Integral %')"
    );
    $colision->execute();
    if ((int) $colision->fetchColumn() !== 0) {
        throw new RuntimeException(
            'Ya existen datos con el prefijo Prueba Integral; no se tocaron registros previos.'
        );
    }

    $referencias = [
        'categoria' => (int) $pdo->query('SELECT Id_Categoria FROM categoria ORDER BY Id_Categoria LIMIT 1')->fetchColumn(),
        'unidad' => (int) $pdo->query('SELECT Id_Unidad FROM unidad_medida ORDER BY Id_Unidad LIMIT 1')->fetchColumn()
    ];
    if ($referencias['categoria'] <= 0 || $referencias['unidad'] <= 0) {
        throw new RuntimeException('No hay categoría y unidad de medida de referencia para crear productos.');
    }

    for ($indice = 0; $indice < 10; $indice++) {
        $sufijo .= chr(random_int(65, 90));
    }
    $nombreProveedor = 'Prueba Integral Proveedor ' . $sufijo;
    $nombresProductos = [
        'Prueba Integral Producto Uno ' . $sufijo,
        'Prueba Integral Producto Dos ' . $sufijo,
        'Prueba Integral Producto Tres ' . $sufijo,
        'Prueba Integral Producto Inactivo ' . $sufijo
    ];

    $consultaProveedor = $pdo->prepare(
        "INSERT INTO proveedor (Nombre, Contacto, Telefono, Correo, Direccion, Tipo, Estado)
         VALUES (?, ?, ?, ?, ?, 'Mayorista', 1)"
    );
    $consultaProveedor->execute([
        $nombreProveedor,
        'Prueba Integral Contacto',
        '5551234567',
        'pruebaintegral' . strtolower($sufijo) . '@example.com',
        'Prueba Integral Direccion'
    ]);
    $idProveedor = (int) $pdo->lastInsertId();

    foreach ($nombresProductos as $indice => $nombreProducto) {
        $codigoBarras = 'PI' . $sufijo . str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT);
        $consultaProducto = $pdo->prepare(
            'INSERT INTO productos
                (Codigo_Barras, Nombre, Precio_Venta, Precio_Compra, Stock_Minimo, Stock_Maximo,
                 Estado, Id_Categoria, Id_Unidad, Id_Proveedor)
             VALUES (?, ?, 25.00, 5.00, 1, 10000, ?, ?, ?, ?)'
        );
        $estadoProducto = $indice === 3 ? 0 : 1;
        $consultaProducto->execute([
            $codigoBarras,
            $nombreProducto,
            $estadoProducto,
            $referencias['categoria'],
            $referencias['unidad'],
            $idProveedor
        ]);
        $idProducto = (int) $pdo->lastInsertId();
        $idsProductos[] = $idProducto;
        $consultaInventario = $pdo->prepare(
            'INSERT INTO inventario (Existencias, Fecha_Entrada, Id_Producto) VALUES (20, CURDATE(), ?)'
        );
        $consultaInventario->execute([$idProducto]);
        $existenciasIniciales[$idProducto] = 20;
    }

    $roles = [
        'Administrador' => ['Puesto' => 'Administrador'],
        'Almacenista' => ['Puesto' => 'Almacenista'],
        'Vendedor' => ['Puesto' => 'Vendedor']
    ];
    $hashContrasena = password_hash(CONTRASENA_PRUEBA, PASSWORD_DEFAULT);
    foreach ($roles as $rol => $datosRol) {
        $correoEmpleado = 'empleado' . strtolower($rol) . strtolower($sufijo) . '@example.com';
        $consultaEmpleado = $pdo->prepare(
            'INSERT INTO empleados (Nombre, Apellidos, Correo, Telefono, Puesto, Estado)
             VALUES (?, ?, ?, ?, ?, 1)'
        );
        $consultaEmpleado->execute([
            'Prueba Integral ' . $rol . ' ' . $sufijo,
            'Prueba Integral',
            $correoEmpleado,
            '5551234567',
            $datosRol['Puesto']
        ]);
        $idEmpleado = (int) $pdo->lastInsertId();
        $variableEmpleado = [
            'Administrador' => 'idEmpleadoAdmin',
            'Almacenista' => 'idEmpleadoAlmacenista',
            'Vendedor' => 'idEmpleadoVendedor'
        ][$rol];
        $$variableEmpleado = $idEmpleado;

        $nombreUsuario = 'pi' . strtolower($rol) . strtolower($sufijo);
        $consultaUsuario = $pdo->prepare(
            'INSERT INTO usuario (Usuario, Password, Rol, Estado, Id_Empleado)
             VALUES (?, ?, ?, 1, ?)'
        );
        $consultaUsuario->execute([$nombreUsuario, $hashContrasena, $rol, $idEmpleado]);
        $idUsuario = (int) $pdo->lastInsertId();
        $variableUsuario = [
            'Administrador' => 'idUsuarioAdmin',
            'Almacenista' => 'idUsuarioAlmacenista',
            'Vendedor' => 'idUsuarioVendedor'
        ][$rol];
        $$variableUsuario = $idUsuario;

        $archivoCookie = tempnam(sys_get_temp_dir(), 'pi_sprint2_' . strtolower($rol) . '_');
        if ($archivoCookie === false) {
            throw new RuntimeException('No se pudo crear el archivo de cookies de ' . $rol . '.');
        }
        $archivosCookies[$rol] = $archivoCookie;
        $archivosCookies['usuarios'][$rol] = $nombreUsuario;
    }
    $archivosCookies['anonimo'] = tempnam(sys_get_temp_dir(), 'pi_sprint2_anonimo_');
    if ($archivosCookies['anonimo'] === false) {
        throw new RuntimeException('No se pudo crear el archivo de cookies anónimo.');
    }

    $iniciarSesionHttp('Administrador');
    $iniciarSesionHttp('Almacenista');
    $iniciarSesionHttp('Vendedor');

    // Los casos de pedidos inválidos se envían como formularios HTTP, nunca llaman al controlador.
    $respuestaLista = $hacerPeticion($archivosCookies['Administrador'], '?c=pedido&a=lista');
    $prueba('P-01', function () use ($respuestaLista, $exigir) {
        $exigir(
            strpos($respuestaLista['cuerpo'], '>Pedidos</span>') !== false
                && strpos($respuestaLista['cuerpo'], '>Entradas</span>') === false,
            'El menú no coincide con el rol Administrador.'
        );
        return 'Menú del Administrador validado por HTTP.';
    });
    $prueba('P-02', function () use ($respuestaLista, &$pdo, $exigir) {
        preg_match('/<tbody>(.*?)<\\/tbody>/s', $respuestaLista['cuerpo'], $coincidencias);
        $cuerpoTabla = $coincidencias[1] ?? '';
        $exigir(
            strpos($respuestaLista['cuerpo'], 'Mostrando') !== false,
            'La lista inicial no mostró el pie "Mostrando…".'
        );
        $exigir(
            strpos($cuerpoTabla, 'Recibido') === false && strpos($cuerpoTabla, 'Cancelado') === false,
            'La lista inicial contiene pedidos con estado distinto de Por recibir.'
        );
        preg_match_all('/<tr>\\s*<td>#(\\d+)<\\/td>/s', $cuerpoTabla, $pedidosTabla);
        $consultaEstado = $pdo->prepare('SELECT Estado FROM compras WHERE Id_Compras = ?');
        foreach ($pedidosTabla[1] ?? [] as $idPedido) {
            $consultaEstado->execute([(int) $idPedido]);
            $estado = $consultaEstado->fetchColumn();
            $exigir($estado === 'Pendiente', "La lista inicial incluyó el pedido #{$idPedido} con estado {$estado}.");
        }
        return 'Las filas iniciales corresponden solo a pedidos Pendiente y se muestra el pie de paginación.';
    });

    $prueba('P-10', function () use ($hacerPeticion, $archivosCookies, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=lista&busqueda=2147483647&estado=Pendiente'
        );
        $exigir(strpos($respuesta['cuerpo'], 'No se encontraron pedidos') !== false, 'No apareció el mensaje sin resultados.');
        return 'La búsqueda con número inexistente mostró el mensaje esperado.';
    });

    $respuestaNuevo = $hacerPeticion($archivosCookies['Administrador'], '?c=pedido&a=nuevo');
    $prueba('P-12', function () use ($respuestaNuevo, &$pdo, &$idProveedor, $exigir) {
        preg_match('/<select id="proveedorPedido".*?<\\/select>/s', $respuestaNuevo['cuerpo'], $coincidencias);
        $exigir(isset($coincidencias[0]), 'No se encontró el combo de proveedores.');
        preg_match_all('/<option\\s+value="(\\d+)"/', $coincidencias[0], $opciones);
        $idsMostrados = array_map('intval', $opciones[1] ?? []);
        $consulta = $pdo->query('SELECT Id_Proveedor FROM proveedor WHERE Estado = 1 ORDER BY Nombre');
        $idsActivos = array_map('intval', $consulta->fetchAll(PDO::FETCH_COLUMN));
        sort($idsMostrados);
        sort($idsActivos);
        $exigir(
            in_array($idProveedor, $idsMostrados, true) && $idsMostrados === $idsActivos,
            'El combo no coincide exactamente con los proveedores activos.'
        );
        return 'El combo contiene exactamente los proveedores activos, incluido el creado para esta prueba.';
    });

    $respuestaJson = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=productosProveedor&id=' . $idProveedor
    );
    $prueba('P-13', function () use ($respuestaJson, $idsProductos, $exigir) {
        $exigir($respuestaJson['codigo'] === 200, 'productosProveedor no devolvió HTTP 200.');
        $exigir(substr($respuestaJson['cuerpo'], 0, 3) !== "\xEF\xBB\xBF", 'La respuesta JSON tiene BOM.');
        $productos = json_decode($respuestaJson['cuerpo'], true);
        $exigir(is_array($productos) && json_last_error() === JSON_ERROR_NONE, 'La respuesta no es JSON válido.');
        $idsDevueltos = array_map(function ($producto) {
            return (int) ($producto['Id_Producto'] ?? 0);
        }, $productos);
        $idsEsperados = array_slice($idsProductos, 0, 3);
        sort($idsDevueltos);
        sort($idsEsperados);
        $exigir($idsDevueltos === $idsEsperados, 'El JSON no devolvió únicamente los productos activos del proveedor.');
        return 'JSON sin BOM; devolvió los tres productos activos y excluyó el producto inactivo.';
    });

    $cantidadAntesInvalidos = $contarPedidosProveedor();
    $prueba('P-16', function () use ($hacerPeticion, $archivosCookies, $idsProductos, $contarPedidosProveedor, $cantidadAntesInvalidos, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=guardar',
            'POST',
            ['proveedor_id' => '', 'productos' => [$idsProductos[0] => ['cantidad' => '1']]]
        );
        $exigir(strpos($respuesta['cuerpo'], 'Elige un proveedor') !== false, 'No se mostró la validación del proveedor.');
        $exigir($contarPedidosProveedor() === $cantidadAntesInvalidos, 'Se creó un pedido sin proveedor.');
        return 'El POST fue rechazado y no insertó pedidos.';
    });
    $prueba('P-17', function () use ($hacerPeticion, $archivosCookies, $idProveedor, $contarPedidosProveedor, $cantidadAntesInvalidos, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=guardar',
            'POST',
            ['proveedor_id' => $idProveedor, 'productos' => []]
        );
        $exigir(strpos($respuesta['cuerpo'], 'Agrega al menos un producto') !== false, 'No se mostró la validación de productos.');
        $exigir($contarPedidosProveedor() === $cantidadAntesInvalidos, 'Se creó un pedido sin productos.');
        return 'El POST fue rechazado y no insertó pedidos.';
    });
    $prueba('P-18', function () use ($hacerPeticion, $archivosCookies, $idProveedor, $idsProductos, $contarPedidosProveedor, $cantidadAntesInvalidos, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=guardar',
            'POST',
            ['proveedor_id' => $idProveedor, 'productos' => [$idsProductos[0] => ['cantidad' => '0']]]
        );
        $exigir(strpos($respuesta['cuerpo'], 'La cantidad debe ser mayor a 0') !== false, 'No se mostró el error de cantidad.');
        $exigir($contarPedidosProveedor() === $cantidadAntesInvalidos, 'Se creó un pedido con cantidad cero.');
        return 'El POST fue rechazado y no insertó pedidos.';
    });
    $pedidoCostoFalso = null;
    $prueba('P-19', function () use ($crearPedidoHttp, &$idsProductos, &$pdo, &$pedidoCostoFalso, $exigir) {
        $pedidoCostoFalso = $crearPedidoHttp([
            $idsProductos[0] => ['cantidad' => '1', 'costo' => '0.01']
        ]);
        $exigir($pedidoCostoFalso['id'] !== null, 'El pedido con costo manipulado no se guardó.');
        $consulta = $pdo->prepare(
            'SELECT c.Total, d.Precio_Unitario
             FROM compras c
             INNER JOIN detalle_compras d ON d.Id_Compras = c.Id_Compras
             WHERE c.Id_Compras = ?'
        );
        $consulta->execute([$pedidoCostoFalso['id']]);
        $fila = $consulta->fetch();
        $exigir(
            $fila && abs((float) $fila['Precio_Unitario'] - 5.00) < 0.001
                && abs((float) $fila['Total'] - 5.00) < 0.001,
            'El servidor no usó Precio_Compra del catálogo en el detalle y total.'
        );
        return 'El costo enviado (0.01) fue ignorado; se guardó el costo del catálogo ($5.00).';
    });
    $cantidadAntesInvalidos = $contarPedidosProveedor();

    $prueba('P-20', function () use (
        $hacerPeticion,
        &$archivosCookies,
        &$idProveedor,
        &$idsProductos,
        &$nombresProductos,
        &$pdo,
        &$cantidadAntesInvalidos,
        $contarPedidosProveedor,
        $exigir
    ) {
        $actualizarPrecio = $pdo->prepare(
            'UPDATE productos SET Precio_Compra = NULL WHERE Id_Producto = ?'
        );
        $actualizarPrecio->execute([$idsProductos[0]]);
        try {
            $respuesta = $hacerPeticion(
                $archivosCookies['Administrador'],
                '?c=pedido&a=guardar',
                'POST',
                [
                    'proveedor_id' => $idProveedor,
                    'productos' => [$idsProductos[0] => ['cantidad' => '1', 'costo' => '5.00']]
                ]
            );
            $exigir(
                strpos(
                    $respuesta['cuerpo'],
                    'El producto ' . $nombresProductos[0]
                        . ' no tiene precio de compra; asígnalo en Productos'
                ) !== false,
                'No se mostró el mensaje de producto sin costo.'
            );
            $exigir(
                $contarPedidosProveedor() === $cantidadAntesInvalidos,
                'Se creó un pedido para un producto sin costo.'
            );
        } finally {
            $actualizarPrecio = $pdo->prepare(
                'UPDATE productos SET Precio_Compra = 5.00 WHERE Id_Producto = ?'
            );
            $actualizarPrecio->execute([$idsProductos[0]]);
        }
        return 'El servidor rechazó el producto sin costo y no guardó el pedido.';
    });
    $prueba('P-21', function () use ($hacerPeticion, $archivosCookies, $idProveedor, $idsProductos, $contarPedidosProveedor, $cantidadAntesInvalidos, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=guardar',
            'POST',
            [
                'proveedor_id' => $idProveedor,
                'productos' => [$idsProductos[0] => ['cantidad' => '10001']]
            ]
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'La compra supera el stock máximo permitido') !== false,
            'No apareció el mensaje del trigger al superar Stock_Maximo.'
        );
        $exigir($contarPedidosProveedor() === $cantidadAntesInvalidos, 'El pedido rechazado quedó guardado.');
        return 'El trigger rechazó la cantidad y el pedido no quedó persistido.';
    });

    $pedidoPrincipal = $crearPedidoHttp([
        $idsProductos[0] => ['cantidad' => '2', 'costo' => '10.00'],
        $idsProductos[1] => ['cantidad' => '2', 'costo' => '5.00'],
        $idsProductos[2] => ['cantidad' => '3', 'costo' => '7.00']
    ]);
    if ($pedidoPrincipal['id'] !== null) {
        $idsPedidos[$pedidoPrincipal['id']] = $pedidoPrincipal['id'];
        $consultaDetalles = $pdo->prepare(
            'SELECT Id_Detalle, Id_Producto FROM detalle_compras WHERE Id_Compras = ?'
        );
        $consultaDetalles->execute([$pedidoPrincipal['id']]);
        foreach ($consultaDetalles->fetchAll() as $detalle) {
            $idsDetalles[(int) $detalle['Id_Producto']] = (int) $detalle['Id_Detalle'];
        }
    }
    $prueba('P-22', function () use ($pedidoPrincipal, &$pdo, &$idsProductos, &$idProveedor, &$nombreProveedor, $exigir) {
        $exigir($pedidoPrincipal['id'] !== null, 'No se encontró el pedido insertado por HTTP.');
        $id = $pedidoPrincipal['id'];
        $exigir(
            strpos($pedidoPrincipal['respuesta']['cuerpo'], 'Pedido #' . $id . ' registrado') !== false,
            'No apareció el mensaje de pedido registrado.'
        );
        $consultaCompra = $pdo->prepare(
            'SELECT Total, Estado, Nombre_Proveedor FROM compras WHERE Id_Compras = ?'
        );
        $consultaCompra->execute([$id]);
        $compra = $consultaCompra->fetch();
        $exigir(
            $compra && $compra['Estado'] === 'Pendiente'
                && abs((float) $compra['Total'] - 35.00) < 0.001
                && $compra['Nombre_Proveedor'] === $nombreProveedor,
            'Estado, total recalculado o copia del proveedor incorrectos.'
        );
        $consultaDetalles = $pdo->prepare(
            'SELECT Id_Producto, Nombre_Producto, Precio_Unitario FROM detalle_compras WHERE Id_Compras = ?'
        );
        $consultaDetalles->execute([$id]);
        $detalles = $consultaDetalles->fetchAll();
        $exigir(count($detalles) === 3, 'El pedido no contiene tres renglones.');
        foreach ($detalles as $detalle) {
            $exigir(
                in_array((int) $detalle['Id_Producto'], array_slice($idsProductos, 0, 3), true)
                    && $detalle['Nombre_Producto'] !== null
                    && $detalle['Nombre_Producto'] !== ''
                    && abs((float) $detalle['Precio_Unitario'] - 5.00) < 0.001,
                'El trigger no llenó Nombre_Producto en todos los detalles.'
            );
        }
        return "Pedido #{$id}: Pendiente, total $35.00; los costos manipulados se ignoraron y se usó el catálogo.";
    });

    $prueba('P-24', function () use ($exigir, &$tamanoLogOriginal, &$nombreProveedor, &$nombresProductos, &$pedidoPrincipal) {
        $contenidoActual = file_get_contents(RUTA_LOG_CORREOS);
        $contenidoNuevo = $contenidoActual === false ? '' : substr($contenidoActual, $tamanoLogOriginal);
        $exigir(
            $pedidoPrincipal['id'] !== null
                && strpos($contenidoNuevo, 'Pedido #' . $pedidoPrincipal['id']) !== false
                && strpos($contenidoNuevo, $nombreProveedor) !== false
                && strpos($contenidoNuevo, $nombresProductos[0]) !== false
                && strpos($contenidoNuevo, 'Total: $35.00') !== false,
            'El log no contiene correo de pedido, productos y total en los bytes nuevos.'
        );
        return 'El tramo nuevo del log contiene el pedido, sus productos y el total recalculado.';
    });

    $respuestaBuscaConHash = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=lista&busqueda=%23' . $pedidoPrincipal['id'] . '&estado=Pendiente'
    );
    $prueba('P-04', function () use ($respuestaBuscaConHash, &$pedidoPrincipal, $exigir) {
        $exigir(
            strpos($respuestaBuscaConHash['cuerpo'], '#' . $pedidoPrincipal['id']) !== false,
            'No se encontró el pedido al buscar con #.'
        );
        return 'La búsqueda por número con # mostró el pedido esperado.';
    });
    $respuestaBuscaSinHash = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=lista&busqueda=' . $pedidoPrincipal['id'] . '&estado=Pendiente'
    );
    $prueba('P-05', function () use ($respuestaBuscaSinHash, &$pedidoPrincipal, $exigir) {
        $exigir(
            strpos($respuestaBuscaSinHash['cuerpo'], '#' . $pedidoPrincipal['id']) !== false,
            'No se encontró el pedido al buscar sin #.'
        );
        return 'La búsqueda por número sin # mostró el pedido esperado.';
    });
    $respuestaBuscaProveedor = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=lista&busqueda=' . rawurlencode($nombreProveedor) . '&estado=Pendiente'
    );
    $prueba('P-06', function () use ($respuestaBuscaProveedor, &$pedidoPrincipal, &$nombreProveedor, $exigir) {
        $exigir(
            strpos($respuestaBuscaProveedor['cuerpo'], $nombreProveedor) !== false
                && strpos($respuestaBuscaProveedor['cuerpo'], '#' . $pedidoPrincipal['id']) !== false,
            'La búsqueda por proveedor no mostró el pedido.'
        );
        return 'La búsqueda por nombre de proveedor mostró el pedido pendiente.';
    });

    $prueba('P-27', function () use (
        &$pedidoCancelado,
        &$pedidoExtra,
        &$idsProductos,
        $crearPedidoHttp,
        $hacerPeticion,
        &$archivosCookies,
        &$pdo,
        &$idProveedor,
        &$idEmpleadoAdmin,
        &$idsPedidos,
        $exigir
    ) {
        $pedidoCancelado = $crearPedidoHttp([
            $idsProductos[0] => ['cantidad' => '1', 'costo' => '2.00']
        ]);
        $pedidoExtra = $crearPedidoHttp([
            $idsProductos[2] => ['cantidad' => '1', 'costo' => '3.00']
        ]);
        $exigir(
            $pedidoCancelado['id'] !== null && $pedidoExtra['id'] !== null,
            'No se pudieron crear los pedidos auxiliares por HTTP.'
        );
        $idsPedidos[$pedidoCancelado['id']] = $pedidoCancelado['id'];
        $idsPedidos[$pedidoExtra['id']] = $pedidoExtra['id'];
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=cancelar',
            'POST',
            ['id_pedido' => $pedidoCancelado['id']]
        );
        $consulta = $pdo->prepare('SELECT Estado FROM compras WHERE Id_Compras = ?');
        $consulta->execute([$pedidoCancelado['id']]);
        $estado = $consulta->fetchColumn();
        $exigir(
            $estado === 'Cancelada'
                && strpos($respuesta['cuerpo'], 'Pedido #' . $pedidoCancelado['id'] . ' cancelado.') !== false,
            'No cambió el pedido a Cancelada o faltó el mensaje de éxito.'
        );
        return "Pedido #{$pedidoCancelado['id']} cancelado por HTTP; el auxiliar mantiene una búsqueda múltiple.";
    });
    $prueba('P-25', function () use ($exigir, &$tamanoLogOriginal, &$pedidoCancelado, &$nombreProveedor) {
        $contenidoActual = file_get_contents(RUTA_LOG_CORREOS);
        $contenidoNuevo = $contenidoActual === false ? '' : substr($contenidoActual, $tamanoLogOriginal);
        $exigir(
            $pedidoCancelado['id'] !== null
                && strpos($contenidoNuevo, 'Pedido #' . $pedidoCancelado['id'] . ' cancelado') !== false
                && strpos($contenidoNuevo, $nombreProveedor) !== false,
            'No se encontró el correo de cancelación en los bytes nuevos del log.'
        );
        return 'El log nuevo contiene el correo de cancelación del pedido.';
    });

    $respuestaEntradasInicio = $hacerPeticion($archivosCookies['Almacenista'], '?c=entrada&a=index');
    $prueba('E-01', function () use ($respuestaEntradasInicio, $exigir) {
        $exigir(
            strpos($respuestaEntradasInicio['cuerpo'], '>Entradas</span>') !== false,
            'El menú no muestra Entradas al Almacenista.'
        );
        return 'El menú de Almacenista muestra Entradas.';
    });
    $prueba('E-02', function () use ($respuestaEntradasInicio, $exigir) {
        $exigir(
            strpos($respuestaEntradasInicio['cuerpo'], 'Pedidos por recibir') !== false
                && strpos($respuestaEntradasInicio['cuerpo'], '>Recibir</a>') === false,
            'La lista inicial no está visible o todavía tiene botón Recibir.'
        );
        return 'La lista inicial está visible y no contiene botón Recibir.';
    });

    $rutaPrincipal = '?c=entrada&a=index&busqueda=%23' . $pedidoPrincipal['id'];
    $respuestaEntradaNumero = $hacerPeticion($archivosCookies['Almacenista'], $rutaPrincipal);
    $prueba('E-03', function () use ($respuestaEntradaNumero, &$pedidoPrincipal, &$nombreProveedor, $exigir) {
        $exigir(
            strpos($respuestaEntradaNumero['cuerpo'], 'Pedido #' . $pedidoPrincipal['id']) !== false
                && strpos($respuestaEntradaNumero['cuerpo'], $nombreProveedor) !== false
                && strpos($respuestaEntradaNumero['cuerpo'], 'Registrar entrada de mercancía') !== false
                && strpos($respuestaEntradaNumero['cuerpo'], 'Pedidos por recibir') === false,
            'No abrió el pedido o la lista inferior sigue visible.'
        );
        return 'El pedido se abrió y no se renderizó la tabla inferior.';
    });

    $prueba('E-05', function () use ($hacerPeticion, &$archivosCookies, &$nombreProveedor, &$pedidoPrincipal, &$pedidoExtra, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Almacenista'],
            '?c=entrada&a=index&busqueda=' . rawurlencode($nombreProveedor)
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'Pedidos encontrados') !== false
                && strpos($respuesta['cuerpo'], '#' . $pedidoPrincipal['id']) !== false
                && strpos($respuesta['cuerpo'], '#' . $pedidoExtra['id']) !== false,
            'La búsqueda por proveedor no mostró únicamente la lista con los pedidos pendientes de prueba.'
        );
        return 'El proveedor devuelve los dos pedidos pendientes de prueba en la tabla filtrada.';
    });
    $prueba('E-06', function () use ($hacerPeticion, &$archivosCookies, &$nombresProductos, &$pedidoPrincipal, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Almacenista'],
            '?c=entrada&a=index&busqueda=' . rawurlencode($nombresProductos[1])
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'Pedido #' . $pedidoPrincipal['id']) !== false
                && strpos($respuesta['cuerpo'], $nombresProductos[1]) !== false
                && strpos($respuesta['cuerpo'], 'Pedidos por recibir') === false,
            'La búsqueda por producto no abrió el pedido que lo contiene.'
        );
        return 'La búsqueda por producto encontró y abrió el pedido correcto.';
    });

    $existenciasAntesRecepcion = [];
    foreach (array_slice($idsProductos, 0, 3) as $idProducto) {
        $existenciasAntesRecepcion[$idProducto] = $consultarEntero(
            'SELECT Existencias FROM inventario WHERE Id_Producto = ?',
            [$idProducto]
        );
    }
    $idDetalleDaniado = $idsDetalles[$idsProductos[1]] ?? 0;
    $idDetalleFaltante = $idsDetalles[$idsProductos[2]] ?? 0;
    $prueba('E-11', function () use (
        $hacerPeticion,
        &$archivosCookies,
        &$pedidoPrincipal,
        &$idDetalleDaniado,
        &$existenciasAntesRecepcion,
        $consultarEntero,
        $exigir
    ) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Almacenista'],
            '?c=entrada&a=registrar',
            'POST',
            [
                'id_compra' => $pedidoPrincipal['id'],
                'reportes' => [
                    $idDetalleDaniado => [
                        'cantidad_recibida' => '1',
                        'incidencia' => 'Dañado',
                        'motivo' => ''
                    ]
                ]
            ]
        );
        $estado = $consultarEntero(
            "SELECT COUNT(*) FROM compras WHERE Id_Compras = ? AND Estado = 'Pendiente'",
            [$pedidoPrincipal['id']]
        );
        $stockSinCambios = true;
        foreach ($existenciasAntesRecepcion as $idProducto => $existencia) {
            $stockSinCambios = $stockSinCambios
                && $consultarEntero('SELECT Existencias FROM inventario WHERE Id_Producto = ?', [$idProducto]) === $existencia;
        }
        $exigir(
            strpos($respuesta['cuerpo'], 'El motivo es obligatorio') !== false
                && $estado === 1 && $stockSinCambios,
            'No se rechazó el reporte sin motivo o cambió el pedido/inventario.'
        );
        return 'Reporte sin motivo rechazado; el pedido sigue Pendiente y el inventario no cambió.';
    });
    $prueba('E-12', function () use (
        $hacerPeticion,
        &$archivosCookies,
        &$pedidoPrincipal,
        &$idDetalleDaniado,
        $consultarEntero,
        $exigir
    ) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Almacenista'],
            '?c=entrada&a=registrar',
            'POST',
            [
                'id_compra' => $pedidoPrincipal['id'],
                'reportes' => [
                    $idDetalleDaniado => [
                        'cantidad_recibida' => '3',
                        'incidencia' => 'Dañado',
                        'motivo' => 'Prueba Integral Motivo'
                    ]
                ]
            ]
        );
        $pendiente = $consultarEntero(
            "SELECT COUNT(*) FROM compras WHERE Id_Compras = ? AND Estado = 'Pendiente'",
            [$pedidoPrincipal['id']]
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'La cantidad recibida está fuera del rango permitido') !== false
                && $pendiente === 1,
            'No rechazó cantidad recibida superior a la pedida.'
        );
        return 'El reporte con cantidad mayor a la pedida fue rechazado.';
    });

    $respuestaEntradaRegistro = $hacerPeticion(
        $archivosCookies['Almacenista'],
        '?c=entrada&a=registrar',
        'POST',
        [
            'id_compra' => $pedidoPrincipal['id'],
            'reportes' => [
                $idDetalleDaniado => [
                    'cantidad_recibida' => '1',
                    'incidencia' => 'Dañado',
                    'motivo' => 'Prueba Integral Dano'
                ],
                $idDetalleFaltante => [
                    'cantidad_recibida' => '0',
                    'incidencia' => 'Faltante',
                    'motivo' => 'Prueba Integral Faltante'
                ]
            ]
        ]
    );
    $prueba('E-18', function () use ($respuestaEntradaRegistro, &$pedidoPrincipal, $consultarEntero, $exigir) {
        $estado = $consultarEntero(
            "SELECT COUNT(*) FROM compras WHERE Id_Compras = ? AND Estado = 'Recibida'",
            [$pedidoPrincipal['id']]
        );
        $exigir(
            strpos($respuestaEntradaRegistro['cuerpo'], 'Entrada registrada. El pedido #' . $pedidoPrincipal['id'] . ' pasó a Recibido.') !== false
                && $estado === 1,
            'No apareció el mensaje final o el pedido no pasó a Recibida.'
        );
        return 'La entrada se registró por HTTP y el pedido pasó a Recibida.';
    });

    $prueba('E-19', function () use ($hacerPeticion, &$archivosCookies, &$pedidoPrincipal, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Almacenista'],
            '?c=entrada&a=index&busqueda=' . $pedidoPrincipal['id']
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'Pedido no encontrado') !== false,
            'El pedido recibido todavía se pudo encontrar en Entradas.'
        );
        return 'El pedido recibido ya no aparece en la búsqueda de entradas.';
    });

    $prueba('E-21', function () use (
        &$idsProductos,
        &$existenciasAntesRecepcion,
        &$pedidoPrincipal,
        &$idEmpleadoAlmacenista,
        &$pdo,
        $exigir
    ) {
        $esperadas = [
            $idsProductos[0] => $existenciasAntesRecepcion[$idsProductos[0]] + 2,
            $idsProductos[1] => $existenciasAntesRecepcion[$idsProductos[1]] + 1,
            $idsProductos[2] => $existenciasAntesRecepcion[$idsProductos[2]]
        ];
        foreach ($esperadas as $idProducto => $existenciaEsperada) {
            $consulta = $pdo->prepare('SELECT Existencias FROM inventario WHERE Id_Producto = ?');
            $consulta->execute([$idProducto]);
            $exigir((int) $consulta->fetchColumn() === $existenciaEsperada, "Stock incorrecto para producto {$idProducto}.");
        }
        $consultaMovimiento = $pdo->prepare(
            'SELECT Motivo, Id_Empleado FROM movimiento_inventario WHERE Id_Producto = ?'
        );
        $consultaMovimiento->execute([$idsProductos[0]]);
        $movimientoCompleto = $consultaMovimiento->fetch();
        $consultaMovimiento->execute([$idsProductos[1]]);
        $movimientoDanado = $consultaMovimiento->fetch();
        $consultaMovimiento->execute([$idsProductos[2]]);
        $movimientoFaltante = $consultaMovimiento->fetch();
        $movimientosObtenidos = [
            'completo' => $movimientoCompleto,
            'danado' => $movimientoDanado,
            'faltante' => $movimientoFaltante
        ];
        $movimientosCorrectos = $movimientoCompleto
            && $movimientoCompleto['Motivo'] === 'Pedido #' . $pedidoPrincipal['id']
            && (int) $movimientoCompleto['Id_Empleado'] === $idEmpleadoAlmacenista
            && $movimientoDanado
            && $movimientoDanado['Motivo'] === 'Pedido #' . $pedidoPrincipal['id'] . ' - Dañado: Prueba Integral Dano'
            && (int) $movimientoDanado['Id_Empleado'] === $idEmpleadoAlmacenista;
        if ($movimientoFaltante) {
            $movimientosCorrectos = $movimientosCorrectos
                && $movimientoFaltante['Motivo'] === 'Pedido #' . $pedidoPrincipal['id'] . ' - Faltante: Prueba Integral Faltante'
                && (int) $movimientoFaltante['Id_Empleado'] === $idEmpleadoAlmacenista;
        }
        $exigir(
            $movimientosCorrectos,
            'No coinciden stock/motivos/empleados. Movimientos: ' . json_encode($movimientosObtenidos, JSON_UNESCAPED_UNICODE)
                . '; Id_Empleado esperado: ' . $idEmpleadoAlmacenista . '.'
        );
        return 'El stock refleja 2, 1 y 0 piezas; los movimientos registran la entrada completa y el daño.';
    });

    $prueba('E-22', function () use (&$pdo, &$pedidoPrincipal, &$idsProductos, $exigir) {
        $consulta = $pdo->prepare(
            'SELECT Id_Producto, Cantidad_Recibida, Incidencia, Motivo_Incidencia
             FROM detalle_compras WHERE Id_Compras = ?'
        );
        $consulta->execute([$pedidoPrincipal['id']]);
        $detalles = [];
        foreach ($consulta->fetchAll() as $detalle) {
            $detalles[(int) $detalle['Id_Producto']] = $detalle;
        }
        $exigir(
            (int) $detalles[$idsProductos[0]]['Cantidad_Recibida'] === 2
                && $detalles[$idsProductos[0]]['Incidencia'] === 'Ninguna'
                && (int) $detalles[$idsProductos[1]]['Cantidad_Recibida'] === 1
                && $detalles[$idsProductos[1]]['Incidencia'] === 'Dañado'
                && $detalles[$idsProductos[1]]['Motivo_Incidencia'] === 'Prueba Integral Dano'
                && (int) $detalles[$idsProductos[2]]['Cantidad_Recibida'] === 0
                && $detalles[$idsProductos[2]]['Incidencia'] === 'Faltante'
                && $detalles[$idsProductos[2]]['Motivo_Incidencia'] === 'Prueba Integral Faltante',
            'Los valores guardados en detalle_compras no coinciden con los reportes.'
        );
        return 'detalle_compras conserva cantidades, incidencias y motivos enviados por HTTP.';
    });
    $prueba('E-23', function () use (&$pdo, &$pedidoPrincipal, &$idEmpleadoAlmacenista, $exigir) {
        $consulta = $pdo->prepare(
            'SELECT Fecha_Recepcion, Id_Empleado_Recibe FROM compras WHERE Id_Compras = ?'
        );
        $consulta->execute([$pedidoPrincipal['id']]);
        $compra = $consulta->fetch();
        $exigir(
            $compra && !empty($compra['Fecha_Recepcion'])
                && (int) $compra['Id_Empleado_Recibe'] === $idEmpleadoAlmacenista,
            'No se guardó Fecha_Recepcion o el empleado de recepción es incorrecto.'
        );
        return 'Fecha_Recepcion e Id_Empleado_Recibe corresponden al Almacenista de prueba.';
    });
    $prueba('E-20', function () use ($hacerPeticion, &$archivosCookies, &$pedidoPrincipal, &$nombresProductos, &$sufijo, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=ver&id=' . $pedidoPrincipal['id']
        );
        $nombreAlmacenista = 'Prueba Integral Almacenista ' . $sufijo;
        $exigir(
            strpos($respuesta['cuerpo'], 'Dañado: 1 - Prueba Integral Dano') !== false
                && strpos($respuesta['cuerpo'], $nombresProductos[1]) !== false
                && strpos($respuesta['cuerpo'], $nombreAlmacenista) !== false,
            'El Administrador no ve el daño o el empleado que recibió.'
        );
        return 'La vista administrativa de Ver muestra la observación del daño y el Almacenista.';
    });

    $respuestaRecibidos = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=lista&busqueda=' . $pedidoPrincipal['id'] . '&estado=Recibida'
    );
    $prueba('P-07', function () use ($respuestaRecibidos, &$pedidoPrincipal, $exigir) {
        $exigir(
            strpos($respuestaRecibidos['cuerpo'], '#' . $pedidoPrincipal['id']) !== false
                && strpos($respuestaRecibidos['cuerpo'], 'Recibido') !== false
                && strpos($respuestaRecibidos['cuerpo'], 'Cancelar pedido') === false,
            'El filtro Recibido no aisló el pedido o mostró acción de cancelar.'
        );
        return 'El pedido recibido aparece sin botón Cancelar pedido.';
    });
    $respuestaCancelados = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=lista&busqueda=' . $pedidoCancelado['id'] . '&estado=Cancelada'
    );
    $prueba('P-08', function () use ($respuestaCancelados, &$pedidoCancelado, $exigir) {
        $exigir(
            strpos($respuestaCancelados['cuerpo'], '#' . $pedidoCancelado['id']) !== false
                && strpos($respuestaCancelados['cuerpo'], 'Cancelado') !== false,
            'El filtro Cancelado no mostró el pedido cancelado.'
        );
        return 'El filtro Cancelado muestra el pedido cancelado de prueba.';
    });
    $respuestaTodos = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=lista&busqueda=' . rawurlencode($nombreProveedor) . '&estado=Todos'
    );
    $prueba('P-09', function () use ($respuestaTodos, &$pedidoPrincipal, &$pedidoCancelado, &$pedidoExtra, $exigir) {
        $exigir(
            strpos($respuestaTodos['cuerpo'], '#' . $pedidoPrincipal['id']) !== false
                && strpos($respuestaTodos['cuerpo'], '#' . $pedidoCancelado['id']) !== false
                && strpos($respuestaTodos['cuerpo'], '#' . $pedidoExtra['id']) !== false,
            'El filtro Todos no incluyó pedidos en distintos estados.'
        );
        return 'El filtro Todos muestra pedidos Pendiente, Recibida y Cancelada del proveedor.';
    });
    $respuestaDetalleRecibido = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=pedido&a=ver&id=' . $pedidoPrincipal['id']
    );
    $prueba('P-11', function () use ($respuestaDetalleRecibido, &$pedidoPrincipal, &$idEmpleadoAlmacenista, &$nombresProductos, &$sufijo, $exigir) {
        $nombreAlmacenista = 'Prueba Integral Almacenista ' . $sufijo;
        $exigir(
            strpos($respuestaDetalleRecibido['cuerpo'], 'Fecha de recepción:') !== false
                && strpos($respuestaDetalleRecibido['cuerpo'], 'Recibido por:') !== false
                && strpos($respuestaDetalleRecibido['cuerpo'], $nombreAlmacenista) !== false
                && strpos($respuestaDetalleRecibido['cuerpo'], $nombresProductos[1]) !== false
                && strpos($respuestaDetalleRecibido['cuerpo'], 'Dañado: 1 - Prueba Integral Dano') !== false,
            'El detalle recibido no muestra fecha, empleado u observación del daño.'
        );
        return 'El historial recibido muestra al almacenista y la observación del producto dañado.';
    });

    $prueba('P-28', function () use ($hacerPeticion, &$archivosCookies, &$pedidoPrincipal, $consultarEntero, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=pedido&a=cancelar',
            'POST',
            ['id_pedido' => $pedidoPrincipal['id']]
        );
        $estadoRecibido = $consultarEntero(
            "SELECT COUNT(*) FROM compras WHERE Id_Compras = ? AND Estado = 'Recibida'",
            [$pedidoPrincipal['id']]
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'El pedido ya no está por recibir') !== false
                && $estadoRecibido === 1,
            'El sistema canceló el pedido recibido o no mostró el rechazo.'
        );
        return 'El intento de cancelar un pedido recibido se rechazó y conservó Recibida.';
    });

    $prueba('E-07', function () use ($hacerPeticion, &$archivosCookies, &$pedidoPrincipal, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Almacenista'],
            '?c=entrada&a=index&busqueda=' . $pedidoPrincipal['id']
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'Pedido no encontrado') !== false,
            'La búsqueda de pedido Recibido no mostró Pedido no encontrado.'
        );
        return 'La búsqueda de pedido Recibido fue rechazada.';
    });
    $prueba('E-08', function () use ($hacerPeticion, &$archivosCookies, &$pedidoCancelado, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Almacenista'],
            '?c=entrada&a=index&busqueda=' . $pedidoCancelado['id']
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'Pedido no encontrado') !== false,
            'La búsqueda de pedido Cancelado no mostró Pedido no encontrado.'
        );
        return 'La búsqueda de pedido Cancelado fue rechazada.';
    });

    $respuestaAdminEntrada = $hacerPeticion(
        $archivosCookies['Administrador'],
        '?c=entrada&a=index'
    );
    $prueba('R-01', function () use ($hacerPeticion, &$archivosCookies, $exigir) {
        $respuesta = $hacerPeticion($archivosCookies['Almacenista'], '?c=pedido&a=lista');
        $exigir(
            strpos($respuesta['url'], 'c=inicio') !== false
                && strpos($respuesta['cuerpo'], 'Pedidos a proveedores') === false,
            'El Almacenista pudo entrar a Pedidos.'
        );
        return 'La ruta redirigió a Inicio y no mostró Pedidos.';
    });
    $prueba('R-02', function () use ($hacerPeticion, &$archivosCookies, $exigir) {
        $respuesta = $hacerPeticion($archivosCookies['Vendedor'], '?c=pedido&a=lista');
        $exigir(
            strpos($respuesta['url'], 'c=inicio') !== false
                && strpos($respuesta['cuerpo'], 'Pedidos a proveedores') === false,
            'El Vendedor pudo entrar a Pedidos.'
        );
        return 'La ruta redirigió a Inicio y no mostró Pedidos.';
    });
    $prueba('R-03', function () use ($hacerPeticion, &$archivosCookies, $exigir) {
        $respuesta = $hacerPeticion($archivosCookies['Vendedor'], '?c=entrada&a=index');
        $exigir(
            strpos($respuesta['url'], 'c=inicio') !== false
                && strpos($respuesta['cuerpo'], 'Registrar entrada de mercancía') === false,
            'El Vendedor pudo entrar a Entradas.'
        );
        return 'La ruta redirigió a Inicio y no mostró Entradas.';
    });
    $prueba('R-04', function () use ($respuestaAdminEntrada, $exigir) {
        $exigir(
            strpos($respuestaAdminEntrada['url'], 'c=inicio') !== false
                && strpos($respuestaAdminEntrada['cuerpo'], 'Registrar entrada de mercancía') === false,
            'El Administrador pudo entrar a Entradas.'
        );
        return 'La ruta redirigió a Inicio y no mostró Entradas.';
    });
    $prueba('R-05', function () use ($hacerPeticion, &$archivosCookies, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['anonimo'],
            '?c=pedido&a=lista'
        );
        $exigir(
            strpos($respuesta['url'], 'c=pedido') === false
                && strpos($respuesta['cuerpo'], 'Iniciar sesión') !== false,
            'La sesión anónima no fue enviada al login.'
        );
        return 'La sesión anónima terminó en el formulario de login.';
    });

    $prueba('A-01', function () use ($hacerPeticion, &$archivosCookies, &$sufijo, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=proveedor&a=guardar',
            'POST',
            [
                'nombre' => 'Prueba Integral Proveedor Sin Correo ' . $sufijo,
                'tipo' => 'Mayorista',
                'contacto' => 'Prueba Integral Contacto',
                'telefono' => '5557654321',
                'correo' => '',
                'direccion' => 'Prueba Integral Direccion'
            ]
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'El correo es obligatorio') !== false,
            'No se mostró la validación de correo obligatorio al agregar.'
        );
        return 'El formulario de alta rechazó el proveedor sin correo.';
    });
    $prueba('A-02', function () use ($hacerPeticion, &$archivosCookies, &$idProveedor, &$nombreProveedor, &$sufijo, $exigir) {
        $respuesta = $hacerPeticion(
            $archivosCookies['Administrador'],
            '?c=proveedor&a=guardar',
            'POST',
            [
                'id_proveedor' => $idProveedor,
                'nombre' => $nombreProveedor,
                'tipo' => 'Mayorista',
                'contacto' => 'Prueba Integral Contacto',
                'telefono' => '5551234567',
                'correo' => '',
                'direccion' => 'Prueba Integral Direccion'
            ]
        );
        $exigir(
            strpos($respuesta['cuerpo'], 'El correo es obligatorio') !== false,
            'No se mostró la validación de correo obligatorio al editar.'
        );
        return 'El formulario de edición rechazó el correo vacío.';
    });

    $prueba('T-01', function () {
        $archivoSuite = __DIR__ . DIRECTORY_SEPARATOR . 'run.php';
        $descriptores = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['redirect', 1]
        ];
        $proceso = proc_open([PHP_BINARY, $archivoSuite], $descriptores, $tuberias, dirname(__DIR__));
        if (!is_resource($proceso)) {
            throw new RuntimeException('No se pudo iniciar tests/run.php.');
        }
        fclose($tuberias[0]);
        $salida = stream_get_contents($tuberias[1]);
        fclose($tuberias[1]);
        $codigo = proc_close($proceso);
        if (!preg_match('/Total:\\s*(\\d+)\\s+pruebas;\\s*(\\d+)\\s+OK;\\s*(\\d+)\\s+FALLA;\\s*(\\d+)\\s+OMITIDA\\./', $salida, $totales)) {
            throw new RuntimeException('No se pudo extraer el total de tests/run.php. Código ' . $codigo . '.');
        }
        $detalle = "{$totales[1]} pruebas; {$totales[2]} OK; {$totales[3]} FALLA; {$totales[4]} OMITIDA.";
        if ($codigo !== 0 || (int) $totales[3] !== 0) {
            $lineasFalla = [];
            foreach (preg_split('/\\r\\n|\\n|\\r/', $salida) as $linea) {
                if (preg_match('/\\|\\s*FALLA\\s*$/', $linea) === 1) {
                    $lineasFalla[] = trim($linea);
                }
            }
            throw new RuntimeException(
                'La suite reportó fallas: ' . $detalle
                . (empty($lineasFalla) ? '' : ' Detalle: ' . implode(' / ', $lineasFalla))
            );
        }
        return $detalle;
    });
} catch (Throwable $error) {
    $mensajePreparacion = $error->getMessage();
    foreach ($definicionesPruebas as $id => $descripcion) {
        if (!isset($resultadosPruebas[$id])) {
            $resultadosPruebas[$id] = [
                'resultado' => 'FALLA',
                'detalle' => 'No se pudo ejecutar: ' . $mensajePreparacion
            ];
        }
    }
} finally {
    // Elimina solo registros ligados al proveedor recién creado y restaura el log original.
    if ($pdo instanceof PDO && $idProveedor !== null) {
        try {
            $consultaPedidosPropios = $pdo->prepare(
                'SELECT Id_Compras FROM compras WHERE Id_Proveedor = ? AND Id_Empleado = ?'
            );
            $consultaPedidosPropios->execute([$idProveedor, $idEmpleadoAdmin]);
            foreach ($consultaPedidosPropios->fetchAll(PDO::FETCH_COLUMN) as $idPedido) {
                $idsPedidos[(int) $idPedido] = (int) $idPedido;
            }

            $pdo->beginTransaction();
            $idsEmpleados = array_values(array_filter([
                $idEmpleadoAdmin,
                $idEmpleadoAlmacenista,
                $idEmpleadoVendedor
            ]));
            if (!empty($idsProductos) || !empty($idsEmpleados)) {
                $marcasProductos = empty($idsProductos)
                    ? ''
                    : implode(',', array_fill(0, count($idsProductos), '?'));
                $parametrosMovimiento = $idsProductos;
                $condicionesMovimiento = [];
                if ($marcasProductos !== '') {
                    $condicionesMovimiento[] = "Id_Producto IN ({$marcasProductos})";
                }
                if (!empty($idsEmpleados)) {
                    $marcasEmpleados = implode(',', array_fill(0, count($idsEmpleados), '?'));
                    $condicionesMovimiento[] = "Id_Empleado IN ({$marcasEmpleados})";
                    $parametrosMovimiento = array_merge($parametrosMovimiento, $idsEmpleados);
                }
                if (!empty($condicionesMovimiento)) {
                    $borrarMovimientos = $pdo->prepare(
                        'DELETE FROM movimiento_inventario WHERE ' . implode(' OR ', $condicionesMovimiento)
                    );
                    $borrarMovimientos->execute($parametrosMovimiento);
                }
            }

            if (!empty($idsPedidos)) {
                $marcasPedidos = implode(',', array_fill(0, count($idsPedidos), '?'));
                $borrarDetalles = $pdo->prepare(
                    "DELETE FROM detalle_compras WHERE Id_Compras IN ({$marcasPedidos})"
                );
                $borrarDetalles->execute(array_values($idsPedidos));
                $borrarCompras = $pdo->prepare(
                    "DELETE FROM compras WHERE Id_Compras IN ({$marcasPedidos})"
                );
                $borrarCompras->execute(array_values($idsPedidos));
            }
            if (!empty($idsProductos)) {
                $marcasProductos = implode(',', array_fill(0, count($idsProductos), '?'));
                $borrarInventario = $pdo->prepare(
                    "DELETE FROM inventario WHERE Id_Producto IN ({$marcasProductos})"
                );
                $borrarInventario->execute($idsProductos);
                $borrarProductos = $pdo->prepare(
                    "DELETE FROM productos WHERE Id_Producto IN ({$marcasProductos})"
                );
                $borrarProductos->execute($idsProductos);
            }
            if ($idProveedor !== null) {
                $borrarProveedor = $pdo->prepare('DELETE FROM proveedor WHERE Id_Proveedor = ?');
                $borrarProveedor->execute([$idProveedor]);
            }
            $idsUsuarios = array_values(array_filter([
                $idUsuarioAdmin,
                $idUsuarioAlmacenista,
                $idUsuarioVendedor
            ]));
            if (!empty($idsUsuarios)) {
                $marcasUsuarios = implode(',', array_fill(0, count($idsUsuarios), '?'));
                $borrarUsuarios = $pdo->prepare(
                    "DELETE FROM usuario WHERE Id_Usuario IN ({$marcasUsuarios})"
                );
                $borrarUsuarios->execute($idsUsuarios);
            }
            $idsEmpleados = array_values(array_filter([
                $idEmpleadoAdmin,
                $idEmpleadoAlmacenista,
                $idEmpleadoVendedor
            ]));
            if (!empty($idsEmpleados)) {
                $marcasEmpleados = implode(',', array_fill(0, count($idsEmpleados), '?'));
                $borrarEmpleados = $pdo->prepare(
                    "DELETE FROM empleados WHERE Id_Empleado IN ({$marcasEmpleados})"
                );
                $borrarEmpleados->execute($idsEmpleados);
            }
            $pdo->commit();
        } catch (Throwable $errorLimpieza) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erroresLimpieza[] = 'Error al borrar datos temporales: ' . $errorLimpieza->getMessage();
        }
    }

    if (isset($contenidoLogOriginal) && ($existeLogOriginal || file_exists(RUTA_LOG_CORREOS))) {
        if ($existeLogOriginal) {
            if (file_put_contents(RUTA_LOG_CORREOS, $contenidoLogOriginal, LOCK_EX) === false) {
                $erroresLimpieza[] = 'No se pudo restaurar correos.log.';
            }
        } elseif (file_exists(RUTA_LOG_CORREOS) && !unlink(RUTA_LOG_CORREOS)) {
            $erroresLimpieza[] = 'No se pudo quitar el correos.log creado durante la prueba.';
        }
    }
    if ($modoCorreoExistia) {
        $_ENV['MAIL_MODO'] = $modoCorreoOriginal;
    } else {
        unset($_ENV['MAIL_MODO']);
    }
    if ($envModificadoTemporalmente && is_string($contenidoEnvOriginal)) {
        $rutaEnv = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
        if (file_put_contents($rutaEnv, $contenidoEnvOriginal, LOCK_EX) === false) {
            $erroresLimpieza[] = 'No se pudo restaurar .env al contenido original.';
        }
    }
    foreach ($archivosCookies as $rol => $archivoCookie) {
        if ($rol === 'usuarios' || !is_string($archivoCookie) || $archivoCookie === '') {
            continue;
        }
        if (file_exists($archivoCookie) && !unlink($archivoCookie)) {
            $erroresLimpieza[] = 'No se pudo borrar el archivo de cookies de ' . $rol . '.';
        }
    }
}

if ($pdo instanceof PDO) {
    try {
        $consultaRestantes = $pdo->query(
            "SELECT
                (SELECT COUNT(*) FROM proveedor WHERE Nombre LIKE 'Prueba Integral %')
              + (SELECT COUNT(*) FROM productos WHERE Nombre LIKE 'Prueba Integral %')
              + (SELECT COUNT(*) FROM empleados WHERE Nombre LIKE 'Prueba Integral %')
              + (SELECT COUNT(*) FROM compras WHERE Nombre_Proveedor LIKE 'Prueba Integral %')
              + (SELECT COUNT(*) FROM detalle_compras WHERE Nombre_Producto LIKE 'Prueba Integral %')"
        );
        if ((int) $consultaRestantes->fetchColumn() !== 0) {
            $erroresLimpieza[] = 'La verificación final encontró filas con el prefijo Prueba Integral.';
        }
    } catch (Throwable $errorVerificacion) {
        $erroresLimpieza[] = 'No se pudo verificar la limpieza: ' . $errorVerificacion->getMessage();
    }
}

foreach ($definicionesPruebas as $id => $descripcion) {
    if (!isset($resultadosPruebas[$id])) {
        $resultadosPruebas[$id] = [
            'resultado' => 'FALLA',
            'detalle' => 'La prueba no llegó a ejecutarse.'
        ];
    }
}

$cantidadOk = count(array_filter($resultadosPruebas, function ($resultado) {
    return $resultado['resultado'] === 'OK';
}));
$fechaInforme = date('Y-m-d H:i:s');
$limpiezaCorrecta = empty($erroresLimpieza);
$lineasInforme = [
    '# Pruebas integrales del Sprint 2',
    '',
    'Fecha de ejecución: ' . $fechaInforme,
    '',
    '| ID | Prueba | OK/FALLA | Detalle |',
    '|---|---|---|---|'
];
foreach ($definicionesPruebas as $id => $descripcion) {
    $resultado = $resultadosPruebas[$id];
    $detalle = str_replace('|', '\\|', $resultado['detalle']);
    $lineasInforme[] = '| ' . $id . ' | ' . $descripcion . ' | ' . $resultado['resultado'] . ' | ' . $detalle . ' |';
}
$lineasInforme[] = '';
$lineasInforme[] = $cantidadOk . ' de ' . count($definicionesPruebas) . ' pruebas OK';
$lineasInforme[] = '';
$lineasInforme[] = $limpiezaCorrecta
    ? 'Limpieza: OK'
    : 'Limpieza: FALLA — ' . implode(' ', $erroresLimpieza);
$lineasInforme[] = '';
$lineasInforme[] = '## Pruebas manuales pendientes';
$lineasInforme[] = '';
$lineasInforme[] = implode(', ', $pruebasManualesPendientes) . '.';
$textoInforme = implode(PHP_EOL, $lineasInforme) . PHP_EOL;
if (file_put_contents(RUTA_REPORTE_INTEGRAL, $textoInforme, LOCK_EX) === false) {
    $erroresLimpieza[] = 'No se pudo escribir docs/sprint2/pruebas_integrales.md.';
    $limpiezaCorrecta = false;
}

printf("%-6s | %-90s | %-7s | %s\n", 'ID', 'Prueba', 'OK/FALLA', 'Detalle');
printf("%s\n", str_repeat('-', 160));
foreach ($definicionesPruebas as $id => $descripcion) {
    $resultado = $resultadosPruebas[$id];
    printf(
        "%-6s | %-90s | %-7s | %s\n",
        $id,
        $descripcion,
        $resultado['resultado'],
        $resultado['detalle']
    );
}
printf("\n%d de %d pruebas OK\n", $cantidadOk, count($definicionesPruebas));
printf(
    "Limpieza: %s%s\n",
    $limpiezaCorrecta ? 'OK' : 'FALLA',
    empty($erroresLimpieza) ? '' : ' — ' . implode(' ', $erroresLimpieza)
);

exit($cantidadOk === count($definicionesPruebas) && $limpiezaCorrecta ? 0 : 1);
