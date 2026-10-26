# Tareas del Sprint 2 — Ferredog

Las tareas siguen el Sprint Backlog (HU y número de tarea).
Reglas: cada vista debe seguir su wireframe de `docs/sprint2/wireframes/` (tabla en design.md) y su diagrama de `docs/sprint2/diagramas/`. Hacer **una tarea a la vez**, decir qué archivos se crearon o modificaron y cómo probarla, y esperar confirmación ("confirmado") antes de hacer commit y pasar a la siguiente.

## 0. Base de datos (Enc. Base de Datos) — YA HECHO, no lo repite el agente
- [x] 0.1 Columnas `Cantidad_Recibida`, `Incidencia`, `Motivo_Incidencia` en `detalle_compras`
- [x] 0.2 Columnas `Fecha_Recepcion`, `Id_Empleado_Recibe`, `Motivo_Cancelacion` en `compras`
- [x] 0.3 Trigger `trg_compra_recibida` suma la cantidad recibida y guarda el motivo; bloquea Recibido/Cancelado
- [x] 0.4 Script en `Base de datos/sprint2_ferredog.sql`

## HU-7 — Pedidos a proveedores (Administrador)
- [ ] **HU-7 T1** Menú "Pedidos" activo para el Administrador (quitar "Entradas" de su menú). `PedidoController` (lista, cancelar), `Pedido.php` (listarPorRecibir, cancelar), vista `pedidos/lista.php` (W-10) con la ventana de confirmación (W-11), `pedidos.js` y `Correo::enviarCancelacion()`. (Req. 8 y 9)
- [ ] **HU-7 T2** Nuevo pedido: vista `pedidos/formulario.php` (W-12), acción `productosProveedor` (JSON), cálculo de subtotales y total, validaciones en JS y en el controlador, guardado en transacción y `Correo::enviarPedido()`. (Req. 10)

## HU-3 — Entrada de mercancía (Almacenista)
- [ ] **HU-3 T1** Menú "Entradas" activo para el Almacenista. `EntradaController` (index con búsqueda), vista `entradas/registrar.php` (W-13 y W-14): buscar por número o proveedor solo pedidos Por recibir, encabezado del pedido, tabla de productos y lista para elegir si hay varios. (Req. 11)
- [ ] **HU-3 T2** Ventana "Reportar faltante o daño" (W-15) en `entradas.js`: tipo, cantidad en buen estado, motivo, aviso "Se sumará N pieza(s)…", observación en la tabla, "Editar reporte" y quitar reporte. (Req. 12)
- [ ] **HU-3 T3** Acción `registrar`: confirmación, validación en el controlador, transacción (detalle + estado Recibida) y mensaje final. (Req. 13)

## Ajustes
- [ ] **AJ-1** Proveedores: correo obligatorio al agregar y editar. (Req. 14)

## Técnico — Pruebas
- [ ] **Técnico T1** `tests/PedidoTest.php` con los casos de design.md → Pruebas; ejecutar `tests\run.php` y escribir el resultado en `docs/sprint2/pruebas_unitarias.md`.
- [ ] **Técnico T2** Probar cada requisito de requirements.md con Administrador y Almacenista (y que el Vendedor no entre) y entregar una tabla: requisito / resultado / observación.
- [ ] **Técnico T3** README: agregar los módulos Pedidos y Entradas y cómo probarlos.

## Criterios de "terminado" para cada tarea
1. Sigue el wireframe y el diagrama.
2. Validación en el navegador **y** en el controlador.
3. Consultas parametrizadas; transacción cuando escribe en más de una tabla.
4. No duplica lo que hacen los triggers.
5. `php -l` sin errores en los archivos tocados.
6. El agente muestra la lista de archivos y los pasos para probar, y NO hace commit hasta "confirmado".
