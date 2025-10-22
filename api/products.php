<?php
/**
 * API de Productos - Medical Works
 * Versión DUAL: Funciona como función PHP Y como endpoint HTTP (sin implementar)
 * 
 * Uso como función (en páginas PHP):
 *   require_once '../api/products.php';
 *   $products = getProducts($pdo, ['limit' => 8]);
 * 
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Obtiene productos de la base de datos
 * 
 * @param PDO $pdo Conexión a la base de datos
 * 
 */
function getProducts($pdo, $params = []) {
    $ids = $params['ids'] ?? null;
    $limit = isset($params['limit']) ? intval($params['limit']) : null;
    
    try {
        // Productos específicos por IDs
        if ($ids) {
            // Convertir a array si viene como string
            if (is_string($ids)) {
                $idsArray = explode(',', $ids);
            } else {
                $idsArray = $ids;
            }
            
            // Limpiar y validar IDs
            $idsArray = array_map('intval', $idsArray);
            $idsArray = array_filter($idsArray, function($id) {
                return $id > 0; // Solo IDs válidos
            });
            
            // Si no hay IDs válidos, retornar array vacío
            if (empty($idsArray)) {
                return [];
            }
            
            // Construir placeholders y ejecutar query
            $placeholders = str_repeat('?,', count($idsArray) - 1) . '?';
            $sql = "SELECT * FROM products WHERE id_product IN ($placeholders) AND status = 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($idsArray);
            
        } 
        // Todos los productos o con límite
        else {
            $sql = "SELECT * FROM products WHERE status = 1 ORDER BY id_product ASC";
            
            if ($limit && $limit > 0) {
                $sql .= " LIMIT :limit";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            } else {
                $stmt = $pdo->prepare($sql);
            }
            
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Log del error (en producción debería ir a un archivo de log)
        error_log("Error en getProducts: " . $e->getMessage());
        return [];
    }
}


?>