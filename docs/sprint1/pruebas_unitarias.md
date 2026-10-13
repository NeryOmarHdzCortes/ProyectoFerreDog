# Pruebas unitarias del Sprint 1

## Ejecución

Desde la raíz del proyecto:

```powershell
C:\xampp\php\php.exe tests\run.php
```

La suite carga la configuración existente desde `.env`. Los casos que escriben datos
usan una transacción y ejecutan `rollBack()` en `finally`.

## Resultado de la última ejecución

| ID | Historia | Qué valida | Resultado |
|---|---|---|---|
| HU1-01 | HU-1 | Búsqueda por correo y autenticación de un usuario activo; rechazo del inactivo. | OK |
| HU1-02 | HU-1 | Verificación del hash de `Ferredog#2026` y rechazo de una contraseña incorrecta. | OK |
| HU1-03 | HU-1 | Incremento de intentos fallidos y bloqueo al quinto intento. | FALLA |
| HU1-04 | HU-1 | Código de recuperación vigente, expirado y limpiado al cambiar la contraseña. | OK |
| HU1-05 | HU-1 | Escritura del código en el log con `MAIL_MODO=prueba`; restaura el modo y el log al terminar. | OK |
| HU4-01 | HU-4 | Crear/leer usuario, detectar nombre y correo existentes y excluir al propio usuario al editar. | OK |
| HU4-02 | HU-4 | Búsqueda de usuario por nombre de usuario. | OK |
| HU4-03 | HU-4 | Activar/desactivar y traducir el estado a `Activo`/`Inactivo`. | OK |
| HU5-01 | HU-5 | Crear producto con inventario y editar sus datos. | OK |
| HU5-02 | HU-5 | Detectar código duplicado y buscar por nombre o código. | OK |
| HU5-03 | HU-5 | Paginación de productos limitada a diez elementos. | OK |
| HU5-04 | HU-5 | Producto inexistente, rechazo de precio cero/negativo y desactivación. | OK |
| HU5-05 | HU-5 | Mostrar existencias cero cuando el producto no tiene fila en inventario. | OK |
| HU6-01 | HU-6 | Crear/editar proveedor, detectar nombre/correo duplicados y excluir al proveedor editado. | OK |
| HU6-02 | HU-6 | Desactivar proveedor y comprobar `Estado = 0`. | OK |
| HU6-03 | HU-6 | Contar y listar productos que surte un proveedor. | OK |
| TEC-01 | Técnico | Consultar `vw_usuarios`, `vw_catalogo_productos` y comprobar los tres roles. | OK |

**Total:** 17 pruebas; 16 OK, 1 FALLA, 0 OMITIDA.  
**Aserciones:** 108 pasadas, 1 fallida.

### Diagnóstico de HU1-03

La prueba guardó `Fecha_Bloqueo=2026-10-13 09:12:33`. PHP reportó la zona horaria
`Europe/Berlin` y la hora `2026-10-13 17:12:33`; al interpretar el valor local de la
base de datos con la zona horaria de PHP, calculó 28 800 segundos transcurridos.
Por ello `Usuario::autenticar()` trató el bloqueo como vencido y permitió el acceso.
La corrección correspondería al manejo de zona horaria en
`app/models/Usuario.php` o a alinear la zona horaria de PHP y MariaDB.
