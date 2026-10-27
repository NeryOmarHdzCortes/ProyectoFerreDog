<?php
$datosProducto = $_SESSION['datos_producto'] ?? [];
unset($_SESSION['datos_producto']);
?>
<!-- Contenido principal -->
        <main class="contenido-principal">
            <!-- Ruta de navegación -->
            <div class="ruta-navegacion">
                <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista">Productos</a>
                <span> / </span>
                <span><?php echo $esEdicion ? 'Editar producto' : 'Agregar producto'; ?></span>
            </div>
            
            <h1 class="titulo-pagina"><?php echo $esEdicion ? 'Editar producto' : 'Agregar producto'; ?></h1>
            
            <?php if (isset($_SESSION['errores'])): ?>
                <div class="alerta-error">
                    <ul>
                        <?php foreach ($_SESSION['errores'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errores']); ?>
            <?php endif; ?>
            
            <form method="POST" 
                  action="/ProyectoFerredog/public/index.php?c=producto&a=guardar" 
                  class="formulario-producto">
                
                <?php if ($esEdicion): ?>
                    <input type="hidden" name="id_producto" value="<?php echo $producto['Id_Producto']; ?>">
                <?php endif; ?>
                
                <div class="seccion-card">
                    <h2 class="subtitulo-seccion">Datos del producto</h2>
                    
                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label for="codigo">Código *</label>
                            <input type="text"
                                   id="codigo" 
                                   name="codigo" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($datosProducto['codigo'] ?? ($producto['Codigo_Barras'] ?? '')); ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nombre">Nombre del producto *</label>
                            <input type="text"
                                   id="nombre" 
                                   name="nombre" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($datosProducto['nombre'] ?? ($producto['Nombre'] ?? '')); ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label for="marca">Marca *</label>
                            <select id="marca" name="marca" class="form-control" required>
                                <option value="">Selecciona una marca</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?php echo $marca['Id_Marca']; ?>"
                                            <?php 
                                            $seleccionado = ($datosProducto['marca'] ?? ($producto['Id_Marca'] ?? '')) == $marca['Id_Marca'];
                                            echo $seleccionado ? 'selected' : ''; 
                                            ?>>
                                        <?php echo htmlspecialchars($marca['Nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="categoria">Categoría *</label>
                            <select id="categoria" name="categoria" class="form-control" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['Id_Categoria']; ?>"
                                            <?php 
                                            $seleccionado = ($datosProducto['categoria'] ?? ($producto['Id_Categoria'] ?? '')) == $cat['Id_Categoria'];
                                            echo $seleccionado ? 'selected' : ''; 
                                            ?>>
                                        <?php echo htmlspecialchars($cat['Nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label for="proveedor">Proveedor * (solo activos)</label>
                            <select id="proveedor" name="proveedor" class="form-control" required>
                                <option value="">Selecciona un proveedor</option>
                                <?php foreach ($proveedores as $prov): ?>
                                    <option value="<?php echo $prov['Id_Proveedor']; ?>"
                                            <?php 
                                            $seleccionado = ($datosProducto['proveedor'] ?? ($producto['Id_Proveedor'] ?? '')) == $prov['Id_Proveedor'];
                                            echo $seleccionado ? 'selected' : ''; 
                                            ?>>
                                        <?php echo htmlspecialchars($prov['Nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="unidad">Unidad *</label>
                            <select id="unidad" name="unidad" class="form-control" required>
                                <option value="">Selecciona una unidad</option>
                                <?php foreach ($unidades as $unidad): ?>
                                    <option value="<?php echo $unidad['Id_Unidad']; ?>"
                                            <?php 
                                            $seleccionado = ($datosProducto['unidad'] ?? ($producto['Id_Unidad'] ?? '')) == $unidad['Id_Unidad'];
                                            echo $seleccionado ? 'selected' : ''; 
                                            ?>>
                                        <?php echo htmlspecialchars($unidad['Nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="seccion-card">
                    <h2 class="subtitulo-seccion">Precio e inventario</h2>
                    
                    <div class="form-grid-3col">
                        <div class="form-group">
                            <label for="precio_venta">Precio de venta</label>
                            <input type="text"
                                   id="precio_venta"
                                   name="precio_venta"
                                   class="form-control"
                                   data-solo-decimales
                                   step="0.01"
                                   min="0"
                                   value="<?php echo htmlspecialchars($datosProducto['precio_venta'] ?? ($producto['Precio_Venta'] ?? '')); ?>">
                        </div>

                        <div class="form-group">
                            <label for="precio_compra">Precio de compra</label>
                            <?php if ($_SESSION['Rol'] === 'Administrador'): ?>
                                <input type="text"
                                       id="precio_compra"
                                       name="precio_compra"
                                       class="form-control"
                                       data-solo-decimales
                                       step="0.01"
                                       min="0"
                                       value="<?php echo htmlspecialchars($datosProducto['precio_compra'] ?? ($producto['Precio_Compra'] ?? '')); ?>"
                                       aria-describedby="precio_compra_error">
                                <small id="precio_compra_error" class="form-text text-danger" role="alert" hidden></small>
                            <?php else: ?>
                                <input type="text"
                                       id="precio_compra"
                                       class="form-control"
                                       data-solo-decimales
                                       value="<?php echo htmlspecialchars($producto['Precio_Compra'] ?? ''); ?>"
                                       disabled>
                                <small class="form-text text-muted">Lo asigna el Administrador</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_inicial">Stock inicial *</label>
                            <?php if ($esEdicion): ?>
                                <input type="text"
                                       id="stock_inicial" 
                                       class="form-control" 
                                       data-solo-enteros
                                       value="<?php echo $producto['Existencias'] ?? 0; ?>"
                                       disabled>
                            <?php else: ?>
                                <input type="text"
                                       id="stock_inicial" 
                                       name="stock_inicial" 
                                       class="form-control" 
                                       data-solo-enteros
                                       min="0"
                                       value="<?php echo htmlspecialchars($datosProducto['stock_inicial'] ?? ''); ?>"
                                       required>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_minimo">Stock mínimo *</label>
                            <input type="text"
                                   id="stock_minimo" 
                                   name="stock_minimo" 
                                   class="form-control" 
                                   data-solo-enteros
                                   min="1"
                                   step="1"
                                   value="<?php echo htmlspecialchars($datosProducto['stock_minimo'] ?? ($producto['Stock_Minimo'] ?? '')); ?>"
                                   required
                                   title="El stock mínimo debe ser un número entero mayor a 0.">
                            <small id="stock_minimo_error" class="form-text text-danger" role="alert" hidden></small>
                            <small class="form-text text-muted">Si la existencia es igual o menor, el producto se marca en rojo</small>
                        </div>

                        <div class="form-group">
                            <label for="stock_maximo">Stock máximo *</label>
                            <input type="text"
                                   id="stock_maximo"
                                   name="stock_maximo"
                                   class="form-control"
                                   data-solo-enteros
                                   min="1"
                                   step="1"
                                   value="<?php echo htmlspecialchars($datosProducto['stock_maximo'] ?? ($producto['Stock_Maximo'] ?? '')); ?>"
                                   required
                                   title="El stock máximo debe ser un número entero mayor o igual al stock mínimo.">
                            <small id="stock_maximo_error" class="form-text text-danger" role="alert" hidden></small>
                        </div>

                        <?php if (!$esEdicion): ?>
                            <small id="stock_inicial_error" class="form-text text-danger" role="alert" hidden></small>
                        <?php endif; ?>
                    </div>
                    <div id="avisoPrecioMenorCosto" class="alert alert-warning py-2 mt-3 mb-0" role="status" hidden>
                        El precio de venta es menor que el costo
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="/ProyectoFerredog/public/index.php?c=producto&a=lista" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </main>
