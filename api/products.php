<?php
/**
 * API de Productos - Medical Works
 */

require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

function sanitizeProduct($product) {
    return [
        'id_product' => (int)$product['id_product'],
        'name' => htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'),
        'description' => htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'),
        'image_path' => htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8'),
        'id_category' => (int)$product['id_category'],
        'sku' => isset($product['sku']) 
            ? htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') 
            : null,
        'status' => (int)$product['status']
    ];
}

try {
    $sql = "SELECT * FROM products WHERE status = 1 ORDER BY id_product ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sanitizedProducts = array_map('sanitizeProduct', $products);
    
    echo json_encode([
        'success' => true,
        'products' => $sanitizedProducts,
        'total' => count($sanitizedProducts)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener productos'
    ]);
    error_log("Error en products.php: " . $e->getMessage());
}

?>