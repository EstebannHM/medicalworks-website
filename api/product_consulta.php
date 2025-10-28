<?php

require_once __DIR__ . '/../config/config.php';

function getProducts($pdo, $params = [])
{
    if (!($pdo instanceof PDO)) return [];

    $ids   = $params['ids'] ?? null;
    $id    = $params['id'] ?? null;
    $limit = isset($params['limit']) ? intval($params['limit']) : null;

    try {

        if ($id) {
            $idInt = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($idInt === false) return [];

            $stmt = $pdo->prepare("SELECT * FROM products WHERE id_product = :id AND status = 1 LIMIT 1");
            $stmt->bindValue(':id', $idInt, PDO::PARAM_INT);
            $stmt->execute();

            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ? [$product] : [];
        }

        if ($ids) {
            if (is_string($ids)) {
                $idsArray = explode(',', $ids);
            } else {
                $idsArray = $ids;
            }

            $idsArray = array_map('intval', $idsArray);
            $idsArray = array_filter($idsArray, fn($id) => $id > 0);

            if (empty($idsArray)) return [];

            $placeholders = str_repeat('?,', count($idsArray) - 1) . '?';
            $sql = "SELECT * FROM products WHERE id_product IN ($placeholders) AND status = 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($idsArray);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT * FROM products WHERE status = 1 ORDER BY id_product ASC";

        if ($limit && $limit > 0) {
            $sql .= " LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error en getProducts: " . $e->getMessage());
        return [];
    }
}

?>