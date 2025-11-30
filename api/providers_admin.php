<?php
/**
 * API de Proveedores para Admin - Medical Works
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
    $sql = "SELECT id_provider, name, description, website_url, image_path, status FROM providers ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar los proveedores
    $sanitizedProviders = array_map(function($provider) {
        return [
            'id_provider' => (int)$provider['id_provider'],
            'name' => htmlspecialchars($provider['name'], ENT_QUOTES, 'UTF-8'),
            'description' => isset($provider['description']) ? htmlspecialchars($provider['description'], ENT_QUOTES, 'UTF-8') : '',
            'website_url' => isset($provider['website_url']) ? htmlspecialchars($provider['website_url'], ENT_QUOTES, 'UTF-8') : '',
            'image_path' => isset($provider['image_path']) ? htmlspecialchars($provider['image_path'], ENT_QUOTES, 'UTF-8') : '',
            'status' => isset($provider['status']) ? (int)$provider['status'] : 1
        ];
    }, $providers);

    // Calcular estadísticas
    $total = count($sanitizedProviders);
    $active = count(array_filter($sanitizedProviders, fn($p) => $p['status'] === 1));
    $inactive = count(array_filter($sanitizedProviders, fn($p) => $p['status'] === 0));

    echo json_encode([
        'success' => true,
        'providers' => $sanitizedProviders,
        'total' => $total,
        'stats' => [
            'active' => $active,
            'inactive' => $inactive,
            'total' => $total
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener proveedores'
    ]);
    error_log("Error en providers_admin.php: " . $e->getMessage());
}
?>