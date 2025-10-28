<?php
/**
 * API de Productos - Medical Works
 * 
 */

require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM products WHERE status = 1 ORDER BY id_product ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'total' => count($products)
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