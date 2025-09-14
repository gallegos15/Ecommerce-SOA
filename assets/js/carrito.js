const btnAddDeseo = document.querySelectorAll('.btnAddDeseo');
const btnAddCarrito = document.querySelectorAll('.btnAddCarrito');
const btnDeseo = document.querySelector('#btnCantidadDeseo');
const btnCarrito = document.querySelector('#btnCantidadCarrito');
const verCarrito = document.querySelector('#verCarrito');
const tableListaCarrito = document.querySelector('#tableListaCarrito tbody');

let listaDeseo, listaCarrito;
document .addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('listaDeseo') != null) {
        listaDeseo = JSON.parse(localStorage.getItem('listaDeseo'));
    }
    if (localStorage.getItem("listaCarrito") != null) {
        listaCarrito = JSON.parse(localStorage.getItem("listaCarrito"));
    }
    for (let i = 0; i < btnAddDeseo.length; i++) {
            btnAddDeseo[i].addEventListener('click', function(){
                let idProducto = btnAddDeseo[i].getAttribute('prod');
                agregarDeseo(idProducto);
            });
        }
    for (let i = 0; i < btnAddCarrito.length; i++) {
            btnAddCarrito[i].addEventListener('click', function() {
                let idProducto = btnAddCarrito[i].getAttribute('prod');
                agregarCarrito(idProducto, 1);
            });
        }
    cantidadDeseo();
    cantidadCarrito();
    var myModal = new bootstrap.Modal(document.getElementById('myModal'))
    verCarrito.addEventListener('click', function() {
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
        btnDeseo.textContent =0;
    }


}

function agregarCarrito(idProducto, cantidad, accion = false) {
    if (localStorage.getItem('listaCarrito') == null) {
        listaCarrito = [];
    } else {
        listaExiste = JSON.parse(localStorage.getItem('listaCarrito'));
        for (let i = 0; i < listaExiste.length; i++) {
            
            if (accion) {
                eliminarListaDeseo(idProducto);
            }
            
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
    const url = base_url + 'principal/listaCarrito';
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
                            <td>
                            <button class="btn btn-danger btnDeletecart" type="button" prod="${producto.id}"><i class="fas fa-times-circle"></i></button>
                            </td> 
                        </tr>`;
            });
            tableListaCarrito.innerHTML = html;
            document.querySelector('#totalGeneral').textContent = res.total;
            btnEliminarCarrito();
        }
    }
}
function btnEliminarCarrito() {
    let listaEliminar = document.querySelectorAll('.btnDeletecart');
    for (let i = 0; i < listaEliminar.length; i++) {
            listaEliminar[i].addEventListener('click', function() {
            let idProducto = listaEliminar[i].getAttribute('prod');
            eliminarListaCarrito(idProducto);
        });
    }
}
function eliminarListaCarrito(idProducto) {
    for (let i = 0; i < listaCarrito.length; i++) {
        if (listaCarrito[i]['idProducto'] == idProducto) {
            listaCarrito.splice(i, 1);
        }
    }
    localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
    getListaCarrito();
    cantidadCarrito();
    Swal.fire({
        title: "Aviso",
        text: "Producto eliminado del carrito",
        icon: "success",
    });
}   