// === Selección de elementos ===
const btnAddDeseo = document.querySelectorAll('.btnAddDeseo');
const btnAddCarrito = document.querySelectorAll('.btnAddCarrito');
const btnDeseo = document.querySelector('#btnCantidadDeseo');
const btnCarrito = document.querySelector('#btnCantidadCarrito');
const verCarrito = document.querySelector('#verCarrito');
const tableListaCarrito = document.querySelector('#tableListaCarrito tbody');

let listaDeseo = [];
let listaCarrito = [];

// === Modal Bootstrap ===
let myModal = null;
const modalElement = document.getElementById('myModal');
if (modalElement) {
    myModal = new bootstrap.Modal(modalElement);
}

// === Esperar a que cargue el DOM ===
document.addEventListener('DOMContentLoaded', function () {

    // Recuperar listas desde localStorage
    listaDeseo = JSON.parse(localStorage.getItem('listaDeseo')) || [];
    listaCarrito = JSON.parse(localStorage.getItem('listaCarrito')) || [];

    // Eventos para botones de deseos
    btnAddDeseo.forEach(btn => {
        btn.addEventListener('click', function () {
            const idProducto = btn.getAttribute('prod');
            agregarDeseo(idProducto);
        });
    });

    // Eventos para botones de carrito
    btnAddCarrito.forEach(btn => {
        btn.addEventListener('click', function () {
            const idProducto = btn.getAttribute('prod');
            agregarCarrito(idProducto, 1);
        });
    });

    // Mostrar cantidades iniciales
    cantidadDeseo();
    cantidadCarrito();

    // Evento para ver carrito (si existe)
    if (verCarrito) {
        verCarrito.addEventListener('click', function () {
            getListaCarrito();
            if (myModal) myModal.show();
        });
    }
});

// === FUNCIONES ===

// Agregar producto a deseos
function agregarDeseo(idProducto) {
    const lista = JSON.parse(localStorage.getItem('listaDeseo')) || [];

    // Verificar si ya existe
    if (lista.some(item => item.idProducto == idProducto)) {
        Swal.fire({
            title: "Aviso",
            text: "El producto ya se encuentra en la lista de deseos",
            icon: "warning",
        });
        return;
    }

    lista.push({ idProducto, cantidad: 1 });
    localStorage.setItem('listaDeseo', JSON.stringify(lista));

    Swal.fire({
        title: 'Aviso',
        text: 'Producto agregado a la lista de deseos',
        icon: 'success',
    });

    cantidadDeseo();
}

// Mostrar cantidad de deseos
function cantidadDeseo() {
    const el = document.getElementById('btnCantidadDeseo');
    if (!el) return;

    const listas = JSON.parse(localStorage.getItem('listaDeseo')) || [];
    el.textContent = listas.length;
}

// Agregar producto al carrito
function agregarCarrito(idProducto, cantidad, accion = false) {
    let lista = JSON.parse(localStorage.getItem('listaCarrito')) || [];

    if (accion) eliminarListaDeseo(idProducto);

    // Verificar si ya existe
    if (lista.some(item => item.idProducto == idProducto)) {
        Swal.fire('Aviso', 'El producto ya está agregado', 'warning');
        return;
    }

    lista.push({ idProducto, cantidad });
    localStorage.setItem('listaCarrito', JSON.stringify(lista));

    Swal.fire('Aviso', 'El producto se agregó al carrito', 'success');
    cantidadCarrito();
}

// Mostrar cantidad de productos en carrito
function cantidadCarrito() {
    const el = document.getElementById('btnCantidadCarrito');
    if (!el) return;

    const listas = JSON.parse(localStorage.getItem('listaCarrito')) || [];
    el.textContent = listas.length;
}

// Obtener lista del carrito (desde el servidor)
function getListaCarrito() {
    const url = base_url + 'principal/listaCarrito';
    const lista = JSON.parse(localStorage.getItem('listaCarrito')) || [];

    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.send(JSON.stringify(lista));

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            let html = '';

            res.productos.forEach(producto => {
                html += `
                    <tr>
                        <td><img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="70px"></td>
                        <td>${producto.nombre}</td>
                        <td>${producto.descripcion}</td>
                        <td><span class="badge bg-warning">${res.moneda} ${producto.precio}</span></td>
                        <td><span class="badge bg-primary">${producto.cantidad}</span></td>
                        <td>${producto.subTotal}</td>
                        <td><button class="btn btn-danger btnDeletecart" type="button" prod="${producto.id}"><i class="fas fa-times-circle"></i></button></td>
                    </tr>`;
            });

            tableListaCarrito.innerHTML = html;
            document.querySelector('#totalGeneral').textContent = res.total;
            btnEliminarCarrito();
        }
    };
}

// Botones para eliminar productos
function btnEliminarCarrito() {
    const botones = document.querySelectorAll('.btnDeletecart');
    botones.forEach(btn => {
        btn.addEventListener('click', function () {
            const idProducto = btn.getAttribute('prod');
            eliminarListaCarrito(idProducto);
        });
    });
}

// Eliminar producto del carrito
function eliminarListaCarrito(idProducto) {
    let lista = JSON.parse(localStorage.getItem('listaCarrito')) || [];
    lista = lista.filter(item => item.idProducto != idProducto);

    localStorage.setItem('listaCarrito', JSON.stringify(lista));
    getListaCarrito();
    cantidadCarrito();

    Swal.fire({
        title: "Aviso",
        text: "Producto eliminado del carrito",
        icon: "success",
    });
}

// Eliminar de la lista de deseos (si pasa del deseo al carrito)
function eliminarListaDeseo(idProducto) {
    let lista = JSON.parse(localStorage.getItem('listaDeseo')) || [];
    lista = lista.filter(item => item.idProducto != idProducto);
    localStorage.setItem('listaDeseo', JSON.stringify(lista));
    cantidadDeseo();
}
