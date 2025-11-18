<?php
/**
 * API de Categorías para Admin - Medical Works
 * Requiere sesión de administrador
 */
require_once __DIR__ . '/../config/config.php';

session_start();
header('Content-Type: application/json');

// Verificar sesión de administrador
if (empty($_SESSION['admin_auth'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

try {
    $sql = "SELECT id_category, name FROM categories ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar las categorías
    $sanitizedCategories = array_map(function($category) {
        return [
            'id_category' => (int)$category['id_category'],
            'name' => htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8')
        ];
    }, $categories);
    
    echo json_encode([
        'success' => true,
        'categories' => $sanitizedCategories,
        'total' => count($sanitizedCategories)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener categorías'
    ]);
    error_log("Error en categories_admin.php: " . $e->getMessage());
}
?>
