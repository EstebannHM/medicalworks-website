<?php
/**
 * API para crear proveedores - Medical Works
 */

require_once __DIR__ . '/../config/config.php';

session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_auth'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

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
    $website_url = isset($_POST['website_url']) ? trim($_POST['website_url']) : null;
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($name)) {
        throw new Exception('El nombre del proveedor es requerido');
    }

    if (strlen($name) > 150) {
        throw new Exception('El nombre no puede exceder 150 caracteres');
    }

    if (!empty($website_url) && strlen($website_url) > 255) {
        throw new Exception('La URL del sitio web no puede exceder 255 caracteres');
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('La imagen del proveedor es requerida');
    }

    $image = $_FILES['image'];
    
    if ($image['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir la imagen');
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($image['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Solo se permiten imágenes JPG, JPEG, PNG y WebP. Tipo detectado: ' . $mimeType);
    }

    $extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
        throw new Exception('Extensión de archivo no válida');
    }

    $maxSize = 5 * 1024 * 1024;
    if ($image['size'] > $maxSize) {
        throw new Exception('La imagen no debe superar los 5MB');
    }

    $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    $sanitizedName = preg_replace('/_+/', '_', $sanitizedName);
    $sanitizedName = trim($sanitizedName, '_');
    $timestamp = time();
    $newImageName = 'provider_' . $sanitizedName . '_' . $timestamp . '.' . $extension;

    // Directorio de destino
    $uploadDir = __DIR__ . '/../assets/img/providers/';

    $uploadPath = $uploadDir . $newImageName;

    if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
        throw new Exception('Error al guardar la imagen');
    }

    $imagePath = 'providers/' . $newImageName;

    $sql = "INSERT INTO providers (name, website_url, image_path, status) 
            VALUES (:name, :website_url, :image_path, :status)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':website_url', $website_url, PDO::PARAM_STR);
    $stmt->bindParam(':image_path', $imagePath, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        if (file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        throw new Exception('Error al guardar el proveedor en la base de datos');
    }

    $providerId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Proveedor creado exitosamente',
        'provider' => [
            'id_provider' => (int)$providerId,
            'name' => $name,
            'website_url' => $website_url,
            'image_path' => $imagePath,
            'status' => $status
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Error en create_provider.php: " . $e->getMessage());
}
?>