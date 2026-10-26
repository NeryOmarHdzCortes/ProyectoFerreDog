<main class="contenido-principal">
    <div class="encabezado-pagina">
        <div>
            <h1 class="titulo-pagina">Proveedores</h1>
            <p class="subtitulo-pagina">Registra y consulta a tus proveedores; solo los activos se usan en productos y pedidos</p>
        </div>
        <a href="/ProyectoFerredog/public/index.php?c=proveedor&a=crear" class="btn btn-success">
            + Agregar proveedor
        </a>
    </div>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alerta-exito">
            <?php echo htmlspecialchars($_SESSION['mensaje_exito'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['mensaje_exito']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alerta-error">
            <?php echo htmlspecialchars($_SESSION['mensaje_error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['mensaje_error']); ?>
        </div>
    <?php endif; ?>

    <section class="seccion-card">
        <form method="GET" action="/ProyectoFerredog/public/index.php" class="filtros-form">
            <input type="hidden" name="c" value="proveedor">
            <input type="hidden" name="a" value="lista">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="visually-hidden" for="busquedaProveedor">Buscar proveedor por nombre o contacto</label>
                    <div class="input-group">
                        <span class="input-group-text" aria-hidden="true">⌕</span>
                        <input
                            type="search"
                            id="busquedaProveedor"
                            name="busqueda"
                            class="form-control"
                            placeholder="Buscar proveedor por nombre o contacto"
                            value="<?php echo htmlspecialchars($busqueda ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="visually-hidden" for="filtroEstadoProveedor">Filtrar por estado</label>
                    <select id="filtroEstadoProveedor" name="estado" class="form-control" data-enviar-al-cambiar>
                        <option value="" <?php echo ($filtroEstado ?? '') === '' ? 'selected' : ''; ?>>Estado: Todos</option>
                        <option value="Activo" <?php echo ($filtroEstado ?? '') === 'Activo' ? 'selected' : ''; ?>>Estado: Activos</option>
                        <option value="Inactivo" <?php echo ($filtroEstado ?? '') === 'Inactivo' ? 'selected' : ''; ?>>Estado: Inactivos</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>PROVEEDOR</th>
                        <th>TIPO</th>
                        <th>CONTACTO</th>
                        <th>TELÉFONO</th>
                        <th>CORREO</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proveedores)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron proveedores</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($proveedor['Nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($proveedor['Tipo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($proveedor['Contacto'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($proveedor['Telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($proveedor['Correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php $claseEstado = ($proveedor['Estado'] ?? '') === 'Activo' ? 'badge-verde' : 'badge-gris'; ?>
                                    <span class="badge <?php echo $claseEstado; ?>">
                                        <?php echo htmlspecialchars($proveedor['Estado'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a
                                            href="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=ver&amp;id=<?php echo (int) $proveedor['Id_Proveedor']; ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >Ver</a>
                                        <a
                                            href="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=editar&amp;id=<?php echo (int) $proveedor['Id_Proveedor']; ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >Editar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        $totalProveedoresVista = $totalProveedores ?? count($proveedores);
        $proveedoresActivosVista = $proveedoresActivos ?? count(array_filter(
            $proveedores,
            function ($proveedor) {
                return ($proveedor['Estado'] ?? '') === 'Activo';
            }
        ));
        ?>
        <div class="tabla-pie">
            Mostrando <?php echo count($proveedores); ?> de <?php echo (int) $totalProveedoresVista; ?> proveedores ·
            <?php echo (int) $proveedoresActivosVista; ?> activos
        </div>
    </section>
</main>
