# proyecto: Ferredog — sistema web de gestión de inventario

## descripcion
Sistema web para la gestión de inventario, ventas, compras, usuarios y proveedores de la ferretería Ferredog.

## comandos

## stack (tecnologias)
- backend: php
- base de datos: mysql (MariaDB en XAMPP)
- frontend: html, css, bootstrap v5 y javascript

## convenciones (como se debe escribir el código)
- Arquitectura modelo-vista-controlador (MVC)
- Consultas SQL siempre parametrizadas (PDO con consultas preparadas)
- Contraseñas con `password_hash()` y `password_verify()`, nunca en texto plano
- Operaciones que escriben en más de una tabla se hacen dentro de una transacción (`beginTransaction()` / `commit()` / `rollBack()`)

### Frontend
- El JavaScript debe escribirse en archivos independientes dentro de `public/assets/js/`.
- No insertar JavaScript en línea dentro de archivos PHP o HTML.
- Cargar los scripts externos con `defer` para que el HTML se procese antes de ejecutarlos.

## Reglas
- Implementar solo lo que se pida
- No modificar archivos de la carpeta docs/ ni AGENTS.md
- Seguir los requisitos, el diseño y las tareas de la carpeta del sprint indicado en docs/
- no uses composer dentro del proyecto
- no crees la base de datos, ya está diseñada por separado en el SGBD

## nomenclatura
- Clases: PascalCase (`UsuarioController`)
- Archivos de clases: igual que la clase (`UsuarioController.php`)
- Métodos y variables: camelCase (`obtenerUsuario`)
- Constantes: UPPER_SNAKE_CASE (`MAX_INTENTOS_LOGIN`)
- Nombres en español, sin abreviaturas
- Tablas y columnas de MySQL: usar exactamente los nombres reales de la base `ferredog`, sin renombrar:
  - Tablas: `usuario`, `empleados`, `productos`, `inventario`, `movimiento_inventario`, `proveedor`, `categoria`, `marcas`, `unidad_medida`, `promociones`, `producto_promocion`, `cliente`, `ventas`, `detalle_ventas`, `factura_cliente`, `metodo_pago`, `compras`, `detalle_compras`
  - Ojo con plurales y singulares mezclados: `empleados` y `marcas` van en plural, `usuario`, `proveedor` y `categoria` en singular
  - Columnas como en la base: `Id_Usuario`, `Id_Empleado`, `Fecha_Alta`, `Codigo_Barras`, etc.

## Documentación
- Comentarios en español explicando bloques de lógica importante (no línea por línea)
- Actualizar el README cuando se agregue un módulo nuevo
