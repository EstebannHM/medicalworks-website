<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Método no permitido']);
  exit;
}

// Leer datos JSON del body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id_product']) || !isset($data['status'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
  exit;
}

$id_product = filter_var($data['id_product'], FILTER_VALIDATE_INT);
$status = filter_var($data['status'], FILTER_VALIDATE_INT);

if ($id_product === false || $status === false || $id_product <= 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
  exit;
}

// Validar que status sea solo 0 o 1
if (!in_array($status, [0, 1], true)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Status inválido. Solo se permiten valores 0 o 1']);
  exit;
}

try {
  require_once __DIR__ . '/../config/config.php';
  
  $stmt = $pdo->prepare("UPDATE products SET status = :status WHERE id_product = :id_product");
  $stmt->bindParam(':status', $status, PDO::PARAM_INT);
  $stmt->bindParam(':id_product', $id_product, PDO::PARAM_INT);
  
  if ($stmt->execute()) {
    echo json_encode([
      'success' => true,
      'message' => 'Status actualizado correctamente',
      'id_product' => $id_product,
      'new_status' => $status
    ]);
  } else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el status']);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Error de base de datos']);
  error_log("Error actualizando status del producto: " . $e->getMessage());
}
