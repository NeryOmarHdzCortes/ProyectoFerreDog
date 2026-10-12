# Tareas del Sprint 1 — Ferredog

Las tareas siguen el Sprint Backlog (HU y número de tarea).
Reglas: cada vista debe seguir su wireframe de `docs/sprint1/wireframes/` (tabla en design.md). Hacer una tarea a la vez, decir qué archivos se crearon y cómo probarla, y esperar confirmación antes de pasar a la siguiente.

## 0. Preparación (base para todas las HU)
- [ ] 0.1 Comparar la estructura existente con la de design.md y crear solo lo que falte (no recrear lo que ya existe; no modificar `index.html` de la raíz). Eliminar `views/login/nueva_contrasena.php` y `views/proveedores/productos_surte.php` si existen y están vacíos (su contenido está ahora en `recuperar.php` y `proveedores/formulario.php`)
- [ ] 0.2 Crear `.env` (BD `ferredog`; `MAIL_MODO=prueba` y las variables MAIL_HOST, MAIL_PORT, MAIL_USER, MAIL_PASS, MAIL_FROM_NAME vacías), `.env.example` con las mismas variables sin valores, `.gitignore` (excluir `.env` y `storage/logs/*.log`) y `config/database.php` que lea `.env` sin composer y abra la conexión PDO
- [ ] 0.3 Crear `public/index.php` (enrutador) y `includes/auth.php` (sesión, rol, inactividad)
- [ ] 0.4 Crear el layout (`header.php`, `sidebar.php` con menú por rol, `footer.php`) y `estilos.css` siguiendo el wireframe `inicio.png` y la sección "Estilo visual" de design.md

## HU-1 — Login
- [ ] HU-1 T1 Crear las vistas `login.php` (W-01, `login.png`) y `recuperar.php` (W-02, `recuperar.png`), con `login.js` y `recuperar.js` (Req. 1 y 3)
- [ ] HU-1 T2 *(Enc. Base de Datos — no la hace el asistente)* Verificar que `usuario` tenga Intentos_Fallidos, Fecha_Bloqueo, Codigo_Recuperacion y Codigo_Expiracion. Si no existen, detenerse y avisar
- [ ] HU-1 T3 Crear `Usuario.php` (métodos de login) y `AuthController.php`: validación con `password_verify()`, menú por rol, bloqueo tras 5 intentos por 15 min, cerrar sesión, cierre por 30 min de inactividad, e `InicioController.php` con `dashboard/inicio.php` (W-03, `inicio.png`) con los conteos según el rol (Req. 1, 2 y 7)
- [ ] HU-1 T4 Recuperación de contraseña: generar código, enviarlo por medio de `app/libraries/Correo.php`, validar código y nueva contraseña, reenviar código (Req. 3). Programar los dos modos de `Correo.php` como dice design.md, pero **probar solo en modo `prueba`** (el código se lee en `storage/logs/correos.log`). La cuenta de Gmail y PHPMailer los configura el equipo al final; no descargar PHPMailer ni pedir credenciales

## HU-4 — Gestionar usuarios
- [ ] HU-4 T1 Crear las vistas `usuarios/lista.php` (W-04) y `usuarios/formulario.php` (W-05), y `usuarios.js` con la confirmación de desactivar y los requisitos de contraseña en vivo (Req. 4)
- [ ] HU-4 T2 Crear `UsuarioController.php` y completar `Usuario.php`: registro (transacción empleados + usuario), actualización, búsqueda y activación/desactivación, con validación de usuario o correo duplicado y de contraseña segura (Req. 4)

## HU-5 — Gestionar catálogo de productos
- [ ] HU-5 T1 Crear las vistas `productos/lista.php` (W-06) y `productos/formulario.php` (W-07), y `productos.js` con la ventana de "Poner precio" y la confirmación de desactivar (Req. 5)
- [ ] HU-5 T2 Crear `Producto.php`, `Catalogo.php` y `ProductoController.php`: registro (transacción productos + inventario), edición y búsqueda con filtros, con validación de código duplicado (Req. 5.1 a 5.6)
- [ ] HU-5 T3 Asignación de precio de venta y desactivación de productos, solo Administrador, validado en el controlador (Req. 5.7 a 5.10)
- [ ] HU-5 T4 *(Enc. Base de Datos — no la hace el asistente)* Verificar que `productos.Precio_Venta` acepte NULL. Si no, detenerse y avisar

## HU-6 — Gestión de proveedores
- [ ] HU-6 T1 Crear las vistas `proveedores/lista.php` (W-08) y `proveedores/formulario.php` (W-09, con los modos Agregar, Ver y Editar y el panel "Productos que surte"), y `proveedores.js` (Req. 6)
- [ ] HU-6 T2 Crear `Proveedor.php` y `ProveedorController.php`: registro, edición, búsqueda, desactivación y consulta de productos que surte, con validación de nombre o correo duplicado (Req. 6)

## Técnico — Pruebas integrales
- [ ] Técnico T1 Probar manualmente cada requisito de requirements.md con los usuarios de prueba (Administrador, Vendedor, Almacenista) y entregar una tabla: requisito / resultado / observación
- [ ] Actualizar el README con: requisitos (XAMPP), configuración de `.env`, URL de acceso, usuarios de prueba, cómo probar la recuperación en modo prueba, y una sección "Activar el envío real por Gmail" con los pasos: crear la cuenta, activar verificación en 2 pasos, crear la contraseña de aplicación, copiar PHPMailer a `app/libraries/PHPMailer/`, llenar las variables MAIL_* y cambiar `MAIL_MODO=smtp`
