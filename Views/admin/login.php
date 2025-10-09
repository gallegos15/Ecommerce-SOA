<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $data['title']; ?></title>

    <!-- Custom fonts for this template-->
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo BASE_URL; ?>assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Centrar todo el contenido */
        body,
        html {
            height: 100%;
        }

        body.bg-gradient-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #3b82f6, #1e3a8a);
        }

        /* Ajuste del card */
        .card {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            border-radius: 15px;
        }

        .card-body {
            padding: 2rem !important;
        }

        /* Quitar la imagen lateral */
        .bg-login-image {
            display: none !important;
        }

        /* Estilo del botón */
        .btn-primary {
            background-color: #2563eb;
            border: none;
            border-radius: 25px;
        }

        .btn-primary:hover {
            background-color: #1e40af;
        }

        /* Inputs */
        .form-control-user {
            border-radius: 25px;
            padding: 0.75rem 1rem;
        }

        /* Texto inferior */
        .text-center a {
            color: #2563eb;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center align-items-center" style="height: 100vh;">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-0">
                        <!-- Row principal sin imagen -->
                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">¡BIENVENIDO!</h1>
                                    </div>
                                    <form class="user" id="formulario">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="email" name="email" aria-describedby="email" value="urbancloud4@gmail.com"
                                                placeholder="Correo Electronico">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="clave" name="clave" value="admin" placeholder="Contraseña">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Recordar</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Ingresar
                                        </button>
                                        <hr>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">¿Olvidaste tu contraseña?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.html">Crear una cuenta</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo BASE_URL; ?>assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo BASE_URL; ?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo BASE_URL; ?>assets/js/sb-admin-2.min.js"></script>
    <script>
        const base_url = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/modulos/login.js"></script>

</body>

</html>