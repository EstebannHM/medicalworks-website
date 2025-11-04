<?php
/**
 * API del Carrito - Medical Works
 * Maneja: agregar, obtener, actualizar cantidad, eliminar productos
 */

session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obtener datos de la petición
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    switch ($action) {

        case 'add':
            $productId = filter_var($input['productId'] ?? 0, FILTER_VALIDATE_INT);
            $quantity = filter_var($input['quantity'] ?? 1, FILTER_VALIDATE_INT);
            
            if (!$productId || $quantity < 1) {
                throw new Exception('Datos inválidos');
            }
            
            // Verificar que el producto existe en la BD
            $stmt = $pdo->prepare('SELECT id_product, name, image_path FROM products WHERE id_product = ? AND status = 1');
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                throw new Exception('Producto no encontrado');
            }
            
            // Buscar si ya existe en el carrito
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] === $productId) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            // Si no existe, agregarlo
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'image' => $product['image_path'], // Guardamos el path completo desde la BD
                    'quantity' => $quantity,
                    'added_at' => time()
                ];
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cartCount' => count($_SESSION['cart']),
                'totalItems' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
            break;
        
        case 'get':
            echo json_encode([
                'success' => true,
                'cart' => $_SESSION['cart'],
                'cartCount' => count($_SESSION['cart']),
                'totalItems' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
            break;
        
        case 'update':
            $productId = filter_var($input['productId'] ?? 0, FILTER_VALIDATE_INT);
            $quantity = filter_var($input['quantity'] ?? 1, FILTER_VALIDATE_INT);
            
            if (!$productId || $quantity < 1) {
                throw new Exception('Datos inválidos');
            }
            
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] === $productId) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'cartCount' => count($_SESSION['cart']),
                'totalItems' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
            break;
        
        case 'remove':
            $productId = filter_var($input['productId'] ?? 0, FILTER_VALIDATE_INT);
            
            if (!$productId) {
                throw new Exception('ID inválido');
            }
            
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productId) {
                return $item['id'] !== $productId;
            });
            
            // Reindexar array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Producto eliminado',
                'cartCount' => count($_SESSION['cart']),
                'totalItems' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
            break;
        
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    error_log("Error en cart.php: " . $e->getMessage());
}
?>
