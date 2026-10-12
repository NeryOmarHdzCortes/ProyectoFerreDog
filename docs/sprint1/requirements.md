# Requisitos del Sprint 1 — Ferredog

Historias del sprint: HU-1, HU-4, HU-5 y HU-6.
Cada requisito indica la historia y la tarea del Sprint Backlog a la que pertenece.

## Roles
Valores exactos de `usuario.Rol`:
- **Administrador:** gestiona usuarios, productos y proveedores.
- **Almacenista:** registra, edita y busca productos.
- **Vendedor:** en este sprint inicia sesión, ve su pantalla de inicio y consulta productos sin modificarlos (ventas es del Sprint 3).

---

## Requisito 1 — Iniciar sesión (HU-1, tarea T3)
**Historia:** Como usuario del sistema, quiero iniciar sesión con mi usuario y contraseña para acceder solo a las funciones que corresponden a mi rol.

1. [Event-driven] Cuando el usuario ingresa su nombre de usuario y contraseña correctos, el sistema debe mostrar la pantalla de inicio con el menú de su rol.
2. [Event-driven] Cuando el usuario ingresa datos incorrectos, el sistema debe mostrar "Usuario o contraseña incorrectos" sin indicar cuál de los dos falló, y sumar un intento fallido.
3. [Event-driven] Cuando una cuenta llega a 5 intentos fallidos, el sistema debe bloquearla durante 15 minutos.
4. [State-driven] Mientras la cuenta esté bloqueada, el sistema debe impedir el acceso aunque la contraseña sea correcta, e indicar que la cuenta está bloqueada temporalmente.
5. [State-driven] Mientras la cuenta esté inactiva (`Estado` = 'Inactivo'), el sistema debe impedir el acceso.
6. [Event-driven] Cuando el usuario inicia sesión correctamente, el sistema debe reiniciar sus intentos fallidos a 0.

## Requisito 2 — Cerrar sesión (HU-1, tarea T3)
1. [Ubiquitous] El usuario debe poder cerrar sesión desde cualquier pantalla; el sistema termina la sesión y regresa al login.
2. [State-driven] Mientras no haya actividad durante 30 minutos, el sistema debe cerrar la sesión automáticamente.
3. [Event-driven] Después de cerrar sesión, el usuario no debe poder volver a páginas internas con el botón "atrás".

## Requisito 3 — Recuperar contraseña (HU-1, tarea T4)
1. [Event-driven] Cuando el usuario elige "¿Olvidaste tu contraseña?" y captura su correo, el sistema debe generar un código de 6 dígitos con vigencia de 10 minutos y enviarlo al correo registrado.
2. [Event-driven] Cuando el correo no está registrado o la cuenta está inactiva, el sistema debe mostrar el mismo mensaje genérico ("Si el correo está registrado, recibirás un código") y no enviar nada.
3. [Event-driven] Cuando el usuario captura el código correcto y vigente y una nueva contraseña válida (igual en los campos Nueva contraseña y Confirmar contraseña), el sistema debe guardarla y permitir iniciar sesión con ella.
4. [Event-driven] Cuando el código es incorrecto o expiró, el sistema debe mostrar un error y no cambiar la contraseña.
5. [Event-driven] Cuando el usuario pide reenviar el código, el sistema debe generar uno nuevo e invalidar el anterior.
6. [Event-driven] Cuando la nueva contraseña no cumple la regla de seguridad, el sistema debe mostrar el error y no guardarla.

## Requisito 4 — Gestionar usuarios (HU-4, tarea T2) — solo Administrador
**Historia:** Como Administrador, quiero gestionar usuarios para llevar un control de quién accede al sistema y con qué rol.

1. [Event-driven] Cuando el administrador registra un usuario, el sistema debe pedir nombre, apellidos, nombre de usuario, correo, rol, contraseña inicial (con confirmación) y estado.
2. [Event-driven] Cuando el nombre de usuario o el correo ya existen, el sistema debe avisar y no guardar.
3. [Event-driven] Cuando el administrador edita un usuario, puede cambiar nombre, apellidos, correo y rol; no se pide contraseña.
4. [Event-driven] Cuando los datos capturados o editados son inválidos, el sistema debe mostrar el error y no guardar.
5. [Event-driven] Cuando el administrador activa o desactiva un usuario desde la lista (al desactivar, previa confirmación), el sistema cambia su estado; un usuario inactivo no puede iniciar sesión y sus datos se conservan.
6. [Event-driven] Cuando el administrador busca usuarios por nombre o usuario y filtra por rol y estado, la tabla muestra nombre, usuario, correo, rol y estado.

## Requisito 5 — Gestionar catálogo de productos (HU-5, tareas T2 y T3)
**Historia:** Como Administrador y Almacenista, quiero gestionar el catálogo de productos para tener actualizados los productos, sus precios y existencias, y encontrarlos rápido.

1. [Event-driven] Cuando el usuario registra un producto, el sistema debe pedir código, nombre, marca, categoría, proveedor, unidad, stock inicial y stock mínimo; el precio de venta es opcional y solo lo puede capturar el Administrador. Si no se captura, el producto queda "Sin precio". (T2)
2. [Event-driven] Cuando el código ya existe, el sistema debe avisar y no guardar. (T2)
3. [Ubiquitous] El combo de proveedor solo debe mostrar proveedores activos. (T2)
4. [Event-driven] Cuando el usuario busca, el sistema debe permitir buscar por nombre o código y filtrar por categoría, proveedor y estado. (T2)
5. [Ubiquitous] Los productos con existencia igual o menor a su stock mínimo deben marcarse en rojo en la lista. (T2)
6. [Event-driven] Cuando el usuario edita un producto desde la lista, el sistema debe validar los datos; si son inválidos, muestra el error y no guarda. (T2)
7. [Event-driven] Cuando el Administrador asigna o cambia el precio de venta desde la lista, el sistema debe guardarlo (mayor a 0). (T3)
8. [Event-driven] Cuando el Administrador desactiva un producto desde la lista (previa confirmación), el sistema cambia su estado a 'Inactivo'. (T3)
9. [State-driven] Mientras el usuario sea Almacenista, el sistema no debe mostrarle las opciones de precio ni de desactivar, y el precio aparece bloqueado en el formulario. Mientras sea Vendedor, solo puede consultar la lista. (T3)
10. [Ubiquitous] Un producto sin precio se muestra como "Sin precio" en la lista.

## Requisito 6 — Gestión de proveedores (HU-6, tarea T2) — solo Administrador
**Historia:** Como Administrador, quiero registrar y consultar proveedores para saber a quién pedir cada producto y trabajar solo con proveedores vigentes.

1. [Event-driven] Cuando el administrador registra un proveedor, el sistema debe pedir nombre, tipo, contacto, teléfono, correo y dirección.
2. [Event-driven] Cuando el nombre o el correo ya existen en otro proveedor, el sistema debe avisar y no guardar.
3. [Event-driven] Cuando el administrador edita un proveedor, el sistema debe validar los datos; si son inválidos, muestra el error y no guarda.
4. [Event-driven] Cuando el administrador busca proveedores, la tabla permite buscar por nombre o contacto y filtrar por estado.
5. [Event-driven] Cuando el administrador elige "Ver" o "Editar" en un proveedor de la lista, el sistema muestra sus datos y los productos que surte.
6. [Event-driven] Cuando el administrador desactiva un proveedor (previa confirmación), este deja de aparecer al registrar o editar productos.

## Requisito 7 — Acceso según el rol (todas las HU)
1. [Event-driven] Cuando alguien sin sesión entra a una página interna, el sistema lo envía al login.
2. [Event-driven] Cuando un usuario entra a un módulo que no corresponde a su rol, el sistema le niega el acceso.
   - Usuarios y Proveedores: solo Administrador.
   - Productos: Administrador y Almacenista (gestión); Vendedor (solo consulta).

## Regla de contraseña (aplica a HU-1 y HU-4)
Mínimo 8 caracteres, con al menos una mayúscula, una minúscula, un número y un símbolo.
