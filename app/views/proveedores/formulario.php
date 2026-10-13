<?php
$modoFormulario = $modoFormulario ?? 'agregar';
$esAgregar = $modoFormulario === 'agregar';
$esVista = $modoFormulario === 'ver';
$esEdicion = $modoFormulario === 'editar';
$soloLectura = $esVista;
$datosFormulario = $_SESSION['datos_proveedor'] ?? [];
unset($_SESSION['datos_proveedor']);

$nombreProveedor = $datosFormulario['nombre'] ?? ($proveedor['Nombre'] ?? '');
$tipoProveedor = $datosFormulario['tipo'] ?? ($proveedor['Tipo'] ?? '');
$contactoProveedor = $datosFormulario['contacto'] ?? ($proveedor['Contacto'] ?? '');
$telefonoProveedor = $datosFormulario['telefono'] ?? ($proveedor['Telefono'] ?? '');
$correoProveedor = $datosFormulario['correo'] ?? ($proveedor['Correo'] ?? '');
$direccionProveedor = $datosFormulario['direccion'] ?? ($proveedor['Direccion'] ?? '');
$idProveedor = (int) ($proveedor['Id_Proveedor'] ?? 0);
$tituloFormulario = $esAgregar ? 'Agregar proveedor' : ($nombreProveedor !== '' ? $nombreProveedor : 'Proveedor');
$accionProductos = $esVista ? 'ver' : 'editar';
$productosQueSurte = $productosQueSurte ?? [];
$totalProductosQueSurte = (int) ($totalProductosQueSurte ?? count($productosQueSurte));
$busquedaProductos = $busquedaProductos ?? '';
?>
<main class="contenido-principal">
    <div class="ruta-navegacion">
        <a href="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=lista">Proveedores</a>
        <span> / </span>
        <span><?php echo htmlspecialchars($tituloFormulario, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>

    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <h1 class="titulo-pagina mb-0"><?php echo htmlspecialchars($tituloFormulario, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if (!$esAgregar): ?>
            <?php $claseEstado = ($proveedor['Estado'] ?? '') === 'Activo' ? 'badge-verde' : 'badge-gris'; ?>
            <span class="badge <?php echo $claseEstado; ?>">
                <?php echo htmlspecialchars($proveedor['Estado'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </span>
        <?php endif; ?>
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
    <?php if (isset($_SESSION['errores'])): ?>
        <div class="alerta-error">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errores'] as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errores']); ?>
    <?php endif; ?>

    <div class="row g-3 align-items-stretch">
        <div class="<?php echo $esAgregar ? 'col-12 col-xl-8' : 'col-12 col-xl-6'; ?>">
            <section class="seccion-card h-100">
                <h2 class="subtitulo-seccion">Datos del proveedor</h2>
                <?php if (!$esVista): ?>
                    <form id="formularioProveedor" method="POST" action="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=guardar">
                        <?php if ($esEdicion): ?>
                            <input type="hidden" name="id_proveedor" value="<?php echo $idProveedor; ?>">
                        <?php endif; ?>
                <?php endif; ?>

                <div class="form-group">
                    <label for="nombreProveedor">Nombre o razón social *</label>
                    <input
                        type="text"
                        id="nombreProveedor"
                        name="nombre"
                        class="form-control"
                        value="<?php echo htmlspecialchars($nombreProveedor, ENT_QUOTES, 'UTF-8'); ?>"
                        <?php echo $soloLectura ? 'disabled' : 'required'; ?>
                    >
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="tipoProveedor">Tipo *</label>
                        <select id="tipoProveedor" name="tipo" class="form-control" <?php echo $soloLectura ? 'disabled' : 'required'; ?>>
                            <option value="">Selecciona un tipo</option>
                            <?php foreach (['Distribuidor', 'Mayorista', 'Fabricante'] as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $tipoProveedor === $tipo ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contactoProveedor">Persona de contacto *</label>
                        <input
                            type="text"
                            id="contactoProveedor"
                            name="contacto"
                            class="form-control"
                            value="<?php echo htmlspecialchars($contactoProveedor, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $soloLectura ? 'disabled' : 'required'; ?>
                        >
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="telefonoProveedor">Teléfono *</label>
                        <input
                            type="tel"
                            id="telefonoProveedor"
                            name="telefono"
                            class="form-control"
                            value="<?php echo htmlspecialchars($telefonoProveedor, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $soloLectura ? 'disabled' : 'required'; ?>
                        >
                    </div>
                    <div class="form-group">
                        <label for="correoProveedor">Correo electrónico *</label>
                        <input
                            type="email"
                            id="correoProveedor"
                            name="correo"
                            class="form-control"
                            value="<?php echo htmlspecialchars($correoProveedor, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $soloLectura ? 'disabled' : 'required'; ?>
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccionProveedor">Dirección</label>
                    <textarea
                        id="direccionProveedor"
                        name="direccion"
                        class="form-control"
                        rows="2"
                        <?php echo $soloLectura ? 'disabled' : ''; ?>
                    ><?php echo htmlspecialchars($direccionProveedor, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <?php if (!$esAgregar): ?>
                    <p class="form-text mb-4">
                        Al desactivarlo ya no aparece al agregar productos ni al hacer pedidos.<br>
                        No se permiten dos proveedores con el mismo nombre o correo.
                    </p>
                <?php endif; ?>
                <?php if (!$esVista): ?>
                    </form>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
                    <div>
                        <?php if ($esEdicion && ($proveedor['Estado'] ?? '') === 'Activo'): ?>
                            <form method="POST"
                                  action="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=desactivar"
                                  data-confirmar-envio
                                  data-mensaje-confirmacion="<?php echo htmlspecialchars('¿Desactivar a ' . $nombreProveedor . '? Ya no aparecerá al agregar productos ni al hacer pedidos.', ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="id_proveedor" value="<?php echo $idProveedor; ?>">
                                <button type="submit" class="btn btn-secondary">Desactivar proveedor</button>
                            </form>
                        <?php elseif ($esVista): ?>
                            <a href="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=editar&amp;id=<?php echo $idProveedor; ?>" class="btn btn-sm btn-outline-primary">
                                Editar
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/ProyectoFerredog/public/index.php?c=proveedor&amp;a=lista" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                        <?php if (!$esVista): ?>
                            <button type="submit" form="formularioProveedor" class="btn btn-success">Guardar</button>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

        <?php if (!$esAgregar): ?>
            <div class="col-12 col-xl-6">
                <section class="seccion-card h-100">
                    <h2 class="subtitulo-seccion">
                        Productos que surte (<?php echo $totalProductosQueSurte; ?>)
                    </h2>
                    <form method="GET" action="/ProyectoFerredog/public/index.php" class="mb-3">
                        <input type="hidden" name="c" value="proveedor">
                        <input type="hidden" name="a" value="<?php echo htmlspecialchars($accionProductos, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="id" value="<?php echo $idProveedor; ?>">
                        <label class="visually-hidden" for="busquedaProductosProveedor">Buscar en sus productos</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">⌕</span>
                            <input
                                type="search"
                                id="busquedaProductosProveedor"
                                name="busqueda_productos"
                                class="form-control"
                                placeholder="Buscar en sus productos"
                                value="<?php echo htmlspecialchars($busquedaProductos, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </div>
                    </form>

                    <div class="table-responsive mt-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>CÓDIGO</th>
                                    <th>PRODUCTO</th>
                                    <th class="text-end">STOCK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($productosQueSurte)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No se encontraron productos</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($productosQueSurte, 0, 7) as $productoSurtido): ?>
                                        <?php
                                        $existencias = (int) ($productoSurtido['Existencias'] ?? 0);
                                        $stockMinimo = (int) ($productoSurtido['Stock_Minimo'] ?? 0);
                                        $claseStock = $existencias <= $stockMinimo ? 'texto-stock-minimo' : '';
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($productoSurtido['Codigo_Barras'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($productoSurtido['Nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-end <?php echo $claseStock; ?>"><?php echo $existencias; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    $urlTodosProductos = '/ProyectoFerredog/public/index.php?' . http_build_query([
                        'c' => 'producto',
                        'a' => 'lista',
                        'proveedor' => $idProveedor
                    ]);
                    ?>
                    <div class="text-center mt-3">
                        <a href="<?php echo htmlspecialchars($urlTodosProductos, ENT_QUOTES, 'UTF-8'); ?>" class="tarjeta-enlace">
                            Ver los <?php echo $totalProductosQueSurte; ?> productos →
                        </a>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </div>
</main>
