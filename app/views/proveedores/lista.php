<main class="contenido-principal">
    <div class="encabezado-pagina">
        <div>
            <h1 class="titulo-pagina"><?php echo $verEliminados ? 'Proveedores eliminados' : 'Proveedores'; ?></h1>
            <p class="subtitulo-pagina"><?php echo $verEliminados ? 'Proveedores que puedes restaurar' : 'Registra y consulta a tus proveedores; solo los activos se usan en productos y pedidos'; ?></p>
        </div>
        <?php if (!$verEliminados): ?>
            <a href="/ProyectoFerredog/public/index.php?c=proveedor&a=crear" class="btn btn-success">
                + Agregar proveedor
            </a>
        <?php endif; ?>
    </div>

    <nav class="mb-3" aria-label="Vistas de proveedores">
        <a href="/ProyectoFerredog/public/index.php?c=proveedor&a=lista"
           class="btn btn-sm <?php echo $verEliminados ? 'btn-outline-secondary' : 'btn-primary'; ?>">
            Proveedores
        </a>
        <a href="/ProyectoFerredog/public/index.php?c=proveedor&a=lista&eliminados=1"
           class="btn btn-sm <?php echo $verEliminados ? 'btn-primary' : 'btn-outline-secondary'; ?>">
            Eliminados
        </a>
    </nav>

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
            <?php if ($verEliminados): ?>
                <input type="hidden" name="eliminados" value="1">
            <?php endif; ?>
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
                                        <?php if ($verEliminados): ?>
                                            <form method="POST"
                                                  action="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=restaurar"
                                                  class="d-inline">
                                                <input type="hidden" name="id_proveedor" value="<?php echo (int) $proveedor['Id_Proveedor']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Restaurar</button>
                                            </form>
                                        <?php else: ?>
                                            <a
                                                href="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=ver&amp;id=<?php echo (int) $proveedor['Id_Proveedor']; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >Ver</a>
                                            <a
                                                href="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=editar&amp;id=<?php echo (int) $proveedor['Id_Proveedor']; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >Editar</a>
                                            <?php
                                            $confirmacionEliminar = "¿Eliminar '" . $proveedor['Nombre']
                                                . "'? Dejará de aparecer en el sistema. Podrás restaurarlo desde 'Eliminados'.";
                                            ?>
                                            <form method="POST"
                                                  action="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=desactivar"
                                                  data-confirmar-envio
                                                  data-mensaje-confirmacion="<?php echo htmlspecialchars($confirmacionEliminar, ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="id_proveedor" value="<?php echo (int) $proveedor['Id_Proveedor']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
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
        ?>
        <div class="tabla-pie">
            Mostrando <?php echo count($proveedores); ?> de <?php echo (int) $totalProveedoresVista; ?>
            <?php echo $verEliminados ? 'proveedores eliminados' : 'proveedores activos'; ?>
        </div>
    </section>
</main>
