<?php
/**
 * API de Proveedores - Medical Works
 */

require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    $sql = "SELECT id_provider, name, status FROM providers ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar los proveedores
    $sanitizedProviders = array_map(function($provider) {
        return [
            'id_provider' => (int)$provider['id_provider'],
            'name' => htmlspecialchars($provider['name'], ENT_QUOTES, 'UTF-8'),
            'status' => (int)$provider['status']
        ];
    }, $providers);
    
    echo json_encode([
        'success' => true,
        'providers' => $sanitizedProviders,
        'total' => count($sanitizedProviders)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener proveedores'
    ]);
    error_log("Error en providers.php: " . $e->getMessage());
}
?>
