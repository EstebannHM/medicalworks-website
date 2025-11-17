<?php
require_once __DIR__ . '/env_loader.php'; // Cargar el .env
date_default_timezone_set('America/Costa_Rica');

// Conexión PDO
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false // Mysql recibe los errores no los emula php, más seguridad
            
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Definir constantes para usar en el proyecto
define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_PORT', $_ENV['SMTP_PORT']);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME']);
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD']);
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL']);
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME']);

define('PDF_STORAGE_PATH', __DIR__ . '/../' . $_ENV['PDF_STORAGE_PATH']);

?>