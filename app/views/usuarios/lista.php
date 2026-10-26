        <!-- Contenido principal -->
        <main class="contenido-principal">
            <div class="encabezado-pagina">
                <div>
                    <h1 class="titulo-pagina">Usuarios</h1>
                    <p class="subtitulo-pagina">Controla quién accede al sistema y con qué rol</p>
                </div>
                <a href="/ProyectoFerredog/public/index.php?c=usuario&a=crear" class="btn btn-primary btn-agregar-usuario">
                    + Agregar usuario
                </a>
            </div>
            
            <?php if (isset($_SESSION['mensaje_exito'])): ?>
                <div class="alerta-exito">
                    <?php echo htmlspecialchars($_SESSION['mensaje_exito']); unset($_SESSION['mensaje_exito']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['mensaje_error'])): ?>
                <div class="alerta-error">
                    <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="seccion-card">
                <!-- Buscador y filtros -->
                <form method="GET" action="/ProyectoFerredog/public/index.php" class="filtros-form">
                    <input type="hidden" name="c" value="usuario">
                    <input type="hidden" name="a" value="lista">
                    
                    <div class="filtros-grid">
                        <div class="filtro-item">
                            <input 
                                type="text" 
                                name="busqueda" 
                                class="form-control" 
                                placeholder="Buscar usuario por nombre o usuario"
                                value="<?php echo htmlspecialchars($busqueda); ?>"
                            >
                        </div>
                        
                        <div class="filtro-item">
                            <select name="rol" class="form-control">
                                <option value="">Rol: Todos</option>
                                <option value="Administrador" <?php echo $filtroRol === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                                <option value="Almacenista" <?php echo $filtroRol === 'Almacenista' ? 'selected' : ''; ?>>Almacenista</option>
                                <option value="Vendedor" <?php echo $filtroRol === 'Vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                            </select>
                        </div>
                        
                        <div class="filtro-item">
                            <button type="submit" class="btn btn-secondary">Buscar</button>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla de usuarios -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NOMBRE</th>
                                <th>USUARIO</th>
                                <th>CORREO</th>
                                <th>ROL</th>
                                <th>ESTADO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron usuarios</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($usuario['Nombre'] . ' ' . $usuario['Apellidos']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['Usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['Correo']); ?></td>
                                        <td>
                                            <?php
                                            $claseBadge = 'badge-azul';
                                            if ($usuario['Rol'] === 'Almacenista') $claseBadge = 'badge-amarillo';
                                            if ($usuario['Rol'] === 'Vendedor') $claseBadge = 'badge-verde';
                                            ?>
                                            <span class="badge <?php echo $claseBadge; ?>">
                                                <?php echo htmlspecialchars($usuario['Rol']); ?>
                                            </span>
                                        </td>
                                        <td>Activo</td>
                                        <td>
                                            <a href="/ProyectoFerredog/public/index.php?c=usuario&a=editar&id=<?php echo $usuario['Id_Usuario']; ?>" 
                                               class="btn btn-sm btn-primary">
                                                Editar
                                            </a>
                                            <?php if (($_SESSION['Rol'] ?? '') === 'Administrador'
                                                && (int) $usuario['Id_Usuario'] !== (int) ($_SESSION['Id_Usuario'] ?? 0)): ?>
                                                <?php
                                                $nombreCompleto = $usuario['Nombre'] . ' ' . $usuario['Apellidos'];
                                                $confirmacion = "Se borrarán definitivamente los datos personales de '"
                                                    . $nombreCompleto
                                                    . "'. Esta acción no se puede deshacer. ¿Continuar?";
                                                ?>
                                                <form method="POST"
                                                      action="/ProyectoFerredog/public/index.php?c=usuario&a=eliminar"
                                                      class="form-eliminar-usuario d-inline"
                                                      data-confirmacion="<?php echo htmlspecialchars($confirmacion, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="id_usuario" value="<?php echo (int) $usuario['Id_Usuario']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pie de tabla -->
                <div class="tabla-pie">
                    Mostrando <?php echo count($usuarios); ?> de <?php echo count($usuarios); ?> usuarios
                </div>
            </div>
        </main>
        
