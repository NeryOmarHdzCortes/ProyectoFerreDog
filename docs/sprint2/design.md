# Diseño del Sprint 2 — Ferredog

El Sprint 2 se construye sobre el Sprint 1. Todo lo que dice `docs/sprint1/design.md` (estilo visual, layout, enrutamiento, reglas de Estado, nomenclatura) **sigue vigente**. Aquí solo va lo nuevo.

## Wireframes (obligatorio seguirlos)
Las pantallas están en `docs/sprint2/wireframes/`. Antes de crear cada vista, abre su imagen y respeta el acomodo, los campos, los textos, las columnas y los colores de botones.

| Wireframe | Imagen | Vista |
|---|---|---|
| W-10 Pedidos a proveedores (lista) | `pedidos_lista.png` | `app/views/pedidos/lista.php` |
| W-11 Confirmar cancelación | `pedidos_cancelar.png` | ventana modal dentro de `pedidos/lista.php` |
| W-12 Nuevo pedido | `pedidos_nuevo.png` | `app/views/pedidos/formulario.php` |
| W-13 Registrar entrada (pedido encontrado) | `entradas_registrar.png` | `app/views/entradas/registrar.php` |
| W-14 Pedido no encontrado | `entradas_no_encontrado.png` | mensaje dentro de `entradas/registrar.php` |
| W-15 Reportar faltante o daño | `entradas_reportar.png` | ventana modal dentro de `entradas/registrar.php` |

Diagramas de secuencia (flujo que debe seguir el código) en `docs/sprint2/diagramas/`:
`hu7_consultar_pedidos.png`, `hu7_cancelar_pedido.png`, `hu7_registrar_pedido.png`, `hu3_entrada_mercancia.png`.

### Qué NO se construye de los wireframes
- Los recuadros amarillos ("HU-7 · Registrar pedido", "HU-3 · Error", etc.): son notas del diseñador.
- Los datos de ejemplo (Truper, Martillo 16 oz, #12, Laura Martínez): todo sale de la base.

### Diferencias con los wireframes (respetar esto)
1. **W-11:** el wireframe no tiene campo de motivo; la cancelación NO pide motivo. La columna `compras.Motivo_Cancelacion` existe pero queda en NULL (uso futuro).
2. **W-10:** si no hay pedidos, en lugar de la tabla se muestra "No hay pedidos por recibir" (diagrama de consultar pedidos).
3. **W-13:** si la búsqueda por proveedor encuentra varios pedidos, antes de la tabla se muestra una lista corta para elegir uno.
4. **W-12 Marca:** sale de `marcas.Nombre` por `productos.Id_Marca` (puede ser NULL → "—").

## Menú lateral (actualiza la tabla del Sprint 1)
Se mantienen los emojis actuales del menú.

| Rol | Opciones activas | Opciones deshabilitadas |
|---|---|---|
| Administrador | Inicio, Usuarios, Productos, Proveedores, **Pedidos** | Ventas (Sprint 3) |
| Almacenista | Inicio, Productos, **Entradas** | Alertas de stock (Sprint 3) |
| Vendedor | Inicio, Productos (solo consulta) | Ventas (Sprint 3) |

El Administrador ya NO muestra "Entradas" (en el wireframe W-10 no aparece).

## Estilo de lo nuevo (tomado de los wireframes)
- Insignia "Por recibir": fondo amarillo claro, texto amarillo oscuro. "Recibido": verde claro. "Cancelado": gris.
- Botón "+ Nuevo pedido" y "Guardar pedido": azul `#2563eb`.
- Botón "Cancelar pedido" de la tabla: borde y texto rojo `#dc2626`, fondo blanco. En la ventana: "No, mantenerlo" blanco con borde gris y "Sí, cancelar pedido" rojo relleno.
- Stock bajo en W-12: insignia roja clara con el texto "N · bajo".
- Campo con error: borde rojo y mensaje rojo debajo ("La cantidad debe ser mayor a 0").
- "Total del pedido": tarjeta a la derecha con el total en negritas grandes.
- Botón "Registrar entrada de mercancía": verde `#16a34a`.
- Botón "Reportar": borde y texto rojo. "Editar reporte": borde gris. Renglón con reporte: fondo rojo muy claro y observación con insignia roja ("Dañado: 1").
- Ventana "Reportar faltante o daño": "Cancelar" blanco y "Guardar reporte" rojo.
- Mensaje "Pedido no encontrado": recuadro rojo claro con barra roja a la izquierda, como en W-14.

## Estructura (archivos nuevos)
```
app/
├── controllers/
│   ├── PedidoController.php     HU-7: lista, cancelar, nuevo (formulario), productosProveedor (JSON), guardar
│   └── EntradaController.php    HU-3: index (buscar), registrar
├── models/
│   └── Pedido.php               tablas compras + detalle_compras (lo usan los dos controladores)
├── views/
│   ├── pedidos/                 lista.php, formulario.php
│   └── entradas/                registrar.php
└── libraries/
    └── Correo.php               + enviarPedido() y enviarCancelacion()
public/assets/js/
├── pedidos.js                   cálculo de subtotales/total, validación, carga de productos, modal cancelar
└── entradas.js                  modal reportar, estado de los reportes, confirmación de registrar
tests/
└── PedidoTest.php               pruebas del Sprint 2 (con rollBack)
```
Rutas: `?c=pedido&a=lista|cancelar|nuevo|productosProveedor|guardar` y `?c=entrada&a=index|registrar`.

## Datos (estructura REAL, ya creada en la base; NO crear ni alterar tablas)

### `compras` (el "pedido")
Id_Compras, Fecha_Compra (date, default hoy), Total, **Estado** ENUM('Pendiente','Recibida','Cancelada'),
**Fecha_Recepcion** (datetime NULL), **Id_Empleado_Recibe** (NULL, FK a empleados), **Motivo_Cancelacion** (NULL),
Id_Proveedor (puede ser NULL si el proveedor se eliminó), **Nombre_Proveedor**, **RFC_Proveedor** (copia que llena el trigger), Id_Empleado (quien registró el pedido).

### `detalle_compras`
Id_Detalle, Cantidad (> 0), **Cantidad_Recibida** (NULL hasta recibir; 0..Cantidad),
**Incidencia** ENUM('Ninguna','Faltante','Dañado') default 'Ninguna', **Motivo_Incidencia** (obligatorio si Incidencia ≠ 'Ninguna'),
Precio_Unitario (el costo), Total (Cantidad × Precio_Unitario), Id_Compras,
Id_Producto (puede ser NULL si el producto se eliminó), **Codigo_Producto**, **Nombre_Producto** (copia que llena el trigger).

### `movimiento_inventario`
Lo llena SOLO la base (triggers). PHP no inserta aquí en este sprint.

### Empleado de la sesión
La sesión guarda `$_SESSION['Id_Usuario']`, no el empleado. Para `compras.Id_Empleado` y `compras.Id_Empleado_Recibe` se obtiene `usuario.Id_Empleado` con `SELECT Id_Empleado FROM usuario WHERE Id_Usuario = ?`.

## Lo que hace la base sola (triggers ya instalados; NO duplicar en PHP)
| Trigger | Cuándo | Qué hace |
|---|---|---|
| `trg_compra_foto_proveedor` | INSERT en compras | Llena `Nombre_Proveedor` y `RFC_Proveedor` |
| `trg_validar_compra` | INSERT en detalle_compras | Rechaza producto inexistente o inactivo y cantidades que superen `Stock_Maximo`; llena `Codigo_Producto` y `Nombre_Producto` |
| `trg_compra_recibida` | UPDATE de compras a 'Recibida' | Suma `COALESCE(Cantidad_Recibida, Cantidad)` de cada renglón al inventario y guarda el movimiento "Pedido #N" o "Pedido #N - Dañado: motivo". Bloquea cambiar un pedido Recibido o Cancelado |

- **PHP NUNCA** actualiza `inventario` ni inserta en `movimiento_inventario` para pedidos.
- Los errores de los triggers llegan como `PDOException` con SQLSTATE `45000`; se hace rollBack y se muestra su mensaje (`errorInfo[2]`) al usuario.
- Errores de CHECK (SQLSTATE 23000 / código 4025): rollBack y mensaje amigable.

## Reglas de lógica

### HU-7 T1 Lista y cancelar
- Lista:
  ```sql
  SELECT c.Id_Compras, c.Fecha_Compra, c.Total, c.Estado,
         COALESCE(pr.Nombre, c.Nombre_Proveedor) AS Proveedor,
         (SELECT COUNT(*) FROM detalle_compras d WHERE d.Id_Compras = c.Id_Compras) AS Productos
  FROM compras c LEFT JOIN proveedor pr ON pr.Id_Proveedor = c.Id_Proveedor
  WHERE c.Estado = 'Pendiente'
  ORDER BY c.Fecha_Compra DESC, c.Id_Compras DESC
  ```
- Cancelar (POST, con el Id): `UPDATE compras SET Estado = 'Cancelada' WHERE Id_Compras = ? AND Estado = 'Pendiente'`. Si `rowCount() = 0` → "El pedido ya no está por recibir". Si se canceló → `Correo::enviarCancelacion()` al correo del proveedor (si el proveedor ya no existe, no se envía).
- Mensajes con el mismo sistema de mensajes de éxito/error de los otros módulos.

### HU-7 T2 Nuevo pedido
- Combo de proveedores: `proveedor` con `Estado = 1` ordenado por nombre.
- Al elegir proveedor, `pedidos.js` pide `?c=pedido&a=productosProveedor&id=N` y recibe JSON:
  ```sql
  SELECT p.Id_Producto, p.Nombre, m.Nombre AS Marca, COALESCE(i.Existencias, 0) AS Existencias,
         p.Stock_Minimo, p.Stock_Maximo, p.Precio_Compra
  FROM productos p
  LEFT JOIN inventario i ON i.Id_Producto = p.Id_Producto
  LEFT JOIN marcas m ON m.Id_Marca = p.Id_Marca
  WHERE p.Id_Proveedor = ? AND p.Estado = 1
  ORDER BY p.Nombre
  ```
- Costo inicial = `Precio_Compra` (si es NULL, el campo queda vacío). Cantidad vacía = el producto no se pide.
- Validar en JS (para marcar en rojo) **y otra vez en el controlador** (nunca confiar en el navegador):
  proveedor activo existente; al menos un renglón con cantidad; cantidad entera > 0; costo > 0 (2 decimales); cada producto pertenece a ese proveedor y está activo.
- El controlador **recalcula** subtotales y total; no usa el total que manda el navegador.
- Guardar en UNA transacción:
  1. `INSERT INTO compras (Fecha_Compra, Total, Estado, Id_Proveedor, Id_Empleado) VALUES (CURDATE(), ?, 'Pendiente', ?, ?)`
  2. Por cada renglón: `INSERT INTO detalle_compras (Cantidad, Precio_Unitario, Total, Id_Compras, Id_Producto) VALUES (?, ?, ?, ?, ?)`
  3. commit. Si algo falla: rollBack y mensaje (los del trigger se muestran tal cual).
- Después del commit: `Correo::enviarPedido()` con el detalle. Mensaje: "Pedido #N registrado y enviado a <proveedor>." (o el aviso de correo fallido) y regresar a la lista.
- No se modifica `productos.Precio_Compra` al cambiar el costo en el pedido.

### HU-3 T1 Buscar
- Texto de búsqueda: si son solo dígitos (con o sin "#"), buscar por `Id_Compras`; si no, por `COALESCE(pr.Nombre, c.Nombre_Proveedor) LIKE ?`.
- SIEMPRE con `c.Estado = 'Pendiente'`. Cero resultados → mensaje W-14.
- Detalle del pedido:
  ```sql
  SELECT d.Id_Detalle, d.Cantidad, COALESCE(p.Nombre, d.Nombre_Producto) AS Producto,
         m.Nombre AS Marca, d.Id_Producto
  FROM detalle_compras d
  LEFT JOIN productos p ON p.Id_Producto = d.Id_Producto
  LEFT JOIN marcas m ON m.Id_Marca = p.Id_Marca
  WHERE d.Id_Compras = ?
  ```
  Si `Id_Producto` es NULL (producto eliminado) se muestra con "(eliminado)", sin botón Reportar; el trigger no lo suma.

### HU-3 T2 Reportar (solo en el navegador)
- `entradas.js` guarda los reportes en memoria y en campos ocultos del formulario por `Id_Detalle`: cantidad recibida, incidencia, motivo.
- Validación de la ventana: tipo obligatorio; cantidad entera entre 0 y Cantidad − 1; motivo obligatorio (máx. 200).
- Observación: "Faltante: N" o "Dañado: N", con N = Cantidad − Cantidad recibida.

### HU-3 T3 Registrar entrada (POST)
- Confirmación antes de enviar: "¿Registrar la entrada del pedido #N? Las existencias se actualizarán con las cantidades recibidas."
- El controlador valida otra vez cada renglón (que el `Id_Detalle` pertenezca al pedido, rango de cantidad, motivo si hay incidencia).
- En UNA transacción:
  1. Por cada renglón: `UPDATE detalle_compras SET Cantidad_Recibida = ?, Incidencia = ?, Motivo_Incidencia = ? WHERE Id_Detalle = ? AND Id_Compras = ?` (sin reporte: Cantidad_Recibida = Cantidad, 'Ninguna', NULL).
  2. `UPDATE compras SET Estado = 'Recibida', Fecha_Recepcion = NOW(), Id_Empleado_Recibe = ? WHERE Id_Compras = ? AND Estado = 'Pendiente'`
  3. Si el paso 2 da `rowCount() = 0` → rollBack y "El pedido ya no está por recibir".
  4. commit. El trigger ya sumó el stock y guardó los movimientos.
- Mensaje: "Entrada registrada. El pedido #N pasó a Recibido."

### Correo.php (nuevos métodos)
- `enviarPedido($correo, $proveedor, $idPedido, $fecha, $renglones, $total)` y `enviarCancelacion($correo, $proveedor, $idPedido, $total)`.
- Mismo esquema que `enviarCodigo()`: con `MAIL_MODO=prueba` escribe en `storage/logs/correos.log`; con `gmail` envía con PHPMailer. Devuelven `true`/`false`; nunca detienen la operación.
- Asunto: "Ferredog - Pedido #N" / "Ferredog - Pedido #N cancelado". Cuerpo en HTML sencillo con tabla Producto / Cantidad / Costo / Subtotal y total.

### Proveedores (ajuste)
- `ProveedorController` y `proveedores.js`: el correo es obligatorio al agregar y editar (mensaje "El correo es obligatorio").

## Pruebas
- `tests/PedidoTest.php` dentro de la suite existente (`C:\xampp\php\php.exe tests\run.php`), con transacción y `rollBack()` en `finally`.
- Casos mínimos: lista solo Pendiente; cancelar Pendiente sí y Recibido no; guardar pedido válido; rechazar cantidad 0 / costo 0 / sin productos; recibir completo (+cantidad); recibir con daño (+recibida, motivo en movimiento); pedido recibido no se encuentra; correo en modo prueba escribe en el log.
- Resultado en `docs/sprint2/pruebas_unitarias.md` (lo crea el agente al terminar; es la única excepción a "no modificar docs/").
