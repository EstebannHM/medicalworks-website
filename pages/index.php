<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Medical Works - Tu aliado en suministros médicos y equipos de protección">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas">

    <title>Medical Works - Inicio</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="../assets/css/carousel.css">
    <link rel="stylesheet" href="../assets/css/about-section.css">
    <link rel="stylesheet" href="../assets/css/product-card.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/whatsapp-fab.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>

    <?php
    include_once('../includes/header.php');
    ?>

    <main>
        <!-- HERO  -->
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

        <!-- Sección de productos destacados -->
        <section class="featured-products">
            <div class="container">
                <h2>Productos Destacados</h2>
                <p class="featured-subtitle">Descubre nuestra selección de equipamiento médico más solicitado</p>

                <div class="products-grid">
                    <?php

                    require_once __DIR__ . '/../includes/product-card.php';
                    require_once __DIR__ . '/../includes/function/product.php';

                    $products = getProducts($pdo, ['ids' => '1,2,3,4,5,6,7,8']);

                    if (count($products) > 0) {
                        foreach ($products as $product) {
                            renderProductCard($product);
                        }
                    } else {
                        echo '<p style="text-align: center; grid-column: 1/-1;">No hay productos destacados actualmente.</p>';
                    }
                    ?>
                </div>
                <div class="view-all-container">
                    <a href="catalog.php" class="btn-view-all">Ver todos los productos</a>
                </div>
            </div>
        </section>

        <!-- Sección ¿Por qué Medical Works? -->
        <section class="why-choose-us">
            <div class="why-choose-container">
                <!-- Columna de texto -->
                <div class="why-text-content">
                    <h2>¿Por qué Medical Works?</h2>
                    <p>
                        Somos líderes en distribución de equipamiento médico en Costa Rica. Con más de
                        15 años de experiencia, ayudamos a hospitales, clínicas y consultorios a obtener
                        los mejores productos médicos con garantía y soporte personalizado.
                    </p>
                    <p>
                        Nuestro equipo de expertos está disponible para asesorarte y brindarte la mejor
                        solución según tus necesidades.
                    </p>
                    <a href="about.php" class="btn-learn-more">Conoce más sobre nosotros</a>
                </div>

                <!-- Columna de imagen -->
                <div class="why-image-content">
                    <img src="../assets/img/banners/banner-1.jpg"
                        alt="Equipamiento médico Medical Works">
                </div>
            </div>
        </section>

        <!-- Sección de Características -->
        <section class="features-section">
            <div class="features-grid">

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="64px" height="64px" viewBox="0 0 24 24" stroke="#1d6172">
                            <path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#1d6172"></path>
                            <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#1d6172"></path>
                        </svg>
                    </div>
                    <h3>Calidad Garantizada</h3>
                    <p>Productos certificados y aprobados por organismos internacionales</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="#1d6172" width="64px" height="64px" viewBox="0 0 32.00 32.00" stroke="#1d6172">
                            <path d="M25.41,7.09l-9-4a1,1,0,0,0-.82,0l-9,4A1,1,0,0,0,6,8v8.56A8.69,8.69,0,0,0,8.91,23l6.43,5.71a1,1,0,0,0,1.32,0L23.09,23A8.69,8.69,0,0,0,26,16.56V8A1,1,0,0,0,25.41,7.09ZM24,16.56a6.67,6.67,0,0,1-2.24,5L16,26.66l-5.76-5.12a6.67,6.67,0,0,1-2.24-5V8.65l8-3.56,8,3.56Z"></path>
                            <path d="M13,14.29a1,1,0,0,0-1.42,1.42l2.5,2.5a1,1,0,0,0,1.42,0l5-5A1,1,0,0,0,19,11.79l-4.29,4.3Z"></path>
                        </svg>
                    </div>
                    <h3>Seguridad Primero</h3>
                    <p>Cumplimos con todas las normas de seguridad y regulaciones legales</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="#1d6172" width="64px" height="64px" viewBox="0 0 1024 1024" stroke="#1d6172">
                            <path d="M824.2 699.9a301.55 301.55 0 0 0-86.4-60.4C783.1 602.8 812 546.8 812 484c0-110.8-92.4-201.7-203.2-200-109.1 1.7-197 90.6-197 200 0 62.8 29 118.8 74.2 155.5a300.95 300.95 0 0 0-86.4 60.4C345 754.6 314 826.8 312 903.8a8 8 0 0 0 8 8.2h56c4.3 0 7.9-3.4 8-7.7 1.9-58 25.4-112.3 66.7-153.5A226.62 226.62 0 0 1 612 684c60.9 0 118.2 23.7 161.3 66.8C814.5 792 838 846.3 840 904.3c.1 4.3 3.7 7.7 8 7.7h56a8 8 0 0 0 8-8.2c-2-77-33-149.2-87.8-203.9zM612 612c-34.2 0-66.4-13.3-90.5-37.5a126.86 126.86 0 0 1-37.5-91.8c.3-32.8 13.4-64.5 36.3-88 24-24.6 56.1-38.3 90.4-38.7 33.9-.3 66.8 12.9 91 36.6 24.8 24.3 38.4 56.8 38.4 91.4 0 34.2-13.3 66.3-37.5 90.5A127.3 127.3 0 0 1 612 612zM361.5 510.4c-.9-8.7-1.4-17.5-1.4-26.4 0-15.9 1.5-31.4 4.3-46.5.7-3.6-1.2-7.3-4.5-8.8-13.6-6.1-26.1-14.5-36.9-25.1a127.54 127.54 0 0 1-38.7-95.4c.9-32.1 13.8-62.6 36.3-85.6 24.7-25.3 57.9-39.1 93.2-38.7 31.9.3 62.7 12.6 86 34.4 7.9 7.4 14.7 15.6 20.4 24.4 2 3.1 5.9 4.4 9.3 3.2 17.6-6.1 36.2-10.4 55.3-12.4 5.6-.6 8.8-6.6 6.3-11.6-32.5-64.3-98.9-108.7-175.7-109.9-110.9-1.7-203.3 89.2-203.3 199.9 0 62.8 28.9 118.8 74.2 155.5-31.8 14.7-61.1 35-86.5 60.4-54.8 54.7-85.8 126.9-87.8 204a8 8 0 0 0 8 8.2h56.1c4.3 0 7.9-3.4 8-7.7 1.9-58 25.4-112.3 66.7-153.5 29.4-29.4 65.4-49.8 104.7-59.7 3.9-1 6.5-4.7 6-8.7z"></path>
                        </svg>
                    </div>
                    <h3>Asesoría Experta</h3>
                    <p>Equipo profesional disponible para ayudarte en cada paso</p>
                </div>

            </div>
        </section>

        <!-- Sección de Proveedores -->
        <section class="providers-section">
            <div class="container">
                <h2>Nuestros Proveedores</h2>
                <p class="section-subtitle">Trabajamos con las marcas médicas más reconocidas a nivel mundial</p>

                <div class="swiper providers-swiper">
                    <div class="swiper-wrapper">

                        <?php
                        // Consulta: Solo proveedores con status = 1 (activos)
                        $stmt = $pdo->prepare("
                    SELECT name, image_path, website_url, status 
                    FROM providers 
                    WHERE status = 1 
                    ORDER BY name ASC
                ");
                        $stmt->execute();
                        $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($providers as $provider):
                        ?>

                            <div class="swiper-slide">
                                <a href="<?php echo htmlspecialchars($provider['website_url']); ?>"
                                    class="provider-card"
                                    target="_blank"
                                    rel="noopener noreferrer">

                                    <img src="../assets/img/<?php echo htmlspecialchars($provider['image_path']); ?>"
                                        alt="Logo de <?php echo htmlspecialchars($provider['name']); ?>"
                                        class="provider-logo">

                                    <h3 class="provider-name"><?php echo htmlspecialchars($provider['name']); ?></h3>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Paginación -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>

    </main>

    <?php
    include_once('../includes/footer.php');
    include_once('../includes/whatsapp-button.php');
    ?>

    <!-- Librerías externas -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Scripts del proyecto -->
    <script src="../assets/js/cart-badge.js"></script>
    <script src="../assets/js/carousel.js"></script>
    <script src="../assets/js/product-card.js"></script>
    <script src="../assets/js/header.js"></script>

</body>

</html>