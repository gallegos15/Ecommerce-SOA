const btnRegister = document.querySelector('#btnRegistrarse');
const btnLogin = document.querySelector('#btnLogin');
const frmLogin = document.querySelector('#frmLogin');
const frmRegister = document.querySelector('#frmRegister');
const registrarse = document.querySelector('#registrarse');

const nombreRegistro = document.querySelector('#nombreRegistro');
const correoRegistro = document.querySelector('#correoRegistro');
const claveRegistro = document.querySelector('#claveRegistro');


document.addEventListener('DOMContentLoaded', function () {
    btnRegister.addEventListener('click', function () {
        frmLogin.classList.add('d-none');
        frmRegister.classList.remove('d-none');

    })
    btnLogin.addEventListener('click', function () {
        frmRegister.classList.add('d-none');
        frmLogin.classList.remove('d-none');

    })
    //registro
    registrarse.addEventListener('click', function () {
        if (nombreRegistro.value == "" ||
            correoRegistro.value =="" || 
            claveRegistro.value == ""
        ) {
            Swal.fire("Aviso?", 'TODOS LOS CAMPOS SON REQUERIDOS', 'warning');
        }else {
        let formData = new FormData();
        formData.append('nombre', nombreRegistro.value);
        formData.append('correo', correoRegistro.value);
        formData.append('clave', claveRegistro.value);
        const url = base_url + 'clientes/registroDirecto';
        const hhtp = new XMLHttpRequest();
        hhtp.open('POST', url, true);
        hhtp.send(formData);
        hhtp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                Swal.fire('Aviso?', res.msg, res.icono)
                if (res.icono == 'success') {
                    setTimeout(() => {
                        enviarCorreo(correoRegistro.value, res.token);
                    }, 2000);
                }
            }
        }
        }
        
    });
});

function enviarCorreo(correo, token){
    let formData = new FormData();
        formData.append('correo', correo);
        formData.append('token', token);
    const url = base_url + 'clientes/enviarCorreo';
        const hhtp = new XMLHttpRequest();
        hhtp.open('POST', url, true);
        hhtp.send(formData);
        hhtp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                Swal.fire(
                    'Aviso?',
                    res.msg,
                    res.icono,
                )
                if (res.icono == 'success') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            }
        }
}

