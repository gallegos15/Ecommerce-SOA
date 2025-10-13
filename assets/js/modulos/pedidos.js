let tblPendientes, tblFinalizados, tblProceso;

const myModal = new bootstrap.Modal(document.getElementById('modalPedidos'));

document.addEventListener('DOMContentLoaded', function () {
    tblPendientes = $('#tblPendientes').DataTable({
        ajax: {
            url: base_url + 'pedidos/listarPedidos',
            dataSrc: ''
        },
        columns: [
            { data: 'id_transaccion' },
            { data: 'monto' },
            { data: 'estado' },
            { data: 'fecha' },
            { data: 'email' },
            { data: 'nombre' },
            { data: 'apellido' },
            { data: 'direccion' },
            { data: 'accion' }
        ],
        language,
        dom,
        buttons
    });
    tblProceso = $('#tblProceso').DataTable({
        ajax: {
            url: base_url + 'pedidos/listarProceso',
            dataSrc: ''
        },
        columns: [
            { data: 'id_transaccion' },
            { data: 'monto' },
            { data: 'estado' },
            { data: 'fecha' },
            { data: 'email' },
            { data: 'nombre' },
            { data: 'apellido' },
            { data: 'direccion' },
            { data: 'accion' }
        ],
        language,
        dom,
        buttons
    });
    tblFinalizados = $('#tblFinalizados').DataTable({
        ajax: {
            url: base_url + 'pedidos/listarFinalizados',
            dataSrc: ''
        },
        columns: [
            { data: 'id_transaccion' },
            { data: 'monto' },
            { data: 'estado' },
            { data: 'fecha' },
            { data: 'email' },
            { data: 'nombre' },
            { data: 'apellido' },
            { data: 'direccion' },
            { data: 'accion' }
        ],
        language,
        dom,
        buttons
    });
});


function cambiarProceso(idPedido, proceso) {
    Swal.fire({
        title: 'Aviso?',
        text: "¿Está seguro de cambiar el estado?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, cambiar',
    }).then((result) => {
        if (result.isConfirmed) {
            // El controlador espera un único parámetro con formato "id,proceso"
            const url = base_url + "pedidos/update/" + idPedido + "," + proceso;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    if (res.icono === 'success') {
                        tblPendientes.ajax.reload();
                        tblProceso.ajax.reload();
                        tblFinalizados.ajax.reload();
                    }
                    Swal.fire('Aviso', res.msg.toUpperCase(), res.icono);
                }
            }
        }
    });
}

function verPedido (idPedido){
    // Endpoint real en el controlador: Clientes::verPedido
    fetch(base_url + 'clientes/verPedido/' + idPedido)
        .then(resp => {
            if (!resp.ok) throw new Error('HTTP error ' + resp.status);
            return resp.json();
        })
        .then(res => {
            console.log('verPedido response:', res);
            let html = '';
            if (Array.isArray(res.productos) && res.productos.length > 0) {
                res.productos.forEach(item => {
                    const precio = parseFloat(item.precio) || 0;
                    const cantidad = parseInt(item.cantidad) || 0;
                    const subTotal = (precio * cantidad).toFixed(2);
                    html += `<tr>
                        <td>${item.producto}</td>
                        <td><span class="badge bg-warning">${res.moneda} ${precio.toFixed(2)}</span></td>
                        <td><span class="badge bg-primary">${cantidad}</span></td>
                        <td>${res.moneda} ${subTotal}</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No hay productos para este pedido</td></tr>';
            }

            const tbody = document.querySelector('#tableListaProductos tbody');
            if (tbody) {
                tbody.innerHTML = html;
                myModal.show();
            } else {
                console.error('No se encontró el tbody de #tableListaProductos');
                Swal.fire('Error', 'No se pudo mostrar los productos del pedido (tabla no encontrada)', 'error');
            }
        }).catch(err => {
            console.error('Error en verPedido:', err);
            Swal.fire('Error', 'No se pudo obtener los productos del pedido', 'error');
        });
}
