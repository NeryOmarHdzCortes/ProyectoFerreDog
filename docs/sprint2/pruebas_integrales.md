# Pruebas integrales del Sprint 2

Fecha de ejecución: 2026-10-26 20:34:09

| ID | Prueba | OK/FALLA | Detalle |
|---|---|---|---|
| P-01 | El menú muestra Pedidos y no muestra Entradas | OK | Menú del Administrador validado por HTTP. |
| P-02 | La lista inicial muestra solo pedidos Por recibir y el pie "Mostrando…" | OK | Las filas iniciales corresponden solo a pedidos Pendiente y se muestra el pie de paginación. |
| P-04 | Buscar por número con # encuentra el pedido | OK | La búsqueda por número con # mostró el pedido esperado. |
| P-05 | Buscar por número sin # encuentra el pedido | OK | La búsqueda por número sin # mostró el pedido esperado. |
| P-06 | Buscar por nombre de proveedor | OK | La búsqueda por nombre de proveedor mostró el pedido pendiente. |
| P-07 | Filtro Recibido: solo recibidos y sin botón Cancelar pedido | OK | El pedido recibido aparece sin botón Cancelar pedido. |
| P-08 | Filtro Cancelado | OK | El filtro Cancelado muestra el pedido cancelado de prueba. |
| P-09 | Filtro Todos | OK | El filtro Todos muestra pedidos Pendiente, Recibida y Cancelada del proveedor. |
| P-10 | Número inexistente: "No se encontraron pedidos" | OK | La búsqueda con número inexistente mostró el mensaje esperado. |
| P-11 | Ver detalle de un pedido recibido muestra recepción y observación del daño | OK | El historial recibido muestra al almacenista y la observación del producto dañado. |
| P-12 | Nuevo pedido: el combo solo trae proveedores activos | OK | El combo contiene exactamente los proveedores activos, incluido el creado para esta prueba. |
| P-13 | productosProveedor devuelve JSON válido (sin BOM) solo con productos activos del proveedor | OK | JSON sin BOM; devolvió los tres productos activos y excluyó el producto inactivo. |
| P-16 | Guardar sin proveedor → "Elige un proveedor" y no se crea nada | OK | El POST fue rechazado y no insertó pedidos. |
| P-17 | Sin productos → "Agrega al menos un producto" | OK | El POST fue rechazado y no insertó pedidos. |
| P-18 | Cantidad 0 → "La cantidad debe ser mayor a 0" | OK | El POST fue rechazado y no insertó pedidos. |
| P-19 | Costo 0 → "El costo debe ser mayor a 0" | OK | El POST fue rechazado y no insertó pedidos. |
| P-21 | Cantidad que supera Stock_Maximo → mensaje de la base y no se crea nada | OK | El trigger rechazó la cantidad y el pedido no quedó persistido. |
| P-22 | Pedido válido: mensaje "Pedido #N registrado…", Estado Pendiente, total recalculado por el servidor (manda un total falso y comprueba que se ignora) y Nombre_Producto/Nombre_Proveedor llenos por los triggers | OK | Pedido #489: Pendiente, total $51.00; el total falso fue ignorado y los triggers copiaron nombres. |
| P-24 | correos.log tiene el correo del pedido con productos y total | OK | El tramo nuevo del log contiene el pedido, sus productos y el total recalculado. |
| P-27 | Cancelar un pedido Por recibir → Cancelada y mensaje | OK | Pedido #490 cancelado por HTTP; el auxiliar mantiene una búsqueda múltiple. |
| P-25 | correos.log tiene el correo de cancelación | OK | El log nuevo contiene el correo de cancelación del pedido. |
| P-28 | Cancelar un pedido Recibido → "El pedido ya no está por recibir" y sigue Recibida | OK | El intento de cancelar un pedido recibido se rechazó y conservó Recibida. |
| E-01 | El menú muestra Entradas | OK | El menú de Almacenista muestra Entradas. |
| E-02 | La lista inicial "Pedidos por recibir" no tiene botón Recibir | OK | La lista inicial está visible y no contiene botón Recibir. |
| E-03 | Buscar por número abre el pedido y NO muestra la lista de abajo | OK | El pedido se abrió y no se renderizó la tabla inferior. |
| E-05 | Buscar por proveedor muestra la lista filtrada | OK | El proveedor devuelve los dos pedidos pendientes de prueba en la tabla filtrada. |
| E-06 | Buscar por producto encuentra el pedido que lo trae | OK | La búsqueda por producto encontró y abrió el pedido correcto. |
| E-07 | Buscar un pedido Recibido → "Pedido no encontrado" | OK | La búsqueda de pedido Recibido fue rechazada. |
| E-08 | Buscar un pedido Cancelado → "Pedido no encontrado" | OK | La búsqueda de pedido Cancelado fue rechazada. |
| E-11 | Registrar con Dañado y sin motivo → rechazado; el pedido sigue Pendiente y el stock no cambia | OK | Reporte sin motivo rechazado; el pedido sigue Pendiente y el inventario no cambió. |
| E-12 | Cantidad recibida mayor a la pedida → rechazado | OK | El reporte con cantidad mayor a la pedida fue rechazado. |
| E-18 | Registrar entrada (producto 1 completo; producto 2 Dañado 1 de 2 con motivo; producto 3 Faltante 0 de 3 con motivo) → mensaje "Entrada registrada…" y Estado Recibida | OK | La entrada se registró por HTTP y el pedido pasó a Recibida. |
| E-19 | Volver a buscar ese pedido → "Pedido no encontrado" | OK | El pedido recibido ya no aparece en la búsqueda de entradas. |
| E-21 | Stock: el producto 1 sube lo pedido, el producto 2 sube 1, el producto 3 no sube; los movimientos tienen "Pedido #N" y "Pedido #N - Dañado: …" | OK | El stock refleja 2, 1 y 0 piezas; los movimientos registran la entrada completa y el daño. |
| E-22 | detalle_compras guardó Cantidad_Recibida, Incidencia y Motivo_Incidencia | OK | detalle_compras conserva cantidades, incidencias y motivos enviados por HTTP. |
| E-23 | compras guardó Fecha_Recepcion e Id_Empleado_Recibe del almacenista | OK | Fecha_Recepcion e Id_Empleado_Recibe corresponden al Almacenista de prueba. |
| E-20 | El Administrador ve en "Ver" el daño y quién recibió | OK | La vista administrativa de Ver muestra la observación del daño y el Almacenista. |
| R-01 | Almacenista en ?c=pedido&a=lista → acceso negado | OK | La ruta redirigió a Inicio y no mostró Pedidos. |
| R-02 | Vendedor en ?c=pedido&a=lista → acceso negado | OK | La ruta redirigió a Inicio y no mostró Pedidos. |
| R-03 | Vendedor en ?c=entrada&a=index → acceso negado | OK | La ruta redirigió a Inicio y no mostró Entradas. |
| R-04 | Administrador en ?c=entrada&a=index → acceso negado | OK | La ruta redirigió a Inicio y no mostró Entradas. |
| R-05 | Sin sesión, ?c=pedido&a=lista → manda al login | OK | La sesión anónima terminó en el formulario de login. |
| A-01 | Agregar proveedor sin correo → "El correo es obligatorio" | OK | El formulario de alta rechazó el proveedor sin correo. |
| A-02 | Editar proveedor borrando el correo → mismo mensaje | OK | El formulario de edición rechazó el correo vacío. |
| T-01 | Ejecuta C:\xampp\php\php.exe tests\run.php e incluye el total de OK/FALLA | FALLA | La suite reportó fallas: 60 pruebas; 58 OK; 2 FALLA; 0 OMITIDA. Detalle: HU5-09       \| HU-5       \| Eliminar producto con historial y conservar los datos de venta: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'Codigo_Producto' in 'SELECT' \| FALLA / HU6-06       \| HU-6       \| Eliminar proveedor con compras y conservar sus datos en el historial: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'RFC_Proveedor' in 'SELECT' \| FALLA |

44 de 45 pruebas OK

Limpieza: OK

## Pruebas manuales pendientes

P-03, P-14, P-15, P-20, P-23, P-26, E-04, E-09, E-10, E-13, E-14, E-15, E-16, E-17.
