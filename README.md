# Ferredog

Sistema web de control de inventario para una ferretería. Permite administrar usuarios por rol, el catálogo de productos con su stock, proveedores, pedidos a proveedores y entradas de mercancía. El acceso está protegido con inicio de sesión, bloqueo por intentos fallidos y recuperación de contraseña por correo.

Proyecto de la materia **Desarrollo e Implementación de Sistemas de Información** — Instituto Tecnológico del Valle de Oaxaca.

**Integrantes:** Nery Omar Hernandez Cortes, Carlos  Gabriel Policarpo Sanchez, Alex Jimenez Martinez,Felipe De Jesus Antonio Ramirez 

---

## Requisitos

- XAMPP con **Apache** y **PHP 8+** (extensiones `pdo_mysql` y `openssl` activas)
- **MariaDB 12** (o MySQL 8) en el puerto 3306
- Navegador moderno (Chrome, Edge o Firefox)
- Opcional: una cuenta de Gmail con contraseña de aplicación para enviar correos reales

## Instalación

1. Clona el repositorio dentro de `htdocs`:
```
   cd C:\xampp\htdocs
   git clone https://github.com/NeryOmarHdzCortes/ProyectoFerreDog.git ProyectoFerredog
```
2. Crea la base de datos e importa el archivo `sql/ferredog.sql`:
```sql
   CREATE DATABASE ferredog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
   Después importa `sql/ferredog.sql` desde DBeaver, phpMyAdmin o la consola de MariaDB.
3. Crea un usuario de base de datos solo con los permisos necesarios:
```sql
   CREATE USER 'ferredog_app'@'localhost' IDENTIFIED BY 'tu_contraseña';
   GRANT SELECT, INSERT, UPDATE, DELETE ON ferredog.* TO 'ferredog_app'@'localhost';
```
4. Copia `.env.example` a `.env` y llena tus datos:
```
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=ferredog
   DB_USER=ferredog_app
   DB_PASS=tu_contraseña

   APP_TIMEZONE=America/Mexico_City

   MAIL_MODO=prueba
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USER=correo_del_sistema@gmail.com
   MAIL_PASS=contraseña_de_aplicacion
   MAIL_FROM_NAME=Ferredog
```
5. En `C:\xampp\php\php.ini` deja **una sola** línea activa de zona horaria y reinicia Apache:
```
   date.timezone=America/Mexico_City
```
6. Abre en el navegador:
```
   http://localhost/ProyectoFerredog/public/
```

## Usuarios de prueba

| Rol | Usuario | Contraseña |
|---|---|---|
| Administrador | pchavez | Ferredog#2026 |
| Almacenista | <usuario> | Ferredog#2026 |
| Vendedor | <usuario> | Ferredog#2026 |

> Son datos de prueba. En un entorno real se deben cambiar.

| Rol | Puede hacer |
|---|---|
| Administrador | Usuarios, productos, precios, proveedores y pedidos a proveedores |
| Almacenista | Consultar productos y stock; buscar pedidos por recibir y registrar entradas |
| Vendedor | Solo consultar y buscar productos |

## Recuperación de contraseña

- **Modo prueba** (`MAIL_MODO=prueba`): el código de 6 dígitos se escribe en `storage/logs/correos.log`.
- **Modo Gmail** (`MAIL_MODO=gmail`):
  1. Activa la verificación en 2 pasos en la cuenta de Gmail del sistema.
  2. Crea una contraseña de aplicación en *myaccount.google.com/apppasswords*.
  3. Pon el correo en `MAIL_USER` y las 16 letras (sin espacios) en `MAIL_PASS`.

El código vence a los 10 minutos y solo se puede usar una vez.

## Estructura principal

```
app/controllers/   Controladores (login, inicio, usuarios, productos, proveedores, pedidos, entradas)
app/models/        Acceso a datos con PDO y consultas preparadas
app/views/         Vistas HTML/PHP
app/libraries/     Envío de correo (PHPMailer, sin Composer)
config/            Lectura del .env y conexión a la base
includes/          Control de sesión y permisos por rol
public/            Punto de entrada (index.php), CSS, JS e imágenes
storage/logs/      Registros (no se suben al repositorio)
tests/             Pruebas unitarias
docs/sprint1/      Wireframes, pruebas y consultas de verificación
sql/               Base de datos del proyecto
```

## Pruebas unitarias

No requieren Composer. Usan la base real dentro de transacciones que se deshacen al final, así que **no dejan datos**:

```
C:\xampp\php\php.exe tests\run.php
```

El resultado de la última ejecución está en `docs/sprint2/pruebas_unitarias.md`.

## Verificación en la base de datos

`docs/sprint1/consultas_verificacion.sql` contiene una consulta por cada tarea del Sprint Backlog para comprobar en DBeaver que lo que se hace en el sistema queda guardado correctamente.

## Pruebas manuales del Sprint 1

### HU-1 · Inicio de sesión
- [ ] Entrar con usuario y contraseña correctos → abre el inicio con el menú de su rol.
- [ ] Contraseña incorrecta → "Usuario o contraseña incorrectos".
- [ ] 5 intentos fallidos → la cuenta se bloquea 15 minutos, aunque después se escriba la contraseña correcta.
- [ ] Usuario inactivo → no puede entrar.
- [ ] Cerrar sesión → regresa al login y con "atrás" no se puede volver.
- [ ] Recuperar contraseña → llega el código, vence a los 10 minutos y permite crear una nueva contraseña.

### HU-4 · Gestión de usuarios (Administrador)
- [ ] Registrar un usuario nuevo con nombre, apellidos, correo, usuario y rol.
- [ ] Usuario o correo repetido → mensaje de duplicado.
- [ ] Nombre o apellidos con números o símbolos → no se aceptan.
- [ ] Editar y buscar usuarios; filtrar por rol y estado.
- [ ] Activar y desactivar; el administrador no puede desactivarse a sí mismo.

### HU-5 · Catálogo de productos
- [ ] Registrar y editar un producto; un código repetido muestra un mensaje.
- [ ] Buscar por nombre o código; la lista muestra 10 por página.
- [ ] Asignar precio: 0, negativo o vacío se rechaza.
- [ ] Desactivar un producto: deja de aparecer, pero sigue en la base.
- [ ] El stock en o por debajo del mínimo se marca en rojo.
- [ ] Un producto que no existe muestra un mensaje en lugar de un error.
- [ ] Vendedor y Almacenista solo pueden consultar.

### HU-6 · Proveedores (Administrador)
- [ ] Registrar y editar un proveedor; nombre o correo repetido muestra un mensaje y el correo es obligatorio.
- [ ] Buscar proveedores.
- [ ] Desactivar con confirmación; desaparece de la lista de activos y de los productos.
- [ ] "Ver los N productos" muestra los productos que surte.

### Acceso por rol
- [ ] Escribir a mano una URL de administrador con otro rol → regresa al inicio.

## Nota

El Sprint 1 cubre el inicio de sesión, la gestión de usuarios, el catálogo de productos y los proveedores. El Sprint 2 agrega los módulos de pedidos y entradas. Ventas y alertas de stock corresponden al Sprint 3.

## Módulos del Sprint 2

### Pedidos a proveedores (Administrador)

- Desde **Pedidos**, revisa los pedidos Por recibir y usa **+ Nuevo pedido**.
- Elige un proveedor activo, agrega cantidades y costos mayores a cero y guarda el pedido.
- Verifica que el pedido aparezca en la lista y que, con `MAIL_MODO=prueba`, el correo se registre en `storage/logs/correos.log`.
- Cancela un pedido de prueba y confirma que deje de aparecer en Por recibir.

### Entradas de mercancía (Almacenista)

- Desde **Entradas**, busca un pedido pendiente por número (con o sin `#`) o por proveedor.
- Usa **Reportar** para registrar un faltante o daño de prueba y luego pulsa **Registrar entrada de mercancía**.
- Confirma que el pedido pase a Recibido, desaparezca de la búsqueda y el inventario aumente únicamente por la cantidad recibida en buen estado.
- Revisa en DBeaver el pedido, sus detalles, las existencias y los movimientos. Sustituye `123` por el número de pedido probado:

```sql
SELECT c.Id_Compras, c.Estado, d.Id_Detalle, d.Cantidad,
       d.Cantidad_Recibida, d.Incidencia, d.Motivo_Incidencia
FROM compras c
INNER JOIN detalle_compras d ON d.Id_Compras = c.Id_Compras
WHERE c.Id_Compras = 123;

SELECT i.Id_Producto, p.Nombre, i.Existencias,
       m.Tipo, m.Cantidad, m.Stock_Antes, m.Stock_Despues, m.Motivo
FROM inventario i
INNER JOIN productos p ON p.Id_Producto = i.Id_Producto
LEFT JOIN movimiento_inventario m ON m.Id_Producto = i.Id_Producto
WHERE m.Motivo LIKE '%Pedido #123%'
ORDER BY m.Id_Movimiento DESC;
```

### Acceso por rol

- **Administrador:** puede entrar a Pedidos; Entradas no aparece en su menú y su acceso directo debe denegarse.
- **Almacenista:** puede entrar a Entradas; Pedidos no aparece en su menú y su acceso directo debe denegarse.
- **Vendedor:** solo consulta productos; el acceso directo a Pedidos y Entradas debe denegarse.

Los módulos de Sprint 2 no deben probarse usando pedidos reales de operación. Para validar inventario y movimientos, utiliza pedidos y productos preparados para la prueba.