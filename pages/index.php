<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Medical Works - Tu aliado en suministros médicos y equipos de protección">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas">

    <title>Medical Works - Inicio</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/carousel.css">
    <link rel="stylesheet" href="../assets/css/product-card.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>

    <?php
        include_once('../includes/header.php');
    ?>
  
    <main>
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">


                <div class="swiper-slide">
                    <img src="../assets/img/banners/banner-1.jpg" alt="Suministros médicos">
                    <div class="slide-content">
                        <h1>Tu aliado en suministros médicos</h1>
                        <p>Asesoría profesional y atención personalizada</p>
                        <div class="slide-buttons">
                            <a href="catalog.php" class="btn-primary">Ver productos</a>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <img src="../assets/img/banners/banner-2.jpg" alt="Sobre nosotros">
                    <div class="slide-content">
                        <h1>Más de 10 años de experiencia</h1>
                        <p>Conoce nuestra historia y compromiso con la salud</p>
                        <div class="slide-buttons">
                            <a href="about.php" class="btn-secondary">Quiénes somos</a>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <img src="../assets/img/banners/banner-3.jpg" alt="Equipos de protección">
                    <div class="slide-content">
                        <h1>¿Tienes preguntas? ¡Contáctanos!</h1>
                        <p>Estamos aquí para ayudarte y brindarte la mejor atención</p>
                        <div class="slide-buttons">
                            <a href="contact.php" class="btn-primary">Contactar ahora</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            <div class="swiper-pagination"></div>
        </div>

        <section class="featured-products">
            <div class="container">
                <h2>Productos Destacados</h2>
                <p class="featured-subtitle">Descubre nuestra selección de equipamiento médico más solicitado</p>

                <div class="products-grid">
                    <?php

                    require_once __DIR__ . '/../includes/product-card.php';
                    require_once __DIR__ . '/../api/products.php';

                    $products = getProducts($pdo, ['limit' => 8]);

                    if (count($products) > 0) {
                        foreach ($products as $product) {
                            renderProductCard($product);
                        }
                    } else {
                        echo '<p style="text-align: center; grid-column: 1/-1;">No hay productos disponibles.</p>';
                    }
                    ?>
                </div>
                <div class="view-all-container">
                    <a href="catalog.php" class="btn-view-all">Ver todos los productos</a>
                </div>
            </div>
        </section>
    </main>
  
    <?php 
        include_once('../includes/footer.php');
    ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="../assets/js/carousel.js"></script>
    <script src="../assets/js/product-card.js"></script>
    <script src="../assets/js/header.js"></script>
    <script src="../assets/js/footer.js"></script>

</body>

</html>