const tableLista = document.querySelector('#tableListaProductos tbody');
const tblPendientes = document.querySelector('#tblPendientes');

document.addEventListener('DOMContentLoaded', function () {
    if (tableLista) getListaProductos();

    if (tblPendientes) {
        $('#tblPendientes').DataTable({
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
        });
    }
});

function getCarrito() {
    try {
        if (Array.isArray(window.listaCarrito) && window.listaCarrito.length) return window.listaCarrito;
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

    if (!carrito.length) {
        tableLista.innerHTML = '';
        const tot = document.querySelector('#totalProducto');
        if (tot) tot.textContent = 'TOTAL A PAGAR: USD 0.00';
        const cont = document.getElementById('paypal-button-container');
        if (cont) cont.innerHTML = '';
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(carrito));

    xhr.onreadystatechange = function () {
        if (this.readyState !== 4) return;
        if (this.status !== 200) return console.error('HTTP', this.status, this.responseText);

        let res;
        try { res = JSON.parse(this.responseText); } catch (e) { return console.error('Respuesta NO-JSON:', this.responseText); }

        let html = '';
        (res.productos || []).forEach((producto, i) => {
            html += `<tr>
                <td>${i + 1}</td>
                <td><img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="70px"></td>
                <td>${producto.descripcion}</td>
                <td><span class="badge bg-warning">${res.moneda + ' ' + producto.precio}</span></td>
                <td><span class="badge bg-primary">${producto.cantidad}</span></td>
                <td>${producto.subTotal}</td>
            </tr>`;
        });
        tableLista.innerHTML = html;

        const tot = document.querySelector('#totalProducto');
        if (tot) tot.textContent = 'TOTAL A PAGAR: ' + res.moneda + ' ' + res.total;

        window.productosParaRegistrar = (res.productos || []).map(p => ({
            idProducto: p.idProducto ?? p.id ?? null,
            nombre: p.nombre,
            precio: p.precio,
            cantidad: p.cantidad
        }));

        const totalSan = Number(String(res.totalPaypal ?? res.total ?? 0).replace(',', '.'));
        botonPaypal(totalSan);
    };
}

function botonPaypal(total) {
    const cont = document.getElementById('paypal-button-container');
    if (!cont) return;
    cont.innerHTML = '';

    if (!Number.isFinite(total) || total <= 0) return;

    const amountStr = Number(total).toFixed(2);
    const CURRENCY = 'USD';

    window.paypal.Buttons({
        style: { shape: "rect", layout: "vertical", color: "gold", label: "paypal" },

        async createOrder(data, actions) {
            return actions.order.create({
                intent: "CAPTURE",
                purchase_units: [{
                    amount: { currency_code: CURRENCY, value: amountStr, breakdown: { item_total: { currency_code: CURRENCY, value: amountStr } } },
                    items: [{ name: "Carrito", quantity: "1", unit_amount: { currency_code: CURRENCY, value: amountStr } }]
                }],
                application_context: { shipping_preference: "NO_SHIPPING" }
            });
        },

        async onApprove(data, actions) {
            try {
                const orderData = await actions.order.capture();
                const resp = await fetch(base_url + 'clientes/registrarPedido', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pedido: orderData, productos: window.productosParaRegistrar || [] })
                });
                const txt = await resp.text();
                if (!resp.ok) throw new Error(`HTTP ${resp.status}: ${txt}`);
                const payload = JSON.parse(txt);

                if (payload.ok) {
                    localStorage.removeItem('listaCarrito');
                    window.listaCarrito = [];
                    window.productosParaRegistrar = [];
                    getListaProductos();
                    resultMessage('Pedido registrado correctamente.', 'success');
                } else resultMessage('No se pudo registrar el pedido.', 'danger');
            } catch (err) {
                console.error(err);
                resultMessage('Error al registrar el pedido: ' + (err.message || err), 'danger');
            }
        },

        onError(err) {
            console.error('PayPal error:', err);
            resultMessage('Error con PayPal.', 'danger');
        }
    }).render('#paypal-button-container');
}

function resultMessage(message, variant = 'info', timeout = 4000) {
    const container = document.querySelector('#result-message');
    if (!container) return;

    container.innerHTML = `
        <div class="alert alert-${variant} alert-dismissible fade show" role="alert" style="margin-top:10px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    const alertEl = container.querySelector('.alert');
    window.setTimeout(() => {
        if (!alertEl) return;
        alertEl.classList.remove('show');
        window.setTimeout(() => { if (alertEl && alertEl.parentNode) alertEl.parentNode.removeChild(alertEl); }, 200);
    }, timeout);
}

function verPedido(idPedido) {
    const mPedido = new bootstrap.Modal(document.getElementById('modalPedido'), { backdrop: 'static', keyboard: true });
    fetch(base_url + 'clientes/verPedido/' + idPedido)
        .then(resp => resp.json())
        .then(res => {
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
        }).catch(err => console.error(err));
}
