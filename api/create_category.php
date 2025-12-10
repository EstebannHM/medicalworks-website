<?php
/**
 * API para crear categorías - Medical Works
 */

session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Verificar autenticación
if (empty($_SESSION['admin_auth'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    // Validaciones
    if (empty($name)) {
        throw new Exception('El nombre de la categoría es requerido');
    }

    if (strlen($name) > 120) {
        throw new Exception('El nombre no puede exceder 120 caracteres');
    }

    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT id_category FROM categories WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        throw new Exception('Ya existe una categoría con ese nombre');
    }

    // Insertar categoría
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name]);

    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Categoría creada exitosamente',
        'id_category' => (int)$newId
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Error en create_category.php: " . $e->getMessage());
}
?>
