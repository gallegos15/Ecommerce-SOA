<?php
include_once __DIR__ . '/../../Config/Config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo TITLE; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>assets/img/apple-icon.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/img/favicon.ico">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/templatemo.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/custom.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/fontawesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/slick.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/DataTables/datatables.min.css">

    <!-- PayPal SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&currency=<?php echo MONEDA ?>&components=buttons&enable-funding=venmo,paylater,card"></script>
</head>

<body>

<!-- Top Nav -->
<nav class="navbar navbar-expand-lg bg-dark navbar-light d-none d-lg-block" id="templatemo_nav_top">
    <div class="container text-light">
        <div class="w-100 d-flex justify-content-between">
            <div>
                <i class="fa fa-envelope mx-2"></i>
                <a class="navbar-sm-brand text-light text-decoration-none" href="mailto:info@company.com">info@company.com</a>
                <i class="fa fa-phone mx-2"></i>
                <a class="navbar-sm-brand text-light text-decoration-none" href="tel:010-020-0340">010-020-0340</a>
            </div>
            <div>
                <a class="text-light" href="https://fb.com/templatemo" target="_blank" rel="sponsored"><i class="fab fa-facebook-f fa-sm fa-fw me-2"></i></a>
                <a class="text-light" href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram fa-sm fa-fw me-2"></i></a>
                <a class="text-light" href="https://twitter.com/" target="_blank"><i class="fab fa-twitter fa-sm fa-fw me-2"></i></a>
                <a class="text-light" href="https://www.linkedin.com/" target="_blank"><i class="fab fa-linkedin fa-sm fa-fw"></i></a>
            </div>
        </div>
    </div>
</nav>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light shadow">
    <div class="container d-flex justify-content-between align-items-center">

        <a class="navbar-brand text-success logo h1 align-self-center" href="<?php echo BASE_URL;?>">
            <img src="<?php echo BASE_URL; ?>assets/img/apple-icon.png" alt="logo1" width="55">
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="align-self-center collapse navbar-collapse flex-fill d-lg-flex justify-content-lg-between" id="templatemo_main_nav">
            <div class="flex-fill">
                <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL . 'Views/principal/about.php'; ?>">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL . 'principal/shop'; ?>">Tienda</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL . 'Views/principal/contact.php'; ?>">Contacto</a></li>
                </ul>
            </div>

            <div class="navbar align-self-center d-flex">
                <!-- Lupa escritorio -->
                <a class="nav-icon d-none d-lg-inline" href="#" data-bs-toggle="modal" data-bs-target="#templatemo_search" aria-label="Buscar" onclick="event.preventDefault();">
                    <i class="fas fa-fw fa-search text-dark me-2"></i>
                </a>

                <?php $perfil = (isset($data['perfil'])) ? $data['perfil'] : 'no'; ?>

                <?php if ($perfil === 'no') { ?>
                    <a class="nav-icon position-relative text-decoration-none me-2" href="#" id="verCarrito" aria-label="Ver carrito" onclick="event.preventDefault();">
                        <i class="fas fa-fw fa-cart-arrow-down text-dark"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark" id="btnCantidadCarrito">0</span>
                    </a>
                    <a class="nav-icon position-relative text-decoration-none me-3" href="<?php echo BASE_URL . 'principal/deseo';?>" aria-label="Lista de deseos">
                        <i class="fa fa-fw fa-heart text-dark"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark" id="btnCantidadDeseo">0</span>
                    </a>
                <?php } ?>

                <?php if (!empty($_SESSION['correoCliente'])) { ?>
                    <a class="nav-icon position-relative text-decoration-none" href="<?php echo BASE_URL . 'clientes'; ?>" aria-label="Mi cuenta">
                        <img class="img-thumbnail" src="<?php echo BASE_URL . 'assets/img/apple-icon.png'; ?>" alt="-LOGO-CLIENTE" width="50">
                    </a>
                <?php } else { ?>
                    <a class="nav-icon position-relative text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#modalLogin" aria-label="Iniciar sesión" onclick="event.preventDefault();">
                        <i class="fas fa-fw fa-user text-dark me-3"></i>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<!-- JS globales -->
<script>
if (typeof window.base_url === 'undefined') window.base_url = "<?php echo BASE_URL; ?>";
if (typeof window.MONEDA === 'undefined') window.MONEDA = "<?php echo MONEDA; ?>";
</script>

<!-- 🔹 Modal Login / Registro -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      <!-- 🔸 Imagen superior -->
      <div class="text-center bg-light p-4">
        <img src="https://cdn-icons-png.flaticon.com/512/5087/5087579.png"
             alt="Logo del sistema"
             class="img-fluid"
             style="width: 100px; height: 100px; border-radius: 50%;">
      </div>

      <!-- 🔸 Cuerpo del modal -->
      <div class="modal-body p-4">

        <!-- Login -->
        <form id="frmLogin" onsubmit="return false;">
          <div class="mb-3">
            <label for="correoLoginModal" class="form-label fw-semibold">Correo electrónico</label>
            <input type="email" class="form-control form-control-lg" id="correoLoginModal"
                   name="correoLogin" placeholder="Ingrese su correo" autocomplete="username" required>
          </div>

          <div class="mb-3">
            <label for="claveLoginModal" class="form-label fw-semibold">Contraseña</label>
            <input type="password" class="form-control form-control-lg" id="claveLoginModal"
                   name="claveLogin" placeholder="Ingrese su contraseña" autocomplete="current-password" required>
          </div>

          <button type="button" id="loginModalBtn" class="btn btn-primary w-100 py-2 fw-semibold">
            Iniciar sesión
          </button>

          <div class="text-center mt-3">
            <small class="text-muted">
              ¿No tienes cuenta?
              <a href="#" id="mostrarRegistro" class="text-decoration-none fw-semibold">
                Crear una
              </a>
            </small>
          </div>
        </form>

        <!-- Registro -->
        <form id="frmRegister" class="d-none mt-3" onsubmit="return false;">
          <div class="mb-3">
            <label for="nombreRegistroModal" class="form-label fw-semibold">Nombre completo</label>
            <input type="text" class="form-control form-control-lg" id="nombreRegistroModal"
                   name="nombreRegistro" placeholder="Ingrese su nombre completo" autocomplete="name" required>
          </div>

          <div class="mb-3">
            <label for="correoRegistroModal" class="form-label fw-semibold">Correo electrónico</label>
            <input type="email" class="form-control form-control-lg" id="correoRegistroModal"
                   name="correoRegistro" placeholder="Ingrese su correo" autocomplete="email" required>
          </div>

          <div class="mb-3">
            <label for="claveRegistroModal" class="form-label fw-semibold">Contraseña</label>
            <input type="password" class="form-control form-control-lg" id="claveRegistroModal"
                   name="claveRegistro" placeholder="Cree una contraseña" autocomplete="new-password" required>
          </div>

          <button type="button" id="registrarseModalBtn" class="btn btn-success w-100 py-2 fw-semibold">
            Registrarse
          </button>

          <div class="text-center mt-3">
            <small class="text-muted">
              ¿Ya tienes cuenta?
              <a href="#" id="mostrarLogin" class="text-decoration-none fw-semibold">
                Iniciar sesión
              </a>
            </small>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<!-- 🔹 Script para alternar formularios -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("frmLogin");
  const registerForm = document.getElementById("frmRegister");
  const mostrarRegistro = document.getElementById("mostrarRegistro");
  const mostrarLogin = document.getElementById("mostrarLogin");

  if (mostrarRegistro && mostrarLogin) {
    mostrarRegistro.addEventListener("click", (e) => {
      e.preventDefault();
      loginForm.classList.add("d-none");
      registerForm.classList.remove("d-none");
    });

    mostrarLogin.addEventListener("click", (e) => {
      e.preventDefault();
      registerForm.classList.add("d-none");
      loginForm.classList.remove("d-none");
    });
  }
});
</script>

<!-- Modal Búsqueda -->
<div class="modal fade bg-white" id="templatemo_search" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="w-100 pt-1 mb-5 text-end">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formModalSearch" class="modal-content modal-body border-0 p-0" onsubmit="return false;">
            <div class="input-group mb-2">
                <input type="text" class="form-control" id="inputModalSearch" name="q" placeholder="Search ...">
                <button id="btnModalSearchSubmit" type="button" class="input-group-text bg-success text-light" aria-label="Buscar">
                    <i class="fa fa-fw fa-search text-white"></i>
                </button>
            </div>
            <div id="searchResults" class="mt-2">
                <p class="text-muted">Escribe para buscar productos...</p>
            </div>
        </form>
    </div>
</div>

<div id="contenidoPrincipal">
    <!-- Contenido de la página -->
</div>
