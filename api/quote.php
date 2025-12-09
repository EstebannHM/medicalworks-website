<?php

/**
 * API para generar cotizaciones en PDF
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/function/QuotePDFGenerator.php';
require_once __DIR__ . '/../includes/function/QuoteRepository.php';
require_once __DIR__ . '/../includes/function/EmailService.php';

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

    // Validar que los campos no estén vacíos
    if (empty($userData['fullName']) || empty($userData['email']) || empty($userData['phone'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Validar longitud del nombre (máximo 100 caracteres)
    if (strlen($userData['fullName']) > 100) {
        throw new Exception('El nombre no puede exceder 100 caracteres');
    }

    // Validar longitud mínima del nombre
    if (strlen($userData['fullName']) < 3) {
        throw new Exception('El nombre debe tener al menos 3 caracteres');
    }

    // Validar email
    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Validar longitud del email (máximo 200 caracteres)
    if (strlen($userData['email']) > 200) {
        throw new Exception('El correo no puede exceder 200 caracteres');
    }

    // Validar formato de teléfono (8 dígitos, opcional +506)
    if (!preg_match('/^(\+506)?[0-9]{8}$/', $userData['phone'])) {
        throw new Exception('El teléfono debe tener 8 dígitos numéricos');
    }

    // Productos del carrito
    $products = $_SESSION['cart'];

    // Generar PDF
    $generator = new QuotePDFGenerator();
    $pdfResult = $generator->generateQuotePDF($userData, $products);

    if (!$pdfResult['success']) {
        throw new Exception($pdfResult['error']);
    }

    $quoteRepo = new QuoteRepository($pdo);
    $quoteId = $quoteRepo->saveQuote($userData, $products, $pdfResult['filename']);

    // Envia correo electronico
    $emailService = new EmailService();
    $quoteData = [
        'totalProducts' => count($products),
        'totalQuantity' => array_sum(array_column($products, 'quantity'))
    ];

    $emailSent = $emailService->sendQuoteEmail(
        $userData,
        $pdfResult['filepath'],
        $quoteData
    );

    if (!$emailSent) {
        error_log("Advertencia: Email no enviado para cotización #{$quoteId}");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Cotización generada y enviada correctamente',
        'quoteId' => $quoteId,
        'emailSent' => $emailSent,
        'filename' => $pdfResult['filename']
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    error_log("Error en quote.php: " . $e->getMessage());
}
