<?php
/**
 * API para generar cotizaciones en PDF
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/function/QuotePDFGenerator.php';

header('Content-Type: application/json');

try {
    // Verificar carrito
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        throw new Exception('No hay productos en el carrito');
    }
    
    // Obtener datos
    $input = json_decode(file_get_contents('php://input'), true);

    // Validar datos del usuario
    $userData = [
        'fullName' => htmlspecialchars(strip_tags($input['fullName'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'email' => filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL),
        'phone' => htmlspecialchars(strip_tags($input['phone'] ?? ''), ENT_QUOTES, 'UTF-8')
    ];
    
    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    if (empty($userData['fullName']) || empty($userData['phone'])) {
        throw new Exception('Todos los campos son requeridos');
    }
    
    // Productos del carrito
    $products = $_SESSION['cart'];
    
    // Generar PDF
    $generator = new QuotePDFGenerator();
    $result = $generator->generateQuotePDF($userData, $products);
    
    if (!$result['success']) {
        throw new Exception($result['error']);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cotización generada correctamente',
        'filename' => $result['filename'],
        'data' => [
            'customerName' => $userData['fullName'],
            'totalProducts' => count($products),
            'totalQuantity' => array_sum(array_column($products, 'quantity'))
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    error_log("Error en quote.php: " . $e->getMessage());
}