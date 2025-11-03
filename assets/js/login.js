if (window.__login_js_loaded) { console.warn('login.js ya cargado'); } 
else {
    window.__login_js_loaded = true;

    const frmLogin = document.querySelector('#frmLogin');
    const frmRegister = document.querySelector('#frmRegister');
    const loginBtn = document.querySelector('#loginModalBtn');
    const registrarseBtn = document.querySelector('#registrarseModalBtn');

    const correoLogin = document.querySelector('#correoLoginModal');
    const claveLogin = document.querySelector('#claveLoginModal');

    const nombreRegistro = document.querySelector('#nombreRegistroModal');
    const correoRegistro = document.querySelector('#correoRegistroModal');
    const claveRegistro = document.querySelector('#claveRegistroModal');

    const inputBusqueda = document.querySelector('#inputModalSearch');

    let listaDeseo = JSON.parse(localStorage.getItem('listaDeseo') || '[]');
    let listaCarrito = JSON.parse(localStorage.getItem('listaCarrito') || '[]');

    const btnDeseo = document.querySelector('#btnCantidadDeseo');
    const btnCarrito = document.querySelector('#btnCantidadCarrito');
    const verCarrito = document.querySelector('#verCarrito');

    document.addEventListener('DOMContentLoaded', function () {

        function actualizarBadges() {
            if (btnDeseo) btnDeseo.textContent = listaDeseo.length;
            if (btnCarrito) btnCarrito.textContent = listaCarrito.length;
        }
        actualizarBadges();

        // Toggle login/register
        const btnLoginToggle = document.querySelector('#btnLogin');
        const btnRegisterToggle = document.querySelector('#btnRegistrarse');
        if (btnLoginToggle) btnLoginToggle.addEventListener('click', () => { frmRegister.classList.add('d-none'); frmLogin.classList.remove('d-none'); });
        if (btnRegisterToggle) btnRegisterToggle.addEventListener('click', () => { frmLogin.classList.add('d-none'); frmRegister.classList.remove('d-none'); });

        // Login
        if (loginBtn) loginBtn.addEventListener('click', () => {
            if (!correoLogin.value || !claveLogin.value) { Swal.fire('Aviso?', 'TODOS LOS CAMPOS SON REQUERIDOS', 'warning'); return; }
            const formData = new FormData();
            formData.append('correoLogin', correoLogin.value);
            formData.append('claveLogin', claveLogin.value);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', base_url + 'clientes/loginDirecto', true);
            xhr.send(formData);
            xhr.onreadystatechange = () => {
                if (xhr.readyState==4 && xhr.status==200) {
                    const res = JSON.parse(xhr.responseText);
                    Swal.fire('Aviso?', res.msg, res.icono);
                    // Si el backend devolvió un JWT lo guardamos en localStorage para inspección y uso
                    if (res.jwt) {
                        try { localStorage.setItem('jwt', res.jwt); console.info('JWT guardado en localStorage:', res.jwt); } catch(e){ console.warn('No se pudo guardar jwt en localStorage', e); }
                        // opcional: también establecer cookie de sesión (si se desea)
                        try { document.cookie = 'jwt=' + res.jwt + '; path=/'; } catch(e){}
                    }
                    if(res.icono==='success') setTimeout(()=>window.location.reload(), 2000);
                }
            };
        });

        // Registro
        if (registrarseBtn) registrarseBtn.addEventListener('click', () => {
            if (!nombreRegistro.value || !correoRegistro.value || !claveRegistro.value) { Swal.fire('Aviso?', 'TODOS LOS CAMPOS SON REQUERIDOS', 'warning'); return; }
            const formData = new FormData();
            formData.append('nombre', nombreRegistro.value);
            formData.append('correo', correoRegistro.value);
            formData.append('clave', claveRegistro.value);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', base_url + 'clientes/registroDirecto', true);
            xhr.send(formData);
            xhr.onreadystatechange = () => {
                if (xhr.readyState==4 && xhr.status==200) {
                    const res = JSON.parse(xhr.responseText);
                    Swal.fire('Aviso?', res.msg, res.icono);
                    // Guardar jwt si viene en la respuesta
                    if (res.jwt) {
                        try { localStorage.setItem('jwt', res.jwt); console.info('JWT guardado en localStorage (registro):', res.jwt); } catch(e){ console.warn('No se pudo guardar jwt en localStorage', e); }
                        try { document.cookie = 'jwt=' + res.jwt + '; path=/'; } catch(e){}
                        // Additionally log a short instruction for quick inspection
                        console.info('Verifica Application -> Local Storage -> jwt o pestaña Network para ver Authorization header en peticiones posteriores.');
                    }
                    if(res.icono==='success') setTimeout(()=>enviarCorreo(correoRegistro.value,res.token),2000);
                }
            };
        });

        function enviarCorreo(correo, token){
            const formData = new FormData();
            formData.append('correo', correo);
            formData.append('token', token);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', base_url + 'clientes/enviarCorreo', true);
            xhr.send(formData);
            xhr.onreadystatechange = () => {
                if(xhr.readyState==4 && xhr.status==200){
                    const res = JSON.parse(xhr.responseText);
                    Swal.fire('Aviso?', res.msg, res.icono);
                    if(res.icono==='success') setTimeout(()=>window.location.reload(),2000);
                }
            };
        }

        // Busqueda
        if (inputBusqueda) {
            const container = document.getElementById('searchResults');
            let timer = null;
            inputBusqueda.addEventListener('keyup', ()=>{
                if(timer) clearTimeout(timer);
                timer = setTimeout(()=>doSearch(inputBusqueda.value.trim(), container),300);
            });
            document.getElementById('btnModalSearchSubmit')?.addEventListener('click',()=>doSearch(inputBusqueda.value.trim(),container));
        }

        function doSearch(query, container){
            if(!query){ container.innerHTML='<p class="text-muted">Escribe para buscar productos...</p>'; return; }
            // usa authFetch si hay jwt para incluir Authorization header automáticamente
            const token = localStorage.getItem('jwt');
            (typeof authFetch === 'function' && token ? authFetch(base_url+'index.php?url=principal/busqueda/'+encodeURIComponent(query)) : fetch(base_url+'index.php?url=principal/busqueda/'+encodeURIComponent(query)))
            .then(res=>res.json())
            .then(data=>{
                const arr = Array.isArray(data)?data:(data.productos||[]);
                let html='<div class="row">';
                arr.forEach(p=>{
                    let img = p.imagen || base_url+'assets/img/shop_01.jpg';
                    if(!img.startsWith('http')) img=base_url.replace(/\/$/,'')+'/'+img.replace(/^\//,'');
                    html+=`<div class="col-12 col-md-4 mb-4">
                        <div class="card h-100">
                            <a href="${base_url}principal/shop_single/${p.id}"><img src="${img}" class="card-img-top" alt="${p.nombre}" style="height:220px; object-fit:cover;"></a>
                            <div class="card-body">
                                <ul class="list-unstyled d-flex justify-content-between">
                                    <li>
                                        <i class="text-warning fa fa-star"></i>
                                        <i class="text-warning fa fa-star"></i>
                                        <i class="text-warning fa fa-star"></i>
                                        <i class="text-muted fa fa-star"></i>
                                        <i class="text-muted fa fa-star"></i>
                                    </li>
                                    <li class="text-muted text-right">${MONEDA} ${p.precio}</li>
                                </ul>
                                <a href="${base_url}principal/shop_single/${p.id}" class="h6 text-decoration-none text-dark">${p.nombre}</a>
                                <p class="card-text" style="font-size:0.85rem; color:#666;">${p.descripcion?.substring(0,80) || ''}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary btnAddCarrito" prod="${p.id}"><i class="fas fa-cart-plus"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btnAddDeseo" prod="${p.id}"><i class="fa fa-heart"></i></button>
                                    </div>
                                    <small class="text-muted">Reviews (24)</small>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                html+='</div>';
                container.innerHTML=html;
                document.querySelectorAll('.btnAddCarrito').forEach(b=>b.addEventListener('click',()=>agregarCarrito(b.getAttribute('prod'))));
                document.querySelectorAll('.btnAddDeseo').forEach(b=>b.addEventListener('click',()=>agregarDeseo(b.getAttribute('prod'))));
            }).catch(()=>container.innerHTML='<div class="text-danger">Error al buscar. Intenta de nuevo.</div>');
        }

        // Helper: fetch que añade Authorization: Bearer <jwt> si existe en localStorage
        // Uso: authFetch(url, opts)
        window.authFetch = function(url, opts = {}){
            const token = localStorage.getItem('jwt');
            opts = opts || {};
            opts.headers = opts.headers || {};
            if (token) {
                // Si headers fue un objeto Headers o un array, convertir no cubierto; manejamos objeto simple
                opts.headers['Authorization'] = 'Bearer ' + token;
            }
            return fetch(url, opts);
        };

        function agregarDeseo(id){
            if(listaDeseo.some(p=>p.idProducto==id)) return;
            listaDeseo.push({idProducto:id, cantidad:1});
            localStorage.setItem('listaDeseo',JSON.stringify(listaDeseo));
            actualizarBadges();
            Swal.fire('Aviso?', 'Producto agregado a deseos ✔','success');
        }

        function agregarCarrito(id){
            if(listaCarrito.some(p=>p.idProducto==id)) return;
            listaCarrito.push({idProducto:id, cantidad:1});
            localStorage.setItem('listaCarrito',JSON.stringify(listaCarrito));
            actualizarBadges();
            Swal.fire('Aviso?', 'Producto agregado al carrito ✔','success');
        }

        document.querySelectorAll('.btnAddCarrito').forEach(b=>b.addEventListener('click',()=>agregarCarrito(b.getAttribute('prod'))));
        document.querySelectorAll('.btnAddDeseo').forEach(b=>b.addEventListener('click',()=>agregarDeseo(b.getAttribute('prod'))));
    });
}
