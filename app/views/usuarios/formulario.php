        <!-- Contenido principal -->
        <main class="contenido-principal">
            <div class="ruta-navegacion">
                <a href="/ProyectoFerredog/public/index.php?c=usuario&a=lista">Usuarios</a>
                <span>/</span>
                <span><?php echo $accion === 'crear' ? 'Agregar usuario' : 'Editar usuario'; ?></span>
            </div>
            
            <h1 class="titulo-pagina"><?php echo $accion === 'crear' ? 'Agregar usuario' : 'Editar usuario'; ?></h1>
            
            <?php
            // Recuperar datos del formulario si hubo error
            $datos = $_SESSION['datos_formulario'] ?? [];
            unset($_SESSION['datos_formulario']);
            
            if ($accion === 'editar' && empty($datos)) {
                $datos = $usuario;
            }
            ?>
            
            <?php if (isset($_SESSION['mensaje_error'])): ?>
                <div class="alerta-error">
                    <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/ProyectoFerredog/public/index.php?c=usuario&a=guardar" id="formUsuario">
                <?php if ($accion === 'editar'): ?>
                    <input type="hidden" name="id_usuario" value="<?php echo $usuario['Id_Usuario']; ?>">
                <?php else: ?>
                    <input type="hidden" name="id_usuario" value="0">
                <?php endif; ?>
                
                <div class="seccion-card">
                    <h2 class="seccion-titulo">Datos del usuario</h2>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input 
                                    type="text" 
                                    id="nombre" 
                                    name="nombre" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($datos['Nombre'] ?? ''); ?>"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellidos" class="form-label">Apellidos *</label>
                                <input 
                                    type="text" 
                                    id="apellidos" 
                                    name="apellidos" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($datos['Apellidos'] ?? ''); ?>"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_usuario" class="form-label">Nombre de usuario *</label>
                                <input 
                                    type="text" 
                                    id="nombre_usuario" 
                                    name="nombre_usuario" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($datos['Usuario'] ?? ''); ?>"
                                    <?php echo $accion === 'editar' ? 'readonly' : ''; ?>
                                    required
                                >
                                <?php if ($accion === 'editar'): ?>
                                    <small class="form-text">El nombre de usuario no se puede cambiar</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correo" class="form-label">Correo electrónico *</label>
                                <input 
                                    type="email" 
                                    id="correo" 
                                    name="correo" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($datos['Correo'] ?? ''); ?>"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rol" class="form-label">Rol *</label>
                                <select id="rol" name="rol" class="form-control" required>
                                    <option value="">Selecciona un rol</option>
                                    <option value="Administrador" <?php echo ($datos['Rol'] ?? '') === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="Almacenista" <?php echo ($datos['Rol'] ?? '') === 'Almacenista' ? 'selected' : ''; ?>>Almacenista</option>
                                    <option value="Vendedor" <?php echo ($datos['Rol'] ?? '') === 'Vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($accion === 'crear'): ?>
                    <div class="seccion-card mt-4">
                        <h2 class="seccion-titulo">Acceso</h2>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contrasena" class="form-label">Contraseña inicial *</label>
                                    <div class="input-group">
                                        <input 
                                            type="password" 
                                            id="contrasena" 
                                            name="contrasena" 
                                            class="form-control"
                                            required
                                        >
                                        <span class="input-icon" id="toggleContrasena">👁️</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirmar_contrasena" class="form-label">Confirmar contraseña *</label>
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
                            </div>
                        </div>
                        
                        <!-- Requisitos de contraseña -->
                        <div class="requisitos-contrasena mt-3">
                            <p class="requisitos-titulo">Requisitos de la contraseña:</p>
                            <ul id="requisitos-lista">
                                <li id="req-longitud" class="requisito-no-cumplido">✗ Mínimo 8 caracteres</li>
                                <li id="req-numero" class="requisito-no-cumplido">✗ Un número</li>
                                <li id="req-mayuscula" class="requisito-no-cumplido">✗ Mayúsculas y minúsculas</li>
                                <li id="req-simbolo" class="requisito-no-cumplido">✗ Un símbolo (!@#$%)</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="seccion-card mt-4">
                    <div class="form-group">
                        <label class="switch-label">
                            <span class="form-label">Estado</span>
                            <label class="switch">
                                <input 
                                    type="checkbox" 
                                    name="estado" 
                                    value="Activo"
                                    <?php echo ($datos['Estado'] ?? 'Activo') === 'Activo' ? 'checked' : ''; ?>
                                >
                                <span class="slider"></span>
                            </label>
                            <span class="switch-texto">Activo</span>
                        </label>
                    </div>
                </div>
                
                <div class="acciones-form">
                    <a href="/ProyectoFerredog/public/index.php?c=usuario&a=lista" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </main>

