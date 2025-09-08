<?php include_once 'template-principal/header.php';?>


    <!-- Start Banner Hero -->
    <div id="template-mo-zay-hero-carousel" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <li data-bs-target="#template-mo-zay-hero-carousel" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#template-mo-zay-hero-carousel" data-bs-slide-to="1"></li>
            <li data-bs-target="#template-mo-zay-hero-carousel" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="container">
                    <div class="row p-5">
                        <div class="mx-auto col-md-8 col-lg-6 order-lg-last">
                            <img class="img-fluid" src="<?php echo BASE_URL; ?>assets/img/banner_img_01.jpg" alt="">
                        </div>
                        <div class="col-lg-6 mb-0 d-flex align-items-center">
                        <div class="text-center align-self-center">
                            <div class="text-success">
                                <h2 class="fw-semibold mb-2" style="font-size: 4rem;">HASTA</h2>
                                <h1 class="fw-bold mb-2" style="font-size: 10rem;">60%</h1>
                                <h2 class="fw-semibold mb-3" style="font-size: 4rem;">DE DSCTO</h2>
                            </div>
                            <h3 class="h2 text-dark">Lleva tus pasos al siguiente nivel</h3>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="container">
                    <div class="row p-5">
                        <div class="mx-auto col-md-8 col-lg-6 order-lg-last">
                            <img class="img-fluid" src="<?php echo BASE_URL; ?>assets/img/banner_img_02.jpg" alt="">
                        </div>
                        <div class="col-lg-6 mb-0 d-flex align-items-center">
                        <div class="text-center text-success w-100">
                            <!-- Frase principal gigante -->
                            <h1 class="fw-bold mb-2" style="font-size: 5rem;">¡Llegaron</h1>
                            <h1 class="fw-bold mb-2" style="font-size: 5rem;">en exclusivas!</h1>

                            <!-- Nombre del modelo, mediano y negrita -->
                            <h2 class="fw-bold mb-2" style="font-size: 3rem;">AirMax</h2>

                            <!-- Subtítulo mediano, sin negrita -->
                            <h2 class="fw-normal" style="font-size: 3rem;">Solo en</h2>
                            <h2 class="fw-normal" style="font-size: 3rem;">Urban Cloud</h2>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="container">
                    <div class="row p-5">
                        <div class="mx-auto col-md-8 col-lg-6 order-lg-last">
                            <img class="img-fluid" src="<?php echo BASE_URL; ?>assets/img/banner_img_03.jpg" alt="">
                        </div>
                        <div class="col-lg-6 mb-0 d-flex align-items-center">
                        <div class="text-center w-100">
                            <!-- Texto verde, tamaño mediano -->
                            <h2 class="fw-bold mb-2 text-success" style="font-size: 4rem;">SOLO PARA</h2>

                            <!-- Texto verde, gigante -->
                            <h1 class="fw-bold mb-2 text-success" style="font-size: 8rem;">CRACKS</h1>

                            <!-- Texto negro, tamaño mediano -->
                            <h2 class="fw-semibold mb-2 text-dark" style="font-size: 2rem;">POR TU COMPRA LLÉVATE</h2>
                            <h2 class="fw-semibold mb-2 text-dark" style="font-size: 2rem;">GRATIS UN PAR DE MEDIAS</h2>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev text-decoration-none w-auto ps-3" href="#template-mo-zay-hero-carousel" role="button" data-bs-slide="prev">
            <i class="fas fa-chevron-left"></i>
        </a>
        <a class="carousel-control-next text-decoration-none w-auto pe-3" href="#template-mo-zay-hero-carousel" role="button" data-bs-slide="next">
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
    <!-- End Banner Hero -->


    <!-- Start Categories of The Month -->
    <section class="container py-5">
        <div class="row text-center pt-3">
            <div class="col-lg-6 m-auto">
                <h1 class="h1">Categorías</h1>
            </div>
        </div>
        <div class="row">
        <?php if (!empty($data) && !empty($data['categorias']) && is_array($data['categorias'])): ?>
            <?php foreach ($data['categorias'] as $categoria): ?>
                <div class="col-12 col-md-2 p-5 mt-3">
                    <a href="<?php echo BASE_URL . 'principal/categorias/' . $categoria['id']; ?>"><img src="<?php echo $categoria['imagen']; ?>" class="rounded-circle img-fluid border"></a>
                    <h5 class="text-center mt-3 mb-3"><?php echo htmlspecialchars($categoria['categoria'] ?? ''); ?></h5>
                </div>
        <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No hay categorías para mostrar.</p>
            <?php endif; ?>
        </div>
    </section>
    <!-- End Categories of The Month -->


    <!-- Start Featured Product -->
    <section class="bg-light">
        <div class="container py-5">
            <div class="row text-center py-3">
                <div class="col-lg-6 m-auto">
                    <h1 class="h1">Productos</h1>
                </div>
            </div>
            <div class="row">
                <?php foreach ($data['nuevoProductos'] as $producto) {?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="card h-100">
                        <a href="<?php echo BASE_URL . 'principal/shop_single/' . $producto['id']; ?>">
                            <img src="<?php echo $producto['imagen']; ?>" class="card-img-top" alt="nombre">
                        </a>
                        <div class="card-body">
                            <ul class="list-unstyled d-flex justify-content-between">
                                <li>
                                    <i class="text-warning fa fa-star"></i>
                                    <i class="text-warning fa fa-star"></i>
                                    <i class="text-warning fa fa-star"></i>
                                    <i class="text-muted fa fa-star"></i>
                                    <i class="text-muted fa fa-star"></i>
                                </li>
                                <li class="text-muted text-right"><?php echo MONEDA . ' ' . $producto['precio']; ?></li>
                            </ul>
                            <a href="<?php echo BASE_URL . 'principal/shop_single/' . $producto['id']; ?>" class="h2 text-decoration-none text-dark"><?php echo $producto['nombre']; ?></a>
                            <p class="card-text">
                                <?php echo $producto['descripcion']; ?>
                            </p>
                            <p class="text-muted">Reviews (24)</p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- End Featured Product -->

    <!-- End Script -->
<?php include_once 'template-principal/footer.php';?>
</body>

</html>