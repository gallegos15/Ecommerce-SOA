const tableLista = document.querySelector('#tableListaDeseo tbody');
document .addEventListener('DOMContentLoaded', function() {
    getListaDeseo()
});
//ESTO PONE DATOS A LA TABLA
function getListaDeseo() {
    const url = base_url + 'principal/getListaDeseo';
    const hhtp = new XMLHttpRequest();
    hhtp.open('POST', url, true);
    hhtp.send(JSON.stringify(listaDeseo));
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
                            <td>
                            <button type="button" class="btn btn-danger btnEliminarDeseo" prod="${producto.id}"><i class="fas fa-trash"></i></button>
                            <button type="button" class="btn btn-primary"><i class="fas fa-cart-plus"></i></button>
                            </td>   
                        </tr>`;
            });
            tableLista.innerHTML = html;
            btnEliminarDeseo();
        }
    }
}
function btnEliminarDeseo() {
    let listaEliminar = document.querySelectorAll('.btnEliminarDeseo');
    for (let i = 0; i < listaEliminar.length; i++) {
            listaEliminar[i].addEventListener('click', function() {
            let idProducto = listaEliminar[i].getAttribute('prod');
            eliminarListaDeseo(idProducto);
        });
    }
}
function eliminarListaDeseo(idProducto) {
    for (let i = 0; i < listaDeseo.length; i++) {
        if (listaDeseo[i]['idProducto'] == idProducto) {
            listaDeseo.splice(i, 1);
        }
    }
    localStorage.setItem("listaDeseo", JSON.stringify(listaDeseo));
    getListaDeseo();
    cantidadDeseo();
    Swal.fire({
        title: "Aviso",
        text: "Producto eliminado de tu lista de deseos",
        icon: "success",
    });
}