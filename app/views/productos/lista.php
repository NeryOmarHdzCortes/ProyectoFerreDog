        <!-- Contenido principal -->
        <main class="contenido-principal">
            <div class="encabezado-pagina">
                <div>
                    <h1 class="titulo-pagina">Productos</h1>
                    <p class="subtitulo-pagina">Catálogo de la ferretería · en rojo los que llegaron a su stock mínimo</p>
                </div>
                <?php if ($_SESSION['Rol'] !== 'Vendedor'): ?>
                    <a href="/ProyectoFerredog/public/index.php?c=producto&a=crear" class="btn btn-primary">
                        + Agregar producto
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (isset($_SESSION['mensaje_exito'])): ?>
                <div class="alerta-exito">
                    <?php echo htmlspecialchars($_SESSION['mensaje_exito']); unset($_SESSION['mensaje_exito']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['mensaje_error'])): ?>
                <div class="alerta-error">
                    <?php echo htmlspecialchars($_SESSION['mensaje_error']); unset($_SESSION['mensaje_error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="seccion-card">
                <!-- Buscador y filtros -->
                <form method="GET" action="/ProyectoFerredog/public/index.php" class="filtros-form">
                    <input type="hidden" name="c" value="producto">
                    <input type="hidden" name="a" value="lista">
                    
                    <div class="filtros-grid">
                        <div class="filtro-item">
                            <input 
                                type="text" 
                                name="busqueda" 
                                class="form-control" 
                                placeholder="Buscar por nombre o código"
                                value="<?php echo htmlspecialchars($busqueda); ?>"
                            >
                        </div>
                        
                        <div class="filtro-item">
                            <select name="categoria" class="form-control">
                                <option value="">Categoría: Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['Id_Categoria']; ?>" 
                                            <?php echo $filtroCategoria == $cat['Id_Categoria'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['Nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filtro-item">
                            <select name="proveedor" class="form-control">
                                <option value="">Proveedor: Todos</option>
                                <?php foreach ($proveedores as $prov): ?>
                                    <option value="<?php echo $prov['Id_Proveedor']; ?>" 
                                            <?php echo $filtroProveedor == $prov['Id_Proveedor'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prov['Nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filtro-item">
                            <select name="estado" class="form-control">
                                <option value="">Estado: Todos</option>
                                <option value="Activo" <?php echo $filtroEstado === 'Activo' ? 'selected' : ''; ?>>Activos</option>
                                <option value="Inactivo" <?php echo $filtroEstado === 'Inactivo' ? 'selected' : ''; ?>>Inactivos</option>
                            </select>
                        </div>
                        
                        <div class="filtro-item">
                            <button type="submit" class="btn btn-secondary">Buscar</button>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla de productos -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>CÓDIGO</th>
                                <th>PRODUCTO</th>
                                <th>MARCA</th>
                                <th>CATEGORÍA</th>
                                <th>PROVEEDOR</th>
                                <th>PRECIO</th>
                                <th>STOCK</th>
                                <th>MÍN.</th>
                                <?php if ($_SESSION['Rol'] !== 'Vendedor'): ?>
                                    <th>ACCIONES</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productos)): ?>
                                <tr>
                                    <td colspan="<?php echo $_SESSION['Rol'] === 'Vendedor' ? '8' : '9'; ?>" class="text-center">
                                        No se encontraron productos
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $producto): ?>
                                    <?php 
                                    // Verificar si está en stock mínimo
                                    $enStockMinimo = ($producto['Existencias'] <= $producto['Stock_Minimo']);
                                    $claseFilaStock = $enStockMinimo ? 'fila-stock-minimo' : '';
                                    ?>
                                    <tr class="<?php echo $claseFilaStock; ?>">
                                        <td><?php echo htmlspecialchars($producto['Codigo_Barras']); ?></td>
                                        <td><?php echo htmlspecialchars($producto['Nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($producto['Marca'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($producto['Categoria'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($producto['Proveedor'] ?? ''); ?></td>
                                        <td>
                                            <?php if ($producto['Precio_Venta']): ?>
                                                $<?php echo number_format($producto['Precio_Venta'], 2); ?>
                                            <?php else: ?>
                                                <span class="badge badge-amarillo">Sin precio</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="<?php echo $enStockMinimo ? 'texto-stock-minimo' : ''; ?>">
                                            <?php echo $producto['Existencias'] ?? 0; ?>
                                        </td>
                                        <td><?php echo $producto['Stock_Minimo'] ?? 0; ?></td>
                                        
                                        <?php if ($_SESSION['Rol'] !== 'Vendedor'): ?>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="/ProyectoFerredog/public/index.php?c=producto&a=editar&id=<?php echo $producto['Id_Producto']; ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        Editar
                                                    </a>
                                                    
                                                    <?php if (in_array($_SESSION['Rol'], ['Administrador', 'Almacenista'], true)): ?>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success"
                                                                onclick="abrirModalPrecio(<?php echo $producto['Id_Producto']; ?>, '<?php echo htmlspecialchars($producto['Nombre'], ENT_QUOTES); ?>', <?php echo $producto['Precio_Venta'] ?? 0; ?>)">
                                                            Poner precio
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if ($_SESSION['Rol'] === 'Administrador'): ?>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-secondary"
                                                                onclick="confirmarDesactivar(<?php echo $producto['Id_Producto']; ?>, '<?php echo htmlspecialchars($producto['Nombre'], ENT_QUOTES); ?>')">
                                                            Desactivar
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pie de tabla -->
                <div class="tabla-pie d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <span>
                        Mostrando <?php echo count($productos); ?> de <?php echo $totalProductos; ?> productos ·
                        <?php echo $productosStockMinimo; ?> en stock mínimo
                    </span>
                    <?php if ($totalPaginas > 1): ?>
                        <?php
                        $paginasVisibles = [1];
                        for ($numero = max(2, $pagina - 1); $numero <= min($totalPaginas - 1, $pagina + 2); $numero++) {
                            $paginasVisibles[] = $numero;
                        }
                        if ($totalPaginas > 1) {
                            $paginasVisibles[] = $totalPaginas;
                        }
                        ?>
                        <nav aria-label="Paginación de productos">
                            <ul class="pagination mb-0">
                                <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                                    <?php if ($pagina <= 1): ?>
                                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                    <?php else: ?>
                                        <a class="page-link" href="<?php echo htmlspecialchars('/ProyectoFerredog/public/index.php?' . http_build_query(array_merge($parametrosPaginacion, ['pagina' => $pagina - 1])), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Página anterior">&lsaquo;</a>
                                    <?php endif; ?>
                                </li>
                                <?php $paginaAnterior = 0; ?>
                                <?php foreach ($paginasVisibles as $numero): ?>
                                    <?php if ($paginaAnterior > 0 && $numero - $paginaAnterior > 1): ?>
                                        <li class="page-item disabled"><span class="page-link">…</span></li>
                                    <?php endif; ?>
                                    <li class="page-item <?php echo $numero === $pagina ? 'active' : ''; ?>">
                                        <?php if ($numero === $pagina): ?>
                                            <span class="page-link" aria-current="page"><?php echo $numero; ?></span>
                                        <?php else: ?>
                                            <a class="page-link" href="<?php echo htmlspecialchars('/ProyectoFerredog/public/index.php?' . http_build_query(array_merge($parametrosPaginacion, ['pagina' => $numero])), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $numero; ?></a>
                                        <?php endif; ?>
                                    </li>
                                    <?php $paginaAnterior = $numero; ?>
                                <?php endforeach; ?>
                                <li class="page-item <?php echo $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
                                    <?php if ($pagina >= $totalPaginas): ?>
                                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                                    <?php else: ?>
                                        <a class="page-link" href="<?php echo htmlspecialchars('/ProyectoFerredog/public/index.php?' . http_build_query(array_merge($parametrosPaginacion, ['pagina' => $pagina + 1])), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Página siguiente">&rsaquo;</a>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        
        <!-- Modal para poner precio -->
        <?php if (in_array($_SESSION['Rol'], ['Administrador', 'Almacenista'], true)): ?>
        <div id="modalPrecio" class="modal" style="display: none;">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-titulo">Poner precio</h3>
                    </div>
                    <div class="modal-body">
                        <p id="modalPrecioNombre"></p>
                        <div class="form-group">
                            <label>Precio de venta *</label>
                            <input type="number" 
                                   id="inputPrecioVenta" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0.01"
                                   required
                                   placeholder="0.00">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalPrecio()">Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="guardarPrecio()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal de confirmación para desactivar -->
        <div id="modalDesactivar" class="modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-titulo">¿Desactivar producto?</h3>
                    </div>
                    <div class="modal-body">
                        <p id="modalDesactivarMensaje"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalDesactivar()">Cancelar</button>
                        <form id="formDesactivar" method="POST" action="/ProyectoFerredog/public/index.php?c=producto&a=desactivar" style="display: inline;">
                            <input type="hidden" name="id_producto" id="inputIdProducto">
                            <button type="submit" class="btn btn-danger">Desactivar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
