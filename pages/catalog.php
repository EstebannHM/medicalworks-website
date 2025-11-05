<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas, productos medical works">
    <title>Medical Works - Productos</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="/assets/css/product-card.css">
    <link rel="stylesheet" href="/assets/css/catalog.css">
    <link rel="stylesheet" href="../assets/css/whatsapp-fab.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>
    <?php
    include_once('../includes/header.php');
    ?>

    <main>

        <section class="catalog-hero">
            <div class="hero-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                Catálogo completo de productos médicos
            </div>

            <h1>Equipamiento Médico<br>de Calidad</h1>

            <p class="subtitle">
                Encuentra el equipamiento médico que necesitas para tu negocio<br>
                con la mejor calidad y precio del mercado
            </p>

            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    <div class="stat-value">8+</div>
                    <div class="stat-label">Productos</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Certificados</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-value">24h</div>
                    <div class="stat-label">Respuesta</div>
                </div>
            </div>
        </section>

        <div class="container">
            <div class="catalog-controls">
                <div class="search-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="search-input" placeholder="Buscar por nombre, descripción o ID...">
                </div>

                <div class="filter-section">
                    <span class="filter-label">Categoría:</span>
                    <button class="filter-btn active" data-category="all">Todos</button>
                    <div id="categoriesContainer"><!-- aquí se insertarán los botones dinámicos --></div>
                </div>
                <br>
                <div class="filter-section">
                    <span class="filter-label">Proveedor:</span>
                    <button class="filter-btn active" data-provider="all">Todos</button>
                    <div id="providersContainer"><!-- aquí se insertarán los botones dinámicos --></div>
                </div>
            </div>

            <div class="products-section">
                <div class="section-header">
                    <h2 class="section-title">Mostrando: <span id="pageInfo">Cargando...</span></h2>
                    <span id="totalInfo" class="product-count">Total: -- productos</span>
                </div>

                <div id="loading" class="loading-state">
                    <p>Cargando productos...</p>
                </div>

                <div id="productsContainer" style="display: none;">
                    <div class="products-grid" id="productsGrid"></div>

                    <div class="pagination" id="pagination"></div>
                </div>
            </div>
        </div>

    </main>

    <?php
    include_once('../includes/footer.php');
    include_once('../includes/whatsapp-button.php');
    ?>

    <script src="../assets/js/cart-badge.js"></script>
    <script src="../assets/js/header.js"></script>
    <script src="../assets/js/product-card.js"></script>
    <script src="../assets/js/catalog.js"></script>

</body>

</html>