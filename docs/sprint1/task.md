# Tareas del Sprint 1 — Ferredog

Las tareas siguen el Sprint Backlog (HU y número de tarea).
Reglas: cada vista debe seguir su wireframe de `docs/sprint1/wireframes/` (tabla en design.md). Hacer una tarea a la vez, decir qué archivos se crearon y cómo probarla, y esperar confirmación antes de pasar a la siguiente.

## 0. Preparación (base para todas las HU)
- [x] 0.1 Estructura del proyecto
- [x] 0.2 `.env`, `.env.example`, `.gitignore` y `config/database.php`
- [x] 0.3 `public/index.php` (enrutador) e `includes/auth.php`
- [x] 0.4 Layout (`header.php`, `sidebar.php`, `footer.php`) y `estilos.css`

## HU-1 — Login
- [x] HU-1 T1 Vistas `login.php` (W-01) y `recuperar.php` (W-02), con `login.js` y `recuperar.js`
- [x] HU-1 T2 (Enc. Base de Datos) Columnas de bloqueo y recuperación en `usuario`
- [x] HU-1 T3 Login, menú por rol, bloqueo, cerrar sesión, inactividad e inicio (W-03)
- [x] HU-1 T4 Recuperación de contraseña con `Correo.php` en modo prueba

## HU-4 — Gestionar usuarios
- [x] HU-4 T1 Vistas `usuarios/lista.php` (W-04) y `usuarios/formulario.php` (W-05), y `usuarios.js`
- [x] HU-4 T2 `UsuarioController.php` y `Usuario.php`: registro, actualización, búsqueda y activación/desactivación

## HU-5 — Gestionar catálogo de productos
- [x] HU-5 T1 Vistas `productos/lista.php` (W-06) y `productos/formulario.php` (W-07), y `productos.js`
- [x] HU-5 T2 En `ProductoController.php` y `Producto.php`: acción `guardar` para registrar (transacción productos + inventario) y editar, con validación de campos obligatorios y de código duplicado; mensajes de éxito/error como en usuarios; paginación de 10 en 10 en la lista con el pie "Mostrando N de N productos · N en stock mínimo" (Req. 5.1 a 5.6)
- [ ] HU-5 T3 Acciones `ponerPrecio` (precio mayor a 0) y `desactivar` (Estado = 0), solo Administrador, validado en el controlador; el Vendedor solo consulta (Req. 5.7 a 5.10)
- [x] HU-5 T4 (Enc. Base de Datos) `productos.Precio_Venta` acepta NULL; tabla `marcas` y `productos.Id_Marca` creadas

## HU-6 — Gestión de proveedores
- [ ] HU-6 T1 Crear las vistas `proveedores/lista.php` (W-08) y `proveedores/formulario.php` (W-09, con los modos Agregar, Ver y Editar y el panel "Productos que surte"), y `proveedores.js` (Req. 6)
- [ ] HU-6 T2 Crear `ProveedorController.php` y completar `Proveedor.php`: registro, edición, búsqueda, desactivación y consulta de productos que surte, con validación de nombre o correo duplicado (Req. 6)

## Ajustes visuales
- [ ] AV-1 `sidebar.php`: íconos como en `inicio.png` (cuadros, no emojis). `usuarios/lista.php`: botón "+ Agregar usuario" a la derecha con su tamaño normal, como en `usuarios_lista.png`

## Técnico — Pruebas integrales
- [ ] Técnico T1 Probar cada requisito de requirements.md con los 3 roles y entregar una tabla: requisito / resultado / observación
- [ ] Actualizar el README con: requisitos (XAMPP con Apache; MariaDB 12 aparte), configuración de `.env`, URL `http://localhost/ProyectoFerredog/public/`, usuarios de prueba, cómo probar la recuperación en modo prueba, y una sección "Activar el envío real por Gmail" (crear la cuenta, verificación en 2 pasos, contraseña de aplicación, copiar PHPMailer a `app/libraries/PHPMailer/`, llenar MAIL_* y cambiar `MAIL_MODO=smtp`)dd