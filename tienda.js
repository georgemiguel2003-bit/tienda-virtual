/* ============================================================
   tienda.js — Modal de detalle + Agregar al carrito
   Consume el SERVICIO WEB Carrito_agregar.php con fetch.
   window.ID_USUARIO se inyecta desde la sesion PHP en index.php
   ============================================================ */

// ---------- ABRIR / CERRAR MODAL DE DETALLE ----------
function abrirModal(boton) {
  const d = boton.dataset; // lee los data-* de la tarjeta
  document.getElementById('m-id').value          = d.id;
  document.getElementById('m-nombre').textContent = d.nombre;
  document.getElementById('m-precio').textContent = 'S/. ' + parseFloat(d.precio).toFixed(2);
  document.getElementById('m-imagen').src         = d.imagen;
  document.getElementById('m-imagen').alt         = d.nombre;
  document.getElementById('m-categoria').textContent = d.categoria;
  document.getElementById('m-marca').textContent  = d.marca || '—';
  document.getElementById('m-desc').textContent   = d.descripcion || 'Sin descripción.';

  const stock = parseInt(d.stock, 10);
  const sp = document.getElementById('m-stock');
  sp.textContent = stock > 0 ? ('En stock (' + stock + ')') : 'Agotado';
  sp.className   = 'meta-valor ' + (stock > 0 ? 'en-stock' : 'sin-stock');

  const btn = document.getElementById('m-btn-agregar');
  btn.disabled = stock <= 0;
  btn.classList.toggle('disabled', stock <= 0);

  document.getElementById('modal-producto').style.display = 'flex';
}

function cerrarModal() {
  document.getElementById('modal-producto').style.display = 'none';
}

// Cerrar al hacer clic fuera de la caja
window.addEventListener('click', function (e) {
  const modal = document.getElementById('modal-producto');
  if (e.target === modal) cerrarModal();
});

// ---------- AGREGAR AL CARRITO (consume servicio web) ----------
async function agregarAlCarrito() {
  const productoId = parseInt(document.getElementById('m-id').value, 10);

  // Si no hay sesion, llevar al login
  if (!window.ID_USUARIO || window.ID_USUARIO <= 0) {
    if (confirm('Debes iniciar sesión para agregar productos. ¿Ir al login?')) {
      window.location.href = 'login.php';
    }
    return;
  }

  // Tu servicio Carrito_agregar.php espera POST: id_usuario, producto_id
  const datos = new FormData();
  datos.append('id_usuario',  window.ID_USUARIO);
  datos.append('producto_id', productoId);

  try {
    const res  = await fetch('Carrito_agregar.php', { method: 'POST', body: datos });
    const data = await res.json();
    alert(data.mensaje);
    if (data.exito) cerrarModal();
  } catch (e) {
    alert('Error de conexión con el servicio web.');
  }
}
