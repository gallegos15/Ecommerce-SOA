const tableLista = document.querySelector("#tableListaDeseo tbody");
document .addEventListener("DOMContentLoaded", function() {
    getListaDeseo()
});

function getListaDeseo() {
    const url = base_url + 'principal/getListaDeseo';
    const hhtp = new XMLHttpRequest();
    hhtp.open('POST', url, true);
    hhtp.send(JSON.stringify(listaDeseo));
    hhtp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            res.forEach(producto => {
                html += `<tr>
                            <td>
                            <img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="70px">
                            </td>
                            <td>${producto.nombre}</td>
                            <td>${producto.descripcion}</td>
                            <td>${producto.precio}</td>
                            <td>${producto.cantidad}</td>
                            <td>
                            <button type="button" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                            <button type="button" class="btn btn-info"><i class="fas fa-cart-plus"></i></button>
                            </td>   
                        </tr>`;
            });
            tableLista.innerHTML = html;
        }
    }
}