<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_auth'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../config/config.php';

try {

    if (empty($_POST['id_provider'])) {
        throw new Exception('ID de proveedor es requerido');
    }
    
    $id_provider = intval($_POST['id_provider']);
    
    $stmt = $pdo->prepare("SELECT * FROM providers WHERE id_provider = ?");
    $stmt->execute([$id_provider]);
    $existingProvider = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingProvider) {
        throw new Exception('Proveedor no encontrado');
    }
    
    if (empty($_POST['name'])) {
        throw new Exception('El nombre del proveedor es requerido');
    }
    
    $name = trim($_POST['name']);
    $website_url = !empty($_POST['website_url']) ? trim($_POST['website_url']) : null;
    $status = isset($_POST['status']) ? 1 : 0;
    
    if (strlen($name) > 150) {
        throw new Exception('El nombre no puede exceder 150 caracteres');
    }
    
    if ($website_url && strlen($website_url) > 500) {
        throw new Exception('La URL del sitio web no puede exceder 500 caracteres');
    }
    
    $imagePath = $existingProvider['image_path'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        
        if ($image['size'] > 5 * 1024 * 1024) {
            throw new Exception('La imagen no debe superar los 5MB');
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($image['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Solo se permiten imágenes JPG, JPEG, PNG y WebP. Tipo detectado: ' . $mimeType);
        }
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Extensión de archivo no permitida');
        }
        
        $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $newImageName = 'provider_' . $sanitizedName . '_' . time() . '.' . $fileExtension;
        
        $uploadDir = __DIR__ . '/../assets/img/providers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . $newImageName;
        
        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            throw new Exception('Error al subir la imagen');
        }
        
        $imagePath = 'providers/' . $newImageName;
        
        if (!empty($existingProvider['image_path'])) {
            $oldImagePath = __DIR__ . '/../assets/img/' . $existingProvider['image_path'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }
    
    $stmt = $pdo->prepare("
        UPDATE providers 
        SET name = ?, image_path = ?, website_url = ?, status = ?
        WHERE id_provider = ?
    ");
    
    $success = $stmt->execute([
        $name,
        $imagePath,
        $website_url,
        $status,
        $id_provider
    ]);
    
    if (!$success) {
        if (isset($newImageName) && file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        throw new Exception('Error al actualizar el proveedor en la base de datos');
    }
    
    // Obtener datos actualizados del proveedor
    $stmt = $pdo->prepare("SELECT * FROM providers WHERE id_provider = ?");
    $stmt->execute([$id_provider]);
    $updatedProvider = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Proveedor actualizado exitosamente',
        'provider' => $updatedProvider
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}