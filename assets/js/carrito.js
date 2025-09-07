const btnAddDeseo = document.querySelectorAll(".btnAddDeseo");
const btnDeseo = document.querySelector("#btnCantidadDeseo");
let listaDeseo;
document .addEventListener("DOMContentLoaded", function() {
    if (localStorage.getItem("listaDeseo") != null) {
        listaDeseo = JSON.parse(localStorage.getItem("listaDeseo"));
    }
    cantidadDeseo();
    for (let i = 0; i < btnAddDeseo.length; i++) {
            btnAddDeseo[i].addEventListener("click", function() {
                let idProducto = btnAddDeseo[i].getAttribute("prod");
                agregarDeseo(idProducto);
            });
        }
});
function agregarDeseo(idProducto) {
    let listaDeseo;
    if (localStorage.getItem("listaDeseo") == null) {
        listaDeseo = [];
    } else {
        listaDeseo = JSON.parse(localStorage.getItem("listaDeseo")); // <-- CORRECTO
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
    localStorage.setItem("listaDeseo", JSON.stringify(listaDeseo));

    Swal.fire({
        title: "Aviso",
        text: "Producto agregado a la lista de deseos",
        icon: "success",
    });

    cantidadDeseo();
}
function cantidadDeseo() {
    let listas = JSON.parse(localStorage.getItem("listaDeseo"));
    if (listas != null) {
        btnDeseo.textContent = listas.length;
    } else {
        btnDeseo.textContent =0;
    }


}