# Diseño del Sprint 1 — Ferredog

## Wireframes (obligatorio seguirlos)
Las 9 pantallas de Figma están en `docs/sprint1/wireframes/`. Antes de crear cada vista, abre su imagen y respeta el acomodo, los campos, los textos, las columnas, los filtros y los colores de botones que muestra.

| Wireframe | Imagen | Vista |
|---|---|---|
| W-01 Iniciar sesión | `login.png` | `views/login/login.php` |
| W-02 Recuperar contraseña | `recuperar.png` | `views/login/recuperar.php` (los dos pasos en la misma página) |
| W-03 Inicio con menú por rol | `inicio.png` | `views/dashboard/inicio.php` + layout (`header.php`, `sidebar.php`) |
| W-04 Lista de usuarios | `usuarios_lista.png` | `views/usuarios/lista.php` |
| W-05 Formulario de usuario | `usuarios_formulario.png` | `views/usuarios/formulario.php` |
| W-06 Lista de productos | `productos_lista.png` | `views/productos/lista.php` |
| W-07 Formulario de producto | `productos_formulario.png` | `views/productos/formulario.php` |
| W-08 Lista de proveedores | `proveedores_lista.png` | `views/proveedores/lista.php` |
| W-09 Formulario de proveedor | `proveedores_formulario.png` | `views/proveedores/formulario.php` (incluye "Productos que surte") |

### Qué NO se construye de los wireframes
Son notas del diseñador, no parte de la pantalla:
- Los recuadros amarillos ("Reglas del formulario", "Permisos por rol").
- La tarjeta "Menú según el rol" de W-03.
- La tarjeta "Vista del Almacenista" de W-07 (solo explica cómo se ve el precio para ese rol).
- Los datos de ejemplo (Laura Martínez, Herramientas del Valle, 248 productos, etc.): todo sale de la base.

### Diferencias con los wireframes (decididas por la base de datos; respetar esto)
1. **W-05 "Nombre completo":** la base guarda `Nombre` y `Apellidos` por separado (ambos obligatorios). Se usan dos campos, "Nombre" y "Apellidos", en el lugar de "Nombre completo".
2. **W-07 "Marca":** es un select con las marcas de la tabla `marcas`, no un campo de texto libre.
3. **W-07 "Stock inicial" al editar:** se muestra deshabilitado con la existencia actual; solo se captura al registrar.
4. **W-03 menú lateral:** las opciones futuras se muestran deshabilitadas con la etiqueta de su sprint real: Pedidos y Entradas "Sprint 2"; Ventas y Alertas de stock "Sprint 3".

## Estilo visual (tomado de los wireframes)
- Bootstrap 5 + `public/assets/css/estilos.css` con estas variables:
  - Menú lateral y panel izquierdo del login: azul marino oscuro `#1f2937`
  - Fondo de páginas: gris claro `#f3f4f6`; tarjetas blancas con borde gris y esquinas redondeadas
  - Botón principal (Iniciar sesión, Enviar código, + Agregar usuario, + Agregar producto, Editar, Ver): azul `#2563eb`
  - Botón guardar y "+ Agregar proveedor": verde `#16a34a`
  - Botón "Desactivar proveedor" y "Cerrar sesión": gris `#6b7280`
  - Errores, stock mínimo y botón "Desactivar" del modal: rojo `#dc2626`; fila de stock mínimo con fondo rojo muy claro
  - Acento del logo y opción activa del menú: amarillo `#eab308`
- Fuente Arial; títulos en Arial bold.
- Logo: usar `public/assets/img/logo.png`; si no existe, un cuadro amarillo con la letra "F" y el texto "FERREDOG".
- Insignias de rol (W-04): Administrador azul claro, Vendedor verde claro, Almacenista amarillo claro.
- Insignias de estado: "Activo" verde claro, "Inactivo" gris; "Sin precio" amarillo claro (W-06).
- Campos con error: borde rojo y mensaje rojo debajo ("Este nombre de usuario ya existe", "Este código ya existe").
- Requisitos de contraseña (W-02 y W-05): lista que se actualiza mientras se escribe, ✓ verde si se cumple, ✗ rojo si no.
- Botón con ícono de ojo para mostrar u ocultar contraseña (W-01, W-02).

## Layout interno (W-03 a W-09)
- **Encabezado:** logo a la izquierda; a la derecha nombre del usuario, su rol y botón "Cerrar sesión".
- **Menú lateral fijo:** título "MENÚ · <ROL>" y opciones según el rol; la opción actual resaltada con barra amarilla.

| Rol | Opciones activas | Opciones deshabilitadas |
|---|---|---|
| Administrador | Inicio, Usuarios, Productos, Proveedores | Pedidos (Sprint 2), Entradas (Sprint 2), Ventas (Sprint 3) |
| Almacenista | Inicio, Productos | Entradas (Sprint 2), Alertas de stock (Sprint 3) |
| Vendedor | Inicio, Productos (solo consulta) | Ventas (Sprint 3) |

## Pantallas

### W-01 Login
- Panel izquierdo oscuro con logo, "Control de inventario de la ferretería" y "Productos, proveedores, pedidos y ventas en un solo lugar."
- Tarjeta: "Iniciar sesión", campos Usuario y Contraseña, botón azul, enlace "¿Olvidaste tu contraseña?", nota al pie "Solo usuarios activos pueden ingresar. La sesión se cierra tras 30 min sin actividad."
- Mensajes en recuadro rojo: "Usuario o contraseña incorrectos" con la nota "Al 5.º intento fallido la cuenta se bloquea 15 min."; y el de cuenta bloqueada.

### W-02 Recuperar contraseña
- Una sola página con dos tarjetas: "1 Solicitar código" (correo + "Enviar código" + "¿No te llegó? Reenviar código") y "2 Crear nueva contraseña" (código de 6 dígitos, nueva contraseña, confirmar contraseña, requisitos, "Guardar contraseña").
- La tarjeta 2 se habilita después de enviar el código.
- Mensaje verde "Código enviado. Revisa tu bandeja de entrada (vence en 10 min)."
- Enlace "← Volver a iniciar sesión" arriba a la derecha.

### W-03 Inicio
- "Bienvenido(a), <nombre>".
- Tarjetas con conteos reales y enlace "Ver →", según el rol:
  - Administrador: Usuarios activos (azul), Productos en catálogo, Proveedores activos (verde), Productos en stock mínimo (rojo).
  - Almacenista: Productos en catálogo, Productos en stock mínimo.
  - Vendedor: Productos en catálogo.
- "Accesos rápidos" con los botones de los módulos que el rol puede usar.

### W-04 Lista de usuarios
- Columnas: Nombre, Usuario, Correo, Rol, Estado, Acciones.
- Buscador por nombre o usuario; filtros Rol y Estado; botón "+ Agregar usuario".
- Estado con interruptor (switch). Al desactivar sale una ventana de confirmación "¿Desactivar a <nombre>? Ya no podrá iniciar sesión." con "Cancelar" y "Desactivar" (rojo). Al activar no se pide confirmación.
- Pie: "Mostrando N de N usuarios".

### W-05 Formulario de usuario
- Ruta de navegación "Usuarios / Agregar usuario" (o "Editar usuario").
- Sección "Datos del usuario": Nombre, Apellidos, Nombre de usuario, Correo electrónico, Rol.
- Sección "Acceso" (solo al agregar): Contraseña inicial, Confirmar contraseña, requisitos.
- Interruptor Estado; botones "Cancelar" y "Guardar".
- Al editar: mismo formulario con los datos cargados, nombre de usuario de solo lectura y sin la sección "Acceso".

### W-06 Lista de productos
- Columnas: Código, Producto, Marca, Categoría, Proveedor, Precio, Stock, Mín., Acciones.
- Buscador por nombre o código; filtros Categoría, Proveedor y Estado (Activos por defecto); botón "+ Agregar producto".
- Fila con fondo rojo claro y número de stock en rojo si `Existencias <= Stock_Minimo`.
- Precio vacío → insignia "Sin precio".
- Acciones: "Editar" (Administrador y Almacenista); "Poner precio" y "Desactivar" solo Administrador. El Vendedor no ve acciones ni "+ Agregar producto".
- "Poner precio" abre una ventana pequeña con el precio actual y un campo para el nuevo. "Desactivar" pide confirmación como en W-04.
- Paginación de 10 en 10; pie "Mostrando N de N productos · N en stock mínimo".
- Acepta `?proveedor=<id>` para abrir la lista ya filtrada (lo usa W-09).

### W-07 Formulario de producto
- Ruta "Productos / Agregar producto" (o "Editar producto").
- "Datos del producto": Código, Nombre del producto, Marca, Categoría, Proveedor (solo activos), Unidad.
- "Precio e inventario": Precio de venta (opcional), Stock inicial, Stock mínimo.
- Precio de venta: el Administrador lo puede capturar; para el Almacenista aparece bloqueado con el texto "Lo asigna el Administrador" y el producto se guarda "Sin precio".
- Botones "Cancelar" y "Guardar".

### W-08 Lista de proveedores
- Columnas: Proveedor, Tipo, Contacto, Teléfono, Correo, Estado, Acciones (Ver, Editar).
- Buscador por nombre o contacto; filtro Estado; botón verde "+ Agregar proveedor".
- Pie: "Mostrando N de N proveedores · N activos".

### W-09 Formulario de proveedor
- Encabezado con el nombre del proveedor y su insignia de estado.
- "Datos del proveedor": Nombre o razón social, Tipo (select: Distribuidor, Mayorista, Fabricante), Persona de contacto, Teléfono, Correo electrónico, Dirección.
- Botones: "Desactivar proveedor" (gris, con confirmación), "Cancelar", "Guardar".
- Panel derecho "Productos que surte (N)": buscador, columnas Código, Producto, Stock (rojo si está en mínimo), máximo 7 filas y enlace "Ver los N productos →" que abre W-06 filtrado por ese proveedor.
- **Editar:** formulario editable + panel de productos.
- **Ver:** mismos datos de solo lectura, sin "Guardar"; con botón "Editar".
- **Agregar:** solo el formulario, sin panel de productos ni "Desactivar".
- Obligatorios: todos menos Dirección.

## Estructura del proyecto
```
ProyectoFerredog/
├── AGENTS.md
├── README.md
├── .env                      (no se sube a Git)
├── .env.example
├── .gitignore
├── index.html                ya existía; no se modifica
├── docs/sprint1/             requirements.md, design.md, task.md, wireframes/
├── config/
│   └── database.php          lee .env sin composer y abre la conexión PDO
├── includes/
│   └── auth.php              verifica sesión, rol y 30 min de inactividad
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── InicioController.php
│   │   ├── UsuarioController.php
│   │   ├── ProductoController.php
│   │   └── ProveedorController.php
│   ├── models/
│   │   ├── Usuario.php       tablas usuario + empleados
│   │   ├── Producto.php      tablas productos + inventario
│   │   ├── Proveedor.php     tabla proveedor
│   │   └── Catalogo.php      categoria, marcas, unidad_medida (combos)
│   ├── views/
│   │   ├── layout/           header.php, sidebar.php, footer.php
│   │   ├── login/            login.php, recuperar.php
│   │   ├── dashboard/        inicio.php
│   │   ├── usuarios/         lista.php, formulario.php
│   │   ├── productos/        lista.php, formulario.php
│   │   └── proveedores/      lista.php, formulario.php
│   └── libraries/
│       ├── Correo.php        envío del código (modo prueba o smtp)
│       └── PHPMailer/        vacía por ahora; el equipo copia PHPMailer al final
├── storage/
│   └── logs/
│       └── correos.log       códigos en modo prueba (no se sube a Git)
└── public/
    ├── index.php             punto de entrada y enrutador
    └── assets/
        ├── css/estilos.css
        ├── js/               login.js, recuperar.js, usuarios.js, productos.js, proveedores.js
        └── img/logo.png
```

## Enrutamiento
- Todo entra por `public/index.php` con parámetros `?c=controlador&a=accion` (ejemplo: `?c=producto&a=lista`).
- Sin parámetros: si hay sesión, va a inicio; si no, al login.
- URL local en XAMPP: `http://localhost/ProyectoFerredog/public/`

## Datos (estructura REAL de la base `ferredog`, verificada con DESCRIBE)

**Regla general de Estado:** en TODAS las tablas, `Estado` es `tinyint(1)`: `1` = activo, `0` = inactivo.
En consultas SQL usa siempre `Estado = 1` / `Estado = 0`, nunca `'Activo'`.
El modelo traduce para las vistas: al leer usa `CASE WHEN Estado = 1 THEN 'Activo' ELSE 'Inactivo' END AS Estado`
y al guardar convierte `'Activo'` → `1`, `'Inactivo'` → `0` (ver `estadoANumero()` en `app/models/Usuario.php`).

### `usuario`
Id_Usuario, Usuario (UNIQUE), Password, Rol (varchar: 'Administrador' | 'Vendedor' | 'Almacenista'), Fecha_Alta (datetime),
Estado (tinyint), Intentos_Fallidos, Fecha_Bloqueo, Codigo_Recuperacion, Codigo_Expiracion, Id_Empleado (UNIQUE)

### `empleados` (plural)
Id_Empleado, Nombre, Apellidos, Correo (UNIQUE), Telefono, Puesto, Fecha_Alta (date), Estado (tinyint)

### `productos`
Id_Producto, Codigo_Barras (UNIQUE), Nombre, Descripcion, Precio_Venta (NULL = sin precio), Precio_Compra (NULL),
**Stock_Minimo** (int, default 5), **Stock_Maximo** (int, default 100), Fecha_Registro (date, default hoy),
Estado (tinyint), Id_Categoria, Id_Unidad, Id_Proveedor (NULL), Id_Marca (NULL, FK a `marcas`)

> El stock mínimo y máximo están en `productos`, NO en `inventario`.

### `inventario`
Id_Inventario, Existencias (int, default 0), Fecha_Entrada (date), Fecha_Salida (date), Id_Producto (UNIQUE)

> Solo guarda existencias. **No todos los productos tienen fila en `inventario`** (hay más productos que filas de inventario).
> Por eso SIEMPRE se une con `LEFT JOIN` y la existencia se lee como `COALESCE(i.Existencias, 0)`:
> `productos p LEFT JOIN inventario i ON i.Id_Producto = p.Id_Producto WHERE COALESCE(i.Existencias, 0) <= p.Stock_Minimo`

### `proveedor`
Id_Proveedor, Nombre, Contacto, Telefono, Correo, Direccion, RFC (varchar 10, opcional, no se usa en el Sprint 1), Tipo, Estado (tinyint)

### `marcas`
Id_Marca, Nombre (UNIQUE), Descripcion, Estado (tinyint)

### Catálogos para combos
- `categoria`: Id_Categoria, Nombre, Descripcion, Fecha_Creacion, Estado (tinyint) — en combos solo `Estado = 1`
- `marcas`: Id_Marca, Nombre — en combos solo `Estado = 1`
- `unidad_medida`: Id_Unidad, Nombre, Abreviatura (no tiene Estado)

## Reglas de lógica

### HU-1 Login
- Login por `usuario.Usuario` con `password_verify()`.
- Bloqueo: cada fallo suma `Intentos_Fallidos`; al llegar a 5 se guarda `Fecha_Bloqueo = NOW()`. Bloqueada mientras `NOW() < Fecha_Bloqueo + 15 min`. Al pasar el tiempo o entrar bien, se reinicia a 0 y `Fecha_Bloqueo` a NULL.
- Al entrar: `session_regenerate_id(true)`; en sesión se guardan Id_Usuario, nombre, rol y hora del último acceso.
- Inactividad: `includes/auth.php` compara la hora del último acceso en cada carga; si pasaron 30 min, destruye la sesión.
- Páginas internas envían `Cache-Control: no-store` para que "atrás" no muestre contenido tras cerrar sesión.

### HU-1 Recuperar contraseña
- Buscar por `empleados.Correo` con JOIN a `usuario`, solo cuentas con `usuario.Estado = 1`.
- Código de 6 dígitos con `random_int()`; se guarda con `password_hash()` en `Codigo_Recuperacion` y `Codigo_Expiracion = NOW() + 10 min`.
- El Id del usuario en recuperación se guarda en `$_SESSION` entre el paso 1 y el paso 2.
- La nueva contraseña y su confirmación deben coincidir.
- Al cambiar la contraseña: se borran el código y la expiración, y se reinician intentos y bloqueo.
- El envío se hace en una sola clase, `app/libraries/Correo.php`, con un método `enviarCodigo($correo, $nombre, $codigo)`. El controlador solo llama a ese método.
- `Correo.php` funciona en dos modos según `MAIL_MODO` en `.env`:
  - **`prueba` (modo actual, por defecto):** no envía nada; escribe en `storage/logs/correos.log` una línea con fecha, correo destino y código. No usa PHPMailer.
  - **`smtp` (se activa al final del proyecto):** envía con PHPMailer por SMTP de Gmail (smtp.gmail.com, puerto 587, STARTTLS) usando MAIL_HOST, MAIL_PORT, MAIL_USER, MAIL_PASS y MAIL_FROM_NAME de `.env`. Los archivos de PHPMailer se cargan con `require` **solo dentro de este modo**.
- Si `MAIL_MODO` es `smtp` y falta PHPMailer o falla el envío, se muestra "No se pudo enviar el correo, intenta más tarde" y se registra el error en `storage/logs/correos.log`.

### HU-4 Gestionar usuarios
- **Crear = 2 inserts en una transacción:** primero `empleados` (Nombre, Apellidos, Correo, Puesto = rol, Estado 1/0), luego `usuario` (Usuario, Password con `password_hash()`, Rol, Estado 1/0, Id_Empleado con `lastInsertId()`). Si falla uno, `rollBack()`.
- **Editar:** nombre, apellidos y correo en `empleados`; rol y estado en `usuario`. El nombre de usuario no se edita.
- **Listar/buscar:** `usuario JOIN empleados ON usuario.Id_Empleado = empleados.Id_Empleado`, con filtros de rol y estado.
- **Duplicados:** usuario contra `usuario.Usuario`; correo contra `empleados.Correo` (al editar, excluyendo al mismo empleado).
- El administrador no puede desactivarse a sí mismo ni quitarse su propio rol.

### HU-5 Gestionar productos
- **Registrar = 2 inserts en una transacción:** `productos` (Codigo_Barras, Nombre, Id_Marca, Id_Categoria, Id_Proveedor, Id_Unidad, Stock_Minimo, Precio_Venta capturado por el Administrador o NULL, Estado = 1) e `inventario` (Id_Producto con `lastInsertId()`, Existencias = stock inicial, Fecha_Entrada = hoy).
- Si quien guarda es Almacenista, el controlador ignora cualquier precio recibido.
- **Editar:** datos del producto y `productos.Stock_Minimo`; el Administrador también puede cambiar el precio. `inventario.Existencias` NO se edita.
- **Lista:** `productos p LEFT JOIN inventario i ON i.Id_Producto = p.Id_Producto` (existencia = `COALESCE(i.Existencias, 0)`), `LEFT JOIN marcas m`, `JOIN categoria c`, `LEFT JOIN proveedor pr`, `JOIN unidad_medida u`. Columna "Mín." = `p.Stock_Minimo`. Fila en rojo si `COALESCE(i.Existencias, 0) <= p.Stock_Minimo`.
- Filtro Estado: "Activos" → `p.Estado = 1` (por defecto), "Inactivos" → `p.Estado = 0`.
- **Duplicados:** código contra `productos.Codigo_Barras` (al editar, excluyendo al mismo producto).
- **Solo Administrador:** poner precio (mayor a 0) y desactivar (`Estado = 0`). Se valida en el controlador, no solo ocultando botones.
- **Vendedor:** solo puede ver la lista; cualquier otra acción se niega.
- Combos: categorías, marcas y proveedores con `Estado = 1`; unidades todas.
- Productos existentes pueden tener `Id_Marca` NULL: en la lista se muestra "—" y al editar el campo Marca es obligatorio.

### HU-6 Gestión de proveedores
- **Duplicados:** nombre y correo contra `proveedor` (al editar, excluyendo al mismo proveedor). La base no tiene UNIQUE en esas columnas: la validación es del controlador.
- **Productos que surte:** `productos p LEFT JOIN inventario i ON i.Id_Producto = p.Id_Producto WHERE p.Id_Proveedor = ?`; rojo si `COALESCE(i.Existencias, 0) <= p.Stock_Minimo`.
- **Desactivar:** `Estado = 0`. No se borra.
- Filtro Estado: Activos (`1`), Inactivos (`0`), Todos.
- RFC no aparece en el wireframe W-09: no se captura ni se muestra en el Sprint 1.

### Seguridad general
- Toda salida de datos en vistas con `htmlspecialchars()`.
- Todas las consultas con PDO y consultas preparadas.
- El rol se verifica en cada acción del controlador.