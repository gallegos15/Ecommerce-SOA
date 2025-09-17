<?php include_once(__DIR__ . '/../../Config/Config.php'); ?>
<?php include_once(__DIR__ . '/../template-principal/header.php'); ?>



<!-- Start Content -->
<div class="container py-5">
    <div class="row">
        <?php if ($data['verificar']['verify'] == 1) { ?>
            <div class="col-md 8">
                <div class="card">
                    <div class="card shadow-lg">
                        <div class="card-body text-center">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle"
                                    id="tableListaProductos">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th>Descripcion</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>SubTotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <h3 id="totalProducto"></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card shadow-lg">
                        <div class="card-body text-center">
                            <img class="img-thumbnail rounded-circle"
                                src="<?php echo BASE_URL . 'assets/img/brand_01.png'; ?>" alt="" width="150">
                            <hr>
                            <p><?php echo $_SESSION['nombreCliente']; ?></p>
                            <p><i class="fas fa-envelope"></i> <?php echo $_SESSION['correoCliente']; ?></p>
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Paypal
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">

                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Otros metodos de pago
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <strong>This is the second item's accordion body.</strong> It is hidden by
                                            default, until the collapse plugin adds the appropriate classes that we use to
                                            style each element. These classes control the overall appearance, as well as the
                                            showing and hiding via CSS transitions. You can modify any of this with custom
                                            CSS or overriding our default variables. It's also worth noting that just about
                                            any HTML can go within the <code>.accordion-body</code>, though the transition
                                            does limit overflow.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-danger text-center" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img"
                    aria-label="Warning:">
                    <path
                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                </svg>
                <div>
                    <div class="h3"></div>
                    VERIFICA TU CORREO ELECTRONICO
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<!-- End Content -->

<?php include_once __DIR__ . '/../template-principal/footer.php'; ?>
<script src="<?php echo BASE_URL . 'assets/js/clientes.js'; ?>"></script>
</body>

</html>