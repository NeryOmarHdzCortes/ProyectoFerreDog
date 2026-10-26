# Pruebas unitarias del Sprint 2

## Ejecución

Desde la raíz del proyecto:

```powershell
C:\xampp\php\php.exe tests\run.php
```

Los casos de pedidos crean sus proveedores, productos, pedidos y detalles dentro
de transacciones y ejecutan `rollBack()` en `finally`. Para las llaves foráneas
se consultan únicamente registros de referencia de empleado y catálogo. La prueba
de correo restaura `MAIL_MODO` y el contenido anterior de `storage/logs/correos.log`.

## Resultado de la última ejecución

| ID | Historia | Qué valida | Resultado |
|---|---|---|---|
| TEC-PED-01 | Técnico | La lista devuelve pedidos Pendiente y excluye los Recibidos. | OK |
| TEC-PED-02 | Técnico | Cancela pedidos Pendiente, pero no pedidos Recibidos. | OK |
| TEC-PED-03 | Técnico | Inserta un pedido válido y verifica que los triggers copien los nombres del proveedor y producto. | OK |
| TEC-PED-04 | Técnico | Rechaza cantidad cero, costo cero y pedidos sin productos usando las validaciones del controlador. | OK |
| TEC-PED-05 | Técnico | La recepción completa incrementa existencias por la cantidad pedida. | OK |
| TEC-PED-06 | Técnico | La recepción con daño incrementa solo las existencias recibidas y conserva el motivo en el movimiento. | OK |
| TEC-PED-07 | Técnico | Un pedido Recibido ya no aparece en las búsquedas por número ni proveedor. | OK |
| TEC-PED-08 | Técnico | El correo en modo prueba se escribe en el log y restaura su contenido al terminar. | OK |
| HU6-06 | HU-6 | Elimina proveedor con compras y conserva sus datos en el historial. | FALLA |

**Total:** 53 pruebas; 52 OK, 1 FALLA, 0 OMITIDA.  
**Aserciones:** 279 pasadas, 0 fallidas.

Los otros 44 casos existentes de la suite pasaron.

### Diagnóstico de HU6-06

El caso preexistente intenta escribir un RFC de 11 caracteres en `proveedor.RFC`,
pero la columna de la base de datos configurada para las pruebas tiene un límite
menor. MariaDB responde `SQLSTATE[22001]`, error 1406 (`Data too long for column
'RFC'`). No corresponde a las pruebas de pedidos ni se modificó la base de datos.
