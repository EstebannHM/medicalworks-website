<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/function/product.php';

// Validar y obtener el ID del producto
$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$productId) {
    header('Location: index.php');
    exit;
}

// Verificar conexión a BD
if (!$pdo) {
    http_response_code(500);
    die('Error: No se pudo conectar a la base de datos.');
}

// Obtener el producto
$products = getProducts($pdo, ['id' => $productId]);
$product = $products[0] ?? null;

if (!$product) {
    http_response_code(404);
    header('Location: catalog.php');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas, productos medical works">
    <title><?= $product ? htmlspecialchars($product['name']) . ' - Medical Works' : 'Producto no encontrado - Medical Works' ?></title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/product-detail.css">
    <link rel="stylesheet" href="../assets/css/whatsapp-fab.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>

    <?php
    include_once('../includes/header.php');
    ?>

    <main>
        <?php include_once('../includes/product-detail-view.php');
        renderProductDetail($product);
        ?>
    </main>

    <?php
    include_once('../includes/footer.php');
    include_once('../includes/whatsapp-button.php');
    ?>

    <script src="../assets/js/cart-badge.js"></script>
    <script src="../assets/js/product-card.js"></script>
    <script src="../assets/js/header.js"></script>
</body>

</html>