<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/product-detail.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas, productos medical works">
    <title>Medical Works - Producto</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/product-detail.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>
    <?php include_once(__DIR__ . '/../includes/header.php'); ?>

    <main>
        <?php
        if ($pdo !== null) {
         
            showProductDetailsById($pdo, $id);
        } else {
            echo '<p style="text-align:center;">Error: no fue posible conectar a la base de datos.</p>';
        }
        ?>
    </main>

    <?php include_once(__DIR__ . '/../includes/footer.php'); ?>

    <script src="../assets/js/product-card.js"></script>
    <script src="../assets/js/header.js"></script>
</body>

</html>
