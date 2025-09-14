const btnAddDeseo = document.querySelectorAll('.btnAddDeseo');
const btnAddCarrito = document.querySelectorAll('.btnAddCarrito');
const btnDeseo = document.querySelector('#btnCantidadDeseo');
const btnCarrito = document.querySelector('#btnCantidadCarrito');
const verCarrito = document.querySelector('#verCarrito');
const tableListaCarrito = document.querySelector('#tableListaCarrito tbody');

let listaDeseo, listaCarrito;
document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('listaDeseo') != null) {
        listaDeseo = JSON.parse(localStorage.getItem('listaDeseo'));
    }
    if (localStorage.getItem("listaCarrito") != null) {
        listaCarrito = JSON.parse(localStorage.getItem("listaCarrito"));
    }
    for (let i = 0; i < btnAddDeseo.length; i++) {
        btnAddDeseo[i].addEventListener('click', function () {
            let idProducto = btnAddDeseo[i].getAttribute('prod');
            agregarDeseo(idProducto);
        });
    }
    for (let i = 0; i < btnAddCarrito.length; i++) {
        btnAddCarrito[i].addEventListener('click', function () {
            let idProducto = btnAddCarrito[i].getAttribute('prod');
            agregarCarrito(idProducto, 1);
        });
    }
    cantidadDeseo();
    cantidadCarrito();
    var myModal = new bootstrap.Modal(document.getElementById('myModal'))
    verCarrito.addEventListener('click', function () {
        getListaCarrito()
        myModal.show();
    })
});
function agregarDeseo(idProducto) {
    let listaDeseo;
    if (localStorage.getItem("listaDeseo") == null) {
        listaDeseo = [];
    } else {
        listaDeseo = JSON.parse(localStorage.getItem("listaDeseo")); // <-- se quito let antes de listaDeseo xq validacion no funciona 
        for (let i = 0; i < listaDeseo.length; i++) {
            if (listaDeseo[i]['idProducto'] == idProducto) {
                Swal.fire({
                    title: "Aviso",
                    text: "El producto ya se encuentra en la lista de deseos",
                    icon: "warning",
                });
                return;
            }
        }
    }
    listaDeseo.push({
        "idProducto": idProducto,
        "cantidad": 1
    });
    localStorage.setItem('listaDeseo', JSON.stringify(listaDeseo));

    Swal.fire({
        title: 'Aviso',
        text: 'Producto agregado a la lista de deseos',
        icon: 'success',
    });

    cantidadDeseo();
}
function cantidadDeseo() {
    let listas = JSON.parse(localStorage.getItem('listaDeseo'));
    if (listas != null) {
        btnDeseo.textContent = listas.length;
    } else {
        btnDeseo.textContent = 0;
    }


}

function agregarCarrito(idProducto, cantidad) {
    if (localStorage.getItem('listaCarrito') == null) {
        listaCarrito = [];
    } else {
        listaExiste = JSON.parse(localStorage.getItem('listaCarrito'));
        for (let i = 0; i < listaExiste.length; i++) {
            if (listaExiste[i]["idProducto"] == idProducto) {
                Swal.fire(
                    'Aviso?',
                    'El producto ya esta agregado',
                    'warning',
                )
                return;
            }
        }
        listaCarrito.concat(localStorage.getItem('listaCarrito'));
    }
    listaCarrito.push({
        "idProducto": idProducto,
        "cantidad": cantidad
    });
    localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
    Swal.fire(
        'Aviso?',
        'El producto se agrego al carrito',
        'success',
    )
    cantidadCarrito();
}
function cantidadCarrito() {
    let listas = JSON.parse(localStorage.getItem('listaCarrito'));
    if (listas != null) {
        btnCarrito.textContent = listas.length;
    } else {
        btnCarrito.textContent = 0;
    }
}

function getListaCarrito() {
     listaCarrito = JSON.parse(localStorage.getItem('listaCarrito')) || [];
    const url = base_url + 'principal/listaCarrito';
    const hhtp = new XMLHttpRequest();
    hhtp.open('POST', url, true);
    hhtp.send(JSON.stringify(listaCarrito));
    hhtp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            res.productos.forEach(producto => {
                const subtotal = producto.precio * producto.cantidad;
                html += `<tr>
                            <td>
                            <img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="8760px">
                            </td>
                            <td>${producto.nombre}</td>
                            <td>${producto.descripcion}</td>
                            <td><span class="badge bg-warning">${res.moneda + ' ' + producto.precio}</span></td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <button class="btn btn-sm btn-secondary btnRestarCantidad mx-1" data-id="${producto.id}">-</button>
                                    <span class="badge bg-primary mx-2">${producto.cantidad}</span>
                                    <button class="btn btn-sm btn-secondary btnSumarCantidad mx-1" data-id="${producto.id}">+</button>
                                </div>
                            </td>
                            <td><span class="badge bg-success">${res.moneda + ' ' + subtotal.toFixed(2)}</span></td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <button type="button" class="btn btn-danger btnEliminarDeseo mx-1" prod="${producto.id}"><i class="fas fa-trash"></i></button>
                                    <button type="button" class="btn btn-primary mx-1"><i class="fas fa-cart-plus"></i></button>
                                </div>
                            </td>   
                        </tr>`;
            });
            let total = 0;
            res.productos.forEach(producto => {
                total += producto.precio * producto.cantidad;
            });
            document.getElementById('carritoTotal').innerHTML = `
                <h5>Total: <span class="badge bg-success">${res.moneda + ' ' + total.toFixed(2)}</span></h5>
            `;
            tableListaCarrito.innerHTML = html;
            //btnEliminarDeseo();
            // Agregar eventos para sumar/restar cantidad
            document.querySelectorAll('.btnSumarCantidad').forEach(btn => {
                btn.addEventListener('click', function () {
                    modificarCantidad(btn.getAttribute('data-id'), 1);
                });
            });
            document.querySelectorAll('.btnRestarCantidad').forEach(btn => {
                btn.addEventListener('click', function () {
                    modificarCantidad(btn.getAttribute('data-id'), -1);
                });
            });
        }
    }
}

function modificarCantidad(idProducto, cambio) {
    let lista = JSON.parse(localStorage.getItem('listaCarrito')) || [];
    lista = lista.map(item => {
        if (item.idProducto == idProducto) {
            let nuevaCantidad = item.cantidad + cambio;
            item.cantidad = nuevaCantidad > 0 ? nuevaCantidad : 1; // Evita cantidades menores a 1
        }
        return item;
    });
    localStorage.setItem('listaCarrito', JSON.stringify(lista));
    getListaCarrito(); // Actualiza la tabla
    cantidadCarrito(); // Actualiza el contador
}