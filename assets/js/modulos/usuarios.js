const nuevo = document.querySelector('#nuevo_registro');
const frm = document.querySelector('#frmRegistro');
const titleModal = document.querySelector('#titleModal');
const btnAccion = document.querySelector('#btnAccion');
const myModal = new bootstrap.Modal(document.getElementById('nuevoModal'))
let tblUsuarios;
document.addEventListener('DOMContentLoaded', function () {
    tblUsuarios = $('#tblUsuarios').DataTable({
        ajax: {
            url: base_url + 'usuarios/listar',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'apellidos' },
            { data: 'correo' },
            { data: 'perfil' },
            { data: 'accion' }
        ],
        language,
        dom,
        buttons
    });
    //levantar modal
    nuevo.addEventListener('click', function () {
        document.querySelector('#id').value = '';
        titleModal.textContent = 'Nuevo Usuario';
        btnAccion.textContent = 'Registrar';
        frm.reset();
        document.querySelector('#clave').removeAttribute('readonly');
        myModal.show();
    })
    //submit usuarios
    frm.addEventListener('submit', function (e) {
        e.preventDefault();
        let data = new FormData(this);
        const url = base_url + "usuarios/registrar";
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.resnposeText);
                const res = JSON.parse(this.responseText);
                if (res.icono === 'success') {
                    myModal.hide();
                    tblUsuarios.ajax.reload();
                }
                Swal.fire('Aviso', res.msg.toUpperCase(), res.icono);
            }
        }
    });
});


function eliminarUser(idUser) {
    Swal.fire({
        title: 'Aviso?',
        text: "¿Está seguro de eliminar el registro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar',
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "usuarios/delete/" + idUser;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    console.log(this.resnposeText);
                    const res = JSON.parse(this.responseText);
                    if (res.icono === 'success') {
                        tblUsuarios.ajax.reload();
                    }
                    Swal.fire('Aviso', res.msg.toUpperCase(), res.icono);
                }
            }
        }
    });
}

function editUser(idUser) {
    const url = base_url + "usuarios/edit/" + idUser;
        const http = new XMLHttpRequest();
        http.open("GET", url, true);
        http.send();
        http.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.resnposeText);
                const res = JSON.parse(this.responseText);
                document.querySelector('#id').value = res.id;
                document.querySelector('#nombre').value = res.nombre;
                document.querySelector('#apellidos').value = res.apellidos;
                document.querySelector('#correo').value = res.correo;
                document.querySelector('#clave').setAttribute('readonly', 'readonly');
                btnAccion.textContent = 'Actualizar';
                titleModal.textContent = 'Modificar Usuario';
                myModal.show();
                //$('#nuevoModal').modal('show');
            }
        }
}