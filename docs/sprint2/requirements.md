# Requisitos del Sprint 2 — Ferredog

Historias del sprint: **HU-7** (Pedidos a proveedores) y **HU-3** (Entrada de mercancía).
Cada requisito indica la historia y la tarea del Sprint Backlog a la que pertenece.
Los diagramas de secuencia de cada historia están en `docs/sprint2/diagramas/`.

## Roles en este sprint
- **Administrador:** consulta, registra y cancela pedidos a proveedores (HU-7).
- **Almacenista:** busca pedidos "Por recibir" y registra la entrada de mercancía (HU-3).
- **Vendedor:** sin cambios (ventas es del Sprint 3).

## Estados del pedido
En la base, `compras.Estado` ya existe con estos valores. En pantalla SIEMPRE se muestran con el texto de la derecha:

| Valor en la base | Texto en pantalla | Insignia |
|---|---|---|
| `Pendiente` | Por recibir | amarillo claro |
| `Recibida` | Recibido | verde claro |
| `Cancelada` | Cancelado | gris |

---

## Requisito 8 — Consultar pedidos (HU-7, tarea T1) — solo Administrador
**Historia:** Como Administrador, quiero registrar y consultar los pedidos a proveedores para reabastecer el inventario y saber qué mercancía está por llegar.

1. [Event-driven] Cuando el Administrador entra al módulo Pedidos, el sistema debe mostrar directamente los pedidos **Por recibir**, del más reciente al más antiguo. (Diagrama `hu7_consultar_pedidos.png`)
2. [Ubiquitous] La tabla muestra: N.º, Proveedor, Fecha, Productos (número de productos del pedido), Total, Estado y Acciones.
3. [State-driven] Mientras no haya pedidos Por recibir, el sistema debe mostrar "No hay pedidos por recibir".
4. [Ubiquitous] Debajo de la tabla: "Mostrando N pedidos Por recibir".
5. [State-driven] Cuando el Almacenista registra la entrada de un pedido (HU-3), el pedido pasa a Recibido y deja de aparecer en esta lista.

## Requisito 9 — Cancelar pedido (HU-7, tarea T1) — solo Administrador
1. [Event-driven] Cuando el Administrador presiona "Cancelar pedido", el sistema debe pedir confirmación: "¿Cancelar el pedido #N?", con proveedor, total y estado, y el texto "El pedido pasará a Cancelado y se avisará al proveedor por correo. Esta acción no se puede deshacer." (Diagrama `hu7_cancelar_pedido.png`)
2. [Event-driven] Cuando confirma ("Sí, cancelar pedido"), el pedido pasa a **Cancelado**, se notifica al proveedor por correo y deja de aparecer en la lista.
3. [Event-driven] Cuando no confirma ("No, mantenerlo"), el pedido sigue Por recibir y no se cambia nada.
4. [Unwanted] Si el pedido ya no está Por recibir (por ejemplo, el almacenista lo acaba de recibir), el sistema no lo cancela y muestra "El pedido ya no está por recibir".
5. [Unwanted] Si el correo no se puede enviar, el pedido queda cancelado igual y se avisa "Pedido cancelado, pero no se pudo enviar el correo al proveedor".

## Requisito 10 — Registrar pedido (HU-7, tarea T2) — solo Administrador
1. [Event-driven] Cuando el Administrador elige "+ Nuevo pedido", el sistema muestra el formulario con el combo de proveedores **activos** y la fecha de hoy bloqueada (no editable). (Diagrama `hu7_registrar_pedido.png`)
2. [Event-driven] Cuando elige un proveedor, el sistema muestra solo los productos **activos** de ese proveedor con: Producto, Marca, Stock actual, Costo, Cantidad y Subtotal.
3. [Ubiquitous] El stock actual se marca con la insignia roja "N · bajo" cuando la existencia es igual o menor al stock mínimo del producto.
4. [Ubiquitous] El costo viene cargado del catálogo (`Precio_Compra`) y se puede cambiar. (criterio 1)
5. [Ubiquitous] El subtotal de cada renglón (cantidad × costo) y el total del pedido se calculan solos al escribir. (criterio 2)
6. [Unwanted] Si falta el proveedor, no hay ningún producto con cantidad, o una cantidad o costo es menor o igual a 0, el sistema marca el campo en rojo con su mensaje ("La cantidad debe ser mayor a 0", "El costo debe ser mayor a 0", "Elige un proveedor", "Agrega al menos un producto") y no guarda. (criterio 3)
7. [Event-driven] Cuando los datos son válidos y presiona "Guardar pedido", el pedido se guarda como **Por recibir** y se envía por correo al proveedor con el detalle (productos, cantidades, costos y total). (criterio 4)
8. [Unwanted] Si la base rechaza el pedido (por ejemplo, porque supera el stock máximo de un producto), no se guarda nada y se muestra el mensaje de la base.
9. [Unwanted] Si el correo no se puede enviar, el pedido queda guardado y se avisa "Pedido guardado, pero no se pudo enviar el correo al proveedor".
10. [Event-driven] Cuando presiona "Cancelar" en el formulario, regresa a la lista sin guardar.

## Requisito 11 — Buscar pedido para recibir (HU-3, tarea T1) — Almacenista
**Historia:** Como Almacenista, quiero registrar la entrada de mercancía de un pedido para que las existencias se actualicen con lo que realmente llegó.

1. [Event-driven] Cuando el Almacenista busca por número de pedido (con o sin "#") o por nombre del proveedor, el sistema busca **solo** pedidos Por recibir. (Diagrama `hu3_entrada_mercancia.png`)
2. [Event-driven] Cuando encuentra un pedido, muestra su encabezado ("Pedido #N · Proveedor", fecha del pedido, número de productos, insignia "Por recibir") y la tabla: Producto, Marca, Cant. pedida, Cant. recibida, Observación, Acciones.
3. [Event-driven] Cuando la búsqueda por proveedor encuentra varios pedidos Por recibir, el sistema muestra la lista de esos pedidos (número, fecha, total) para elegir uno.
4. [Unwanted] Si el pedido no existe, ya fue recibido o está cancelado, el sistema muestra "Pedido no encontrado. El pedido no existe o ya fue recibido. No se puede registrar la entrada." (criterio 2: un pedido no se recibe dos veces)

## Requisito 12 — Reportar faltante o daño (HU-3, tarea T2) — Almacenista
1. [Ubiquitous] La cantidad recibida de cada producto es, por defecto, la cantidad pedida, y su observación es "—".
2. [Event-driven] Cuando el Almacenista presiona "Reportar" en un producto, el sistema abre la ventana "Reportar faltante o daño" con el nombre del producto, su marca y la cantidad pedida, y pide:
   - Tipo: Faltante o Dañado (obligatorio)
   - Cantidad recibida en buen estado (obligatoria, entero de 0 a cantidad pedida − 1)
   - Motivo (obligatorio, máximo 200 caracteres)
3. [Ubiquitous] La ventana muestra el aviso "Se sumará N pieza(s) al inventario; el motivo queda en el historial de movimientos." y lo actualiza al cambiar la cantidad.
4. [Event-driven] Cuando presiona "Guardar reporte", el renglón muestra la cantidad recibida, la observación en rojo ("Dañado: N" o "Faltante: N", donde N = pedida − recibida), fondo rojo claro, y el botón cambia a "Editar reporte".
5. [Event-driven] Cuando presiona "Editar reporte", puede cambiar el reporte o quitarlo (vuelve a la cantidad pedida y observación "—").
6. [Unwanted] Si falta el tipo o el motivo, o la cantidad está fuera de rango, la ventana marca el campo en rojo y no guarda el reporte.
7. [Ubiquitous] El reporte NO se guarda en la base hasta que se registra la entrada (Requisito 13).

## Requisito 13 — Registrar entrada de mercancía (HU-3, tarea T3) — Almacenista
1. [Event-driven] Cuando el Almacenista presiona "Registrar entrada de mercancía" y confirma, el sistema guarda la cantidad recibida y los reportes de cada producto, y el pedido pasa a **Recibido**.
2. [Ubiquitous] Las existencias suben **solo** con la cantidad recibida en buen estado, y se guarda un movimiento de inventario por producto con su motivo (por ejemplo "Pedido #12 - Dañado: Llegó con el cristal del nivel roto"). (criterio 4)
3. [Event-driven] Al terminar, el sistema muestra "Entrada registrada. El pedido #N pasó a Recibido." y el pedido ya no se puede volver a buscar.
4. [Unwanted] Si mientras tanto el pedido fue cancelado o ya recibido, no se guarda nada y se muestra "El pedido ya no está por recibir".

## Requisito 14 — Ajustes de proveedores por el Sprint 2 (HU-6)
1. [Ubiquitous] El **correo del proveedor pasa a ser obligatorio** al agregar y editar proveedores, porque los pedidos se envían por correo. (Todos los proveedores actuales ya tienen correo.)

## Requisito 15 — Acceso según el rol (actualiza el Requisito 7)
1. Pedidos: solo Administrador.
2. Entradas: solo Almacenista.
3. Cualquier otro rol que entre por URL recibe acceso denegado (validar en el controlador con `requerirRol()`).
