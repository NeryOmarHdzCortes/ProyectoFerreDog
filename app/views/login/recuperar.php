<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - Ferredog</title>
    <link rel="stylesheet" href="/ProyectoFerredog/public/assets/css/estilos.css">
</head>
<body class="recuperar-body">
    <div class="recuperar-container">
        <div class="recuperar-header">
            <div class="login-logo">
                <img class="login-logo-icono" src="/ProyectoFerredog/public/assets/img/logo.png" alt="Ferredog">
                <span>FERREDOG</span>
            </div>
            <a href="/ProyectoFerredog/public/index.php?c=auth&a=login" class="link">← Volver a iniciar sesión</a>
        </div>
        
        <h1 class="recuperar-titulo">Recuperar contraseña</h1>
        <p class="recuperar-subtitulo">Te enviaremos un código a tu correo registrado para crear una nueva contraseña</p>
        
        <?php if (!empty($error)): ?>
            <div class="alerta-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($exito)): ?>
            <div class="alerta-exito">
                <?php echo htmlspecialchars($exito); ?>
            </div>
        <?php endif; ?>
        
        <div class="recuperar-pasos">
            <!-- PASO 1: Solicitar código -->
            <div class="tarjeta-paso <?php echo $paso === 1 ? 'paso-activo' : 'paso-completado'; ?>">
                <div class="paso-numero">1</div>
                <h2 class="paso-titulo">Solicitar código</h2>
                
                <?php if ($paso === 1): ?>
                    <form method="POST" action="/ProyectoFerredog/public/index.php?c=auth&a=recuperar" id="formSolicitarCodigo">
                        <input type="hidden" name="accion" value="enviar_codigo">
                        
                        <div class="form-group">
                            <label for="correo" class="form-label">Correo electrónico registrado</label>
                            <input 
                                type="email" 
                                id="correo" 
                                name="correo" 
                                class="form-control"
                                value="<?php echo isset($_SESSION['recuperacion_correo']) ? htmlspecialchars($_SESSION['recuperacion_correo']) : (isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''); ?>"
                                required
                            >
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Enviar código</button>
                        
                        <div class="text-center mt-3">
                            <button type="submit" name="accion" value="reenviar_codigo" class="link-button">
                                ¿No te llegó? Reenviar código
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="paso-completado-texto">✓ Código enviado a tu correo</p>
                <?php endif; ?>
            </div>
            
            <!-- PASO 2: Crear nueva contraseña -->
            <div class="tarjeta-paso <?php echo $paso === 2 ? 'paso-activo' : 'paso-deshabilitado'; ?>">
                <div class="paso-numero">2</div>
                <h2 class="paso-titulo">Crear nueva contraseña</h2>
                
                <?php if ($paso === 2): ?>
                    <form method="POST" action="/ProyectoFerredog/public/index.php?c=auth&a=recuperar" id="formCambiarContrasena">
                        <input type="hidden" name="accion" value="cambiar_contrasena">
                        
                        <div class="form-group">
                            <label for="codigo" class="form-label">Código de 6 dígitos</label>
                            <input 
                                type="text" 
                                id="codigo" 
                                name="codigo" 
                                class="form-control"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                placeholder="000000"
                                required
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="nueva_contrasena" class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    id="nueva_contrasena" 
                                    name="nueva_contrasena" 
                                    class="form-control"
                                    required
                                >
                                <span class="input-icon" id="toggleNueva">👁️</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmar_contrasena" class="form-label">Confirmar contraseña</label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    id="confirmar_contrasena" 
                                    name="confirmar_contrasena" 
                                    class="form-control"
                                    required
                                >
                                <span class="input-icon" id="toggleConfirmar">👁️</span>
                            </div>
                        </div>
                        
                        <!-- Requisitos de contraseña -->
                        <div class="requisitos-contrasena">
                            <p class="requisitos-titulo">Requisitos de la contraseña:</p>
                            <ul id="requisitos-lista">
                                <li id="req-longitud" class="requisito-no-cumplido">✗ Mínimo 8 caracteres</li>
                                <li id="req-numero" class="requisito-no-cumplido">✗ Un número</li>
                                <li id="req-mayuscula" class="requisito-no-cumplido">✗ Mayúsculas y minúsculas</li>
                                <li id="req-simbolo" class="requisito-no-cumplido">✗ Un símbolo (!@#$%)</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Guardar contraseña</button>
                    </form>
                <?php else: ?>
                    <p class="paso-deshabilitado-texto">Primero solicita el código en el paso 1</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="/ProyectoFerredog/public/assets/js/recuperar.js" defer></script>
</body>
</html>

