<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas, carrito medical works">
    <title>Medical Works - Carrito</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/shared/toast.css">
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    <link rel="stylesheet" href="../assets/css/components/whatsapp-fab.css">
    <link rel="stylesheet" href="../assets/css/pages/cart.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>
    <?php
    include_once('../includes/header.php');
    ?>

    <main>

        <div class="cart-container">
            <!-- Columna izquierda: Tabla de productos -->
            <div>
                <div class="cart-header">
                    <h1>Carrito de Compras</h1>
                    <p class="cart-subtitle"><span id="cartCount">0</span> productos en tu carrito</p>
                </div>

                <div class="cart-table" id="cartContent">
                </div>
            </div>

            <!-- Columna derecha: Resumen -->
            <div class="cart-summary">
                <h2>Resumen</h2>

                <div class="summary-row">
                    <label>Total de productos:</label>
                    <span class="value" id="totalProducts">0</span>
                </div>

                <div class="summary-row">
                    <label>Cantidad total:</label>
                    <span class="value"><span id="totalQuantity">0</span> unidades</span>
                </div>

                <div class="summary-divider"></div>

                <a href="/pages/quote.php" class="btn-generate">
                    Generar cotización
                </a>

                <a href="/pages/catalog.php" class="btn-continue">
                    Continuar comprando
                </a>
            </div>
        </div>

        <!-- Toast Container -->
        <div class="toast-container" id="toastContainer"></div>
    </main>

    <?php
    include_once('../includes/footer.php');
    include_once('../includes/whatsapp-button.php');
    ?>

    <!-- Shared Toast System -->
    <script src="../assets/js/shared/toast.js"></script>

    <!-- Page Scripts -->
    <script src="../assets/js/cart-badge.js"></script>
    <script src="../assets/js/header.js"></script>
    <script src="../assets/js/cart.js"></script>

</body>

</html>