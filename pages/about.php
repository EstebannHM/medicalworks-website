<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas, información medical works">
    <title>Medical Works - Quiénes somos</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    <link rel="stylesheet" href="../assets/css/components/whatsapp-fab.css">
    <link rel="stylesheet" href="../assets/css/pages/about.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>
    <?php include_once('../includes/header.php'); ?>

    <main>
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="about-hero-content">
                <h1>Quiénes Somos</h1>
                <p>Líderes en distribución de suministros médicos con más de 15 años<br>comprometidos con la salud de Costa Rica</p>
            </div>
        </section>

        <!-- Misión y Visión -->
        <section class="mission-vision-section">
            <div class="container">
                <div class="mission-vision-grid">
                    <!-- Misión -->
                    <div class="mv-card">
                        <div class="mv-icon mv-icon-blue">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                            </svg>
                        </div>
                        <h2>Nuestra Misión</h2>
                        <p>Brindar a nuestros clientes los mejores productos en el área de la salud, a través de la
                            representación de empresas internacionales con productos de alta calidad y tecnología
                            avanzada, que proporcionen soluciones a los especialistas para sus pacientes en busca de
                            calidad de vida para ellos y sus familias. Unido a un trato personalizado y dedicado, que
                            garantice la satisfacción de nuestros clientes, y de los usuarios finales de nuestros productos
                            y marcas representadas</p>
                    </div>

                    <!-- Visión -->
                    <div class="mv-card">
                        <div class="mv-icon mv-icon-green">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                        </div>
                        <h2>Nuestra Visión</h2>
                        <p>Ser una de las empresas líderes del país en el campo de la salud. Llevar nuestras marcas
                            representadas a todos los rincones del país y posicionarlas en mercados internacionales</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nuestros Valores -->
        <section class="values-section">
            <div class="container">
                <h2 class="section-title">Nuestros Valores</h2>
                <div class="values-grid">
                    <!-- Excelencia -->
                    <div class="value-card">
                        <div class="value-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                        <h3>Excelencia</h3>
                        <p>Compromiso constante con la calidad y la búsqueda de la perfección en cada proceso</p>
                    </div>

                    <!-- Integridad -->
                    <div class="value-card">
                        <div class="value-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </div>
                        <h3>Integridad</h3>
                        <p>Transparencia, honestidad y ética en todas nuestras relaciones comerciales</p>
                    </div>

                    <!-- Compromiso -->
                    <div class="value-card">
                        <div class="value-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <h3>Compromiso</h3>
                        <p>Dedicación total con nuestros clientes y la mejora continua del sector salud</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php
    include_once('../includes/footer.php');
    include_once('../includes/whatsapp-button.php');
    ?>

    <script src="../assets/js/header.js"></script>
</body>

</html>