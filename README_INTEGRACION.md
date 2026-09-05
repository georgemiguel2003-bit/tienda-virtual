# Integración — Login + Carrito (sobre tu proyecto `tienda_virtual`)

Estos archivos se adaptan a TU proyecto: clase `Conexion`, base `tienda_virtual`,
respuestas `{exito, mensaje}`, tus estilos (`style.css`) y tus servicios de carrito.

## Qué hace cada archivo

### Nuevos
- `02_auth_y_carrito.sql` — crea la tabla `usuarios`, el **procedimiento almacenado** `sp_validar_usuario`, y formaliza las tablas `carrito` y `carrito_detalle`.
- `Login_validar.php` — **servicio web** de login: invoca `sp_validar_usuario` y responde JSON.
- `login.php` — formulario; **consume** `Login_validar.php` (cURL), guarda la **sesión** en PHP y redirige.
- `logout.php` — destruye la sesión.
- `sesion.php` — protege páginas que requieren login.
- `Carrito_listar.php` — **servicio web** que faltaba: lista el carrito + total.
- `carrito.php` — **vista del carrito**; consume listar/actualizar/eliminar.
- `tienda.js` — modal de detalle + agregar al carrito (consume `Carrito_agregar.php`).
- `carrito_vista.js` — lógica de la vista del carrito.
- `extra.css` — estilos del login, modal y carrito (usa tu misma paleta).

### Reemplazan a los tuyos
- `index.php` — igual que el tuyo (búsqueda + paginado intactos) pero con: sesión, botón **LogIn / Cerrar sesión / Carrito** en el header, y la tarjeta de producto ahora abre el **MODAL de detalle**.
- `detalle.php` — igual que el tuyo, pero "Agregar al carrito" ahora **consume el servicio web** (antes usaba `localStorage`) y reconoce la sesión.

### Sin cambios (siguen igual)
- `Conexion.php`, `Carrito_agregar.php`, `Carrito_actualizar.php`, `Carriyo_eliminar.php`, `style.css`, `img/`.

## Pasos en XAMPP

1. **Copia los archivos nuevos y los reemplazos** dentro de tu carpeta del proyecto en `htdocs` (la misma donde están `index.php` y `Conexion.php`). Deja los archivos sin cambios como están.

2. **Ejecuta el SQL**: abre phpMyAdmin → base `tienda_virtual` → pestaña SQL → pega y ejecuta `02_auth_y_carrito.sql`.

3. **Inicia Apache y MySQL** en el panel de XAMPP.

4. **Prueba el flujo**:
   - Abre `http://localhost/TU_CARPETA/index.php` → arriba verás **LogIn**.
   - Entra con `demo@tienda.com` / `123456` → te redirige a la tienda con tu nombre arriba.
   - Clic en cualquier producto → se abre el **modal de detalle** → "Agregar al carrito".
   - Clic en **Carrito** → ves la lista, cambias cantidades y eliminas.
   - **Cerrar sesión** → vuelve a la tienda mostrando "LogIn".

## Nota sobre `id_usuario` y sesión
La página inyecta `window.ID_USUARIO` desde la sesión PHP y se envía a tus servicios.
Tus 3 servicios de carrito quedan **sin tocar** porque ya reciben `id_usuario` / `detalle_id` por POST.

## Para el INFORME (punto 4)
- **4.1 Funcionalidades** (capturas): formulario de login, tienda con sesión iniciada, modal de detalle, vista del carrito.
- **4.2 Código relevante**: `Login_validar.php`, el SP `sp_validar_usuario` del `.sql`, `Carrito_listar.php`, `tienda.js`.
- **4.3 Problemas y soluciones**: por ejemplo, cómo el login pasó de no existir a consumir un servicio web con procedimiento almacenado; y cómo el "agregar al carrito" pasó de `localStorage` a consumir el servicio web (requisito del enunciado).
- **3 Recursos Tecnológicos**: el diagrama navegador → servicio web → procedimiento almacenado → base de datos.
