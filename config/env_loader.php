<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Carga las variables desde el archivo .env en la raíz del proyecto
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
?>