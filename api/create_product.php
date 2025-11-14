<?php

session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

// Verifica autenticacion de admin
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
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $id_category = filter_var($_POST['id_category'] ?? 0, FILTER_VALIDATE_INT);
    $id_provider = filter_var($_POST['id_provider'] ?? 0, FILTER_VALIDATE_INT);
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    
    // Validacion campos
    if (empty($name)) {
        throw new Exception('El nombre del producto es requerido');
    }
    
    if (empty($sku)) {
        throw new Exception('El SKU es requerido');
    }
    
    if (!$id_category || $id_category <= 0) {
        throw new Exception('Seleccione una categoría válida');
    }
    
    if (!$id_provider || $id_provider <= 0) {
        throw new Exception('Seleccione un proveedor válido');
    }
    
    if (empty($description)) {
        throw new Exception('La descripción es requerida');
    }
    
    // Validacion que exista una imagen
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('La imagen del producto es requerida');
    }
    
    // Verifica si ya existe un producto con el mismo SKU
    $stmt = $pdo->prepare("SELECT id_product FROM products WHERE sku = ?");
    $stmt->execute([$sku]);
    if ($stmt->fetch()) {
        throw new Exception('Ya existe un producto con ese SKU');
    }
    
    $imageFile = $_FILES['image'];
    
    if ($imageFile['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir la imagen');
    }
    
    // Validacion tipo de archivo por extensión y MIME
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $extension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception('Solo se permiten imágenes JPG y PNG');
    }
    
    // Validación adicional del MIME (más flexible)
    $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/pjpeg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $imageFile['tmp_name']);
    finfo_close($finfo);
    
    // Si el MIME no es valido pero la extensión si, permitir (algunos servidores detectan mal el MIME)
    if (!in_array($mimeType, $allowedMimeTypes) && !in_array($extension, $allowedExtensions)) {
        throw new Exception('El archivo no es una imagen válida');
    }
    
    // Validacion del tamaño (5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($imageFile['size'] > $maxSize) {
        throw new Exception('La imagen no debe superar los 5MB');
    }
    
    // Genera nombre único para la imagen
    $newFileName = 'product_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    
    // Definie ruta de destino
    $uploadDir = __DIR__ . '/../assets/img/productos/';
    
    // Crea directorio si no existe
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('No se pudo crear el directorio de imágenes');
        }
    }
    
    $uploadPath = $uploadDir . $newFileName;
    
    // Mueve el  archivo
    if (!move_uploaded_file($imageFile['tmp_name'], $uploadPath)) {
        throw new Exception('Error al guardar la imagen');
    }
    
    // Guardar ruta relativa en la BD
    $imagePath = 'productos/' . $newFileName;
    
    // Inserta producto en la base de datos
    $sql = "INSERT INTO products (name, description, image_path, id_category, id_provider, sku, status) 
            VALUES (:name, :description, :image_path, :id_category, :id_provider, :sku, :status)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':image_path' => $imagePath,
        ':id_category' => $id_category,
        ':id_provider' => $id_provider,
        ':sku' => $sku,
        ':status' => $status
    ]);
    
    if (!$result) {
        // Si falla, eliminar la imagen subida
        if (file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        throw new Exception('Error al guardar el producto en la base de datos');
    }
    
    $productId = $pdo->lastInsertId();
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Producto creado exitosamente',
        'product_id' => $productId,
        'data' => [
            'id_product' => $productId,
            'name' => $name,
            'sku' => $sku,
            'image_path' => $imagePath,
            'id_category' => $id_category,
            'id_provider' => $id_provider,
            'status' => $status
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Error en create_product.php: " . $e->getMessage());
}
?>