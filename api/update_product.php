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
    $id_product = filter_var($_POST['id_product'] ?? 0, FILTER_VALIDATE_INT);
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $id_category = filter_var($_POST['id_category'] ?? 0, FILTER_VALIDATE_INT);
    $id_provider = filter_var($_POST['id_provider'] ?? 0, FILTER_VALIDATE_INT);
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    
    // Validacion del ID del producto
    if (!$id_product || $id_product <= 0) {
        throw new Exception('ID de producto inválido');
    }
    
    // Verifica que el producto existe
    $stmt = $pdo->prepare("SELECT id_product, image_path FROM products WHERE id_product = ?");
    $stmt->execute([$id_product]);
    $existingProduct = $stmt->fetch();
    
    if (!$existingProduct) {
        throw new Exception('El producto no existe');
    }
    
    // Validacion de campos
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
    
    // Verifica si ya existe otro producto con el mismo SKU
    $stmt = $pdo->prepare("SELECT id_product FROM products WHERE sku = ? AND id_product != ?");
    $stmt->execute([$sku, $id_product]);
    if ($stmt->fetch()) {
        throw new Exception('Ya existe otro producto con ese SKU');
    }
    
    // Manejo de la imagen (opcional en edición)
    $imagePath = $existingProduct['image_path']; // Mantiene imagen actual por defecto
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageFile = $_FILES['image'];
        
        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir la imagen');
        }
        
        // Validacion tipo de archivo por extension y MIME
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $extension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Solo se permiten imágenes JPG y PNG');
        }
        
        // Validacion adicional del MIME
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/pjpeg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $imageFile['tmp_name']);
        finfo_close($finfo);
        
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
        
        // Define ruta de destino
        $uploadDir = __DIR__ . '/../assets/img/productos/';
        
        // Crea directorio si no existe
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('No se pudo crear el directorio de imágenes');
            }
        }
        
        $uploadPath = $uploadDir . $newFileName;
        
        // Mueve el archivo
        if (!move_uploaded_file($imageFile['tmp_name'], $uploadPath)) {
            throw new Exception('Error al guardar la imagen');
        }
        
        // Eliminar imagen anterior si existe
        if (!empty($existingProduct['image_path'])) {
            $oldImagePath = __DIR__ . '/../assets/img/' . $existingProduct['image_path'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        
        // Guardar ruta relativa en la BD
        $imagePath = 'productos/' . $newFileName;
    }
    
    // Actualiza producto en la base de datos
    $sql = "UPDATE products 
            SET name = :name, 
                description = :description, 
                image_path = :image_path, 
                id_category = :id_category, 
                id_provider = :id_provider, 
                sku = :sku, 
                status = :status
            WHERE id_product = :id_product";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':image_path' => $imagePath,
        ':id_category' => $id_category,
        ':id_provider' => $id_provider,
        ':sku' => $sku,
        ':status' => $status,
        ':id_product' => $id_product
    ]);
    
    if (!$result) {
        throw new Exception('Error al actualizar el producto en la base de datos');
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Producto actualizado exitosamente',
        'product_id' => $id_product,
        'data' => [
            'id_product' => $id_product,
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
    error_log("Error en update_product.php: " . $e->getMessage());
}
?>