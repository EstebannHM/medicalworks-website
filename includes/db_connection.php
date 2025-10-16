<?php
/**
 * Conexión a Base de Datos - MedicalWorks
 */

// Cargar .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; //Saltar los comentarios en el .env
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2); //Dividimos el nombre y su valor
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Obtener configuración | "??" asigna el valor proporsionado en las ''
// si en la variable no encuentra nada
$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

// Crear conexión PDO
try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Lanza excepcion cuando hay error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Solo accedemos por el nombre y no el indice [nombre] y no [0]
            PDO::ATTR_EMULATE_PREPARES => false // Mysql recibe los errores no los emula php, más seguridad
        ]
    );
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>