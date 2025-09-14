const tableLista = document.querySelector('#tableListaProductos tbody');
document .addEventListener('DOMContentLoaded', function() {
    getListaProductos()
});
function getListaProductos() {
    const url = base_url + 'principal/listaProductos';
    const hhtp = new XMLHttpRequest();
    hhtp.open('POST', url, true);
    hhtp.send(JSON.stringify(listaCarrito));
    hhtp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            res.productos.forEach(producto => {
                html += `<tr>
                            <td>
                            <img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="70px">
                            </td>
                            <td>${producto.nombre}</td>
                            <td>${producto.descripcion}</td>
                            <td><span class="badge bg-warning">${res. moneda + ' ' + producto.precio}</span></td>
                            <td><span class="badge bg-primary">${producto.cantidad}</span></td>
                            <td>${producto.subTotal}</td>
                        </tr>`;
            });
            tableLista.innerHTML = html;
            document.querySelector('#totalProducto').textContent = 'TOTAL A PAGAR: ' + res. moneda + ' ' + res.total;
        }
    }
}