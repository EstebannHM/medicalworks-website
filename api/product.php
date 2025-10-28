<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Devuelve el producto (array) o null si no existe / id inválido.
 *
 * @param PDO|null $pdo
 * @param mixed $id
 * @return array|null
 */

function getProductById($pdo = null, $id) {
    if (!($pdo instanceof PDO)) return null;

 
    $idInt = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($idInt === false) return null;

    try {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id_product = :id AND status = 1 LIMIT 1');
        $stmt->bindValue(':id', $idInt, PDO::PARAM_INT); 
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ?: null;
    } catch (PDOException $e) {
        error_log('getProductById error: ' . $e->getMessage());
        return null;
    }
}
?>