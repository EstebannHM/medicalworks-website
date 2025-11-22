<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Verificar autenticación de administrador
session_start();
if (empty($_SESSION['admin_auth'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

function sanitizeProduct($product) {
    return [
        'id_product' => (int)$product['id_product'],
        'name' => htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'),
        'description' => htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'),
        'image_path' => htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8'),
        'id_category' => (int)$product['id_category'],
        'id_provider' => (int)$product['id_provider'],
        'sku' => isset($product['sku']) 
            ? htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') 
            : null,
        'status' => (int)$product['status']
    ];
}

try {
    require_once __DIR__ . '/../config/config.php';
   
    
    
    $sql = "SELECT * FROM products ORDER BY id_product ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sanitizedProducts = array_map('sanitizeProduct', $products);
    
    // Calcular estadísticas
    $total = count($sanitizedProducts);
    $active = count(array_filter($sanitizedProducts, fn($p) => $p['status'] === 1));
    $inactive = count(array_filter($sanitizedProducts, fn($p) => $p['status'] === 0));
    
    echo json_encode([
        'success' => true,
        'products' => $sanitizedProducts,
        'total' => $total,
        'stats' => [
            'active' => $active,
            'inactive' => $inactive
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener productos'
    ]);
    error_log("Error en all_products_admin.php: " . $e->getMessage());
}

?>
