<?php
require_once __DIR__ . '/../../config/config.php';

/**
 */

function getProducts($pdo, $params = []){

    if (!($pdo instanceof PDO)) return [];
    $ids = $params['ids'] ?? null;
    $id = $params['id'] ?? null;

    try {
        // Caso 1: Obtener UN producto por ID
        if ($id) {
            $idValidate = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($idValidate == false) return [];
            
            //Consulta
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id_product = :id AND status = 1 LIMIT 1');
            $stmt->bindValue(':id', $idValidate, PDO::PARAM_INT);
            $stmt->execute();
            
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ? [$product] : [];
        }
        
        // Caso 2: Obtener VARIOS productos por IDs
        if ($ids) {
            // Convertir string "1,2,3" a array [1, 2, 3] con if
            $idsArray = is_string($ids) ? explode(',', $ids) : $ids;
            
            // Limpiar y validar IDs
            $idsArray = array_map('intval', $idsArray);
            $idsArray = array_filter($idsArray, fn($id) => $id > 0);
            
            if (empty($idsArray)) return [];
            
            // Crear placeholders: ?, ?, ? para la consulta "? = 1, ? = 2 ..."
            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $sql = "SELECT * FROM products WHERE id_product IN ($placeholders) AND status = 1 ORDER BY id_product ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($idsArray);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return [];
        
    } catch (PDOException $e) {
        error_log('Error en getProducts: ' . $e->getMessage());
        return [];
    }
}

?>