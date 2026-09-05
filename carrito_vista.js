/* ============================================================
   carrito_vista.js — Vista del carrito
   Consume los SERVICIOS WEB:
     Carrito_listar.php     (listar)
     Carrito_actualizar.php (cambiar cantidad)
     Carriyo_eliminar.php   (eliminar)
   window.ID_USUARIO se inyecta desde la sesion PHP en carrito.php
   ============================================================ */

async function cargarCarrito() {
  const cont = document.getElementById('lista-carrito');

  const datos = new FormData();
  datos.append('id_usuario', window.ID_USUARIO);

  try {
    const res  = await fetch('Carrito_listar.php', { method: 'POST', body: datos });
    const data = await res.json();

    if (!data.exito) { cont.innerHTML = '<p>' + (data.mensaje || 'Error') + '</p>'; return; }
    if (data.items.length === 0) {
      cont.innerHTML = '<p class="carrito-vacio">Tu carrito está vacío.</p>';
      return;
    }

    let filas = '';
    data.items.forEach(item => {
      filas += `
        <tr>
          <td><img src="${item.imagen}" alt="" class="carrito-img"></td>
          <td>${item.nombre}</td>
          <td>S/. ${parseFloat(item.precio).toFixed(2)}</td>
          <td>
            <input type="number" min="1" value="${item.cantidad}" class="carrito-cant"
                   onchange="actualizarCantidad(${item.detalle_id}, this.value)">
          </td>
          <td class="sub-${item.detalle_id}">S/. ${parseFloat(item.subtotal).toFixed(2)}</td>
          <td><button class="btn-quitar" onclick="eliminarItem(${item.detalle_id})">Quitar</button></td>
        </tr>`;
    });

    cont.innerHTML = `
      <table class="tabla-carrito">
        <thead>
          <tr><th></th><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>
        </thead>
        <tbody>${filas}</tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="total-label">Total</td>
            <td class="total-monto">S/. ${parseFloat(data.total).toFixed(2)}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>`;
  } catch (e) {
    cont.innerHTML = '<p>Error de conexión con el servicio web.</p>';
  }
}

async function actualizarCantidad(detalleId, cantidad) {
  cantidad = parseInt(cantidad, 10);
  if (cantidad < 1) cantidad = 1;

  // Tu servicio Carrito_actualizar.php espera: detalle_id, cantidad
  const datos = new FormData();
  datos.append('detalle_id', detalleId);
  datos.append('cantidad',   cantidad);

  try {
    const res  = await fetch('Carrito_actualizar.php', { method: 'POST', body: datos });
    const data = await res.json();
    if (data.exito) {
      cargarCarrito(); // recarga para recalcular subtotales y total
    } else {
      alert(data.mensaje);
    }
  } catch (e) {
    alert('Error de conexión con el servicio web.');
  }
}

async function eliminarItem(detalleId) {
  if (!confirm('¿Quitar este producto del carrito?')) return;

  // Tu servicio Carriyo_eliminar.php espera: detalle_id
  const datos = new FormData();
  datos.append('detalle_id', detalleId);

  try {
    const res  = await fetch('Carriyo_eliminar.php', { method: 'POST', body: datos });
    const data = await res.json();
    if (data.exito) {
      cargarCarrito();
    } else {
      alert(data.mensaje);
    }
  } catch (e) {
    alert('Error de conexión con el servicio web.');
  }
}

// Cargar al abrir la pagina
cargarCarrito();
