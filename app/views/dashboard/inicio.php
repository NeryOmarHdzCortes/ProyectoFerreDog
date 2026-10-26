        <!-- Contenido principal -->
        <main class="contenido-principal">
            <h1 class="titulo-pagina">Bienvenido(a), <?php echo htmlspecialchars($nombre); ?></h1>
            <p class="subtitulo-pagina">Pantalla de inicio · el menú lateral cambia según el rol</p>
            
            <!-- Tarjetas de conteos -->
            <div class="row g-4 mb-4">
                <?php if ($rol === 'Administrador'): ?>
                    <!-- Tarjeta: Usuarios activos -->
                    <div class="col-md-6 col-lg-3">
                        <div class="tarjeta tarjeta-azul">
                            <div class="tarjeta-contenido">
                                <div class="tarjeta-titulo">Usuarios activos</div>
                                <div class="tarjeta-numero"><?php echo $conteos['usuarios_activos']; ?></div>
                                <a href="/ProyectoFerredog/public/index.php?c=usuario&a=lista" class="tarjeta-enlace">Ver →</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta: Productos en catálogo -->
                    <div class="col-md-6 col-lg-3">
                        <div class="tarjeta tarjeta-gris">
                            <div class="tarjeta-contenido">
                                <div class="tarjeta-titulo">Productos en catálogo</div>
                                <div class="tarjeta-numero"><?php echo $conteos['productos_catalogo']; ?></div>
                                <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" class="tarjeta-enlace">Ver →</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta: Proveedores activos -->
                    <div class="col-md-6 col-lg-3">
                        <div class="tarjeta tarjeta-verde">
                            <div class="tarjeta-contenido">
                                <div class="tarjeta-titulo">Proveedores activos</div>
                                <div class="tarjeta-numero"><?php echo $conteos['proveedores_activos']; ?></div>
                                <a href="/ProyectoFerredog/public/index.php?c=proveedor&a=lista" class="tarjeta-enlace">Ver →</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta: Productos en stock mínimo -->
                    <div class="col-md-6 col-lg-3">
                        <div class="tarjeta tarjeta-rojo">
                            <div class="tarjeta-contenido">
                                <div class="tarjeta-titulo">Productos en stock mínimo</div>
                                <div class="tarjeta-numero"><?php echo $conteos['productos_stock_minimo']; ?></div>
                                <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista&stock=minimo" class="tarjeta-enlace">Ver →</a>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($rol === 'Almacenista'): ?>
                    <!-- Tarjeta: Productos en catálogo -->
                    <div class="col-md-6">
                        <div class="tarjeta tarjeta-gris">
                            <div class="tarjeta-contenido">
                                <div class="tarjeta-titulo">Productos en catálogo</div>
                                <div class="tarjeta-numero"><?php echo $conteos['productos_catalogo']; ?></div>
                                <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" class="tarjeta-enlace">Ver →</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta: Productos en stock mínimo -->
                    <div class="col-md-6">
                        <div class="tarjeta tarjeta-rojo">
                            <div class="tarjeta-contenido">
                                <div class="tarjeta-titulo">Productos en stock mínimo</div>
                                <div class="tarjeta-numero"><?php echo $conteos['productos_stock_minimo']; ?></div>
                                <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista&stock=minimo" class="tarjeta-enlace">Ver →</a>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($rol === 'Vendedor'): ?>
                    <!-- Tarjeta: Productos en catálogo -->
                    <div class="col-md-12">
                        <div class="tarjeta tarjeta-gris">
                            <div class="tarjeta-contenido">
                                <div class="tarjeta-titulo">Productos en catálogo</div>
                                <div class="tarjeta-numero"><?php echo $conteos['productos_catalogo']; ?></div>
                                <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" class="tarjeta-enlace">Ver →</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Accesos rápidos -->
            <div class="seccion-card">
                <h2 class="seccion-titulo">Accesos rápidos</h2>
                <div class="accesos-rapidos">
                    <?php if ($rol === 'Administrador'): ?>
                        <a href="/ProyectoFerredog/public/index.php?c=usuario&a=lista" class="acceso-boton acceso-azul">
                            <span class="acceso-icono">👥</span>
                            <div class="acceso-texto">
                                <div class="acceso-titulo">Gestionar usuarios</div>
                                <div class="acceso-desc">Agregar, editar, buscar y activar/desactivar</div>
                            </div>
                        </a>
                        
                        <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" class="acceso-boton acceso-azul">
                            <span class="acceso-icono">📦</span>
                            <div class="acceso-texto">
                                <div class="acceso-titulo">Gestionar productos</div>
                                <div class="acceso-desc">Catálogo, precios y stock mínimo</div>
                            </div>
                        </a>
                        
                        <a href="/ProyectoFerredog/public/index.php?c=proveedor&a=lista" class="acceso-boton acceso-verde">
                            <span class="acceso-icono">🏢</span>
                            <div class="acceso-texto">
                                <div class="acceso-titulo">Gestionar proveedores</div>
                                <div class="acceso-desc">Datos de contacto y productos que surten</div>
                            </div>
                        </a>
                        
                    <?php elseif ($rol === 'Almacenista'): ?>
                        <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" class="acceso-boton acceso-azul">
                            <span class="acceso-icono">📦</span>
                            <div class="acceso-texto">
                                <div class="acceso-titulo">Gestionar productos</div>
                                <div class="acceso-desc">Catálogo, precios y stock mínimo</div>
                            </div>
                        </a>
                        
                    <?php elseif ($rol === 'Vendedor'): ?>
                        <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" class="acceso-boton acceso-azul">
                            <span class="acceso-icono">📦</span>
                            <div class="acceso-texto">
                                <div class="acceso-titulo">Consultar productos</div>
                                <div class="acceso-desc">Ver catálogo y existencias (solo consulta)</div>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </main>

