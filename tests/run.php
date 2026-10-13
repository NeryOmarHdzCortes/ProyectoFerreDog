<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestCase.php';

$archivos = glob(__DIR__ . '/*Test.php');
sort($archivos);
$resultados = [];
$totales = [
    'pasadas' => 0,
    'fallidas' => 0,
    'aserciones_pasadas' => 0,
    'aserciones_fallidas' => 0
];

foreach ($archivos as $archivo) {
    require_once $archivo;
    $nombreClase = basename($archivo, '.php');

    try {
        $prueba = new $nombreClase($pdo);
        $prueba->ejecutar();
        $resultados = array_merge($resultados, $prueba->obtenerResultados());
        foreach ($prueba->obtenerTotales() as $nombre => $cantidad) {
            $totales[$nombre] += $cantidad;
        }
    } catch (Throwable $error) {
        $resultados[] = [
            'id' => 'SETUP-' . strtoupper($nombreClase),
            'historia' => 'Técnico',
            'prueba' => 'No se pudo ejecutar la suite: ' . $error->getMessage(),
            'resultado' => 'FALLA'
        ];
        $totales['fallidas']++;
        $totales['aserciones_fallidas']++;
    }
}

printf("%-12s | %-10s | %-78s | %s\n", 'ID', 'Historia', 'Prueba', 'Resultado (OK/FALLA)');
printf("%s\n", str_repeat('-', 126));
foreach ($resultados as $resultado) {
    printf(
        "%-12s | %-10s | %-78s | %s\n",
        $resultado['id'],
        $resultado['historia'],
        $resultado['prueba'],
        $resultado['resultado']
    );
}

printf(
    "\nTotal: %d pruebas; %d OK; %d FALLA; %d OMITIDA.\n",
    $totales['pasadas'] + $totales['fallidas'],
    $totales['pasadas'],
    $totales['fallidas'],
    count(array_filter($resultados, function ($resultado) {
        return $resultado['resultado'] === 'OMITIDA';
    }))
);
printf(
    "Aserciones: %d pasadas; %d fallidas.\n",
    $totales['aserciones_pasadas'],
    $totales['aserciones_fallidas']
);

exit($totales['fallidas'] > 0 ? 1 : 0);

