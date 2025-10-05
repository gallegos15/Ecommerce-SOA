const tableLista = document.querySelector('#tableListaProductos tbody');
const tblPendientes = document.querySelector('#tblPendientes');
document.addEventListener('DOMContentLoaded', function () {
  if (tableLista) {
    getListaProductos();
  }
  //cargar datos pendientes con DataTables
  $('#tblPendientes').DataTable( {
    ajax: {
        url: base_url + 'clientes/listarPendientes',
        dataSrc: ''
    },
    columns: [
        { data: 'id_transaccion' },
        { data: 'monto' },
        { data: 'fecha' },
        { data: 'accion' }
    ],
    language,
    dom,
    buttons
} );
});

function getCarrito() {
  try {
    if (Array.isArray(window.listaCarrito) && window.listaCarrito.length) {
      return window.listaCarrito;
    }
    const saved = localStorage.getItem('listaCarrito');
    return saved ? JSON.parse(saved) : [];
  } catch (e) {
    console.warn('No se pudo leer listaCarrito:', e);
    return [];
  }
}

function getListaProductos() {
  const url = base_url + 'principal/listaProductos';
  const carrito = getCarrito();

  // Si no hay productos, limpia vista y PayPal
  if (!carrito.length) {
    tableLista.innerHTML = '';
    const tot = document.querySelector('#totalProducto');
    if (tot) tot.textContent = 'TOTAL A PAGAR: USD 0.00';
    const cont = document.getElementById('paypal-button-container');
    if (cont) cont.innerHTML = '';
    return;
  }

  const hhtp = new XMLHttpRequest();
  hhtp.open('POST', url, true);
  hhtp.setRequestHeader('Content-Type', 'application/json');
  hhtp.send(JSON.stringify(carrito));

  hhtp.onreadystatechange = function () {
    if (this.readyState !== 4) return;

    if (this.status !== 200) {
      console.error('HTTP', this.status, this.responseText);
      resultMessage('No se pudo cargar el carrito.', 'danger');
      return;
    }

    let res;
    try {
      res = JSON.parse(this.responseText);
    } catch (e) {
      console.error('Respuesta NO-JSON:', this.responseText);
      resultMessage('Respuesta inválida del servidor.', 'danger');
      return;
    }

    // Render de filas -> columnas alineadas con <thead>
    let html = '';
    (res.productos || []).forEach((producto, i) => {
      html += `<tr>
        <td>${i + 1}</td> <!-- # -->
        <td>
          <img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="70px">
        </td> <!-- Producto (imagen) -->
        <td>${producto.descripcion}</td> <!-- Descripción -->
        <td><span class="badge bg-warning">${res.moneda + ' ' + producto.precio}</span></td> <!-- Precio -->
        <td><span class="badge bg-primary">${producto.cantidad}</span></td> <!-- Cantidad -->
        <td>${producto.subTotal}</td> <!-- SubTotal -->
      </tr>`;
    });
    tableLista.innerHTML = html;

    // Total
    const tot = document.querySelector('#totalProducto');
    if (tot) tot.textContent = 'TOTAL A PAGAR: ' + res.moneda + ' ' + res.total;

    // Guarda versión enriquecida para registrar (nombre, precio, cantidad, id)
    window.productosParaRegistrar = (res.productos || []).map(p => ({
      idProducto: p.idProducto ?? p.id ?? null,
      nombre: p.nombre,
      precio: p.precio,
      cantidad: p.cantidad
    }));

    // Total San (num > 0)
    const totalSan = Number(String(res.totalPaypal ?? res.total ?? 0).replace(',', '.'));
    botonPaypal(totalSan);
  };
}



function botonPaypal(total) {
  const cont = document.getElementById('paypal-button-container');
  if (!cont) return;
  cont.innerHTML = ''; // evita renders duplicados

  if (!Number.isFinite(total) || total <= 0) {
    // si no hay total válido, no mostramos botón
    return;
  }
  const amountStr = Number(total).toFixed(2);
  const CURRENCY = 'USD'; // cambia si tu cuenta usa otra moneda

  const paypalButtons = window.paypal.Buttons({
    style: {
      shape: "rect",
      layout: "vertical",
      color: "gold",
      label: "paypal",
    },
    message: { amount: 100 },

    async createOrder(data, actions) {
      return actions.order.create({
        intent: "CAPTURE",
        purchase_units: [{
          amount: {
            currency_code: CURRENCY,
            value: amountStr,
            breakdown: {
              item_total: { currency_code: CURRENCY, value: amountStr }
            }
          },
          items: [{
            name: "Carrito",
            quantity: "1",
            unit_amount: { currency_code: CURRENCY, value: amountStr }
          }]
        }],
        application_context: { shipping_preference: "NO_SHIPPING" }
      });
    },

    async onApprove(data, actions) {
      try {
        const orderData = await actions.order.capture();

        // Enviar pedido + productos enriquecidos
        const url = base_url + 'clientes/registrarPedido';
        const resp = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            pedido: orderData,
            productos: window.productosParaRegistrar || []
          })
        });

        const txt = await resp.text();
        if (!resp.ok) throw new Error(`HTTP ${resp.status}: ${txt}`);
        let payload;
        try { payload = JSON.parse(txt); }
        catch { throw new Error('Respuesta no-JSON del backend: ' + txt); }

        console.log('registrarPedido ->', payload);

        if (payload.ok) {
          // ✅ BORRAR CARRITO al procesar el pedido
          try { localStorage.removeItem('listaCarrito'); } catch (_) {}
          window.listaCarrito = [];
          window.productosParaRegistrar = [];
          // refresca UI (tabla, total y botón PayPal)
          getListaProductos();

          resultMessage('Pedido registrado correctamente.', 'success');
        } else {
          resultMessage('No se pudo registrar el pedido.', 'danger');
        }
      } catch (err) {
        console.error(err);
        resultMessage('Error al registrar el pedido: ' + (err.message || err), 'danger');
      }
    },

    onError(err) {
      console.error('PayPal error:', err);
      resultMessage('Error con PayPal.', 'danger');
    }
  });

  paypalButtons.render('#paypal-button-container');
}

// 🔔 Alertas Bootstrap auto-cerrables en #result-message
function resultMessage(message, variant = 'info', timeout = 4000) {
  // Asegúrate de tener <div id="result-message"></div> en tu HTML
  const container = document.querySelector('#result-message');
  if (!container) return;

  // Render de la alerta Bootstrap
  container.innerHTML = `
    <div class="alert alert-${variant} alert-dismissible fade show" role="alert" style="margin-top:10px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
  const alertEl = container.querySelector('.alert');

  // Auto-cerrar tras X ms (sin depender de Bootstrap JS)
  window.setTimeout(() => {
    if (!alertEl) return;
    alertEl.classList.remove('show'); // inicia fade-out
    // quita del DOM tras la animación
    window.setTimeout(() => {
      if (alertEl && alertEl.parentNode) alertEl.parentNode.removeChild(alertEl);
    }, 200); // tiempo del fade (~.2s por defecto)
  }, timeout);
}

function verPedido(idPedido) {
    var mPedido = new bootstrap.Modal(document.getElementById('modalPedido'))
    const url = base_url + 'clientes/verPedido/' + idPedido;
    const hhtp = new XMLHttpRequest();
    hhtp.open('GET', url, true);
    hhtp.send();
    hhtp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            console.log(res); // 👀 para ver qué llega
            let html = '';
            res.productos.forEach(item => {
                const subTotal = (parseFloat(item.precio) * parseInt(item.cantidad)).toFixed(2);
                html += `<tr>                            
                            <td>${item.producto}</td>
                            <td><span class="badge bg-warning">${res.moneda + ' ' + item.precio}</span></td>
                            <td><span class="badge bg-primary">${item.cantidad}</span></td>
                            <td>${subTotal}</td>
                        </tr>`;
            });
            document.querySelector('#tablePedidos tbody').innerHTML = html;
            mPedido.show();
        }
    }
}

