<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Ferredog'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ProyectoFerredog/public/assets/css/estilos.css">
</head>
<body class="layout-interno">
    <!-- Encabezado -->
    <header class="header-interno">
        <div class="header-logo">
            <img class="logo-icono" src="/ProyectoFerredog/public/assets/img/logo.png" alt="Ferredog">
            <span class="logo-texto">FERREDOG</span>
        </div>
        <div class="header-usuario">
            <div class="usuario-info">
                <span class="usuario-nombre"><?php echo htmlspecialchars($_SESSION['Nombre']); ?></span>
                <span class="usuario-rol"><?php echo htmlspecialchars($_SESSION['Rol']); ?></span>
            </div>
            <form method="POST" action="/ProyectoFerredog/public/index.php?c=auth&a=logout" style="display: inline;">
                <button type="submit" class="btn btn-secondary btn-sm">Cerrar sesión</button>
            </form>
        </div>
    </header>
    
    <div class="layout-contenido">
