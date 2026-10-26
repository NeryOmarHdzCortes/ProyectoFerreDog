        <!-- Menú lateral -->
        <aside class="sidebar">
            <div class="sidebar-titulo">MENÚ · <?php echo strtoupper(htmlspecialchars($_SESSION['Rol'])); ?></div>
            
            <nav class="sidebar-menu">
                <?php
                $paginaActual = $_GET['c'] ?? 'inicio';
                $rol = $_SESSION['Rol'];
                
                // Función para marcar opción activa
                function menuActivo($pagina, $actual) {
                    return $pagina === $actual ? 'menu-item-activo' : '';
                }
                ?>
                
                <!-- Inicio - todos los roles -->
                <a href="/ProyectoFerredog/public/index.php?c=inicio&a=index" 
                   class="menu-item <?php echo menuActivo('inicio', $paginaActual); ?>">
                    <span class="menu-icono">🏠</span>
                    <span>Inicio</span>
                </a>
                
                <?php if ($rol === 'Administrador'): ?>
                    <!-- Menú Administrador -->
                    <a href="/ProyectoFerredog/public/index.php?c=usuario&a=lista" 
                       class="menu-item <?php echo menuActivo('usuario', $paginaActual); ?>">
                        <span class="menu-icono">👥</span>
                        <span>Usuarios</span>
                    </a>
                    
                    <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" 
                       class="menu-item <?php echo menuActivo('producto', $paginaActual); ?>">
                        <span class="menu-icono">📦</span>
                        <span>Productos</span>
                    </a>
                    
                    <a href="/ProyectoFerredog/public/index.php?c=proveedor&a=lista" 
                       class="menu-item <?php echo menuActivo('proveedor', $paginaActual); ?>">
                        <span class="menu-icono">🏢</span>
                        <span>Proveedores</span>
                    </a>
                    
                    <a href="/ProyectoFerredog/public/index.php?c=pedido&a=lista"
                       class="menu-item <?php echo menuActivo('pedido', $paginaActual); ?>">
                        <span class="menu-icono">📋</span>
                        <span>Pedidos</span>
                    </a>
                    
                    <div class="menu-item menu-item-deshabilitado">
                        <span class="menu-icono">💰</span>
                        <span>Ventas <small class="badge-sprint">Sprint 3</small></span>
                    </div>
                    
                <?php elseif ($rol === 'Almacenista'): ?>
                    <!-- Menú Almacenista -->
                    <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" 
                       class="menu-item <?php echo menuActivo('producto', $paginaActual); ?>">
                        <span class="menu-icono">📦</span>
                        <span>Productos</span>
                    </a>
                    
                    <a href="/ProyectoFerredog/public/index.php?c=entrada&a=index"
                       class="menu-item <?php echo menuActivo('entrada', $paginaActual); ?>">
                        <span class="menu-icono">📥</span>
                        <span>Entradas</span>
                    </a>
                    
                    <div class="menu-item menu-item-deshabilitado">
                        <span class="menu-icono">⚠️</span>
                        <span>Alertas de stock <small class="badge-sprint">Sprint 3</small></span>
                    </div>
                    
                <?php elseif ($rol === 'Vendedor'): ?>
                    <!-- Menú Vendedor -->
                    <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" 
                       class="menu-item <?php echo menuActivo('producto', $paginaActual); ?>">
                        <span class="menu-icono">📦</span>
                        <span>Productos <small>(solo consulta)</small></span>
                    </a>
                    
                    <div class="menu-item menu-item-deshabilitado">
                        <span class="menu-icono">💰</span>
                        <span>Ventas <small class="badge-sprint">Sprint 3</small></span>
                    </div>
                <?php endif; ?>
            </nav>
        </aside>
