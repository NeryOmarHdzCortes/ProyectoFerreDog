<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Ferredog</title>
    <link rel="stylesheet" href="/ProyectoFerredog/public/assets/css/estilos.css">
</head>
<body>
    <div class="login-container">
        <!-- Panel izquierdo -->
        <div class="login-panel-izquierdo">
            <div class="login-logo">
                <img class="login-logo-icono" src="/ProyectoFerredog/public/assets/img/logo.png" alt="Ferredog">
                <span>FERREDOG</span>
            </div>
            <h1 class="login-titulo">Control de inventario de la ferretería</h1>
            <p class="login-descripcion">Productos, proveedores, pedidos y ventas en un solo lugar.</p>
        </div>
        
        <!-- Panel derecho -->
        <div class="login-panel-derecho">
            <div class="login-tarjeta">
                <h2>Iniciar sesión</h2>
                <p class="login-subtitulo">Ingresa con tu usuario y contraseña</p>
                
                <?php if (!empty($error)): ?>
                    <div class="alerta-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($mensaje)): ?>
                    <div class="alerta-info">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/ProyectoFerredog/public/index.php?c=auth&a=login" id="formLogin">
                    <div class="form-group">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input 
                            type="text" 
                            id="usuario" 
                            name="usuario" 
                            class="form-control"
                            value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"
                            required
                            autocomplete="username"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                id="contrasena" 
                                name="contrasena" 
                                class="form-control"
                                required
                                autocomplete="current-password"
                            >
                            <span class="input-icon" id="togglePassword">👁️</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                    
                    <div class="text-center mt-3">
                        <a href="/ProyectoFerredog/public/index.php?c=auth&a=recuperar" class="link">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>
                
                <p class="nota-pie">
                    Solo usuarios activos pueden ingresar.<br>
                    La sesión se cierra tras 30 min sin actividad.
                </p>
            </div>
        </div>
    </div>
    
    <script src="/ProyectoFerredog/public/assets/js/login.js" defer></script>
</body>
</html>
