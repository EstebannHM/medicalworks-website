<?php
/**
 * API para actualizar categorías - Medical Works
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
    $id_category = filter_var($_POST['id_category'] ?? 0, FILTER_VALIDATE_INT);
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    // Validación del ID
    if (!$id_category || $id_category <= 0) {
        throw new Exception('ID de categoría inválido');
    }

    // Verificar que la categoría existe
    $stmt = $pdo->prepare("SELECT id_category FROM categories WHERE id_category = ?");
    $stmt->execute([$id_category]);
    if (!$stmt->fetch()) {
        throw new Exception('La categoría no existe');
    }

    // Validaciones
    if (empty($name)) {
        throw new Exception('El nombre de la categoría es requerido');
    }

    if (strlen($name) > 100) {
        throw new Exception('El nombre no puede exceder 100 caracteres');
    }

    // Verificar si ya existe otra categoría con el mismo nombre
    $stmt = $pdo->prepare("SELECT id_category FROM categories WHERE name = ? AND id_category != ?");
    $stmt->execute([$name, $id_category]);
    if ($stmt->fetch()) {
        throw new Exception('Ya existe otra categoría con ese nombre');
    }

    // Actualizar categoría
    $sql = "UPDATE categories SET name = ? WHERE id_category = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $id_category]);

    echo json_encode([
        'success' => true,
        'message' => 'Categoría actualizada exitosamente',
        'id_category' => $id_category
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Error en update_category.php: " . $e->getMessage());
}
?>
