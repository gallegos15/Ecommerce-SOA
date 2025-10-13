
let tblPendientes, tblFinalizados;

var firstTabEl = document.querySelector('#myTab li:last-child button')
var firstTab = new bootstrap.Tab(firstTabEl)

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


function cambiarProceso(idPedido) {
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
            const url = base_url + "pedidos/update/" + idPedido;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    console.log(this.resnposeText);
                    const res = JSON.parse(this.responseText);
                    if (res.icono === 'success') {
                        tblPendientes.ajax.reload();
                    }
                    Swal.fire('Aviso', res.msg.toUpperCase(), res.icono);
                }
            }
        }
    });
}
